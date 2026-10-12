<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\Loan;
use App\Models\Disbursement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DisbursementService
{
    protected $activityLogService;
    protected $notificationService;

    public function __construct(
        ActivityLogService $activityLogService = null,
        NotificationService $notificationService = null
    ) {
        $this->activityLogService = $activityLogService ?? resolve(ActivityLogService::class);
        $this->notificationService = $notificationService ?? resolve(NotificationService::class);
    }

    public function disburseLoan($loanId, $adminId, $nominal = null, $tanggalPencairan = null, $buktiTransfer = null, $nomorReferensi = null)
    {
        if (empty($nomorReferensi)) {
            throw new RuntimeException('Nomor referensi transaksi pencairan wajib diisi.');
        }

        if (empty($tanggalPencairan)) {
            throw new RuntimeException('Tanggal pencairan wajib diisi.');
        }

        return DB::transaction(function () use ($loanId, $adminId, $nominal, $tanggalPencairan, $buktiTransfer, $nomorReferensi) {
            $loan = Loan::lockForUpdate()->find($loanId);
            if (!$loan) {
                throw new RuntimeException('Loan tidak ditemukan.');
            }

            if ($loan->status !== 'approved') {
                throw new RuntimeException("Tidak bisa mencairkan loan dengan status {$loan->status}.");
            }

            if (Disbursement::where('loan_id', $loanId)->exists()) {
                throw new RuntimeException('Pinjaman ini sudah dicairkan sebelumnya.');
            }

            // Always enforce authoritative loan amount from database (Section 6)
            $authoritativeNominal = (float)$loan->jumlah_pinjaman;

            $disbursement = Disbursement::create([
                'loan_id' => $loanId,
                'admin_id' => $adminId,
                'nominal' => $authoritativeNominal,
                'tanggal_pencairan' => $tanggalPencairan,
                'nomor_referensi' => $nomorReferensi,
                'bukti_transfer' => $buktiTransfer,
            ]);

            $loan->status = 'disbursed';
            $loan->disbursed_at = now();
            $loan->save();

            $this->activityLogService->record(
                'loan.disbursed',
                $loan,
                "Pencairan pinjaman {$loan->nomor_pinjaman} sebesar Rp " . number_format($disbursement->nominal, 0, ',', '.') . " berhasil diproses.",
                [
                    'loan_id' => $loan->id,
                    'nomor_pinjaman' => $loan->nomor_pinjaman,
                    'disbursement_id' => $disbursement->id,
                    'nominal' => (float)$disbursement->nominal,
                    'nomor_referensi' => $nomorReferensi,
                    'tanggal_pencairan' => $tanggalPencairan,
                ],
                $adminId
            );

            if ($loan->member && $loan->member->user_id) {
                $this->notificationService->send(
                    $loan->member->user_id,
                    NotificationType::LOAN_DISBURSED,
                    'Pinjaman Telah Dicairkan',
                    "Pencairan pinjaman {$loan->nomor_pinjaman} sebesar Rp " . number_format($disbursement->nominal, 0, ',', '.') . " berhasil diproses.",
                    [
                        'loan_id' => $loan->id,
                        'nomor_pinjaman' => $loan->nomor_pinjaman,
                        'nominal' => (float)$disbursement->nominal,
                        'tanggal_pencairan' => $tanggalPencairan,
                    ]
                );
            }

            return $disbursement;
        });
    }
}
