<?php

namespace Database\Seeders;

use App\Constants\NotificationType;
use App\Models\ActivityLog;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\MembershipVerification;
use App\Models\Notification;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------------------------------------------------------------
        // 0. Ensure Demo Dummy Assets in Storage
        // -------------------------------------------------------------
        $dummyReceiptContent = "%PDF-1.4\n1 0 obj\n<< /Title (Bukti Pembayaran KOPIN Demo) >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
        $dummyReceiptPath = 'payment-proofs/savings/dummy_proof.pdf';
        $dummyLoanDocPath = 'loan-documents/demo/sample_doc.pdf';

        if (!Storage::exists($dummyReceiptPath)) {
            Storage::put($dummyReceiptPath, $dummyReceiptContent);
        }
        if (!Storage::exists($dummyLoanDocPath)) {
            Storage::put($dummyLoanDocPath, $dummyReceiptContent);
        }

        // -------------------------------------------------------------
        // 1. Admin Account
        // -------------------------------------------------------------
        $admin = User::firstOrCreate(
            ['email' => 'admin@kopin.id'],
            [
                'name' => 'Administrator KOPIN',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // -------------------------------------------------------------
        // 2. Active Member 1: Budi Santoso (Has Active Loan & Payments)
        // -------------------------------------------------------------
        $userBudi = User::firstOrCreate(
            ['email' => 'budi@kopin.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );

        $memberBudi = Member::firstOrCreate(
            ['user_id' => $userBudi->id],
            [
                'nik' => '3578012345670001',
                'tanggal_lahir' => '1990-05-15',
                'alamat' => 'Jl. Pemuda No. 45, Surabaya',
                'telepon' => '081234567891',
                'pekerjaan' => 'Wiraswasta',
                'perusahaan' => 'Toko Sumber Rejeki',
                'status' => 'active',
            ]
        );

        MembershipVerification::firstOrCreate(
            ['member_id' => $memberBudi->id, 'status_keputusan' => 'approved'],
            [
                'admin_id' => $admin->id,
                'catatan' => 'Berkas identitas dan profil telah diperiksa dan disetujui.',
            ]
        );

        // Budi: Simpanan Pokok (Paid Rp 55.000)
        $pokokBudi = Saving::firstOrCreate(
            ['member_id' => $memberBudi->id, 'jenis_simpanan' => 'pokok', 'periode' => null],
            [
                'nominal' => 55000.00,
                'status' => 'paid',
                'jatuh_tempo' => null,
            ]
        );

        SavingsTransaction::firstOrCreate(
            ['payment_reference' => 'SAV-OFC-POKOK01'],
            [
                'saving_id' => $pokokBudi->id,
                'admin_id' => $admin->id,
                'nominal_bayar' => 55000.00,
                'metode_pembayaran' => 'kantor',
                'tanggal_bayar' => now()->subMonths(3),
                'external_reference' => null,
                'status' => 'success',
                'catatan' => 'Setoran tunai simpanan pokok di kantor koperasi.',
                'bukti_pembayaran' => null,
            ]
        );

        // Budi: Simpanan Wajib (Current Month Paid Rp 100.000)
        $currentMonthPeriod = date('Y-m-01');
        $wajibBudi = Saving::firstOrCreate(
            ['member_id' => $memberBudi->id, 'jenis_simpanan' => 'wajib', 'periode' => $currentMonthPeriod],
            [
                'nominal' => 100000.00,
                'status' => 'paid',
                'jatuh_tempo' => date('Y-m-10'),
            ]
        );

        SavingsTransaction::firstOrCreate(
            ['payment_reference' => 'SAV-OFC-WAJIB01'],
            [
                'saving_id' => $wajibBudi->id,
                'admin_id' => $admin->id,
                'nominal_bayar' => 100000.00,
                'metode_pembayaran' => 'kantor',
                'tanggal_bayar' => now()->subDays(5),
                'external_reference' => null,
                'status' => 'success',
                'catatan' => 'Setoran tunai simpanan wajib bulan berjalan.',
                'bukti_pembayaran' => null,
            ]
        );

        // Budi: Active Loan (Rp 10.000.000, 12 Bulan, Jasa 7%)
        $activeLoanBudi = Loan::firstOrCreate(
            ['nomor_pinjaman' => 'KPN-202610-00001'],
            [
                'member_id' => $memberBudi->id,
                'jumlah_pinjaman' => 10000000.00,
                'tenor' => 12,
                'tujuan_pinjaman' => 'Modal Usaha',
                'keterangan' => 'Pengembangan etalase toko dan penambahan stok barang dagangan',
                'persen_jasa' => 7.00,
                'total_jasa' => 700000.00,
                'total_kewajiban' => 10700000.00,
                'angsuran_per_bulan' => 891667.00,
                'bank_name' => 'BCA',
                'bank_account_number' => '8220192831',
                'bank_account_owner' => 'Budi Santoso',
                'status' => 'active',
                'reviewed_by' => $admin->id,
                'submitted_at' => now()->subDays(60),
                'reviewed_at' => now()->subDays(59),
                'approved_at' => now()->subDays(58),
                'disbursed_at' => now()->subDays(55),
            ]
        );

        foreach (['ktp', 'bukti_penghasilan', 'rekening_bank', 'bukti_usaha'] as $docType) {
            LoanDocument::firstOrCreate(
                ['loan_id' => $activeLoanBudi->id, 'jenis_dokumen' => $docType],
                [
                    'file_path' => $dummyLoanDocPath,
                    'status_verifikasi' => 'sesuai',
                    'catatan' => 'Dokumen sesuai dan terverifikasi.',
                ]
            );
        }

        Disbursement::firstOrCreate(
            ['loan_id' => $activeLoanBudi->id],
            [
                'admin_id' => $admin->id,
                'nominal' => 10000000.00,
                'nomor_referensi' => 'TRX-DISB-BCA-001',
                'tanggal_pencairan' => now()->subDays(55),
                'bukti_transfer' => $dummyLoanDocPath,
            ]
        );

        // Generate Installments for Budi's Active Loan
        $baseDate = Carbon::parse($activeLoanBudi->disbursed_at);
        for ($i = 1; $i <= 12; $i++) {
            $jatuhTempo = $baseDate->copy()->addMonthsNoOverflow($i)->format('Y-m-d');
            $pokok = ($i === 12) ? 833337.00 : 833333.00;
            $jasa = ($i === 12) ? 58330.00 : 58334.00;
            $total = 891667.00;
            $instStatus = ($i === 1) ? 'paid' : 'unpaid';

            $installment = LoanInstallment::firstOrCreate(
                ['loan_id' => $activeLoanBudi->id, 'angsuran_ke' => $i],
                [
                    'jatuh_tempo' => $jatuhTempo,
                    'pokok_angsuran' => $pokok,
                    'jasa_angsuran' => $jasa,
                    'total_angsuran' => $total,
                    'denda' => 0.00,
                    'total_tagihan' => $total,
                    'status' => $instStatus,
                ]
            );

            if ($i === 1) {
                LoanPayment::firstOrCreate(
                    ['payment_reference' => 'PAY-OFC-INST001'],
                    [
                        'installment_id' => $installment->id,
                        'admin_id' => $admin->id,
                        'nominal_bayar' => 891667.00,
                        'denda_dibayar' => 0.00,
                        'metode_pembayaran' => 'kantor',
                        'tanggal_bayar' => now()->subDays(25),
                        'external_reference' => null,
                        'status' => 'success',
                        'catatan' => 'Pembayaran angsuran pertama via kantor koperasi.',
                        'bukti_pembayaran' => null,
                    ]
                );
            }
        }

        // -------------------------------------------------------------
        // 3. Pending Member: Siti Aminah (For Admin Verification Demo)
        // -------------------------------------------------------------
        $userSiti = User::firstOrCreate(
            ['email' => 'siti@kopin.id'],
            [
                'name' => 'Siti Aminah',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );

        Member::firstOrCreate(
            ['user_id' => $userSiti->id],
            [
                'nik' => '3578019876540002',
                'tanggal_lahir' => '1994-08-20',
                'alamat' => 'Jl. Rungkut Asri Timur No. 12, Surabaya',
                'telepon' => '081398765432',
                'pekerjaan' => 'Karyawan Swasta',
                'perusahaan' => 'PT Graha Surya',
                'status' => 'pending',
            ]
        );

        // -------------------------------------------------------------
        // 4. Active Member 2: Ahmad Fauzi (With Pending Loan & Payment)
        // -------------------------------------------------------------
        $userFauzi = User::firstOrCreate(
            ['email' => 'fauzi@kopin.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );

        $memberFauzi = Member::firstOrCreate(
            ['user_id' => $userFauzi->id],
            [
                'nik' => '3578018899000003',
                'tanggal_lahir' => '1988-11-10',
                'alamat' => 'Jl. Manyar Kertoarjo No. 28, Surabaya',
                'telepon' => '081298765433',
                'pekerjaan' => 'PNS',
                'perusahaan' => 'Dinas Pendidikan',
                'status' => 'active',
            ]
        );

        MembershipVerification::firstOrCreate(
            ['member_id' => $memberFauzi->id, 'status_keputusan' => 'approved'],
            [
                'admin_id' => $admin->id,
                'catatan' => 'Verifikasi keanggotaan berhasil disetujui.',
            ]
        );

        // Fauzi: Simpanan Pokok Paid
        $pokokFauzi = Saving::firstOrCreate(
            ['member_id' => $memberFauzi->id, 'jenis_simpanan' => 'pokok', 'periode' => null],
            [
                'nominal' => 55000.00,
                'status' => 'paid',
                'jatuh_tempo' => null,
            ]
        );

        SavingsTransaction::firstOrCreate(
            ['payment_reference' => 'SAV-OFC-POKOK02'],
            [
                'saving_id' => $pokokFauzi->id,
                'admin_id' => $admin->id,
                'nominal_bayar' => 55000.00,
                'metode_pembayaran' => 'kantor',
                'tanggal_bayar' => now()->subMonths(1),
                'external_reference' => null,
                'status' => 'success',
                'catatan' => 'Setor tunai simpanan pokok.',
                'bukti_pembayaran' => null,
            ]
        );

        // Fauzi: Simpanan Wajib with Pending Online Payment (For Payment Verification Demo)
        $wajibFauzi = Saving::firstOrCreate(
            ['member_id' => $memberFauzi->id, 'jenis_simpanan' => 'wajib', 'periode' => $currentMonthPeriod],
            [
                'nominal' => 100000.00,
                'status' => 'unpaid',
                'jatuh_tempo' => date('Y-m-10'),
            ]
        );

        SavingsTransaction::firstOrCreate(
            ['payment_reference' => 'SAV-ONL-DEMO99'],
            [
                'saving_id' => $wajibFauzi->id,
                'admin_id' => null,
                'nominal_bayar' => 100000.00,
                'metode_pembayaran' => 'online',
                'tanggal_bayar' => now(),
                'external_reference' => 'MANDIRI-TRF-8899',
                'status' => 'pending',
                'catatan' => 'Transfer via Mandiri m-banking a.n Ahmad Fauzi',
                'bukti_pembayaran' => $dummyReceiptPath,
            ]
        );

        // Fauzi: Pending Loan Application (For Admin Loan Review Demo)
        $pendingLoanFauzi = Loan::firstOrCreate(
            ['nomor_pinjaman' => 'KPN-202610-00002'],
            [
                'member_id' => $memberFauzi->id,
                'jumlah_pinjaman' => 15000000.00,
                'tenor' => 18,
                'tujuan_pinjaman' => 'Renovasi Rumah',
                'keterangan' => 'Perbaikan atap bocor dan dinding samping rumah tinggal',
                'persen_jasa' => 8.50,
                'total_jasa' => 1275000.00,
                'total_kewajiban' => 16275000.00,
                'angsuran_per_bulan' => 904167.00,
                'bank_name' => 'Mandiri',
                'bank_account_number' => '1420019283741',
                'bank_account_owner' => 'Ahmad Fauzi',
                'status' => 'submitted',
                'reviewed_by' => null,
                'submitted_at' => now()->subHours(6),
                'reviewed_at' => null,
                'approved_at' => null,
                'disbursed_at' => null,
            ]
        );

        foreach (['ktp', 'bukti_penghasilan', 'rekening_bank'] as $docType) {
            LoanDocument::firstOrCreate(
                ['loan_id' => $pendingLoanFauzi->id, 'jenis_dokumen' => $docType],
                [
                    'file_path' => $dummyLoanDocPath,
                    'status_verifikasi' => 'pending',
                    'catatan' => null,
                ]
            );
        }

        // -------------------------------------------------------------
        // 5. Seed Demonstration Notifications & Audit Logs
        // -------------------------------------------------------------
        Notification::firstOrCreate(
            ['user_id' => $userBudi->id, 'type' => NotificationType::MEMBER_VERIFICATION_APPROVED],
            [
                'title' => 'Verifikasi Keanggotaan Disetujui',
                'message' => 'Selamat, pengajuan verifikasi keanggotaan Anda telah disetujui.',
                'data' => ['member_id' => $memberBudi->id],
                'is_read' => true,
                'read_at' => now()->subDays(60),
            ]
        );

        Notification::firstOrCreate(
            ['user_id' => $userBudi->id, 'type' => NotificationType::LOAN_DISBURSED],
            [
                'title' => 'Pinjaman Telah Dicairkan',
                'message' => "Pencairan pinjaman {$activeLoanBudi->nomor_pinjaman} sebesar Rp 10.000.000 berhasil diproses.",
                'data' => [
                    'loan_id' => $activeLoanBudi->id,
                    'nomor_pinjaman' => $activeLoanBudi->nomor_pinjaman,
                    'nominal' => 10000000.00,
                    'tanggal_pencairan' => now()->subDays(55)->toDateString(),
                ],
                'is_read' => false,
                'read_at' => null,
            ]
        );

        ActivityLog::firstOrCreate(
            ['activity' => 'member.verified', 'keterangan' => "Verifikasi keanggotaan disetujui untuk {$userBudi->name}."],
            [
                'user_id' => $admin->id,
                'subject_type' => Member::class,
                'subject_id' => $memberBudi->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DemoSeeder',
                'data' => [
                    'member_id' => $memberBudi->id,
                    'status' => 'active',
                    'catatan' => 'Berkas identitas dan profil telah diperiksa dan disetujui.',
                ],
            ]
        );

        ActivityLog::firstOrCreate(
            ['activity' => 'loan.disbursed', 'keterangan' => "Pencairan pinjaman {$activeLoanBudi->nomor_pinjaman} sebesar Rp 10.000.000 berhasil diproses."],
            [
                'user_id' => $admin->id,
                'subject_type' => Loan::class,
                'subject_id' => $activeLoanBudi->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DemoSeeder',
                'data' => [
                    'loan_id' => $activeLoanBudi->id,
                    'nomor_pinjaman' => $activeLoanBudi->nomor_pinjaman,
                    'nominal' => 10000000.00,
                    'nomor_referensi' => 'TRX-DISB-BCA-001',
                    'tanggal_pencairan' => now()->subDays(55)->toDateString(),
                ],
            ]
        );
    }
}
