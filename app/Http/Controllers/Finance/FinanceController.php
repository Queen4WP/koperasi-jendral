<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CashierClosing;
use App\Models\Loan;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Exception;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function dashboard()
    {
        $reserveMetrics = $this->financeService->getReserveMetrics();

        // Pinjaman yang membutuhkan review dari Finance
        $pendingLoans = Loan::with('member.user')
            ->whereIn('status', ['submitted', 'under_review'])
            ->latest()
            ->get();

        // Closing kas harian kasir yang menunggu verifikasi
        $pendingClosings = CashierClosing::with('kasir')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Pinjaman yang disetujui baru-baru ini
        $recentlyApprovedLoans = Loan::with(['member.user', 'disbursement'])
            ->whereIn('status', ['approved', 'disbursed', 'active'])
            ->latest('approved_at')
            ->take(5)
            ->get();

        return view('finance.dashboard', compact(
            'reserveMetrics',
            'pendingLoans',
            'pendingClosings',
            'recentlyApprovedLoans'
        ));
    }

    public function showLoan(Loan $loan)
    {
        $loan->load(['member.user', 'member.savings', 'documents']);
        $reserveMetrics = $this->financeService->getReserveMetrics();
        $reserveCheck = $this->financeService->canApproveLoan((float) $loan->jumlah_pinjaman);

        // Hitung total simpanan anggota pemohon
        $memberSavingsTotal = $loan->member->savings->where('status', 'paid')->sum('nominal');

        return view('finance.loans.show', compact('loan', 'reserveMetrics', 'reserveCheck', 'memberSavingsTotal'));
    }

    public function approveLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'approval_note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->financeService->approveLoan($loan->id, auth('finance')->user(), $request->approval_note);

            return redirect()->route('finance.dashboard')->with('success', "Pinjaman {$loan->nomor_pinjaman} berhasil DISETUJUI. Notifikasi instruksi penyiapan kas telah dikirim ke Kasir.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $this->financeService->rejectLoan($loan->id, auth('finance')->user(), $request->rejection_reason);

            return redirect()->route('finance.dashboard')->with('success', "Pinjaman {$loan->nomor_pinjaman} telah DITOLAK secara transparan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifyClosing(Request $request, CashierClosing $closing)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->financeService->verifyCashierClosing($closing->id, auth('finance')->user(), $request->catatan);

            return back()->with('success', "Serah terima fisik kas tanggal {$closing->tanggal_closing->format('d/m/Y')} berhasil diverifikasi dan disimpan ke kas koperasi.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman Pengaturan Plafon & Batas Limit Pinjaman (Karyawan & Anggota).
     */
    public function loanLimitsSetting()
    {
        $karyawanMin = \App\Services\KoperasiService::getKaryawanMinLoanAmount();
        $karyawanMax = \App\Services\KoperasiService::getKaryawanMaxLoanAmount();
        $karyawanRate = \App\Services\KoperasiService::getKaryawanRatePercent();

        $anggotaMin = \App\Services\KoperasiService::getMinLoanAmount();
        $anggotaMax = \App\Services\KoperasiService::getMaxLoanAmount();

        $lastUpdated = \App\Models\SystemSetting::whereIn('key', [
            'loan_karyawan_min_limit',
            'loan_karyawan_max_limit',
            'loan_anggota_min_limit',
            'loan_anggota_max_limit',
            'loan_karyawan_rate_percent',
        ])->with('updatedByUser')->latest('updated_at')->first();

        return view('finance.settings.loan_limits', compact(
            'karyawanMin',
            'karyawanMax',
            'karyawanRate',
            'anggotaMin',
            'anggotaMax',
            'lastUpdated'
        ));
    }

    /**
     * Simpan pembaruan plafon & batas limit pinjaman.
     */
    public function updateLoanLimitsSetting(Request $request)
    {
        // Sanitasi input angka berformat ribuan jika ada
        $cleanInputs = [];
        foreach (['loan_karyawan_min_limit', 'loan_karyawan_max_limit', 'loan_anggota_min_limit', 'loan_anggota_max_limit'] as $field) {
            $val = $request->input($field);
            $cleanInputs[$field] = (float) preg_replace('/[^0-9]/', '', (string)$val);
        }
        $request->merge($cleanInputs);

        $request->validate([
            'loan_karyawan_min_limit' => ['required', 'numeric', 'min:100000', 'lt:loan_karyawan_max_limit'],
            'loan_karyawan_max_limit' => ['required', 'numeric', 'gt:loan_karyawan_min_limit', 'max:500000000'],
            'loan_karyawan_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'loan_anggota_min_limit' => ['required', 'numeric', 'min:500000', 'lt:loan_anggota_max_limit'],
            'loan_anggota_max_limit' => ['required', 'numeric', 'gt:loan_anggota_min_limit', 'max:1000000000'],
        ], [
            'loan_karyawan_min_limit.lt' => 'Batas minimum pinjaman karyawan harus lebih kecil dari batas maksimum.',
            'loan_karyawan_max_limit.gt' => 'Batas maksimum pinjaman karyawan harus lebih besar dari batas minimum.',
            'loan_anggota_min_limit.lt' => 'Batas minimum pinjaman anggota harus lebih kecil dari batas maksimum.',
            'loan_anggota_max_limit.gt' => 'Batas maksimum pinjaman anggota harus lebih besar dari batas minimum.',
        ]);

        $user = auth('finance')->user();
        $userId = $user ? $user->id : null;

        \App\Models\SystemSetting::set('loan_karyawan_min_limit', (int)$request->loan_karyawan_min_limit, 'loan_karyawan', 'integer', 'Batas minimum pengajuan pinjaman modal karyawan (Rp)', $userId);
        \App\Models\SystemSetting::set('loan_karyawan_max_limit', (int)$request->loan_karyawan_max_limit, 'loan_karyawan', 'integer', 'Batas maksimum plafon pinjaman modal karyawan via payroll (Rp)', $userId);
        \App\Models\SystemSetting::set('loan_karyawan_rate_percent', (float)$request->loan_karyawan_rate_percent, 'loan_karyawan', 'float', 'Jasa/Bunga pinjaman modal karyawan flat per tahun (%)', $userId);
        \App\Models\SystemSetting::set('loan_anggota_min_limit', (int)$request->loan_anggota_min_limit, 'loan_anggota', 'integer', 'Batas minimum pengajuan pinjaman modal anggota umum (Rp)', $userId);
        \App\Models\SystemSetting::set('loan_anggota_max_limit', (int)$request->loan_anggota_max_limit, 'loan_anggota', 'integer', 'Batas maksimum plafon pinjaman modal anggota umum (Rp)', $userId);

        return redirect()->route('finance.settings.loan_limits')->with('success', 'Plafon dan batas limit pinjaman berhasil diperbarui. Perubahan langsung berlaku secara real-time untuk seluruh pengajuan karyawan dan anggota.');
    }
}
