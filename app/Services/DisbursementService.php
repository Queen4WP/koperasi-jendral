<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Disbursement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DisbursementService
{
    public function disburseLoan($loanId, $adminId, $nominal = null, $tanggalPencairan = null, $buktiTransfer = null, $nomorReferensi = null)
    {
        if (empty($nomorReferensi)) {
            throw new RuntimeException('Nomor referensi transaksi pencairan wajib diisi.');
        }

        if (empty($tanggalPencairan)) {
            throw new RuntimeException('Tanggal pencairan wajib diisi.');
        }

        return DB::transaction(function () use ($loanId, $adminId, $nominal, $tanggalPencairan, $buktiTransfer, $nomorReferensi) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan) {
                throw new RuntimeException('Loan tidak ditemukan.');
            }

            if ($loan->status !== 'approved') {
                throw new RuntimeException("Tidak bisa mencairkan loan dengan status {$loan->status}.");
            }

            if (Disbursement::where('loan_id', $loanId)->exists()) {
                throw new RuntimeException('Pinjaman ini sudah dicairkan sebelumnya.');
            }

            // Always enforce authoritative loan amount from database (Section 6)
            $authoritativeNominal = (float)$loan->jumlah_pinjaman;

            $disbursement = Disbursement::create([
                'loan_id' => $loanId,
                'admin_id' => $adminId,
                'nominal' => $authoritativeNominal,
                'tanggal_pencairan' => $tanggalPencairan,
                'nomor_referensi' => $nomorReferensi,
                'bukti_transfer' => $buktiTransfer,
            ]);

            $loan->status = 'disbursed';
            $loan->disbursed_at = now();
            $loan->save();

            return $disbursement;
        });
    }
}
