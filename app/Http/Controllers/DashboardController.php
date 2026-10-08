<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanInstallment;

class DashboardController extends Controller
{
    public function index()
    {
        $member = auth()->user()->member;
        
        if (!$member) {
            return view('dashboard.member', ['member' => null]);
        }

        // Simpanan Pokok
        $total_simpanan_pokok = Saving::where('member_id', $member->id)
            ->where('jenis_simpanan', 'pokok')
            ->where('status', 'paid')
            ->sum('nominal');

        // Simpanan Wajib
        $total_simpanan_wajib = Saving::where('member_id', $member->id)
            ->where('jenis_simpanan', 'wajib')
            ->where('status', 'paid')
            ->sum('nominal');

        // Sisa Pinjaman (Aktif)
        $sisa_pinjaman = Loan::where('member_id', $member->id)
            ->where('status', 'active')
            ->with(['installments' => function($q) {
                $q->whereIn('status', ['unpaid', 'overdue']);
            }])
            ->get()
            ->pluck('installments')
            ->flatten()
            ->sum('total_angsuran');

        // Next Installment
        $next_installment = LoanInstallment::whereHas('loan', function($q) use ($member) {
                $q->where('member_id', $member->id);
            })
            ->whereIn('status', ['unpaid', 'overdue'])
            ->with('payments')
            ->orderBy('jatuh_tempo', 'asc')
            ->first();

        return view('dashboard.member', compact(
            'member',
            'total_simpanan_pokok',
            'total_simpanan_wajib',
            'sisa_pinjaman',
            'next_installment'
        ));
    }
}
