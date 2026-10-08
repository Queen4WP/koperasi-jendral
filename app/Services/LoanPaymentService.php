<?php

namespace App\Services;

use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Str;

class LoanPaymentService
{
    public function createPaymentAttempt($installmentId, $nominalBayar, $metode, $tanggalBayar, $externalReference = null)
    {
        if ($nominalBayar < 0) {
            throw new RuntimeException('Nominal bayar tidak boleh negatif.');
        }

        return DB::transaction(function () use ($installmentId, $nominalBayar, $metode, $tanggalBayar, $externalReference) {
            $installment = LoanInstallment::lockForUpdate()->find($installmentId);
            if (!$installment) {
                throw new RuntimeException('Tagihan tidak ditemukan.');
            }

            if ($installment->status === 'paid') {
                throw new RuntimeException('Tagihan sudah lunas.');
            }

            if (bccomp((string)$nominalBayar, (string)$installment->total_tagihan, 2) !== 0) {
                throw new RuntimeException('Pembayaran parsial tidak didukung. Nominal harus sesuai tagihan.');
            }

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
                'status' => 'pending',
            ]);
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
                $payment->save();
                throw new RuntimeException('Tagihan sudah lunas oleh pembayaran lain.');
            }

            $hasSuccess = LoanPayment::where('installment_id', $installment->id)
                ->where('status', 'success')
                ->exists();

            if ($hasSuccess) {
                $payment->status = 'failed';
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

            return $payment;
        });
    }

    public function markPaymentFailed($paymentId)
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = LoanPayment::where('id', $paymentId)->lockForUpdate()->first();
            if (!$payment) {
                throw new RuntimeException('Payment tidak ditemukan.');
            }

            if ($payment->status !== 'pending') {
                throw new RuntimeException('Payment tidak dalam status pending.');
            }

            $payment->status = 'failed';
            $payment->save();

            return $payment;
        });
    }
}
