<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Services\SavingsService;
use Exception;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    protected $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    public function index(Request $request)
    {
        $query = Saving::with(['member.user', 'transactions.admin', 'latestTransaction'])->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis_simpanan', $request->jenis);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['pending', 'success', 'failed'])) {
                $query->whereHas('transactions', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            } elseif (in_array($status, ['unpaid', 'paid', 'overdue'])) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('metode')) {
            $metode = $request->metode;
            $query->whereHas('transactions', function ($q) use ($metode) {
                $q->where('metode_pembayaran', $metode);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('member', function ($mq) use ($search) {
                    $mq->where('nik', 'like', "%{$search}%")
                       ->orWhereHas('user', function ($uq) use ($search) {
                           $uq->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                       });
                })->orWhereHas('transactions', function ($tq) use ($search) {
                    $tq->where('payment_reference', 'like', "%{$search}%");
                });
            });
        }

        $savings = $query->paginate(15)->withQueryString();
        $jenis = $request->jenis;
        $status = $request->status;
        $metode = $request->metode;
        $search = $request->search;

        return view('admin.savings.index', compact('savings', 'jenis', 'status', 'metode', 'search'));
    }

    public function show(Saving $saving)
    {
        $saving->load(['member.user', 'transactions.admin']);
        return view('admin.savings.show', compact('saving'));
    }

    public function approvePayment(SavingsTransaction $transaction)
    {
        try {
            $this->savingsService->markPaymentSuccess($transaction->id, auth()->id());
            return redirect()->back()->with('success', 'Pembayaran simpanan berhasil diverifikasi dan disetujui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, SavingsTransaction $transaction)
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:500'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $this->savingsService->markPaymentFailed($transaction->id, auth()->id(), $request->catatan);
            return redirect()->back()->with('success', 'Pembayaran simpanan telah ditolak.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
