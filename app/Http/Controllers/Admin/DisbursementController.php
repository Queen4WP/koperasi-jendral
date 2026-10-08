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
        if ($loan->status !== 'approved') {
            return redirect()->route('admin.loans.show', $loan->id)->with('error', "Pinjaman dengan status {$loan->status} tidak dapat dicairkan.");
        }

        $loan->load('member.user');
        return view('admin.loans.disburse', compact('loan'));
    }

    public function store(DisburseLoanRequest $request, Loan $loan)
    {
        try {
            $adminId = auth()->id();
            
            DB::transaction(function () use ($loan, $adminId, $request) {
                // Authoritative loan amount from database (cannot be tampered by HTTP payload)
                $authoritativeAmount = $loan->jumlah_pinjaman;

                $this->disbursementService->disburseLoan(
                    $loan->id,
                    $adminId,
                    $authoritativeAmount,
                    $request->tanggal_pencairan,
                    $request->bukti_transfer,
                    $request->nomor_referensi
                );
                
                $this->loanService->activateLoan($loan->id, $request->tanggal_pencairan);
            });

            return redirect()->back()->with('success', 'Pinjaman berhasil dicairkan dan diaktifkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
