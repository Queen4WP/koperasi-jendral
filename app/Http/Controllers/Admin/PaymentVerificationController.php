<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsTransaction;
use App\Models\LoanPayment;
use App\Services\SavingsService;
use App\Services\LoanPaymentService;
use Illuminate\Http\Request;
use Exception;

class PaymentVerificationController extends Controller
{
    protected $savingsService;
    protected $loanPaymentService;

    public function __construct(SavingsService $savingsService, LoanPaymentService $loanPaymentService)
    {
        $this->savingsService = $savingsService;
        $this->loanPaymentService = $loanPaymentService;
    }

    public function index()
    {
        $pendingSavings = SavingsTransaction::with(['savings.member.user'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $pendingLoans = LoanPayment::with(['installment.loan.member.user'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.payments.index', compact('pendingSavings', 'pendingLoans'));
    }

    public function showSaving(SavingsTransaction $transaction)
    {
        $transaction->load(['savings.member.user']);
        return view('admin.payments.review-saving', compact('transaction'));
    }

    public function approveSaving(SavingsTransaction $transaction)
    {
        try {
            $this->savingsService->markPaymentSuccess($transaction->id, auth()->id());
            return redirect()->route('admin.payments.index')->with('success', 'Pembayaran simpanan berhasil diverifikasi dan disetujui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectSaving(Request $request, SavingsTransaction $transaction)
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:500'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $this->savingsService->markPaymentFailed($transaction->id, auth()->id(), $request->catatan);
            return redirect()->route('admin.payments.index')->with('success', 'Pembayaran simpanan telah ditolak.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function showLoanPayment(LoanPayment $payment)
    {
        $payment->load(['installment.loan.member.user']);
        return view('admin.payments.review-loan', compact('payment'));
    }

    public function approveLoanPayment(LoanPayment $payment)
    {
        try {
            $this->loanPaymentService->markPaymentSuccess($payment->id, auth()->id());
            return redirect()->route('admin.payments.index')->with('success', 'Pembayaran angsuran berhasil diverifikasi dan disetujui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectLoanPayment(Request $request, LoanPayment $payment)
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:500'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $this->loanPaymentService->markPaymentFailed($payment->id, auth()->id(), $request->catatan);
            return redirect()->route('admin.payments.index')->with('success', 'Pembayaran angsuran telah ditolak.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
