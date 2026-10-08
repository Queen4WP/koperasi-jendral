<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Loan;

class DashboardController extends Controller
{
    public function index()
    {
        $total_anggota = Member::where('status', 'active')->count();
        $pinjaman_pending = Loan::where('status', 'submitted')->count();
        
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

        return view('admin.dashboard', compact('total_anggota', 'pinjaman_pending', 'tasks'));
    }
}
