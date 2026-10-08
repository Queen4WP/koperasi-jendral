<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Models\LoanInstallment;
use App\Services\SavingsService;
use App\Services\LoanPaymentService;
use Illuminate\Http\Request;
use Exception;

class OfficePaymentController extends Controller
{
    protected $savingsService;
    protected $loanPaymentService;

    public function __construct(SavingsService $savingsService, LoanPaymentService $loanPaymentService)
    {
        $this->savingsService = $savingsService;
        $this->loanPaymentService = $loanPaymentService;
    }

    public function storeSaving(Request $request, Saving $saving)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->savingsService->recordOfficePayment($saving->id, auth()->id(), $request->catatan);
            return redirect()->back()->with('success', 'Pembayaran simpanan langsung di kantor berhasil dicatat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeInstallment(Request $request, LoanInstallment $installment)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->loanPaymentService->recordOfficePayment($installment->id, auth()->id(), $request->catatan);
            return redirect()->back()->with('success', 'Pembayaran angsuran langsung di kantor berhasil dicatat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
