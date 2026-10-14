<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\SavingsTransaction;
use App\Models\SavingsWithdrawal;
use App\Services\KasirService;
use Illuminate\Http\Request;
use Exception;

class KasirController extends Controller
{
    protected KasirService $kasirService;

    public function __construct(KasirService $kasirService)
    {
        $this->kasirService = $kasirService;
    }

    public function dashboard()
    {
        $kasirUser = auth('kasir')->user();
        $dailySummary = $this->kasirService->getDailyCashSummary($kasirUser);

        // Pinjaman disetujui Finance yang siap dicairkan uang tunainya di loket
        $readyLoans = Loan::with('member.user')
            ->where('status', 'approved')
            ->latest('approved_at')
            ->get();

        // Riwayat transaksi loket kasir hari ini
        $recentSavingsTrx = SavingsTransaction::with('savings.member.user')
            ->where('admin_id', $kasirUser->id)
            ->where('metode_pembayaran', 'kantor')
            ->latest()
            ->take(5)
            ->get();

        $recentWithdrawals = SavingsWithdrawal::with('member.user')
            ->where('kasir_id', $kasirUser->id)
            ->latest()
            ->take(5)
            ->get();

        $recentDisbursements = Disbursement::with('loan.member.user')
            ->where('admin_id', $kasirUser->id)
            ->latest()
            ->take(5)
            ->get();

        $recentInstallments = LoanPayment::with('installment.loan.member.user')
            ->where('admin_id', $kasirUser->id)
            ->where('metode_pembayaran', 'kantor')
            ->latest()
            ->take(5)
            ->get();

        return view('kasir.dashboard', compact(
            'dailySummary',
            'readyLoans',
            'recentSavingsTrx',
            'recentWithdrawals',
            'recentDisbursements',
            'recentInstallments'
        ));
    }

    // --- 1. Setoran Tunai Simpanan ---
    public function createSavingsDeposit()
    {
        $members = Member::with('user')->where('status', 'active')->orderBy('id')->get();
        return view('kasir.savings.deposit', compact('members'));
    }

