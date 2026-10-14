<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\CashierClosing;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\SavingsWithdrawal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class KasirService
{
    protected ActivityLogService $activityLogService;
    protected NotificationService $notificationService;
    protected LoanService $loanService;

    public function __construct(
        ActivityLogService $activityLogService = null,
        NotificationService $notificationService = null,
        LoanService $loanService = null
    ) {
        $this->activityLogService = $activityLogService ?? resolve(ActivityLogService::class);
        $this->notificationService = $notificationService ?? resolve(NotificationService::class);
        $this->loanService = $loanService ?? resolve(LoanService::class);
    }

    /**
     * Terima Setoran Tunai Simpanan di Loket Kasir (Tanpa Upload Foto, Auto BKM).
     */
    public function processCashSavings(int $memberId, User $kasir, string $jenisSimpanan, float $nominal, ?string $catatan = null): array
    {
        if ($nominal <= 0) {
            throw new RuntimeException('Nominal setoran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($memberId, $kasir, $jenisSimpanan, $nominal, $catatan) {
            $member = Member::with('user')->findOrFail($memberId);

            // BKM (Bukti Kas Masuk) reference
            $nomorBkm = 'BKM-SIMP-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Simpanan Wajib / Pokok / Sukarela
            if ($jenisSimpanan === 'pokok') {
                $saving = Saving::where('member_id', $member->id)
                    ->where('jenis_simpanan', 'pokok')
                    ->lockForUpdate()
                    ->first();

                if ($saving && $saving->status === 'paid') {
                    throw new RuntimeException("Anggota {$member->user->name} sudah pernah melunasi Simpanan Pokok.");
                }

                if ($saving) {
                    $saving->update([
                        'nominal' => $nominal,
                        'status' => 'paid',
                    ]);
                } else {
                    $saving = Saving::create([
                        'member_id' => $member->id,
                        'jenis_simpanan' => 'pokok',
                        'nominal' => $nominal,
                        'periode' => null,
                        'jatuh_tempo' => null,
                        'status' => 'paid',
                    ]);
                }
            } elseif ($jenisSimpanan === 'wajib') {
                $targetObligation = 100000.00;

                // Cari tagihan simpanan wajib yang belum lunas (tertua terlebih dahulu: unpaid, partial, overdue)
                $saving = Saving::where('member_id', $member->id)
                    ->where('jenis_simpanan', 'wajib')
                    ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                    ->orderBy('periode', 'asc')
                    ->lockForUpdate()
                    ->first();

                if (! $saving) {
                    // Cek ketersediaan periode bulan berjalan
                    $currentMonth = now()->startOfMonth()->toDateString();
                    $alreadyExists = Saving::where('member_id', $member->id)
                        ->where('jenis_simpanan', 'wajib')
                        ->where('periode', $currentMonth)
                        ->exists();

                    if (! $alreadyExists) {
                        $targetPeriod = $currentMonth;
                    } else {
                        // Bulan berjalan sudah ada & lunas, alokasikan untuk bulan berikutnya
                        $latest = Saving::where('member_id', $member->id)
                            ->where('jenis_simpanan', 'wajib')
                            ->orderBy('periode', 'desc')
                            ->first();

                        $targetPeriod = ($latest && $latest->periode)
                            ? \Carbon\Carbon::parse($latest->periode)->addMonth()->startOfMonth()->toDateString()
                            : now()->addMonth()->startOfMonth()->toDateString();
                    }

                    $saving = Saving::create([
                        'member_id' => $member->id,
                        'jenis_simpanan' => 'wajib',
                        'nominal' => $targetObligation, // Kewajiban tetap Rp 100.000
                        'periode' => $targetPeriod,
                        'jatuh_tempo' => \Carbon\Carbon::parse($targetPeriod)->endOfMonth()->toDateString(),
                        'status' => 'unpaid',
                    ]);
                }

                // Skenario C: Hitung akumulasi pembayaran cicilan simpanan wajib
                $previouslyPaid = (float) $saving->transactions()->where('status', 'success')->sum('nominal_bayar');
                $totalPaid = $previouslyPaid + $nominal;
                $targetNominal = (float) $saving->nominal > 0 ? (float) $saving->nominal : $targetObligation;

                if ($totalPaid >= $targetNominal) {
                    $saving->status = 'paid';
                } else {
                    $saving->status = 'partial';
                }
                $saving->save();
            } else {
                // Simpanan Sukarela - periode null agar fleksibel dan tidak berbenturan dengan unique index
                $saving = Saving::create([
                    'member_id' => $member->id,
                    'jenis_simpanan' => 'sukarela',
                    'nominal' => $nominal,
                    'periode' => null,
                    'jatuh_tempo' => null,
                    'status' => 'paid',
                ]);
            }

            // Keterangan & detail catatan transaksi loket
            $periodeLabel = $saving->periode ? \Carbon\Carbon::parse($saving->periode)->translatedFormat('F Y') : '';
            $totalAccumulated = ($jenisSimpanan === 'wajib')
                ? ((float) $saving->transactions()->where('status', 'success')->sum('nominal_bayar') + $nominal)
                : $nominal;
            $sisaKewajiban = ($jenisSimpanan === 'wajib')
                ? max(0, ((float) $saving->nominal ?: 100000) - $totalAccumulated)
                : 0;

            if ($jenisSimpanan === 'wajib') {
                if ($saving->status === 'paid') {
                    $keteranganPeriode = " (Pelunasan Periode {$periodeLabel})";
                } else {
                    $keteranganPeriode = " (Cicilan Periode {$periodeLabel} - Terbayar: Rp " . number_format($totalAccumulated, 0, ',', '.') . ", Sisa: Rp " . number_format($sisaKewajiban, 0, ',', '.') . ")";
                }
            } else {
                $keteranganPeriode = '';
            }

            $trx = SavingsTransaction::create([
                'saving_id' => $saving->id,
                'admin_id' => $kasir->id,
                'nominal_bayar' => $nominal,
                'metode_pembayaran' => 'kantor',
                'status' => 'success',
                'payment_reference' => $nomorBkm,
                'catatan' => 'Setoran tunai di loket kasir' . $keteranganPeriode . '. ' . ($catatan ?? ''),
                'tanggal_bayar' => now(),
            ]);

            // Notifikasi ke anggota
            if ($member->user_id) {
                $judulNotif = $saving->status === 'paid'
                    ? 'Setoran Tunai Diterima Kasir (Lunas)'
                    : 'Setoran Sebagian Diterima (Cicilan Simpanan Wajib)';

                $pesanNotif = $saving->status === 'paid'
                    ? "Setoran tunai simpanan {$jenisSimpanan} sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah diterima di loket kasir (LUNAS). Ref: {$nomorBkm}."
                    : "Setoran cicilan simpanan wajib sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah diterima di loket kasir. Total terbayar: Rp " . number_format($totalAccumulated, 0, ',', '.') . " dari Rp 100.000 (Sisa kewajiban: Rp " . number_format($sisaKewajiban, 0, ',', '.') . "). Ref: {$nomorBkm}.";

                $this->notificationService->send(
                    $member->user_id,
                    NotificationType::SAVINGS_PAYMENT_APPROVED,
                    $judulNotif,
                    $pesanNotif,
                    [
                        'saving_id' => $saving->id,
                        'bkm' => $nomorBkm,
                        'nominal' => $nominal,
                        'sisa' => $sisaKewajiban,
                        'status' => $saving->status,
                    ]
                );
            }

            // Audit log
            $this->activityLogService->record(
                'kasir.cash_saving_received',
                $saving,
                "Kasir menerima setoran tunai {$jenisSimpanan} dari {$member->user->name} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ({$nomorBkm})",
                [
                    'member_id' => $member->id,
                    'nominal' => $nominal,
                    'bkm' => $nomorBkm,
                ],
                $kasir->id
            );

            return [
                'saving' => $saving,
                'transaction' => $trx,
                'nomor_bkm' => $nomorBkm,
                'member' => $member,
            ];
        });
    }

    /**
     * Penarikan Tunai Simpanan Wajib / Sukarela di Loket Kasir (BKK)
     * Aturan: Otomatis terkunci jika anggota memiliki tunggakan pinjaman aktif.
     */
    public function processCashWithdrawal(int $memberId, User $kasir, float $nominal, ?string $catatan = null): SavingsWithdrawal
    {
        if ($nominal <= 0) {
            throw new RuntimeException('Nominal penarikan harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($memberId, $kasir, $nominal, $catatan) {
            $member = Member::with('user')->findOrFail($memberId);

            // 1. Cek tunggakan pinjaman (Arrears lock)
            $hasArrears = LoanInstallment::whereHas('loan', function ($q) use ($memberId) {
                $q->where('member_id', $memberId)->whereIn('status', ['active', 'approved', 'disbursed']);
            })
            ->where('jatuh_tempo', '<', now()->toDateString())
            ->where('status', 'unpaid')
            ->exists();

            if ($hasArrears) {
                throw new RuntimeException("Penarikan simpanan ditolak: Anggota {$member->user->name} memiliki tunggakan angsuran pinjaman berjalan yang belum dibayar.");
            }

            // 2. Cek saldo simpanan anggota
            $totalSimpanan = (float) Saving::where('member_id', $memberId)->where('status', 'paid')->sum('nominal');
            $totalTelahDitarik = (float) SavingsWithdrawal::where('member_id', $memberId)->where('status', 'success')->sum('nominal');
            $saldoTersedia = $totalSimpanan - $totalTelahDitarik;

            if ($nominal > $saldoTersedia) {
                throw new RuntimeException("Saldo simpanan tidak mencukupi. Saldo tersedia: Rp " . number_format($saldoTersedia, 0, ',', '.') . ", permintaan penarikan: Rp " . number_format($nominal, 0, ',', '.') . ".");
            }

            // BKK (Bukti Kas Keluar) reference
            $nomorBkk = 'BKK-TARIK-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $withdrawal = SavingsWithdrawal::create([
                'member_id' => $memberId,
                'kasir_id' => $kasir->id,
                'nominal' => $nominal,
                'nomor_referensi' => $nomorBkk,
                'tanggal_penarikan' => now(),
                'status' => 'success',
                'catatan' => $catatan ?? 'Penarikan simpanan tunai di loket kasir.',
            ]);

            // Notifikasi ke anggota
            if ($member->user_id) {
                $this->notificationService->send(
                    $member->user_id,
                    NotificationType::SAVINGS_WITHDRAWAL ?? 'savings_withdrawal',
                    'Penarikan Simpanan Tunai Berhasil',
                    "Penarikan simpanan tunai sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah diserahkan di loket kasir ({$nomorBkk}).",
                    [
                        'withdrawal_id' => $withdrawal->id,
                        'bkk' => $nomorBkk,
                        'nominal' => $nominal,
                    ]
                );
            }

            // Audit log
            $this->activityLogService->record(
                'kasir.savings_withdrawal_paid',
                $withdrawal,
                "Kasir menyerahkan penarikan simpanan tunai Rp " . number_format($nominal, 0, ',', '.') . " kepada {$member->user->name} ({$nomorBkk})",
                [
                    'member_id' => $member->id,
                    'nominal' => $nominal,
                    'bkk' => $nomorBkk,
                ],
                $kasir->id
            );

            return $withdrawal;
        });
    }

    /**
     * Pencairan Pinjaman Tunai di Loket Kasir (DISBURSED - Uang Tunai Diambil di Kantor).
     * Aturan: Dual ledger display - Saldo simpanan tidak terdebet. Anggota tanda tangani tanda terima fisik.
     */
    public function processCashLoanDisbursement(int $loanId, User $kasir, ?string $catatan = null): array
    {
        return DB::transaction(function () use ($loanId, $kasir, $catatan) {
            $loan = Loan::lockForUpdate()->with('member.user')->findOrFail($loanId);

            if ($loan->status !== 'approved') {
                throw new RuntimeException("Pinjaman dengan status '{$loan->status}' tidak dapat dicairkan di loket kasir.");
            }

            if (Disbursement::where('loan_id', $loanId)->exists()) {
                throw new RuntimeException('Pinjaman ini sudah dicairkan sebelumnya.');
            }

            $nomorBkk = 'BKK-CAIR-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $nominal = (float) $loan->jumlah_pinjaman;

            // Catat pencairan di loket
            $disbursement = Disbursement::create([
                'loan_id' => $loan->id,
                'admin_id' => $kasir->id,
                'nominal' => $nominal,
                'tanggal_pencairan' => now()->toDateString(),
                'nomor_referensi' => $nomorBkk,
                'bukti_transfer' => null, // Tunai loket kasir
            ]);

            // Ubah status loan ke disbursed dan aktifkan jadwal angsuran
            $loan->status = 'disbursed';
            $loan->disbursed_at = now();
            $loan->save();

            // Aktifkan loan dan buat tabel angsuran
            $this->loanService->activateLoan($loan->id, now()->toDateString());

            // Notifikasi ke anggota (Live Status Tracking: DISBURSED)
            if ($loan->member && $loan->member->user_id) {
                $this->notificationService->send(
                    $loan->member->user_id,
                    NotificationType::LOAN_DISBURSED,
                    'Uang Pinjaman Telah Dicairkan (DISBURSED)',
                    "Pinjaman {$loan->nomor_pinjaman} sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah DISBURSED / diserahkan secara tunai di loket kasir kantor ({$nomorBkk}). Tanda terima fisik telah ditandatangani.",
                    [
                        'loan_id' => $loan->id,
                        'bkk' => $nomorBkk,
                        'nominal' => $nominal,
                        'status' => 'disbursed',
                    ]
                );
            }

            // Audit log
            $this->activityLogService->record(
                'kasir.loan_disbursed_cash',
                $loan,
                "Kasir mencairkan uang tunai pinjaman {$loan->nomor_pinjaman} kepada {$loan->member->user->name} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ({$nomorBkk})",
                [
                    'loan_id' => $loan->id,
                    'nominal' => $nominal,
                    'bkk' => $nomorBkk,
                ],
                $kasir->id
            );

            return [
                'loan' => $loan,
                'disbursement' => $disbursement,
                'nomor_bkk' => $nomorBkk,
            ];
        });
    }

    /**
     * Bayar Angsuran Pinjaman Tunai di Loket Kasir (Tanpa Upload Bukti, Auto BKM).
     */
    public function processCashInstallmentPayment(int $installmentId, User $kasir, ?string $catatan = null): array
    {
        return DB::transaction(function () use ($installmentId, $kasir, $catatan) {
            $installment = LoanInstallment::lockForUpdate()->with('loan.member.user')->findOrFail($installmentId);

            if ($installment->status === 'paid') {
                throw new RuntimeException('Angsuran ini sudah berstatus lunas.');
            }

            $nomorBkm = 'BKM-ANGS-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $nominal = (float) $installment->total_angsuran;

            // Catat pembayaran angsuran
            $payment = LoanPayment::create([
                'installment_id' => $installment->id,
                'admin_id' => $kasir->id,
                'nominal_bayar' => $nominal,
                'denda_dibayar' => 0,
                'metode_pembayaran' => 'kantor',
                'status' => 'success',
                'payment_reference' => $nomorBkm,
                'catatan' => 'Pembayaran tunai di loket kasir. ' . ($catatan ?? ''),
                'tanggal_bayar' => now(),
            ]);

            // Update status angsuran
            $installment->status = 'paid';
            $installment->save();

            // Cek apakah seluruh angsuran pada loan ini sudah lunas
            $loan = $installment->loan;
            $unpaidCount = LoanInstallment::where('loan_id', $loan->id)->where('status', '!=', 'paid')->count();
            if ($unpaidCount === 0) {
                $loan->status = 'paid_off';
                $loan->paid_off_at = now();
                $loan->save();
            }

            // Notifikasi ke anggota
            if ($loan->member && $loan->member->user_id) {
                $this->notificationService->send(
                    $loan->member->user_id,
                    NotificationType::INSTALLMENT_PAID ?? 'installment_paid',
                    'Pembayaran Angsuran Tunai Diterima',
                    "Pembayaran angsuran ke-{$installment->angsuran_ke} pinjaman {$loan->nomor_pinjaman} sebesar Rp " . number_format($nominal, 0, ',', '.') . " telah diterima di loket kasir ({$nomorBkm}).",
                    [
                        'installment_id' => $installment->id,
                        'bkm' => $nomorBkm,
                        'nominal' => $nominal,
                    ]
                );
            }

            // Audit log
            $this->activityLogService->record(
                'kasir.installment_paid_cash',
                $payment,
                "Kasir menerima pembayaran angsuran ke-{$installment->angsuran_ke} pinjaman {$loan->nomor_pinjaman} sebesar Rp " . number_format($nominal, 0, ',', '.') . " ({$nomorBkm})",
                [
                    'installment_id' => $installment->id,
                    'loan_id' => $loan->id,
                    'nominal' => $nominal,
                    'bkm' => $nomorBkm,
                ],
                $kasir->id
            );

            return [
                'installment' => $installment,
                'payment' => $payment,
                'nomor_bkm' => $nomorBkm,
            ];
        });
    }

    /**
     * Hitung rekap arus kas harian loket kasir hari ini.
     */
    public function getDailyCashSummary(User $kasir, ?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();

        // Kas Masuk hari ini: Setoran simpanan tunai + Bayar angsuran tunai
        $simpananMasuk = (float) SavingsTransaction::where('admin_id', $kasir->id)
            ->where('metode_pembayaran', 'kantor')
            ->whereDate('tanggal_bayar', $targetDate)
            ->sum('nominal_bayar');

        $angsuranMasuk = (float) LoanPayment::where('admin_id', $kasir->id)
            ->where('metode_pembayaran', 'kantor')
            ->whereDate('tanggal_bayar', $targetDate)
            ->sum('nominal_bayar');

        $totalKasMasuk = $simpananMasuk + $angsuranMasuk;

        // Kas Keluar hari ini: Pencairan pinjaman tunai + Penarikan simpanan tunai
        $pinjamanKeluar = (float) Disbursement::where('admin_id', $kasir->id)
            ->whereDate('tanggal_pencairan', $targetDate)
            ->sum('nominal');

        $penarikanKeluar = (float) SavingsWithdrawal::where('kasir_id', $kasir->id)
            ->where('status', 'success')
            ->whereDate('tanggal_penarikan', $targetDate)
            ->sum('nominal');

        $totalKasKeluar = $pinjamanKeluar + $penarikanKeluar;

        // Saldo fisik kas akhir hari ini yang harus diserahkan ke Finance
        $saldoKasFisikTersedia = max(0.0, $totalKasMasuk - $totalKasKeluar);

        // Cek status closing hari ini
        $closingHariIni = CashierClosing::where('kasir_id', $kasir->id)
            ->whereDate('tanggal_closing', $targetDate)
            ->latest()
            ->first();

        return [
            'tanggal' => $targetDate,
            'simpanan_masuk' => $simpananMasuk,
            'angsuran_masuk' => $angsuranMasuk,
            'total_kas_masuk' => $totalKasMasuk,
            'pinjaman_keluar' => $pinjamanKeluar,
            'penarikan_keluar' => $penarikanKeluar,
            'total_kas_keluar' => $totalKasKeluar,
            'saldo_fisik_tersedia' => $saldoKasFisikTersedia,
            'closing' => $closingHariIni,
        ];
    }

    /**
     * Submit Closing Kas Harian Kasir Jam 16:00 (Daily Zero-Cash Handover ke Finance).
     */
    public function submitDailyClosing(User $kasir, float $totalFisikDiserahkan, ?string $catatan = null): CashierClosing
    {
        return DB::transaction(function () use ($kasir, $totalFisikDiserahkan, $catatan) {
            $summary = $this->getDailyCashSummary($kasir);

            $closing = CashierClosing::create([
                'kasir_id' => $kasir->id,
                'tanggal_closing' => now()->toDateString(),
                'total_kas_masuk' => $summary['total_kas_masuk'],
                'total_kas_keluar' => $summary['total_kas_keluar'],
                'total_fisik_diserahkan' => $totalFisikDiserahkan,
                'status' => 'pending',
                'catatan' => $catatan ?? 'Closing loket kasir jam 16:00. Siap serah terima fisik kas ke Finance.',
            ]);

            // Notifikasi ke seluruh staf Finance
            $financeUsers = User::where('role', 'finance')->get();
            foreach ($financeUsers as $finance) {
                $this->notificationService->send(
                    $finance->id,
                    NotificationType::CLOSING_SUBMITTED,
                    'Closing Kas Harian Loket Kasir (Perlu Verifikasi)',
                    "Kasir {$kasir->name} telah melakukan closing kas hari ini. Fisik uang diserahkan: Rp " . number_format($totalFisikDiserahkan, 0, ',', '.') . ". Harap lakukan penghitungan fisik dan verifikasi.",
                    [
                        'closing_id' => $closing->id,
                        'kasir_id' => $kasir->id,
                        'total_fisik' => $totalFisikDiserahkan,
                    ]
                );
            }

            // Audit log
            $this->activityLogService->record(
                'kasir.daily_closing_submitted',
                $closing,
                "Kasir menyerahkan closing kas harian sebesar Rp " . number_format($totalFisikDiserahkan, 0, ',', '.') . " kepada Finance.",
                [
                    'closing_id' => $closing->id,
                    'total_fisik' => $totalFisikDiserahkan,
                ],
                $kasir->id
            );

            return $closing;
        });
    }
}
