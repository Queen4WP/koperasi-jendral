<?php

namespace App\Http\Controllers;

use App\Models\SavingsTransaction;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function showSavingsProof(SavingsTransaction $transaction)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if (!$user->member || $transaction->saving->member_id !== $user->member->id) {
                abort(403, 'Anda tidak berhak mengakses bukti pembayaran ini.');
            }
        }

        $filePath = $transaction->bukti_pembayaran;
        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan di server.');
        }

        return Storage::response($filePath);
    }

    public function showLoanProof(LoanPayment $payment)
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            if (!$user->member || $payment->installment->loan->member_id !== $user->member->id) {
                abort(403, 'Anda tidak berhak mengakses bukti pembayaran ini.');
            }
        }

        $filePath = $payment->bukti_pembayaran;
        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan di server.');
        }

        return Storage::response($filePath);
    }
}
