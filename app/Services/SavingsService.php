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

    public function createPaymentAttempt($savingId, $nominalBayar, $metode, $tanggalBayar, $externalReference = null)
    {
        if ($nominalBayar < 0) {
            throw new RuntimeException('Nominal bayar tidak boleh negatif.');
        }

        return DB::transaction(function () use ($savingId, $nominalBayar, $metode, $tanggalBayar, $externalReference) {
            $saving = Saving::lockForUpdate()->find($savingId);
            if (!$saving) {
                throw new RuntimeException('Data simpanan tidak ditemukan.');
            }
            if ($saving->status === 'paid') {
                throw new RuntimeException('Simpanan ini sudah lunas.');
            }
            if (bccomp((string)$nominalBayar, (string)$saving->nominal, 2) !== 0) {
                throw new RuntimeException('Nominal bayar harus sesuai tagihan.');
            }

            $reference = 'SAV-' . strtoupper(Str::random(10));
            return SavingsTransaction::create([
                'saving_id' => $savingId,
                'admin_id' => null,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => $metode,
                'tanggal_bayar' => $tanggalBayar,
                'payment_reference' => $reference,
                'external_reference' => $externalReference,
                'status' => 'pending',
            ]);
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
                $transaction->save();
                throw new RuntimeException('Tagihan sudah dibayar lunas oleh transaksi lain.');
            }

            $transaction->status = 'success';
            $transaction->admin_id = $adminId;
            $transaction->save();

            $saving->status = 'paid';
            $saving->save();

            return $transaction;
        });
    }

    public function markPaymentFailed($transactionId)
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = SavingsTransaction::where('id', $transactionId)->lockForUpdate()->first();
            if (!$transaction) {
                throw new RuntimeException('Transaksi tidak ditemukan.');
            }
            if ($transaction->status !== 'pending') {
                throw new RuntimeException('Transaksi tidak dalam status pending.');
            }

            $transaction->status = 'failed';
            $transaction->save();

            return $transaction;
        });
    }
}
