<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Str;

class LoanService
{
    public function submitLoan(
        $memberId,
        $jumlahPinjaman,
        $tenor,
        $persenJasa,
        $tujuanPinjaman,
        $bankName,
        $bankAccountNumber,
        $bankAccountOwner,
        $keterangan = null
    ) {
        if ($jumlahPinjaman <= 0) {
            throw new RuntimeException('Jumlah pinjaman harus lebih dari 0.');
        }
        if ($tenor <= 0) {
            throw new RuntimeException('Tenor harus lebih dari 0.');
        }
        if ($persenJasa < 0) {
            throw new RuntimeException('Persen jasa tidak boleh negatif.');
        }
        if (empty($tujuanPinjaman) || empty($bankName) || empty($bankAccountNumber) || empty($bankAccountOwner)) {
            throw new RuntimeException('Data tujuan pinjaman dan informasi bank wajib diisi.');
        }

        return DB::transaction(function () use (
            $memberId,
            $jumlahPinjaman,
            $tenor,
            $persenJasa,
            $tujuanPinjaman,
            $bankName,
            $bankAccountNumber,
            $bankAccountOwner,
            $keterangan
        ) {
            $member = Member::lockForUpdate()->find($memberId);
            if (!$member || $member->status !== 'active') {
                throw new RuntimeException('Member tidak ditemukan atau status tidak aktif.');
            }

            $jumlahPinjaman = round($jumlahPinjaman, 2);
            $totalJasa = round(($jumlahPinjaman * $persenJasa) / 100, 2);
            $totalKewajiban = round($jumlahPinjaman + $totalJasa, 2);
            $angsuranPerBulan = round($totalKewajiban / $tenor, 2);

            $nomorPinjaman = 'KPN-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $loan = Loan::create([
                'member_id' => $memberId,
                'nomor_pinjaman' => $nomorPinjaman,
                'jumlah_pinjaman' => $jumlahPinjaman,
                'tenor' => $tenor,
                'tujuan_pinjaman' => $tujuanPinjaman,
                'keterangan' => $keterangan,
                'persen_jasa' => $persenJasa,
                'total_jasa' => $totalJasa,
                'total_kewajiban' => $totalKewajiban,
                'angsuran_per_bulan' => $angsuranPerBulan,
                'bank_name' => $bankName,
                'bank_account_number' => $bankAccountNumber,
                'bank_account_owner' => $bankAccountOwner,
                'status' => 'submitted',
            ]);
            
            $loan->submitted_at = now();
            $loan->save();

            return $loan;
        });
    }

    public function startReview($loanId, $adminId)
    {
        return DB::transaction(function () use ($loanId, $adminId) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan) {
                throw new RuntimeException('Loan tidak ditemukan.');
            }

            if ($loan->status !== 'submitted') {
                throw new RuntimeException("Tidak bisa melakukan start review pada loan dengan status {$loan->status}.");
            }

            $loan->status = 'under_review';
            $loan->reviewed_by = $adminId;
            $loan->reviewed_at = now();
            $loan->save();

            return $loan;
        });
    }

    public function approveLoan($loanId, $adminId)
    {
        return DB::transaction(function () use ($loanId, $adminId) {
            $loan = Loan::with('documents')->lockForUpdate()->find($loanId);
            if (!$loan) {
                throw new RuntimeException('Loan tidak ditemukan.');
            }

            if ($loan->status !== 'under_review') {
                throw new RuntimeException("Tidak bisa menyetujui loan dengan status {$loan->status}.");
            }

            $docs = $loan->documents()->pluck('jenis_dokumen')->toArray();
            if (!in_array('ktp', $docs)) {
                throw new RuntimeException('Dokumen KTP wajib dilampirkan.');
            }


            foreach ($loan->documents as $doc) {
                if (in_array($doc->status_verifikasi, ['pending', 'tidak_sesuai'])) {
                    throw new RuntimeException('Ada dokumen yang masih pending atau tidak sesuai.');
                }
            }

            $loan->status = 'approved';
            $loan->approved_at = now();
            $loan->reviewed_by = $adminId;
            $loan->reviewed_at = now();
            $loan->save();

            return $loan;
        });
    }

    public function rejectLoan($loanId, $adminId, $reason)
    {
        if (empty($reason)) {
            throw new RuntimeException('Alasan penolakan wajib diisi.');
        }

        return DB::transaction(function () use ($loanId, $adminId, $reason) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan) {
                throw new RuntimeException('Loan tidak ditemukan.');
            }

            if ($loan->status !== 'under_review') {
                throw new RuntimeException("Tidak bisa menolak loan dengan status {$loan->status}.");
            }

            $loan->status = 'rejected';
            $loan->rejection_reason = $reason;
            $loan->rejected_at = now();
            $loan->reviewed_by = $adminId;
            $loan->reviewed_at = now();
            $loan->save();

            return $loan;
        });
    }

    public function activateLoan($loanId)
    {
        return DB::transaction(function () use ($loanId) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan || $loan->status !== 'disbursed') {
                throw new RuntimeException('Loan belum dicairkan.');
            }
            
            $loan->status = 'active';
            $loan->save();
            
            $this->generateInstallments($loanId);
            
            return $loan;
        });
    }

    public function generateInstallments($loanId)
    {
        return DB::transaction(function () use ($loanId) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan) {
                throw new RuntimeException('Loan tidak ditemukan.');
            }

            if (!in_array($loan->status, ['active'])) {
                throw new RuntimeException('Loan tidak dalam status yang valid untuk generate angsuran.');
            }

            $existing = LoanInstallment::where('loan_id', $loanId)->exists();
            if ($existing) {
                throw new RuntimeException('Angsuran sudah dibuat sebelumnya.');
            }

            $jumlahPinjaman = (float)$loan->jumlah_pinjaman;
            $totalJasa = (float)$loan->total_jasa;
            $totalKewajiban = (float)$loan->total_kewajiban;
            $tenor = $loan->tenor;

            $pokokPerBulan = round($jumlahPinjaman / $tenor, 2);
            $jasaPerBulan = round($totalJasa / $tenor, 2);

            $accumulatedPokok = 0;
            $accumulatedJasa = 0;

            $installments = [];
            for ($i = 1; $i <= $tenor; $i++) {
                if ($i === $tenor) {
                    $pokokBulanIni = round($jumlahPinjaman - $accumulatedPokok, 2);
                    $jasaBulanIni = round($totalJasa - $accumulatedJasa, 2);
                } else {
                    $pokokBulanIni = $pokokPerBulan;
                    $jasaBulanIni = $jasaPerBulan;
                }

                $totalBulanIni = round($pokokBulanIni + $jasaBulanIni, 2);
                
                $accumulatedPokok += $pokokBulanIni;
                $accumulatedJasa += $jasaBulanIni;

                $installments[] = [
                    'loan_id' => $loanId,
                    'angsuran_ke' => $i,
                    'jatuh_tempo' => now()->addMonths($i)->format('Y-m-d'),
                    'pokok_angsuran' => $pokokBulanIni,
                    'jasa_angsuran' => $jasaBulanIni,
                    'total_angsuran' => $totalBulanIni,
                    'denda' => 0,
                    'total_tagihan' => $totalBulanIni,
                    'status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            LoanInstallment::insert($installments);
        });
    }

    public function markAsPaidOffIfCompleted($loanId)
    {
        return DB::transaction(function () use ($loanId) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan || $loan->status !== 'active') {
                return;
            }

            $unpaidCount = LoanInstallment::where('loan_id', $loanId)->whereIn('status', ['unpaid', 'overdue'])->count();
            if ($unpaidCount === 0) {
                $loan->status = 'paid_off';
                $loan->paid_off_at = now();
                $loan->save();
            }
        });
    }
}
