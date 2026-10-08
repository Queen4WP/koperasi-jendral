<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoanPaymentRequest;
use App\Services\LoanPaymentService;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\Gate;
use Exception;

class LoanPaymentController extends Controller
{
    protected $loanPaymentService;

    public function __construct(LoanPaymentService $loanPaymentService)
    {
        $this->loanPaymentService = $loanPaymentService;
    }

    public function store(CreateLoanPaymentRequest $request, $installmentId)
    {
        try {
            $installment = LoanInstallment::with('loan')->findOrFail($installmentId);
            Gate::authorize('pay', $installment);

            $adminId = auth()->user()->role === 'admin' ? auth()->id() : null;

            $payment = $this->loanPaymentService->createPaymentAttempt(
                $installmentId,
                $request->nominal_bayar,
                $request->metode_pembayaran,
                $request->tanggal_bayar,
                $request->external_reference
            );

            // V1 immediately marking success
            $this->loanPaymentService->markPaymentSuccess($payment->id, $adminId);

            return redirect()->back()->with('success', 'Pembayaran angsuran berhasil dicatat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
