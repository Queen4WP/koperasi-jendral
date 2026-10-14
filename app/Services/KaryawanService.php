<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\EmployeePayrollSetting;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class KaryawanService
{
    protected ActivityLogService $activityLogService;
    protected NotificationService $notificationService;

    public function __construct(
        ActivityLogService $activityLogService = null,
        NotificationService $notificationService = null
    ) {
        $this->activityLogService = $activityLogService ?? resolve(ActivityLogService::class);
        $this->notificationService = $notificationService ?? resolve(NotificationService::class);
    }

    /**
     * Dapatkan atau inisialisasi pengaturan potong gaji karyawan.
     */
    public function getPayrollSetting(User $user): EmployeePayrollSetting
    {
        return EmployeePayrollSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['nominal_sukarela_bulanan' => 100000.0, 'is_active' => true]
        );
    }

    /**
     * Perbarui nominal potong gaji simpanan sukarela.
     */
    public function updatePayrollSetting(User $user, float $nominalSukarela, bool $isActive = true): EmployeePayrollSetting
    {
        $setting = $this->getPayrollSetting($user);
        $setting->update([
            'nominal_sukarela_bulanan' => $nominalSukarela,
            'is_active' => $isActive,
        ]);

        $this->activityLogService->record(
            'karyawan.payroll_setting_updated',
            $setting,
            "Pengaturan potong gaji simpanan sukarela diperbarui menjadi Rp " . number_format($nominalSukarela, 0, ',', '.'),
            [
                'nominal' => $nominalSukarela,
                'is_active' => $isActive,
            ],
            $user->id
        );

        return $setting;
    }

    /**
     * Eksekusi simulasi pemotongan gaji bulanan (Simpanan Sukarela + Angsuran Pinjaman).
     */
    public function processMonthlyPayrollDeduction(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $setting = $this->getPayrollSetting($user);
            $member = $user->member;

            if (!$member) {
                // Buat data member untuk karyawan jika belum ada
                $member = Member::create([
                    'user_id' => $user->id,
                    'nik' => 'KRY-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                    'tanggal_lahir' => '1990-01-01',
                    'alamat' => 'Kantor KOPIN Pusat',
                    'telepon' => '08123456789',
                    'pekerjaan' => 'Karyawan Koperasi',
                    'perusahaan' => 'KSP KOPIN',
                    'status' => 'active',
                ]);
            }

            $results = [
                'sukarela_deducted' => 0.0,
                'loan_installments_deducted' => 0.0,
                'total_deducted' => 0.0,
                'messages' => [],
            ];

            // 1. Potong gaji untuk Simpanan Sukarela
            if ($setting->is_active && $setting->nominal_sukarela_bulanan > 0) {
                $nominalSukarela = (float) $setting->nominal_sukarela_bulanan;
                $refNo = 'PAYROLL-SUK-' . date('Ym') . '-' . strtoupper(Str::random(4));

                $saving = Saving::create([
                    'member_id' => $member->id,
                    'jenis_simpanan' => 'sukarela',
                    'nominal' => $nominalSukarela,
                    'periode' => null,
                    'jatuh_tempo' => null,
                    'status' => 'paid',
                ]);

                SavingsTransaction::create([
                    'saving_id' => $saving->id,
                    'admin_id' => $user->id,
                    'nominal_bayar' => $nominalSukarela,
                    'metode_pembayaran' => 'kantor',
                    'status' => 'success',
                    'payment_reference' => $refNo,
                    'catatan' => 'Potong gaji otomatis bulanan (Simpanan Sukarela Karyawan - Payroll)',
                    'tanggal_bayar' => now(),
                ]);

                $results['sukarela_deducted'] = $nominalSukarela;
                $results['messages'][] = "Simpanan Sukarela Rp " . number_format($nominalSukarela, 0, ',', '.') . " berhasil dipotong dari payroll ({$refNo}).";
            }

            // 2. Potong gaji untuk Angsuran Pinjaman Karyawan
            $dueInstallments = LoanInstallment::whereHas('loan', function ($q) use ($member) {
                $q->where('member_id', $member->id)->whereIn('status', ['active', 'disbursed']);
            })
            ->where('status', '!=', 'paid')
            ->orderBy('angsuran_ke')
            ->take(1)
            ->get();

            foreach ($dueInstallments as $installment) {
                $nominalAngs = (float) $installment->total_angsuran;
                $refNo = 'PAYROLL-ANGS-' . date('Ym') . '-' . strtoupper(Str::random(4));

                LoanPayment::create([
                    'installment_id' => $installment->id,
                    'admin_id' => $user->id,
                    'nominal_bayar' => $nominalAngs,
                    'denda_dibayar' => 0,
                    'metode_pembayaran' => 'kantor',
                    'status' => 'success',
                    'payment_reference' => $refNo,
                    'catatan' => 'Potong gaji otomatis angsuran pinjaman ke-' . $installment->angsuran_ke . ' (Payroll)',
                    'tanggal_bayar' => now(),
                ]);

                $installment->status = 'paid';
                $installment->save();

                $loan = $installment->loan;
                if ($loan->installments()->where('status', '!=', 'paid')->count() === 0) {
                    $loan->status = 'paid_off';
                    $loan->paid_off_at = now();
                    $loan->save();
                }

                $results['loan_installments_deducted'] += $nominalAngs;
                $results['messages'][] = "Angsuran pinjaman ke-{$installment->angsuran_ke} Rp " . number_format($nominalAngs, 0, ',', '.') . " berhasil dipotong otomatis dari payroll.";
            }

            $results['total_deducted'] = $results['sukarela_deducted'] + $results['loan_installments_deducted'];

            return $results;
        });
    }

    /**
     * Hitung rekap settlement pesangon/saldo simpanan saat karyawan resign.
     * Aturan: Pencairan penuh Simpanan Sukarela dikurangi sisa hutang pinjaman.
     */
    public function getResignationSettlementSummary(User $user): array
    {
        $member = $user->member;
        if (!$member) {
            return [
                'total_sukarela' => 0.0,
                'total_sisa_pinjaman' => 0.0,
                'net_payout' => 0.0,
                'can_settle' => true,
            ];
        }

        // Total simpanan sukarela
        $totalSukarela = (float) Saving::where('member_id', $member->id)
            ->where('jenis_simpanan', 'sukarela')
            ->where('status', 'paid')
            ->sum('nominal');

        // Total sisa hutang pinjaman aktif
        $totalSisaPinjaman = (float) LoanInstallment::whereHas('loan', function ($q) use ($member) {
            $q->where('member_id', $member->id)->whereIn('status', ['active', 'disbursed']);
        })
        ->where('status', '!=', 'paid')
        ->sum('total_angsuran');

        // Net Payout (Hak bersih yang diterima karyawan)
        $netPayout = $totalSukarela - $totalSisaPinjaman;

        return [
            'total_sukarela' => $totalSukarela,
            'total_sisa_pinjaman' => $totalSisaPinjaman,
            'net_payout' => $netPayout,
            'can_settle' => true,
            'is_surplus' => $netPayout >= 0,
        ];
    }
}
