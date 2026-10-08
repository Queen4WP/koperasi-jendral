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
        $filePath = null;
        try {
            $installment = LoanInstallment::with('loan')->findOrFail($installmentId);
            Gate::authorize('pay', $installment);

            $file = $request->file('bukti_pembayaran');
            $fileName = 'loan_' . $installment->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = 'payment-proofs/loans/' . $installment->id;
            $filePath = $file->storeAs($directory, $fileName);

            $this->loanPaymentService->createPaymentAttempt(
                $installment->id,
                'online',
                now(),
                $filePath,
                $request->input('external_reference')
            );

            return redirect()->back()->with('success', 'Bukti pembayaran angsuran berhasil diunggah. Menunggu verifikasi admin.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            if ($filePath && \Illuminate\Support\Facades\Storage::exists($filePath)) {
                \Illuminate\Support\Facades\Storage::delete($filePath);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
