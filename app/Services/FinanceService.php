<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\CashierClosing;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Saving;
use App\Models\SavingsWithdrawal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FinanceService
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
     * Hitung metrik likuiditas dan cadangan wajib 10% (10% Pocket Reserve Rule).
     */
    public function getReserveMetrics(): array
    {
        // 1. Total simpanan wajib lunas dari seluruh anggota
        $totalSimpananWajib = (float) Saving::where('jenis_simpanan', 'wajib')
            ->where('status', 'paid')
            ->sum('nominal');

        // 2. Minimum cadangan mengendap (10% dari total simpanan wajib)
        $minimumReserve = $totalSimpananWajib * 0.10;

        // 3. Arus kas masuk (Simpanan Pokok + Wajib + Sukarela + Angsuran pinjaman yang dibayar)
        $totalSimpananMasuk = (float) Saving::where('status', 'paid')->sum('nominal');
        $totalAngsuranMasuk = (float) LoanInstallment::where('status', 'paid')->sum('total_angsuran');
        
        // Base modal awal kas operasional koperasi (Rp 50.000.000 jika data awal)
        $baseModalKas = 50000000.0;
        $totalKasMasuk = $baseModalKas + $totalSimpananMasuk + $totalAngsuranMasuk;

        // 4. Arus kas keluar (Pinjaman dicairkan + Penarikan simpanan)
        $totalPinjamanKeluar = (float) Disbursement::sum('nominal');
        $totalPenarikanKeluar = (float) SavingsWithdrawal::where('status', 'success')->sum('nominal');
        $totalKasKeluar = $totalPinjamanKeluar + $totalPenarikanKeluar;

        // 5. Pocket kas likuid saat ini
        $currentPocket = max(0.0, $totalKasMasuk - $totalKasKeluar);

        // 6. Dana yang dapat dipinjamkan (Maksimal penyaluran tanpa melanggar 10% cadangan)
        $maxLendable = max(0.0, $currentPocket - $minimumReserve);

        // 7. Persentase rasio cadangan saat ini terhadap simpanan wajib
        $reserveRatioPercent = $totalSimpananWajib > 0 
            ? round(($currentPocket / $totalSimpananWajib) * 100, 2) 
            : 100.0;

        return [
            'total_simpanan_wajib' => $totalSimpananWajib,
            'minimum_reserve' => $minimumReserve,
            'current_pocket' => $currentPocket,
            'max_lendable' => $maxLendable,
            'reserve_ratio_percent' => $reserveRatioPercent,
            'is_healthy' => $currentPocket >= $minimumReserve,
            'total_kas_masuk' => $totalKasMasuk,
            'total_kas_keluar' => $totalKasKeluar,
        ];
    }

    /**
     * Validasi apakah pinjaman aman dicairkan berdasarkan aturan cadangan 10%.
     */
    public function canApproveLoan(float $loanAmount): array
    {
        $metrics = $this->getReserveMetrics();
        $pocketAfterDisbursement = $metrics['current_pocket'] - $loanAmount;

        if ($pocketAfterDisbursement < $metrics['minimum_reserve']) {
            $defisit = $metrics['minimum_reserve'] - $pocketAfterDisbursement;
            return [
                'allowed' => false,
                'current_pocket' => $metrics['current_pocket'],
                'minimum_reserve' => $metrics['minimum_reserve'],
                'pocket_after' => $pocketAfterDisbursement,
                'defisit' => $defisit,
                'reason' => "Pelanggaran Aturan 10% Pocket Reserve: Pencairan pinjaman Rp " . number_format($loanAmount, 0, ',', '.') . 
                    " akan menyisakan kas pocket Rp " . number_format($pocketAfterDisbursement, 0, ',', '.') . 
                    ", di bawah syarat minimum likuiditas 10% (Rp " . number_format($metrics['minimum_reserve'], 0, ',', '.') . "). Defisit likuiditas: Rp " . number_format($defisit, 0, ',', '.') . ".",
            ];
        }

        return [
            'allowed' => true,
            'current_pocket' => $metrics['current_pocket'],
            'minimum_reserve' => $metrics['minimum_reserve'],
            'pocket_after' => $pocketAfterDisbursement,
            'reason' => 'Likuiditas cadangan aman (> 10% total simpanan wajib).',
        ];
    }

    /**
     * Finance menyetujui pinjaman dengan note persetujuan.
     */
    public function approveLoan(int $loanId, User $financeUser, ?string $approvalNote = null): Loan
    {
        return DB::transaction(function () use ($loanId, $financeUser, $approvalNote) {
            $loan = Loan::lockForUpdate()->with('member.user')->findOrFail($loanId);

            if (!in_array($loan->status, ['submitted', 'under_review'])) {
                throw new RuntimeException("Pinjaman dengan status '{$loan->status}' tidak dapat disetujui.");
            }

            // Validasi batasan saldo simpanan (Maksimal pinjaman dibatasi oleh saldo simpanan yg dimiliki anggota)
            $totalSimpananMember = (float) Saving::where('member_id', $loan->member_id)
                ->where('status', 'paid')
                ->sum('nominal');
            
            // Batas maksimal pinjaman dibatasi oleh saldo simpanan yg dimiliki
            if ($loan->jumlah_pinjaman > $totalSimpananMember && $totalSimpananMember > 0) {
                // Opsional warning atau pembatasan ketat sesuai kebutuhan
            }

            // Validasi aturan cadangan 10%
            $reserveCheck = $this->canApproveLoan((float) $loan->jumlah_pinjaman);
            if (!$reserveCheck['allowed']) {
                throw new RuntimeException($reserveCheck['reason']);
            }

            $loan->status = 'approved';
            $loan->finance_id = $financeUser->id;
            $loan->reviewed_by = $financeUser->id;
            $loan->approval_note = $approvalNote ?? 'Disetujui oleh Finance setelah evaluasi likuiditas 10% pocket reserve.';
            $loan->approved_at = now();
            $loan->reviewed_at = now();
            $loan->save();

            // 1. Notifikasi lonceng & email ke role Kasir
            $kasirUsers = User::where('role', 'kasir')->get();
            foreach ($kasirUsers as $kasir) {
                $this->notificationService->send(
                    $kasir->id,
                    NotificationType::LOAN_APPROVED,
                    'Instruksi Kasir: Siapkan Uang Tunai Pinjaman',
                    "Pinjaman {$loan->nomor_pinjaman} atas nama {$loan->member->user->name} telah DISETUJUI oleh Finance. Harap siapkan uang tunai sebesar Rp " . number_format($loan->jumlah_pinjaman, 0, ',', '.') . " di loket kasir.",
                    [
                        'loan_id' => $loan->id,
                        'nomor_pinjaman' => $loan->nomor_pinjaman,
                        'jumlah_pinjaman' => (float)$loan->jumlah_pinjaman,
                        'approval_note' => $loan->approval_note,
                    ]
                );
            }

            // 2. Notifikasi ke Anggota (Live Status Tracking: APPROVED)
            if ($loan->member && $loan->member->user_id) {
                $this->notificationService->send(
                    $loan->member->user_id,
                    NotificationType::LOAN_APPROVED,
                    'Pengajuan Pinjaman Disetujui Finance',
                    "Pengajuan pinjaman Anda ({$loan->nomor_pinjaman}) sebesar Rp " . number_format($loan->jumlah_pinjaman, 0, ',', '.') . " telah DISETUJUI oleh Finance. Catatan persetujuan: \"{$loan->approval_note}\". Silakan pantau status pencairan di loket kasir kantor.",
                    [
                        'loan_id' => $loan->id,
                        'nomor_pinjaman' => $loan->nomor_pinjaman,
                        'status' => 'approved',
                        'approval_note' => $loan->approval_note,
                    ]
                );
            }

            // 3. Audit log
            $this->activityLogService->record(
                'loan.finance_approved',
                $loan,
                "Finance menyetujui pinjaman {$loan->nomor_pinjaman} sebesar Rp " . number_format($loan->jumlah_pinjaman, 0, ',', '.') . ". Catatan: {$loan->approval_note}",
                [
                    'loan_id' => $loan->id,
                    'nomor_pinjaman' => $loan->nomor_pinjaman,
                    'jumlah_pinjaman' => (float)$loan->jumlah_pinjaman,
                    'approval_note' => $loan->approval_note,
                ],
                $financeUser->id
            );

            return $loan;
        });
    }

    /**
     * Finance menolak pinjaman dengan transparansi alasan penolakan.
     */
    public function rejectLoan(int $loanId, User $financeUser, string $rejectionReason): Loan
    {
        return DB::transaction(function () use ($loanId, $financeUser, $rejectionReason) {
            $loan = Loan::lockForUpdate()->with('member.user')->findOrFail($loanId);

            if (!in_array($loan->status, ['submitted', 'under_review'])) {
                throw new RuntimeException("Pinjaman dengan status '{$loan->status}' tidak dapat ditolak.");
            }

            $loan->status = 'rejected';
            $loan->finance_id = $financeUser->id;
            $loan->reviewed_by = $financeUser->id;
            $loan->rejection_reason = $rejectionReason;
            $loan->rejected_at = now();
            $loan->reviewed_at = now();
            $loan->save();

            // Notifikasi ke Anggota
            if ($loan->member && $loan->member->user_id) {
                $this->notificationService->send(
                    $loan->member->user_id,
                    NotificationType::LOAN_REJECTED,
                    'Pengajuan Pinjaman Ditolak Finance',
                    "Pengajuan pinjaman Anda ({$loan->nomor_pinjaman}) belum dapat disetujui. Alasan: \"{$rejectionReason}\".",
                    [
                        'loan_id' => $loan->id,
                        'nomor_pinjaman' => $loan->nomor_pinjaman,
                        'rejection_reason' => $rejectionReason,
                    ]
                );
            }

            // Audit log
            $this->activityLogService->record(
                'loan.finance_rejected',
                $loan,
                "Finance menolak pinjaman {$loan->nomor_pinjaman}. Alasan: {$rejectionReason}",
                [
                    'loan_id' => $loan->id,
                    'rejection_reason' => $rejectionReason,
                ],
                $financeUser->id
            );

            return $loan;
        });
    }

    /**
     * Finance memverifikasi serah terima kas harian kasir jam 16:00 (Daily Zero-Cash Handover).
     */
    public function verifyCashierClosing(int $closingId, User $financeUser, ?string $catatan = null): CashierClosing
    {
        return DB::transaction(function () use ($closingId, $financeUser, $catatan) {
            $closing = CashierClosing::lockForUpdate()->findOrFail($closingId);

            if ($closing->status === 'verified') {
                throw new RuntimeException('Serah terima kas ini sudah diverifikasi sebelumnya.');
            }

            $closing->status = 'verified';
            $closing->finance_id = $financeUser->id;
            $closing->verified_at = now();
            if ($catatan) {
                $closing->catatan = ($closing->catatan ? $closing->catatan . " | " : "") . "Verifikasi Finance: " . $catatan;
            }
            $closing->save();

            // Notifikasi ke Kasir
            $this->notificationService->send(
                $closing->kasir_id,
                NotificationType::CLOSING_VERIFIED,
                'Serah Terima Kas Telah Terverifikasi',
                "Serah terima kas closing tanggal {$closing->tanggal_closing->format('d/m/Y')} sejumlah Rp " . number_format($closing->total_fisik_diserahkan, 0, ',', '.') . " telah diverifikasi dan diterima oleh Finance.",
                ['closing_id' => $closing->id]
            );

            // Audit log
            $this->activityLogService->record(
                'finance.closing_verified',
                $closing,
                "Finance memverifikasi serah terima kas kasir ID #{$closing->id} sebesar Rp " . number_format($closing->total_fisik_diserahkan, 0, ',', '.'),
                [
                    'closing_id' => $closing->id,
                    'kasir_id' => $closing->kasir_id,
                    'total_fisik' => (float)$closing->total_fisik_diserahkan,
                ],
                $financeUser->id
            );

            return $closing;
        });
    }
}
