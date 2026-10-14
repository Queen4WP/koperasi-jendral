<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Services\KaryawanService;
use Illuminate\Http\Request;
use Exception;

class KaryawanController extends Controller
{
    protected KaryawanService $karyawanService;

    public function __construct(KaryawanService $karyawanService)
    {
        $this->karyawanService = $karyawanService;
    }

    public function dashboard()
    {
        $user = auth('karyawan')->user();
        $payrollSetting = $this->karyawanService->getPayrollSetting($user);
        $resignationSummary = $this->karyawanService->getResignationSettlementSummary($user);

        $member = $user->member;
        $totalSukarela = 0.0;
        $recentPayrollDeductions = collect();
        $activeLoans = collect();

        if ($member) {
            $totalSukarela = (float) Saving::where('member_id', $member->id)
                ->where('jenis_simpanan', 'sukarela')
                ->where('status', 'paid')
                ->sum('nominal');

            $recentPayrollDeductions = SavingsTransaction::whereHas('savings', function ($q) use ($member) {
                $q->where('member_id', $member->id);
            })
            ->where('catatan', 'like', '%Payroll%')
            ->latest()
            ->take(10)
            ->get();

            $activeLoans = Loan::with('installments')
                ->where('member_id', $member->id)
                ->latest()
                ->get();
        }

        return view('karyawan.dashboard', compact(
            'user',
            'payrollSetting',
            'resignationSummary',
            'totalSukarela',
            'recentPayrollDeductions',
            'activeLoans'
        ));
    }

    public function updatePayrollSetting(Request $request)
    {
        $request->validate([
            'nominal_sukarela_bulanan' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        try {
            $this->karyawanService->updatePayrollSetting(
                auth('karyawan')->user(),
                (float) $request->nominal_sukarela_bulanan,
                $request->has('is_active')
            );

            return back()->with('success', 'Pengaturan potong gaji simpanan sukarela berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function simulatePayrollCut()
    {
        try {
            $result = $this->karyawanService->processMonthlyPayrollDeduction(auth('karyawan')->user());
            $message = "Simulasi potong gaji bulanan sukses! Total dipotong: Rp " . number_format($result['total_deducted'], 0, ',', '.') . ". " . implode(' ', $result['messages']);

            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
