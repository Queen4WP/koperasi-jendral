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

        $savings = Saving::with(['transactions'])->where('member_id', $member->id)->latest()->get();
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
        $filePath = null;
        try {
            $saving = Saving::findOrFail($savingId);
            Gate::authorize('pay', $saving);

            $file = $request->file('bukti_pembayaran');
            $fileName = 'saving_' . $saving->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = 'payment-proofs/savings/' . $saving->id;
            $filePath = $file->storeAs($directory, $fileName);

            $this->savingsService->createPaymentAttempt(
                $saving->id,
                'online',
                now(),
                $filePath,
                $request->input('external_reference')
            );

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            if ($filePath && \Illuminate\Support\Facades\Storage::exists($filePath)) {
                \Illuminate\Support\Facades\Storage::delete($filePath);
            }
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
