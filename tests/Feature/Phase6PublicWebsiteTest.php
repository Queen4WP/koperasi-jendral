<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use App\Models\Disbursement;
use App\Services\KoperasiService;
use App\Services\LoanService;
use App\Services\DisbursementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Phase6PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected DisbursementService $disbursementService;
    protected KoperasiService $koperasiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loanService = app(LoanService::class);
        $this->disbursementService = app(DisbursementService::class);
        $this->koperasiService = app(KoperasiService::class);
    }

    protected function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    protected function createMember(array $overrides = []): Member
    {
        $user = User::factory()->create(['role' => 'member']);
        return Member::create(array_merge([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Merdeka No. 45',
            'telepon' => '081234567890',
            'pekerjaan' => 'Pengusaha',
            'perusahaan' => 'CV Makmur Jaya',
            'status' => 'active',
        ], $overrides));
    }

    protected function attachVerifiedDocuments(Loan $loan, bool $isModalUsaha = true): void
    {
        $docTypes = ['ktp', 'bukti_penghasilan', 'rekening_bank'];
        if ($isModalUsaha) {
            $docTypes[] = 'bukti_usaha';
        }

        foreach ($docTypes as $type) {
            LoanDocument::create([
                'loan_id' => $loan->id,
                'jenis_dokumen' => $type,
                'file_path' => "loan-documents/{$loan->id}/{$type}.pdf",
                'status_verifikasi' => 'sesuai',
                'catatan' => null,
            ]);
        }
    }

    // ==========================================
    // A. PUBLIC ACCESS
    // ==========================================

    /** @test */
    public function test_1_homepage_accessible_without_login()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('KOPIN');
        $response->assertSee('Solusi Simpan Pinjam Amanah untuk Kebutuhan Anda');
        $response->assertSee('Simpanan Pokok');
        $response->assertSee('Simpanan Wajib');
        $response->assertSee('Simulasi Angsuran');
    }

    /** @test */
    public function test_2_about_page_accessible_without_login()
    {
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
        $response->assertSee('Mengenal Koperasi Simpan Pinjam KOPIN');
        $response->assertSee('Visi KOPIN');
        $response->assertSee('Misi KOPIN');
    }

    /** @test */
    public function test_3_savings_page_accessible_without_login()
    {
        $response = $this->get('/simpanan');
        $response->assertStatus(200);
        $response->assertSee('Produk Simpanan Koperasi KOPIN');
        $response->assertSee('55.000');
        $response->assertSee('100.000');
    }

    /** @test */
    public function test_4_loan_page_accessible_without_login()
    {
        $response = $this->get('/pinjaman');
        $response->assertStatus(200);
        $response->assertSee('Fasilitas Pinjaman Koperasi KOPIN');
        $response->assertSee('5.0%');
        $response->assertSee('7.0%');
        $response->assertSee('8.5%');
        $response->assertSee('10.0%');
        $response->assertSee('Simulasi Perhitungan Angsuran KOPIN');
    }

    /** @test */
    public function test_5_contact_page_accessible_without_login()
    {
        $response = $this->get('/kontak');
        $response->assertStatus(200);
        $response->assertSee('Hubungi Layanan KOPIN');
        $response->assertSee('Kantor Layanan Operasional');
        $response->assertSee('layanan@kopin.id');
    }

    /** @test */
    public function test_6_how_it_works_page_accessible_without_login()
    {
        $response = $this->get('/cara-kerja');
        $response->assertStatus(200);
        $response->assertSee('Cara Kerja Layanan KOPIN');
        $response->assertSee('Pendaftaran Anggota Baru Secara Online');
        $response->assertSee('Pencairan Dana (Disbursement)');
    }

    /** @test */
    public function test_7_about_and_contact_url_aliases_redirect_properly()
    {
        $resAbout = $this->get('/about');
        $resAbout->assertRedirect(route('public.about'));

        $resContact = $this->get('/contact');
        $resContact->assertRedirect(route('public.contact'));
    }

    // ==========================================
    // B. AUTHENTICATION UX
    // ==========================================

    /** @test */
    public function test_8_login_page_renders_with_kopin_brand_and_works()
    {
        $user = User::factory()->create([
            'email' => 'anggota@kopin.id',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $resPage = $this->get('/login');
        $resPage->assertStatus(200);
        $resPage->assertSee('KOPIN');
        $resPage->assertSee('Masuk ke Portal Layanan Koperasi');

        $resLogin = $this->post('/login', [
            'email' => 'anggota@kopin.id',
            'password' => 'password123',
        ]);
        $resLogin->assertRedirect(route('member.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_9_registration_page_renders_and_registers_new_member()
    {
        $resPage = $this->get('/register');
        $resPage->assertStatus(200);
        $resPage->assertSee('Formulir Pendaftaran Anggota Baru');

        $resPost = $this->post('/register', [
            'name' => 'Ahmad Dahlan',
            'email' => 'ahmad@example.com',
            'nik' => '3201123456789012',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tanggal_lahir' => '1990-05-15',
            'telepon' => '081298765432',
            'alamat' => 'Jl. Kebon Jeruk No. 12',
            'pekerjaan' => 'Karyawan Swasta',
            'perusahaan' => 'PT Harapan Bangsa',
        ]);

        $resPost->assertRedirect(route('member.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'ahmad@example.com']);
        $this->assertDatabaseHas('members', ['nik' => '3201123456789012']);
    }

    /** @test */
    public function test_10_authentication_redirects_remain_correct_for_guests()
    {
        $resDashboard = $this->get('/dashboard');
        $resDashboard->assertRedirect(route('login'));

        $resSavings = $this->get('/savings');
        $resSavings->assertRedirect(route('login'));

        $resLoans = $this->get('/loans');
        $resLoans->assertRedirect(route('login'));
    }

    // ==========================================
    // C. AUTHORIZATION & PORTAL ACCESS
    // ==========================================

    /** @test */
    public function test_11_member_cannot_access_admin_dashboard()
    {
        $member = $this->createMember();
        $response = $this->actingAs($member->user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function test_12_admin_can_access_admin_dashboard()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    /** @test */
    public function test_13_member_can_access_own_portal_dashboard()
    {
        $member = $this->createMember();
        $response = $this->actingAs($member->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Anggota');
        $response->assertSee('Status Keanggotaan');
    }

    /** @test */
    public function test_14_member_cannot_access_another_members_data()
    {
        $member1 = $this->createMember();
        $member2 = $this->createMember();

        $loan2 = Loan::create([
            'member_id' => $member2->id,
            'nomor_pinjaman' => 'LOAN-M2-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member 2',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($member1->user)->get(route('member.loans.show', $loan2->id));
        $response->assertStatus(403);
    }

    // ==========================================
    // D. CORE FUNCTIONALITY REGRESSION
    // ==========================================

    /** @test */
    public function test_15_loan_submission_still_works()
    {
        Storage::fake('private');
        $member = $this->createMember();

        // Give eligible savings
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'pokok', 'nominal' => 55000.00, 'status' => 'paid']);
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'wajib', 'nominal' => 100000.00, 'status' => 'paid', 'periode' => now()->startOfMonth()]);

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123456789',
            'bank_account_owner' => 'Budi Santoso',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('gaji.pdf', 100, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('buku.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('member.loans.index'));
        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'status' => 'submitted',
        ]);
    }

    /** @test */
    public function test_16_savings_payment_still_works()
    {
        Storage::fake('private');
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000.00,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($member->user)->post(route('member.savings.pay', $saving->id), [
            'nominal' => 55000,
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('savings_transactions', [
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000.00,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function test_17_loan_payment_still_works()
    {
        Storage::fake('private');
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-PAY-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 6,
            'persen_jasa' => 5.0,
            'total_jasa' => 500000.00,
            'total_kewajiban' => 10500000.00,
            'angsuran_per_bulan' => 1750000.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'status' => 'active',
        ]);

        $installment = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => now()->addMonth(),
            'pokok_angsuran' => 1666667.00,
            'jasa_angsuran' => 83333.00,
            'total_angsuran' => 1750000.00,
            'denda' => 0.00,
            'total_tagihan' => 1750000.00,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($member->user)->post(route('member.loan-payments.store', $installment->id), [
            'nominal' => 1750000,
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => UploadedFile::fake()->create('bayar.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('loan_payments', [
            'installment_id' => $installment->id,
            'nominal_bayar' => 1750000.00,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function test_18_loan_review_still_works()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-REV-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'status' => 'under_review',
        ]);

        $this->attachVerifiedDocuments($loan, false);

        $response = $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function test_19_disbursement_still_works()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-DISB-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2027-03-01',
            'nomor_referensi' => 'TRX-DISB-TEST-001',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('disbursements', [
            'loan_id' => $loan->id,
            'nomor_referensi' => 'TRX-DISB-TEST-001',
        ]);
        $loan->refresh();
        $this->assertEquals('active', $loan->status);
    }

    /** @test */
    public function test_20_installments_still_exist_after_activation()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-INST-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 6,
            'persen_jasa' => 5.0,
            'total_jasa' => 500000.00,
            'total_kewajiban' => 10500000.00,
            'angsuran_per_bulan' => 1750000.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2027-04-01',
            'nomor_referensi' => 'TRX-INST-001',
        ]);

        $this->assertEquals(6, LoanInstallment::where('loan_id', $loan->id)->count());
    }

    // ==========================================
    // E. UI & NAVIGATION
    // ==========================================

    /** @test */
    public function test_21_public_navigation_routes_correctly()
    {
        $response = $this->get('/');
        $response->assertSee(route('home'));
        $response->assertSee(route('public.about'));
        $response->assertSee(route('public.savings'));
        $response->assertSee(route('public.loans'));
        $response->assertSee(route('public.how-it-works'));
        $response->assertSee(route('public.contact'));
    }

    /** @test */
    public function test_22_admin_navigation_routes_correctly()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee(route('admin.dashboard'));
        $response->assertSee(route('admin.members.index'));
        $response->assertSee(route('admin.savings.index'));
        $response->assertSee(route('admin.loans.index'));
        $response->assertSee(route('admin.payments.index'));
    }

    /** @test */
    public function test_23_member_navigation_routes_correctly()
    {
        $member = $this->createMember();
        $response = $this->actingAs($member->user)->get('/dashboard');

        $response->assertSee(route('member.dashboard'));
        $response->assertSee(route('member.profile'));
        $response->assertSee(route('member.savings.index'));
        $response->assertSee(route('member.loans.index'));
    }

    /** @test */
    public function test_24_no_broken_route_references_in_public_views()
    {
        $urls = ['/', '/tentang-kami', '/simpanan', '/pinjaman', '/cara-kerja', '/kontak'];
        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('RouteNotFoundException');
            $response->assertDontSee('Undefined variable');
        }
    }

    // ==========================================
    // F. SECURITY & PRIVACY PROTECTION
    // ==========================================

    /** @test */
    public function test_25_private_document_access_remains_protected()
    {
        Storage::fake('private');
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-SEC-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'status' => 'submitted',
        ]);

        $filePath = 'loans/' . $loan->id . '/ktp_secret.pdf';
        Storage::disk('private')->put($filePath, 'PRIVATE_CONTENT');

        $doc = LoanDocument::create([
            'loan_id' => $loan->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => $filePath,
            'status_verifikasi' => 'pending',
        ]);

        // Guest cannot view document
        $resGuest = $this->get(route('member.loans.documents.show', [$loan->id, $doc->id]));
        $resGuest->assertRedirect(route('login'));

        // Another member cannot view document
        $otherMember = $this->createMember();
        $resOther = $this->actingAs($otherMember->user)->get(route('member.loans.documents.show', [$loan->id, $doc->id]));
        $resOther->assertStatus(403);
    }

    /** @test */
    public function test_26_cross_member_loan_access_remains_protected()
    {
        $member1 = $this->createMember();
        $member2 = $this->createMember();

        $loan1 = Loan::create([
            'member_id' => $member1->id,
            'nomor_pinjaman' => 'LOAN-CROSS-001',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($member2->user)->get(route('member.loans.show', $loan1->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function test_27_cross_loan_document_access_remains_protected()
    {
        $admin = $this->createAdmin();
        $member1 = $this->createMember();
        $member2 = $this->createMember();

        $loan1 = Loan::create([
            'member_id' => $member1->id,
            'nomor_pinjaman' => 'LOAN-CROSS-L1',
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi',
            'status' => 'submitted',
        ]);

        $loan2 = Loan::create([
            'member_id' => $member2->id,
            'nomor_pinjaman' => 'LOAN-CROSS-L2',
            'jumlah_pinjaman' => 15000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 1050000.00,
            'total_kewajiban' => 16050000.00,
            'angsuran_per_bulan' => 1337500.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Siti',
            'status' => 'submitted',
        ]);

        $doc2 = LoanDocument::create([
            'loan_id' => $loan2->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => 'loans/' . $loan2->id . '/ktp.pdf',
            'status_verifikasi' => 'pending',
        ]);

        // Attempt to verify loan2 document under loan1 url must return 404
        $resVerify = $this->actingAs($admin)->post(
            route('admin.loans.documents.verify', [$loan1->id, $doc2->id])
        );
        $resVerify->assertStatus(404);
    }

    /** @test */
    public function test_28_public_pages_do_not_leak_private_data_or_unapproved_formulas()
    {
        $urls = ['/', '/tentang-kami', '/simpanan', '/pinjaman', '/cara-kerja', '/kontak'];
        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('current month - join month');
            $response->assertDontSee('SQLSTATE');
            $response->assertDontSee('SELECT * FROM');
            $response->assertDontSee('disbursements.tanggal_pencairan');
        }
    }

    // ==========================================
    // G. FINANCIAL MATRIX INTEGRITY
    // ==========================================

    /** @test */
    public function test_29_loan_rate_matrix_remains_unchanged()
    {
        $this->assertEquals(5.0, KoperasiService::getRatePercent(6));
        $this->assertEquals(7.0, KoperasiService::getRatePercent(12));
        $this->assertEquals(8.5, KoperasiService::getRatePercent(18));
        $this->assertEquals(10.0, KoperasiService::getRatePercent(24));
    }

    /** @test */
    public function test_30_loan_amount_limits_remain_unchanged()
    {
        $this->assertEquals(5000000, KoperasiService::MIN_LOAN_AMOUNT);
        $this->assertEquals(50000000, KoperasiService::MAX_LOAN_AMOUNT);
    }

    /** @test */
    public function test_31_tenor_rules_remain_unchanged()
    {
        $this->assertEquals([6, 12, 18, 24], KoperasiService::ALLOWED_TENORS);
    }

    /** @test */
    public function test_32_installment_totals_remain_unchanged()
    {
        $calc = $this->koperasiService->calculateLoanInterestAndInstallment(10000000, 12);
        $this->assertEquals(700000.00, $calc['total_jasa']);
        $this->assertEquals(10700000.00, $calc['total_kewajiban']);
        $this->assertEquals(891667.00, $calc['angsuran_per_bulan']);
    }

    /** @test */
    public function test_33_disbursement_amount_remains_authoritative()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-AUTH-001',
            'jumlah_pinjaman' => 15000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 1050000.00,
            'total_kewajiban' => 16050000.00,
            'angsuran_per_bulan' => 1337500.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi',
            'status' => 'approved',
        ]);

        // Attempting to send nominal tampering in payload
        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 999999999, // tampered value
            'tanggal_pencairan' => '2027-04-10',
            'nomor_referensi' => 'TRX-TAMPER-001',
        ]);

        $disbursement = Disbursement::where('loan_id', $loan->id)->first();
        $this->assertNotNull($disbursement);
        $this->assertEquals(15000000.00, (float)$disbursement->nominal);
    }
}
