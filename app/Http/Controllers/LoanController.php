<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitLoanRequest;
use App\Services\LoanService;
use App\Models\Loan;
use Illuminate\Support\Facades\Gate;
use Exception;

class LoanController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index()
    {
        $member = auth()->user()->member;
        if (!$member) abort(403, 'Anggota tidak ditemukan.');

        $loans = Loan::where('member_id', $member->id)->latest()->get();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        return view('loans.create');
    }

    public function show(Loan $loan)
    {
        Gate::authorize('view', $loan);
        
        $loan->load(['member.user', 'documents', 'installments' => function($q) {
            $q->orderBy('angsuran_ke', 'asc');
        }]);

        return view('loans.show', compact('loan'));
    }

    public function store(SubmitLoanRequest $request)
    {
        try {
            $memberId = auth()->user()->member->id;

            $this->loanService->submitLoan(
                $memberId,
                $request->jumlah_pinjaman,
                $request->tenor,
                $request->persen_jasa,
                $request->tujuan_pinjaman,
                $request->bank_name,
                $request->bank_account_number,
                $request->bank_account_owner,
                $request->keterangan
            );

            return redirect()->route('loans.index')->with('success', 'Pengajuan pinjaman berhasil disubmit.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
