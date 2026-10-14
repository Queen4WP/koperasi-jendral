<?php

namespace Database\Seeders;

use App\Models\EmployeePayrollSetting;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnterpriseFiveRolesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@kopin.id'],
            [
                'name' => 'Administrator KOPIN',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. FINANCE
        $finance = User::updateOrCreate(
            ['email' => 'finance@kopin.id'],
            [
                'name' => 'Finance & Treasury KOPIN',
                'password' => Hash::make('password'),
                'role' => 'finance',
                'email_verified_at' => now(),
            ]
        );

        // 3. KASIR
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@kopin.id'],
            [
                'name' => 'Kasir Loket KOPIN',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'email_verified_at' => now(),
            ]
        );

        // 4. ANGGOTA (FAUZI)
        $userFauzi = User::updateOrCreate(
            ['email' => 'fauzi@kopin.id'],
            [
                'name' => 'Fauzi Anggota',
                'password' => Hash::make('password'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );

        $memberFauzi = Member::updateOrCreate(
            ['user_id' => $userFauzi->id],
            [
                'nik' => '3271012345670001',
                'tanggal_lahir' => '1992-07-21',
                'alamat' => 'Jl. Merdeka No. 12, Jakarta',
                'telepon' => '081298765432',
                'pekerjaan' => 'Wirausaha Kuliner',
                'perusahaan' => 'Warung Fauzi Berkah',
                'status' => 'active',
            ]
        );

        // Berikan Simpanan Pokok & Wajib lunas untuk Fauzi agar memiliki kapasitas pinjaman
        Saving::firstOrCreate(
            ['member_id' => $memberFauzi->id, 'jenis_simpanan' => 'pokok'],
            [
                'nominal' => 100000.00,
                'status' => 'paid',
                'jatuh_tempo' => null,
            ]
        );

        Saving::firstOrCreate(
            ['member_id' => $memberFauzi->id, 'jenis_simpanan' => 'wajib', 'periode' => now()->startOfMonth()->toDateString()],
            [
                'nominal' => 20000000.00, // Rp 20.000.000 simpanan wajib lunas (memberikan saldo cukup untuk pinjaman)
                'jatuh_tempo' => now()->endOfMonth()->toDateString(),
                'status' => 'paid',
            ]
        );

        // Buat satu contoh pinjaman modal Fauzi berstatus 'under_review' (siap direview di POV Finance!)
        Loan::firstOrCreate(
            ['nomor_pinjaman' => 'KPN-202610-FAUZI'],
            [
                'member_id' => $memberFauzi->id,
                'jumlah_pinjaman' => 10000000.00, // Rp 10.000.000
                'tenor' => 12,
                'tujuan_pinjaman' => 'Pengembangan Modal Usaha Warung Kuliner Cabang Kedua',
                'keterangan' => 'Kapasitas simpanan mencukupi. Menunggu evaluasi cadangan likuiditas 10% Finance.',
                'persen_jasa' => 1.5,
                'total_jasa' => 1800000.00,
                'total_kewajiban' => 11800000.00,
                'angsuran_per_bulan' => 983333.33,
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_owner' => 'Fauzi Anggota',
                'status' => 'under_review',
                'submitted_at' => now(),
            ]
        );

        // 5. KARYAWAN (INTERNAL STAFF)
        $userKaryawan = User::updateOrCreate(
            ['email' => 'karyawan@kopin.id'],
            [
                'name' => 'Karyawan Internal KOPIN',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'email_verified_at' => now(),
            ]
        );

        $memberKaryawan = Member::updateOrCreate(
            ['user_id' => $userKaryawan->id],
            [
                'nik' => '3271019999990001',
                'tanggal_lahir' => '1995-03-10',
                'alamat' => 'Komplek Koperasi KOPIN No. 1',
                'telepon' => '081211112222',
                'pekerjaan' => 'Staf Operasional Koperasi',
                'perusahaan' => 'KSP KOPIN',
                'status' => 'active',
            ]
        );

        // Setting Payroll Karyawan: Potong gaji Rp 250.000 / bulan untuk Simpanan Sukarela
        EmployeePayrollSetting::updateOrCreate(
            ['user_id' => $userKaryawan->id],
            [
                'nominal_sukarela_bulanan' => 250000.00,
                'is_active' => true,
            ]
        );

        // Berikan saldo awal Simpanan Sukarela hasil potong gaji sebelumnya
        Saving::firstOrCreate(
            ['member_id' => $memberKaryawan->id, 'jenis_simpanan' => 'sukarela', 'periode' => now()->subMonth()->startOfMonth()->toDateString()],
            [
                'nominal' => 1500000.00, // Rp 1.500.000 tabungan sukarela terkumpul
                'status' => 'paid',
                'jatuh_tempo' => now()->subMonth()->endOfMonth()->toDateString(),
            ]
        );
    }
}
