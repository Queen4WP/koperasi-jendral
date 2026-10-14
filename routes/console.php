<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('savings:send-reminders', function () {
    $this->info('Mengirim notifikasi pengingat Simpanan Wajib (Gotong Royong tone)...');
    $unpaidSavings = \App\Models\Saving::with(['member.user', 'transactions'])
        ->where('jenis_simpanan', 'wajib')
        ->whereIn('status', ['unpaid', 'partial', 'overdue'])
        ->whereMonth('jatuh_tempo', now()->month)
        ->get();

    $notificationService = resolve(\App\Services\NotificationService::class);

    foreach ($unpaidSavings as $saving) {
        if ($saving->member && $saving->member->user_id) {
            $totalPaid = (float) $saving->transactions->where('status', 'success')->sum('nominal_bayar');
            $sisa = max(0, (float) $saving->nominal - $totalPaid);
            $nama = $saving->member->user->name;

            if ($totalPaid > 0) {
                // Skenario C: Anggota telah menyetor sebagian (cicilan)
                $pesan = "Salam hangat Saudara {$nama}, kami mencatat Anda telah menyetor sebagian Simpanan Wajib sebesar Rp " . number_format($totalPaid, 0, ',', '.') . ". Sisa kewajiban simpanan wajib Anda bulan ini adalah Rp " . number_format($sisa, 0, ',', '.') . " dari total Rp 100.000 (Jatuh tempo: akhir bulan). Mari genapi simpanan wajib Anda untuk merawat semangat gotong royong KOPIN.";
                $subjek = "Pengingat Sisa Simpanan Wajib (Kurang Rp " . number_format($sisa, 0, ',', '.') . ")";
            } else {
                $pesan = "Salam hangat Saudara {$nama}, mengingatkan iuran Simpanan Wajib Anda bulan ini sebesar Rp " . number_format($saving->nominal, 0, ',', '.') . " jatuh tempo pada akhir bulan. Pembayaran dapat disetor langsung di loket kasir atau via transfer. Terima kasih atas kebersamaan kita.";
                $subjek = "Mari Rawat Semangat Gotong Royong KOPIN";
            }

            $notificationService->send(
                $saving->member->user_id,
                \App\Constants\NotificationType::SAVING_DUE_REMINDER ?? 'saving_reminder',
                $subjek,
                $pesan,
                [
                    'saving_id' => $saving->id,
                    'total_paid' => $totalPaid,
                    'sisa' => $sisa,
                    'status' => $saving->status,
                ]
            );
        }
    }
    $this->info('Berhasil mengirim pengingat ke ' . $unpaidSavings->count() . ' anggota.');
})->purpose('Kirim pengingat simpanan wajib tanggal 15 & 20 dengan nada gotong royong (dukung cicilan parsial)');

Artisan::command('payroll:process-monthly', function () {
    $this->info('Menjalankan pemotongan otomatis payroll bulanan karyawan...');
    $karyawanUsers = \App\Models\User::where('role', 'karyawan')->get();
    $karyawanService = resolve(\App\Services\KaryawanService::class);
    $totalCount = 0;

    foreach ($karyawanUsers as $karyawan) {
        $result = $karyawanService->processMonthlyPayrollDeduction($karyawan);
        $totalCount++;
    }
    $this->info("Pemotongan payroll selesai untuk {$totalCount} akun karyawan.");
})->purpose('Eksekusi otomatis potong gaji simpanan sukarela dan angsuran pinjaman karyawan');
