<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Illuminate\Support\Str;

class LoanService
{
    protected $koperasiService;

    public function __construct(KoperasiService $koperasiService = null)
    {
        $this->koperasiService = $koperasiService ?? new KoperasiService();
    }

    public function submitLoan(
        $memberId,
        $jumlahPinjaman,
        $tenor,
        $persenJasa = null,
        $tujuanPinjaman = null,
        $bankName = null,
        $bankAccountNumber = null,
        $bankAccountOwner = null,
        $keterangan = null,
        array $documents = []
    ) {
        if ($jumlahPinjaman < KoperasiService::MIN_LOAN_AMOUNT || $jumlahPinjaman > KoperasiService::MAX_LOAN_AMOUNT) {
            throw new RuntimeException('Pinjaman di luar batas pengajuan (Rp 5.000.000 - Rp 50.000.000).');
        }
        if (!in_array($tenor, KoperasiService::ALLOWED_TENORS)) {
            throw new RuntimeException('Tenor tidak valid. Pilih 6, 12, 18, atau 24 bulan.');
        }
        if (empty($tujuanPinjaman) || empty($bankName) || empty($bankAccountNumber) || empty($bankAccountOwner)) {
            throw new RuntimeException('Data tujuan pinjaman dan informasi bank wajib diisi.');
        }

        $eligibility = $this->koperasiService->checkPinjamanEligibility($memberId);
        if (!$eligibility['eligible']) {
            throw new RuntimeException($eligibility['message']);
        }

        // Authoritative server-side calculation (overriding any client-supplied rates)
        $calculation = $this->koperasiService->calculateLoanInterestAndInstallment($jumlahPinjaman, $tenor);
        $trustedPersenJasa = $calculation['rate_percent'];
        $totalJasa = $calculation['total_jasa'];
        $totalKewajiban = $calculation['total_kewajiban'];
        $angsuranPerBulan = $calculation['angsuran_per_bulan'];

        return DB::transaction(function () use (
            $memberId,
            $jumlahPinjaman,
            $tenor,
            $trustedPersenJasa,
            $totalJasa,
            $totalKewajiban,
            $angsuranPerBulan,
            $tujuanPinjaman,
            $bankName,
            $bankAccountNumber,
            $bankAccountOwner,
            $keterangan,
            $documents
        ) {
            $member = Member::lockForUpdate()->find($memberId);
            if (!$member || $member->status !== 'active') {
                throw new RuntimeException('Member tidak ditemukan atau status tidak aktif.');
            }

            $jumlahPinjaman = round($jumlahPinjaman, 2);
            $nomorPinjaman = 'KPN-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $loan = Loan::create([
                'member_id' => $memberId,
                'nomor_pinjaman' => $nomorPinjaman,
                'jumlah_pinjaman' => $jumlahPinjaman,
                'tenor' => $tenor,
                'tujuan_pinjaman' => $tujuanPinjaman,
                'keterangan' => $keterangan,
                'persen_jasa' => $trustedPersenJasa,
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

            $storedPaths = [];
            try {
                foreach ($documents as $jenisDokumen => $file) {
                    if (!$file) {
                        continue;
                    }
                    $fileName = $jenisDokumen . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $directory = 'loan-documents/' . $loan->id;
                    $filePath = $file->storeAs($directory, $fileName);
                    if (!$filePath) {
                        throw new RuntimeException("Gagal menyimpan dokumen {$jenisDokumen}.");
                    }
                    $storedPaths[] = $filePath;

                    LoanDocument::create([
                        'loan_id' => $loan->id,
                        'jenis_dokumen' => $jenisDokumen,
                        'file_path' => $filePath,
                        'status_verifikasi' => 'pending',
                        'catatan' => null,
                    ]);
                }
            } catch (\Throwable $e) {
                foreach ($storedPaths as $path) {
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                    }
                }
                throw $e;
            }

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
            if (!in_array('ktp', $docs) || !in_array('bukti_penghasilan', $docs) || !in_array('rekening_bank', $docs)) {
                throw new RuntimeException('Dokumen KTP, Bukti Penghasilan, dan Rekening Bank wajib dilampirkan.');
            }
            if ($loan->tujuan_pinjaman === 'Modal Usaha' && !in_array('bukti_usaha', $docs)) {
                throw new RuntimeException('Dokumen Bukti Usaha wajib dilampirkan untuk pinjaman Modal Usaha.');
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
