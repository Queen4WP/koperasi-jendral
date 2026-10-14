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
    protected $koperasiService;

    public function __construct(LoanService $loanService, \App\Services\KoperasiService $koperasiService)
    {
        $this->loanService = $loanService;
        $this->koperasiService = $koperasiService;
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
        $member = auth()->user()->member;
        if (!$member) abort(403, 'Anggota tidak ditemukan.');

        $eligibility = $this->koperasiService->checkPinjamanEligibility($member->id);

        // Ambil data pinjaman aktif / berjalan di Kartu Pinjaman Modal
        $activeLoan = Loan::where('member_id', $member->id)
            ->whereIn('status', ['submitted', 'under_review', 'approved', 'disbursed', 'active'])
            ->latest()
            ->first();

        return view('loans.create', compact('eligibility', 'activeLoan'));
    }

    public function show(Loan $loan)
    {
        Gate::authorize('view', $loan);
        
        $loan->load(['member.user', 'documents', 'installments' => function($q) {
            $q->orderBy('angsuran_ke', 'asc')->with('payments');
        }]);

        return view('loans.show', compact('loan'));
    }

    public function store(SubmitLoanRequest $request)
    {
        try {
            $member = auth()->user()->member;
            if (!$member) abort(403, 'Anggota tidak ditemukan.');

            $eligibility = $this->koperasiService->checkPinjamanEligibility($member->id);
            if (!$eligibility['eligible']) {
                return redirect()->back()->withInput()->with('error', $eligibility['message']);
            }

            // Opsi A: Strict Hard-Block — Larang pengajuan jika masih ada pinjaman berjalan di Kartu Pinjaman Modal
            $activeLoan = Loan::where('member_id', $member->id)
                ->whereIn('status', ['submitted', 'under_review', 'approved', 'disbursed', 'active'])
                ->latest()
                ->first();

            if ($activeLoan) {
                $statusText = $activeLoan->status === 'submitted' ? 'sedang dalam antrean peninjauan' : 'masih aktif berjalan';
                $sisa = number_format($activeLoan->total_kewajiban, 0, ',', '.');
                $nominalDiminta = number_format($request->jumlah_pinjaman, 0, ',', '.');

                return redirect()->back()->withInput()->with('error', "Validasi Ditolak (Opsi A - Strict Hard-Block): Anda memiliki kewajiban Kartu Pinjaman Modal sebesar Rp {$sisa} ({$activeLoan->nomor_pinjaman}) yang {$statusText}. Pengajuan pinjaman baru sebesar Rp {$nominalDiminta} ditolak oleh sistem. Harap selesaikan pinjaman berjalan terlebih dahulu.");
            }

            $documents = [];
            if ($request->hasFile('ktp')) {
                $documents['ktp'] = $request->file('ktp');
            }
            if ($request->hasFile('bukti_penghasilan')) {
                $documents['bukti_penghasilan'] = $request->file('bukti_penghasilan');
            }
            if ($request->hasFile('rekening_bank')) {
                $documents['rekening_bank'] = $request->file('rekening_bank');
            }
            if ($request->hasFile('bukti_usaha')) {
                $documents['bukti_usaha'] = $request->file('bukti_usaha');
            }

            $this->loanService->submitLoan(
                $member->id,
                $request->jumlah_pinjaman,
                $request->tenor,
                null,
                $request->tujuan_pinjaman,
                $request->bank_name,
                $request->bank_account_number,
                $request->bank_account_owner,
                $request->keterangan,
                $documents
            );

            return redirect()->route('member.loans.index')->with('success', 'Pengajuan pinjaman berhasil disubmit.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
