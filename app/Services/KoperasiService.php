<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class KoperasiService
{
    /**
     * Hitung Jasa Pinjaman berdasarkan PRD Bab 11
     * 6 bulan = 5%, 12 = 7%, 18 = 8.5%, 24 = 10%
     */
    public function calculateLoanInterestAndInstallment($jumlahPinjaman, $tenorBulan)
    {
        if ($jumlahPinjaman < 5000000 || $jumlahPinjaman > 50000000) {
            throw new Exception("Pinjaman di luar batas pengajuan (Rp 5.000.000 - Rp 50.000.000).");
        }

        $persenJasa = 0;
        switch ($tenorBulan) {
            case 6: $persenJasa = 0.05; break;
            case 12: $persenJasa = 0.07; break;
            case 18: $persenJasa = 0.085; break;
            case 24: $persenJasa = 0.10; break;
            default: throw new Exception("Tenor tidak valid. Pilih 6, 12, 18, atau 24 bulan.");
        }

        $totalJasa = $jumlahPinjaman * $persenJasa;
        $totalKewajiban = $jumlahPinjaman + $totalJasa;
        $angsuranPerBulan = round($totalKewajiban / $tenorBulan);

        return [
            'persen_jasa' => $persenJasa,
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
