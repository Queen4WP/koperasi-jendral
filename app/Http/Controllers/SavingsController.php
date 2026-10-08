<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSavingPokokRequest;
use App\Http\Requests\CreateSavingWajibRequest;
use App\Http\Requests\CreateSavingsPaymentRequest;
use App\Services\SavingsService;
use App\Models\Saving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

class SavingsController extends Controller
{
    protected $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    public function index()
    {
        $member = auth()->user()->member;
        if (!$member) abort(403, 'Anggota tidak ditemukan.');

        $savings = Saving::where('member_id', $member->id)->latest()->get();
        return view('savings.index', compact('savings'));
    }

    public function createPokok()
    {
        return view('savings.create-pokok');
    }

    public function createWajib()
    {
        return view('savings.create-wajib');
    }

    public function storePokok(CreateSavingPokokRequest $request)
    {
        try {
            $memberId = $this->getMemberId($request);
            $this->savingsService->createPokok($memberId, $request->nominal);
            return redirect()->back()->with('success', 'Simpanan pokok berhasil dibuat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeWajib(CreateSavingWajibRequest $request)
    {
        try {
            $memberId = $this->getMemberId($request);
            $this->savingsService->createWajib(
                $memberId,
                $request->nominal,
                $request->periode,
                $request->jatuh_tempo
            );
            return redirect()->back()->with('success', 'Simpanan wajib berhasil dibuat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function pay(CreateSavingsPaymentRequest $request, $savingId)
    {
        try {
            $saving = Saving::findOrFail($savingId);
            Gate::authorize('pay', $saving);

            $adminId = auth()->user()->role === 'admin' ? auth()->id() : null;

            $transaction = $this->savingsService->createPaymentAttempt(
                $savingId,
                $request->nominal_bayar,
                $request->metode_pembayaran,
                $request->tanggal_bayar,
                $request->external_reference
            );

            // V1 immediately marking success
            $this->savingsService->markPaymentSuccess($transaction->id, $adminId);

            return redirect()->back()->with('success', 'Pembayaran simpanan berhasil dicatat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    protected function getMemberId(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return $request->member_id;
        }
        return auth()->user()->member->id;
    }
}
