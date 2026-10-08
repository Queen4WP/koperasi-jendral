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
        // Cek Simpanan Pokok
        $pokok = DB::table('savings')
            ->where('member_id', $memberId)
            ->where('jenis_simpanan', 'pokok')
            ->sum('jumlah');

        if ($pokok < 55000) {
            return [
                'eligible' => false,
                'message' => 'Anda belum dapat mengajukan pinjaman karena Simpanan Pokok belum dibayar penuh.'
            ];
        }

        // Cek Simpanan Wajib bulan berjalan
        $wajibBulanIni = DB::table('savings')
            ->where('member_id', $memberId)
            ->where('jenis_simpanan', 'wajib')
            ->whereYear('tanggal_simpan', date('Y'))
            ->whereMonth('tanggal_simpan', date('m'))
            ->sum('jumlah');

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
