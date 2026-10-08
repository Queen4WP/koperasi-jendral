<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;
use App\Models\SavingsTransaction;
use App\Models\LoanPayment;

class DashboardController extends Controller
{
    public function index()
    {
        $total_anggota = Member::where('status', 'active')->count();
        $pinjaman_pending = Loan::where('status', 'submitted')->count();
        $pembayaran_pending = SavingsTransaction::where('status', 'pending')->count() + LoanPayment::where('status', 'pending')->count();
        
        $tasks = [];
        
        // Find members pending review
        $pending_members = Member::where('status', 'pending')->get();
        foreach($pending_members as $m) {
            $tasks[] = [
                'type' => 'Review Keanggotaan',
                'ref' => $m->user->name . ' (NIK: ' . $m->nik . ')',
                'status' => 'pending',
                'url' => route('admin.members.review', $m->id)
            ];
        }

        // Find loans pending review
        $pending_loans = Loan::whereIn('status', ['submitted', 'under_review', 'approved'])->get();
        foreach($pending_loans as $l) {
            if ($l->status === 'submitted' || $l->status === 'under_review') {
                $tasks[] = [
                    'type' => 'Review Pinjaman',
                    'ref' => 'Loan #' . str_pad($l->id, 5, '0', STR_PAD_LEFT) . ' - ' . $l->member->user->name,
                    'status' => $l->status,
                    'url' => route('admin.loans.review', $l->id)
                ];
            } elseif ($l->status === 'approved') {
                $tasks[] = [
                    'type' => 'Pencairan Pinjaman',
                    'ref' => 'Loan #' . str_pad($l->id, 5, '0', STR_PAD_LEFT) . ' - ' . $l->member->user->name,
                    'status' => 'approved',
                    'url' => route('admin.loans.disburse', $l->id)
                ];
            }
        }

        // Find pending savings payments
        $pending_savings_trx = SavingsTransaction::with(['savings.member.user'])->where('status', 'pending')->get();
        foreach($pending_savings_trx as $trx) {
            $tasks[] = [
                'type' => 'Verifikasi Pembayaran Simpanan',
                'ref' => 'Simpanan ' . ucfirst($trx->saving->jenis_simpanan) . ' - ' . ($trx->saving->member->user->name ?? 'Member') . ' (' . $trx->payment_reference . ')',
                'status' => 'pending',
                'url' => route('admin.payments.savings.show', $trx->id)
            ];
        }

        // Find pending loan payments
        $pending_loan_payments = LoanPayment::with(['installment.loan.member.user'])->where('status', 'pending')->get();
        foreach($pending_loan_payments as $pay) {
            $tasks[] = [
                'type' => 'Verifikasi Pembayaran Angsuran',
                'ref' => 'Angsuran Pinjaman #' . str_pad($pay->installment->loan_id, 5, '0', STR_PAD_LEFT) . ' Ke-' . $pay->installment->angsuran_ke . ' - ' . ($pay->installment->loan->member->user->name ?? 'Member'),
                'status' => 'pending',
                'url' => route('admin.payments.loans.show', $pay->id)
            ];
        }

        return view('admin.dashboard', compact('total_anggota', 'pinjaman_pending', 'pembayaran_pending', 'tasks'));
    }
}
