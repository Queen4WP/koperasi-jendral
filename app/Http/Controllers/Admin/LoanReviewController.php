<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectLoanRequest;
use App\Models\Loan;
use App\Services\LoanService;
use Exception;
use Illuminate\Http\Request;

class LoanReviewController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(Request $request)
    {
        $query = Loan::with(['member.user', 'reviewedBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pinjaman', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('nik', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($uq) use ($search) {
                             $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                         });
                  });
            });
        }

        $loans = $query->paginate(15)->withQueryString();
        $status = $request->status;
        $search = $request->search;

        return view('admin.loans.index', compact('loans', 'status', 'search'));
    }

    public function show(Loan $loan)
    {
        $loan->load('member.user', 'documents', 'installments', 'disbursement', 'reviewedBy');
        return view('admin.loans.review', compact('loan'));
    }

    public function startReview(Loan $loan)
    {
        try {
            $adminId = auth()->id();
            $this->loanService->startReview($loan->id, $adminId);
            return redirect()->back()->with('success', 'Review pinjaman dimulai.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve(Loan $loan)
    {
        try {
            $adminId = auth()->id();
            $this->loanService->approveLoan($loan->id, $adminId);
            return redirect()->back()->with('success', 'Pinjaman disetujui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(RejectLoanRequest $request, Loan $loan)
    {
        try {
            $adminId = auth()->id();
            $this->loanService->rejectLoan($loan->id, $adminId, $request->rejection_reason);
            return redirect()->back()->with('success', 'Pinjaman ditolak.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
