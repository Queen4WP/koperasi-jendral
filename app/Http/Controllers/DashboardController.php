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
        $user = auth()->user();
        if ($user) {
            if ($user->role === 'kasir') {
                return redirect()->route('kasir.dashboard');
            }
            if ($user->role === 'finance') {
                return redirect()->route('finance.dashboard');
            }
            if ($user->role === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            }
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        $member = $user ? $user->member : null;
        
        if (!$member) {
            return view('dashboard.member', ['member' => null]);
        }

        // Simpanan Pokok
        $total_simpanan_pokok = Saving::where('member_id', $member->id)
            ->where('jenis_simpanan', 'pokok')
            ->where('status', 'paid')
            ->sum('nominal');

        // Simpanan Wajib (Hitung seluruh setoran simpanan wajib yang berhasil terbayar, termasuk cicilan parsial)
        $trxWajib = (float) \App\Models\SavingsTransaction::whereHas('savings', function ($q) use ($member) {
            $q->where('member_id', $member->id)->where('jenis_simpanan', 'wajib');
        })->where('status', 'success')->sum('nominal_bayar');

        $total_simpanan_wajib = $trxWajib > 0
            ? $trxWajib
            : (float) Saving::where('member_id', $member->id)
                ->where('jenis_simpanan', 'wajib')
                ->where('status', 'paid')
                ->sum('nominal');

        // Simpanan Sukarela
        $total_simpanan_sukarela = Saving::where('member_id', $member->id)
            ->where('jenis_simpanan', 'sukarela')
            ->where('status', 'paid')
            ->sum('nominal');

        // Total Penarikan Tunai Simpanan
        $total_penarikan = \App\Models\SavingsWithdrawal::where('member_id', $member->id)
            ->where('status', 'success')
            ->sum('nominal');

        $total_saldo_tabungan = max(0, ($total_simpanan_pokok + $total_simpanan_wajib + $total_simpanan_sukarela) - $total_penarikan);

        // Sisa Pinjaman (Aktif)
        $sisa_pinjaman = Loan::where('member_id', $member->id)
            ->whereIn('status', ['active', 'disbursed'])
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

        // Cek apakah ada pinjaman yang sudah disetujui Finance tapi belum dicairkan Kasir
        $approved_loan = Loan::where('member_id', $member->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        return view('dashboard.member', compact(
            'member',
            'total_simpanan_pokok',
            'total_simpanan_wajib',
            'total_simpanan_sukarela',
            'total_saldo_tabungan',
            'sisa_pinjaman',
            'next_installment',
            'approved_loan'
        ));
    }
}
