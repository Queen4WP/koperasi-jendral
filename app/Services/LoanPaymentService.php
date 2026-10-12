<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Str;

class LoanPaymentService
{
    protected $activityLogService;
    protected $notificationService;

    public function __construct(
        ActivityLogService $activityLogService = null,
        NotificationService $notificationService = null
    ) {
        $this->activityLogService = $activityLogService ?? resolve(ActivityLogService::class);
        $this->notificationService = $notificationService ?? resolve(NotificationService::class);
    }
    public function createPaymentAttempt($installmentId, $metode = 'online', $tanggalBayar = null, $buktiPembayaran = null, $externalReference = null)
    {
        if (!in_array($metode, ['online', 'kantor'])) {
            throw new RuntimeException("Metode pembayaran tidak valid: {$metode}.");
        }

        if ($metode === 'online' && empty($buktiPembayaran)) {
            throw new RuntimeException('Bukti pembayaran wajib diunggah untuk pembayaran online.');
        }

        return DB::transaction(function () use ($installmentId, $metode, $tanggalBayar, $buktiPembayaran, $externalReference) {
            $installment = LoanInstallment::lockForUpdate()->find($installmentId);
            if (!$installment) {
                throw new RuntimeException('Tagihan tidak ditemukan.');
            }

            if ($installment->status === 'paid') {
                throw new RuntimeException('Tagihan sudah lunas.');
            }

            // Prevent duplicate pending payment attempts
            $existingPending = LoanPayment::where('installment_id', $installmentId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();
            if ($existingPending) {
                throw new RuntimeException('Sudah ada pembayaran angsuran yang sedang menunggu verifikasi.');
            }

            // Server-derived authoritative nominal (full payment only)
            $nominalBayar = $installment->total_tagihan;
            $tanggalBayar = $tanggalBayar ?? now();
            $reference = 'PAY-' . strtoupper(Str::random(10));

            return LoanPayment::create([
                'installment_id' => $installmentId,
                'admin_id' => null,
                'nominal_bayar' => $nominalBayar,
                'denda_dibayar' => 0,
                'metode_pembayaran' => $metode,
                'tanggal_bayar' => $tanggalBayar,
                'payment_reference' => $reference,
                'external_reference' => $externalReference,
                'bukti_pembayaran' => $buktiPembayaran,
                'status' => 'pending',
                'catatan' => null,
            ]);
        });
    }

    public function recordOfficePayment($installmentId, $adminId, $catatan = null)
    {
        return DB::transaction(function () use ($installmentId, $adminId, $catatan) {
            $installment = LoanInstallment::lockForUpdate()->find($installmentId);
            if (!$installment) {
                throw new RuntimeException('Tagihan tidak ditemukan.');
            }
            if ($installment->status === 'paid') {
                throw new RuntimeException('Tagihan sudah lunas.');
            }

            // Invalidate any orphaned pending payment
            LoanPayment::where('installment_id', $installmentId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'failed',
                    'catatan' => 'Dibatalkan karena pembayaran langsung di kantor koperasi.',
                ]);

            $nominalBayar = $installment->total_tagihan;
            $reference = 'PAY-OFC-' . strtoupper(Str::random(8));

            $payment = LoanPayment::create([
                'installment_id' => $installmentId,
                'admin_id' => $adminId,
                'nominal_bayar' => $nominalBayar,
                'denda_dibayar' => 0,
                'metode_pembayaran' => 'kantor',
                'tanggal_bayar' => now(),
                'payment_reference' => $reference,
                'external_reference' => null,
                'bukti_pembayaran' => null,
                'status' => 'success',
                'catatan' => $catatan,
            ]);

            $installment->status = 'paid';
            $installment->save();

            $loanService = app(LoanService::class);
            $loanService->markAsPaidOffIfCompleted($installment->loan_id);

            $this->activityLogService->record(
                'loan_payment.approved',
                $payment,
                "Pembayaran langsung di kantor angsuran ke-{$installment->angsuran_ke} [{$payment->payment_reference}] sebesar Rp " . number_format($payment->nominal_bayar, 0, ',', '.') . " dicatat.",
                [
                    'payment_id' => $payment->id,
                    'installment_id' => $installment->id,
                    'loan_id' => $installment->loan_id,
                    'angsuran_ke' => $installment->angsuran_ke,
                    'payment_reference' => $payment->payment_reference,
                    'nominal' => (float)$payment->nominal_bayar,
                    'metode' => 'kantor',
                    'status' => 'success',
                ],
                $adminId
            );

            $memberUserId = $installment->loan?->member?->user_id;
            if ($memberUserId) {
                $this->notificationService->send(
                    $memberUserId,
                    NotificationType::LOAN_PAYMENT_APPROVED,
                    'Pembayaran Angsuran Kantor Berhasil',
                    "Pembayaran langsung angsuran ke-{$installment->angsuran_ke} di kantor koperasi [{$payment->payment_reference}] sebesar Rp " . number_format($payment->nominal_bayar, 0, ',', '.') . " berhasil dicatat.",
                    [
                        'payment_id' => $payment->id,
                        'installment_id' => $installment->id,
                        'loan_id' => $installment->loan_id,
                        'angsuran_ke' => $installment->angsuran_ke,
                        'payment_reference' => $payment->payment_reference,
                        'nominal' => (float)$payment->nominal_bayar,
                        'metode' => 'kantor',
                    ]
                );
            }

            return $payment;
        });
    }

