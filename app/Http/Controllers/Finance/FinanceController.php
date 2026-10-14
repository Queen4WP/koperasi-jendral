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
}
