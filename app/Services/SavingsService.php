<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Str;

class SavingsService
{
    public const POKOK_AMOUNT = 55000.00;
    public const WAJIB_AMOUNT = 100000.00;

    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService = null)
    {
        $this->activityLogService = $activityLogService ?? resolve(ActivityLogService::class);
    }

    public function createPokok($memberId, $nominal = null)
    {
        // Nilai kewajiban Simpanan Pokok adalah tetap Rp55.000
        if ($nominal !== null && bccomp((string)$nominal, (string)self::POKOK_AMOUNT, 2) !== 0) {
            throw new RuntimeException('Nominal Simpanan Pokok harus Rp 55.000.');
        }
        $nominal = self::POKOK_AMOUNT;

        return DB::transaction(function () use ($memberId, $nominal) {
            $existing = Saving::where('member_id', $memberId)
                ->where('jenis_simpanan', 'pokok')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new RuntimeException('Member sudah memiliki simpanan pokok.');
            }

            return Saving::create([
                'member_id' => $memberId,
                'jenis_simpanan' => 'pokok',
                'periode' => null,
                'nominal' => $nominal,
                'status' => 'unpaid',
            ]);
        });
    }

    public function createWajib($memberId, $nominal = null, $periode, $jatuhTempo = null)
    {
        // Nilai kewajiban Simpanan Wajib adalah tetap Rp100.000 per bulan
        if ($nominal !== null && bccomp((string)$nominal, (string)self::WAJIB_AMOUNT, 2) !== 0) {
            throw new RuntimeException('Nominal Simpanan Wajib harus Rp 100.000 per bulan.');
        }
        $nominal = self::WAJIB_AMOUNT;

        return DB::transaction(function () use ($memberId, $nominal, $periode, $jatuhTempo) {
            $existing = Saving::where('member_id', $memberId)
                ->where('jenis_simpanan', 'wajib')
                ->where('periode', $periode)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new RuntimeException('Simpanan wajib untuk periode ini sudah ada.');
            }

            return Saving::create([
                'member_id' => $memberId,
                'jenis_simpanan' => 'wajib',
                'periode' => $periode,
                'nominal' => $nominal,
                'status' => 'unpaid',
                'jatuh_tempo' => $jatuhTempo,
            ]);
        });
    }

    public function createPaymentAttempt($savingId, $metode = 'online', $tanggalBayar = null, $buktiPembayaran = null, $externalReference = null)
    {
        if (!in_array($metode, ['online', 'kantor'])) {
            throw new RuntimeException("Metode pembayaran tidak valid: {$metode}.");
        }

        if ($metode === 'online' && empty($buktiPembayaran)) {
            throw new RuntimeException('Bukti pembayaran wajib diunggah untuk pembayaran online.');
        }

        return DB::transaction(function () use ($savingId, $metode, $tanggalBayar, $buktiPembayaran, $externalReference) {
            $saving = Saving::lockForUpdate()->find($savingId);
            if (!$saving) {
                throw new RuntimeException('Data simpanan tidak ditemukan.');
            }
            if ($saving->status === 'paid') {
                throw new RuntimeException('Simpanan ini sudah lunas.');
            }

            // Prevent duplicate pending payment attempts
            $existingPending = SavingsTransaction::where('saving_id', $savingId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();
            if ($existingPending) {
                throw new RuntimeException('Sudah ada pembayaran simpanan yang sedang menunggu verifikasi.');
            }

            // Server-derived authoritative nominal
            $nominalBayar = $saving->nominal;
            $tanggalBayar = $tanggalBayar ?? now();
            $reference = 'SAV-' . strtoupper(Str::random(10));

            return SavingsTransaction::create([
                'saving_id' => $savingId,
                'admin_id' => null,
                'nominal_bayar' => $nominalBayar,
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

    public function recordOfficePayment($savingId, $adminId, $catatan = null)
    {
        return DB::transaction(function () use ($savingId, $adminId, $catatan) {
            $saving = Saving::lockForUpdate()->find($savingId);
            if (!$saving) {
                throw new RuntimeException('Data simpanan tidak ditemukan.');
            }
            if ($saving->status === 'paid') {
                throw new RuntimeException('Simpanan ini sudah lunas.');
            }

            // Invalidate any orphaned pending transaction
            SavingsTransaction::where('saving_id', $savingId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'failed',
                    'catatan' => 'Dibatalkan karena pembayaran langsung di kantor koperasi.',
                ]);

            $nominalBayar = $saving->nominal;
            $reference = 'SAV-OFC-' . strtoupper(Str::random(8));

            $transaction = SavingsTransaction::create([
                'saving_id' => $savingId,
                'admin_id' => $adminId,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => 'kantor',
                'tanggal_bayar' => now(),
                'payment_reference' => $reference,
                'external_reference' => null,
                'bukti_pembayaran' => null,
                'status' => 'success',
                'catatan' => $catatan,
            ]);

            $saving->status = 'paid';
            $saving->save();

            $this->activityLogService->record(
                'savings_payment.approved',
                $transaction,
                "Pembayaran simpanan {$saving->jenis_simpanan} [{$transaction->payment_reference}] sebesar Rp " . number_format($transaction->nominal_bayar, 0, ',', '.') . " melalui kantor disetujui.",
                [
                    'transaction_id' => $transaction->id,
                    'saving_id' => $saving->id,
                    'jenis_simpanan' => $saving->jenis_simpanan,
                    'payment_reference' => $transaction->payment_reference,
                    'nominal' => (float)$transaction->nominal_bayar,
                    'metode' => 'kantor',
                    'status' => 'success',
                ],
                $adminId
            );

            return $transaction;
        });
    }

    public function markPaymentSuccess($transactionId, $adminId = null)
    {
        return DB::transaction(function () use ($transactionId, $adminId) {
            $transaction = SavingsTransaction::where('id', $transactionId)->lockForUpdate()->first();
            if (!$transaction) {
                throw new RuntimeException('Transaksi tidak ditemukan.');
            }
            if ($transaction->status !== 'pending') {
                throw new RuntimeException('Transaksi tidak dalam status pending.');
            }

            $saving = Saving::where('id', $transaction->saving_id)->lockForUpdate()->first();
            if ($saving->status === 'paid') {
                $transaction->status = 'failed';
                $transaction->catatan = 'Tagihan sudah dibayar lunas oleh transaksi lain.';
                $transaction->save();
                throw new RuntimeException('Tagihan sudah dibayar lunas oleh transaksi lain.');
            }

            $transaction->status = 'success';
            $transaction->admin_id = $adminId;
            $transaction->save();

            $saving->status = 'paid';
            $saving->save();

            $this->activityLogService->record(
                'savings_payment.approved',
                $transaction,
                "Pembayaran simpanan {$saving->jenis_simpanan} [{$transaction->payment_reference}] sebesar Rp " . number_format($transaction->nominal_bayar, 0, ',', '.') . " disetujui.",
                [
                    'transaction_id' => $transaction->id,
                    'saving_id' => $saving->id,
                    'jenis_simpanan' => $saving->jenis_simpanan,
                    'payment_reference' => $transaction->payment_reference,
                    'nominal' => (float)$transaction->nominal_bayar,
                    'status' => 'success',
                ],
                $adminId
            );

            return $transaction;
        });
    }

    public function markPaymentFailed($transactionId, $adminId = null, $catatan = null)
    {
        return DB::transaction(function () use ($transactionId, $adminId, $catatan) {
            $transaction = SavingsTransaction::where('id', $transactionId)->lockForUpdate()->first();
            if (!$transaction) {
                throw new RuntimeException('Transaksi tidak ditemukan.');
            }
            if ($transaction->status !== 'pending') {
                throw new RuntimeException('Transaksi tidak dalam status pending.');
            }

            $transaction->status = 'failed';
            $transaction->admin_id = $adminId;
            $transaction->catatan = $catatan;
            $transaction->save();

            $this->activityLogService->record(
                'savings_payment.rejected',
                $transaction,
                "Pembayaran simpanan [{$transaction->payment_reference}] ditolak. Alasan: {$catatan}",
                [
                    'transaction_id' => $transaction->id,
                    'saving_id' => $transaction->saving_id,
                    'payment_reference' => $transaction->payment_reference,
                    'rejection_reason' => $catatan,
                    'status' => 'failed',
                ],
                $adminId
            );

            return $transaction;
        });
    }
}