    public function storeSavingsDeposit(Request $request)
    {
        $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'jenis_simpanan' => ['required', 'in:pokok,wajib,sukarela'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $result = $this->kasirService->processCashSavings(
                (int) $request->member_id,
                auth('kasir')->user(),
                $request->jenis_simpanan,
                (float) $request->nominal,
                $request->catatan
            );

            return redirect()->route('kasir.receipt.bkm', $result['transaction']->id)
                ->with('success', "Setoran tunai simpanan berhasil diterima! Nomor {$result['nomor_bkm']}.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // --- 2. Penarikan Tunai Simpanan (BKK) ---
    public function createWithdrawal()
    {
        $members = Member::with(['user', 'savings'])->where('status', 'active')->get();
        return view('kasir.savings.withdraw', compact('members'));
    }

    public function storeWithdrawal(Request $request)
    {
        $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $withdrawal = $this->kasirService->processCashWithdrawal(
                (int) $request->member_id,
                auth('kasir')->user(),
                (float) $request->nominal,
                $request->catatan
            );

            return redirect()->route('kasir.receipt.bkk_withdrawal', $withdrawal->id)
                ->with('success', "Penarikan tunai berhasil diserahkan! Nomor {$withdrawal->nomor_referensi}.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // --- 3. Pencairan Pinjaman Tunai di Loket ---
    public function showLoanDisbursement(Loan $loan)
    {
        if ($loan->status !== 'approved') {
            return redirect()->route('kasir.dashboard')->with('error', 'Hanya pinjaman berstatus APPROVED yang dapat dicairkan.');
        }

        $loan->load('member.user');
        return view('kasir.loans.disburse', compact('loan'));
    }

    public function storeLoanDisbursement(Request $request, Loan $loan)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:255'],
            'konfirmasi_identitas' => ['required', 'accepted'],
        ]);

        try {
            $result = $this->kasirService->processCashLoanDisbursement(
                $loan->id,
                auth('kasir')->user(),
                $request->catatan
            );

            return redirect()->route('kasir.receipt.bkk_disbursement', $result['disbursement']->id)
                ->with('success', "Pinjaman {$loan->nomor_pinjaman} berhasil DISBURSED secara tunai di loket!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- 4. Pembayaran Angsuran Tunai di Loket (POS Card Grid) ---
    public function createInstallmentPayment()
    {
        $activeLoans = Loan::with(['member.user', 'installments' => function($q) {
            $q->where('status', '!=', 'paid')->orderBy('angsuran_ke', 'asc');
        }])
        ->whereIn('status', ['active', 'disbursed'])
        ->get()
        ->filter(fn($loan) => $loan->installments->isNotEmpty())
        ->values();

        $dueCards = $activeLoans->map(function ($loan) {
            $nextInstallment = $loan->installments->first();
            $totalInstallments = $loan->tenor;
            $unpaidCount = $loan->installments->count();

            $hariTerlambat = max(0, (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($nextInstallment->jatuh_tempo)->startOfDay(), false) * -1);
            $denda = ($hariTerlambat > 6) ? (floor($hariTerlambat / 7) * 10000) : 0;

            $pokok = (float) ($nextInstallment->pokok_angsuran ?? round($nextInstallment->total_angsuran * 0.85));
            $jasa = (float) ($nextInstallment->jasa_angsuran ?? ($nextInstallment->total_angsuran - $pokok));

            return [
                'installment_id' => $nextInstallment->id,
                'loan_id' => $loan->id,
                'nomor_pinjaman' => $loan->nomor_pinjaman,
                'member_name' => $loan->member->user->name,
                'member_nik' => $loan->member->nik,
                'tujuan_pinjaman' => $loan->tujuan_pinjaman,
                'angsuran_ke' => $nextInstallment->angsuran_ke,
                'total_tenor' => $totalInstallments,
                'jatuh_tempo' => $nextInstallment->jatuh_tempo,
                'jatuh_tempo_formatted' => \Carbon\Carbon::parse($nextInstallment->jatuh_tempo)->format('d/m/Y'),
                'is_overdue' => \Carbon\Carbon::parse($nextInstallment->jatuh_tempo)->isPast(),
                'hari_terlambat' => $hariTerlambat,
                'pokok_angsuran' => $pokok,
                'jasa_angsuran' => $jasa,
                'total_angsuran' => (float) $nextInstallment->total_angsuran,
                'denda' => (float) $denda,
                'total_tagihan' => (float) $nextInstallment->total_angsuran + $denda,
                'sisa_angsuran_count' => $unpaidCount,
            ];
        });

        return view('kasir.loans.installment', compact('dueCards'));
    }

    public function storeInstallmentPayment(Request $request)
    {
        $request->validate([
            'loan_installment_id' => ['required', 'exists:loan_installments,id'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $result = $this->kasirService->processCashInstallmentPayment(
                (int) $request->loan_installment_id,
                auth('kasir')->user(),
                $request->catatan
            );

            return redirect()->route('kasir.receipt.bkm_installment', $result['payment']->id)
                ->with('success', "Pembayaran angsuran berhasil diterima tunai! Nomor {$result['nomor_bkm']}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- 5. Closing Kas Harian Kasir Jam 16:00 ---
    public function submitDailyClosing(Request $request)
    {
        $request->validate([
            'total_fisik_diserahkan' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $closing = $this->kasirService->submitDailyClosing(
                auth('kasir')->user(),
                (float) $request->total_fisik_diserahkan,
                $request->catatan
            );

            return back()->with('success', "Closing kas harian berhasil disubmit! Menunggu verifikasi serah terima fisik kas oleh Finance.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // --- 6. Struk & Bukti Tanda Terima Fisik Cetak ---
    public function printBkm(SavingsTransaction $transaction)
    {
        $transaction->load(['savings.member.user', 'admin']);
        return view('kasir.receipts.bkm', compact('transaction'));
    }

    public function printBkmInstallment(LoanPayment $payment)
    {
        $payment->load(['installment.loan.member.user', 'admin']);
        return view('kasir.receipts.bkm_installment', compact('payment'));
    }

    public function printBkkWithdrawal(SavingsWithdrawal $withdrawal)
    {
        $withdrawal->load(['member.user', 'kasir']);
        return view('kasir.receipts.bkk_withdrawal', compact('withdrawal'));
    }

    public function printBkkDisbursement(Disbursement $disbursement)
    {
        $disbursement->load(['loan.member.user', 'admin']);
        return view('kasir.receipts.bkk_disbursement', compact('disbursement'));
    }
}