    public function markPaymentSuccess($paymentId, $adminId = null)
    {
        return DB::transaction(function () use ($paymentId, $adminId) {
            $payment = LoanPayment::where('id', $paymentId)->lockForUpdate()->first();
            if (!$payment) {
                throw new RuntimeException('Payment tidak ditemukan.');
            }

            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment tidak dalam status pending.');
            }

            $installment = LoanInstallment::where('id', $payment->installment_id)->lockForUpdate()->first();
            if ($installment->status === 'paid') {
                $payment->status = 'failed';
                $payment->catatan = 'Tagihan sudah lunas oleh pembayaran lain.';
                $payment->save();
                throw new RuntimeException('Tagihan sudah lunas oleh pembayaran lain.');
            }

            $hasSuccess = LoanPayment::where('installment_id', $installment->id)
                ->where('status', 'success')
                ->exists();

            if ($hasSuccess) {
                $payment->status = 'failed';
                $payment->catatan = 'Sudah ada pembayaran sukses untuk tagihan ini.';
                $payment->save();
                throw new RuntimeException('Sudah ada pembayaran sukses untuk tagihan ini.');
            }

            $payment->status = 'success';
            $payment->admin_id = $adminId;
            $payment->save();

            $installment->status = 'paid';
            $installment->save();

            $loanService = app(LoanService::class);
            $loanService->markAsPaidOffIfCompleted($installment->loan_id);

            $this->activityLogService->record(
                'loan_payment.approved',
                $payment,
                "Pembayaran angsuran ke-{$installment->angsuran_ke} [{$payment->payment_reference}] sebesar Rp " . number_format($payment->nominal_bayar, 0, ',', '.') . " disetujui.",
                [
                    'payment_id' => $payment->id,
                    'installment_id' => $installment->id,
                    'loan_id' => $installment->loan_id,
                    'angsuran_ke' => $installment->angsuran_ke,
                    'payment_reference' => $payment->payment_reference,
                    'nominal' => (float)$payment->nominal_bayar,
                    'status' => 'success',
                ],
                $adminId
            );

            $memberUserId = $installment->loan?->member?->user_id;
            if ($memberUserId) {
                $this->notificationService->send(
                    $memberUserId,
                    NotificationType::LOAN_PAYMENT_APPROVED,
                    'Pembayaran Angsuran Disetujui',
                    "Pembayaran angsuran ke-{$installment->angsuran_ke} [{$payment->payment_reference}] sebesar Rp " . number_format($payment->nominal_bayar, 0, ',', '.') . " telah disetujui.",
                    [
                        'payment_id' => $payment->id,
                        'installment_id' => $installment->id,
                        'loan_id' => $installment->loan_id,
                        'angsuran_ke' => $installment->angsuran_ke,
                        'payment_reference' => $payment->payment_reference,
                        'nominal' => (float)$payment->nominal_bayar,
                    ]
                );
            }

            return $payment;
        });
    }

    public function markPaymentFailed($paymentId, $adminId = null, $catatan = null)
    {
        return DB::transaction(function () use ($paymentId, $adminId, $catatan) {
            $payment = LoanPayment::where('id', $paymentId)->lockForUpdate()->first();
            if (!$payment) {
                throw new RuntimeException('Payment tidak ditemukan.');
            }

            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment tidak dalam status pending.');
            }

            $payment->status = 'failed';
            $payment->admin_id = $adminId;
            $payment->catatan = $catatan;
            $payment->save();

            $this->activityLogService->record(
                'loan_payment.rejected',
                $payment,
                "Pembayaran angsuran [{$payment->payment_reference}] ditolak. Alasan: {$catatan}",
                [
                    'payment_id' => $payment->id,
                    'installment_id' => $payment->installment_id,
                    'payment_reference' => $payment->payment_reference,
                    'rejection_reason' => $catatan,
                    'status' => 'failed',
                ],
                $adminId
            );

            $memberUserId = $payment->installment?->loan?->member?->user_id;
            if ($memberUserId) {
                $msg = "Pembayaran angsuran [{$payment->payment_reference}] ditolak." . ($catatan ? " Alasan: {$catatan}" : '');
                $this->notificationService->send(
                    $memberUserId,
                    NotificationType::LOAN_PAYMENT_REJECTED,
                    'Pembayaran Angsuran Ditolak',
                    $msg,
                    [
                        'payment_id' => $payment->id,
                        'installment_id' => $payment->installment_id,
                        'payment_reference' => $payment->payment_reference,
                        'rejection_reason' => $catatan,
                    ]
                );
            }

            return $payment;
        });
    }
}
