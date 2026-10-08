<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DisburseLoanRequest;
use App\Models\Loan;
use App\Services\DisbursementService;
use App\Services\LoanService;
use Illuminate\Support\Facades\DB;
use Exception;

class DisbursementController extends Controller
{
    protected $disbursementService;
    protected $loanService;

    public function __construct(DisbursementService $disbursementService, LoanService $loanService)
    {
        $this->disbursementService = $disbursementService;
        $this->loanService = $loanService;
    }

    public function create(Loan $loan)
    {
        $loan->load('member.user');
        return view('admin.loans.disburse', compact('loan'));
    }

    public function store(DisburseLoanRequest $request, $loanId)
    {
        try {
            $adminId = auth()->id();
            
            DB::transaction(function () use ($loanId, $adminId, $request) {
                $this->disbursementService->disburseLoan(
                    $loanId,
                    $adminId,
                    $request->nominal,
                    $request->tanggal_pencairan,
                    $request->bukti_transfer
                );
                
                $this->loanService->activateLoan($loanId);
            });

            return redirect()->back()->with('success', 'Pinjaman berhasil dicairkan dan diaktifkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
