<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class KoperasiService
{
    public const MIN_LOAN_AMOUNT = 5000000;
    public const MAX_LOAN_AMOUNT = 50000000;
    public const ALLOWED_TENORS = [6, 12, 18, 24];

    public const RATE_MATRIX = [
        6 => 5.0,
        12 => 7.0,
        18 => 8.5,
        24 => 10.0,
    ];

    /**
     * Dapatkan service rate dalam persentase (e.g. 7.0 untuk 12 bulan)
     */
    public static function getRatePercent(int $tenorBulan): float
    {
        if (!isset(self::RATE_MATRIX[$tenorBulan])) {
            throw new Exception("Tenor tidak valid. Pilih 6, 12, 18, atau 24 bulan.");
        }
        return self::RATE_MATRIX[$tenorBulan];
    }

    /**
     * Dapatkan service rate dalam desimal (e.g. 0.07 untuk 12 bulan)
     */
    public static function getRateDecimal(int $tenorBulan): float
    {
        return self::getRatePercent($tenorBulan) / 100;
    }

    /**
     * Hitung Jasa Pinjaman berdasarkan PRD Bab 11 & Matriks Resmi KOPIN
     * 6 bulan = 5%, 12 = 7%, 18 = 8.5%, 24 = 10%
     */
    public function calculateLoanInterestAndInstallment($jumlahPinjaman, $tenorBulan)
    {
        if ($jumlahPinjaman < self::MIN_LOAN_AMOUNT || $jumlahPinjaman > self::MAX_LOAN_AMOUNT) {
            throw new Exception("Pinjaman di luar batas pengajuan (Rp 5.000.000 - Rp 50.000.000).");
        }

        if (!isset(self::RATE_MATRIX[$tenorBulan])) {
            throw new Exception("Tenor tidak valid. Pilih 6, 12, 18, atau 24 bulan.");
        }

        $persenJasaPercent = self::RATE_MATRIX[$tenorBulan];
        $persenJasaDecimal = $persenJasaPercent / 100;

        $totalJasa = round($jumlahPinjaman * $persenJasaDecimal, 2);
        $totalKewajiban = round($jumlahPinjaman + $totalJasa, 2);
        $angsuranPerBulan = round($totalKewajiban / $tenorBulan);

        return [
            'persen_jasa' => $persenJasaDecimal,
            'rate_percent' => $persenJasaPercent,
            'total_jasa' => $totalJasa,
            'total_kewajiban' => $totalKewajiban,
            'angsuran_per_bulan' => $angsuranPerBulan
        ];
    }

    /**
     * Hitung Denda Keterlambatan berdasarkan PRD Bab 20
     */
    public function calculateDenda($hariKeterlambatan)
    {
        if ($hariKeterlambatan <= 6) {
            return 0;
        }
        
        return floor($hariKeterlambatan / 7) * 10000;
    }

    /**
     * Validasi Syarat Pengajuan Pinjaman berdasarkan PRD Bab 9
     */
    public function checkPinjamanEligibility($memberId)
    {
        // 0. Cek Status Keanggotaan (wajib aktif / terverifikasi)
        $member = DB::table('members')->where('id', $memberId)->first();
        if (!$member || $member->status !== 'active') {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mengajukan pinjaman karena akun belum diverifikasi.'
            ];
        }

        // 1. Cek Simpanan Pokok (wajib lunas minimal Rp 55.000)
        $pokok = DB::table('savings')
            ->where('member_id', $memberId)
            ->where('jenis_simpanan', 'pokok')
            ->where('status', 'paid')
            ->sum('nominal');

        if ($pokok < 55000) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mengajukan pinjaman karena Simpanan Pokok belum dibayar penuh.'
            ];
        }

        // 2. Cek Tunggakan Simpanan Wajib (status overdue atau periode lampau yang belum lunas)
        $startOfMonth = date('Y-m-01');
        $today = date('Y-m-d');

        $hasArrears = DB::table('savings')
            ->where('member_id', $memberId)
            ->where('jenis_simpanan', 'wajib')
            ->where(function ($query) use ($startOfMonth, $today) {
                $query->where('status', 'overdue')
                    ->orWhere(function ($q) use ($startOfMonth, $today) {
                        $q->where('status', '!=', 'paid')
                            ->where(function ($sub) use ($startOfMonth, $today) {
                                $sub->where('periode', '<', $startOfMonth)
                                    ->orWhere(function ($j) use ($today) {
                                        $j->whereNotNull('jatuh_tempo')
                                            ->where('jatuh_tempo', '<', $today);
                                    });
                            });
                    });
            })
            ->exists();

        if ($hasArrears) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mengajukan pinjaman karena masih terdapat tunggakan Simpanan Wajib.'
            ];
        }

        // 3. Cek Simpanan Wajib bulan berjalan (wajib lunas minimal Rp 100.000)
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        $wajibBulanIni = DB::table('savings')
            ->where('member_id', $memberId)
            ->where('jenis_simpanan', 'wajib')
            ->where('status', 'paid')
            ->whereYear('periode', $currentYear)
            ->whereMonth('periode', $currentMonth)
            ->sum('nominal');

        if ($wajibBulanIni < 100000) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mengajukan pinjaman karena Simpanan Wajib bulan berjalan belum dibayar penuh.'
            ];
        }

        return [
            'eligible' => true,
            'message' => 'Memenuhi syarat pengajuan pinjaman.'
        ];
    }
}
