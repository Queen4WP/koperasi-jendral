<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\MembershipVerification;
use App\Services\KoperasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function createMember(array $overrides = [], array $userOverrides = []): Member
    {
        $user = User::factory()->create(array_merge([
            'role' => 'member',
        ], $userOverrides));

        return Member::create(array_merge([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1992-05-15',
            'alamat' => 'Jl. Anggrek No. 10, Jakarta',
            'telepon' => '081298765432',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'Toko Berkah',
            'status' => 'active',
        ], $overrides));
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    // -------------------------------------------------------------
    // 1. REGISTRATION TESTS
    // -------------------------------------------------------------

    /** @test */
    public function member_can_register_and_account_starts_in_pending_status()
    {
        $payload = [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nik' => '3201234567890001',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jl. Melati No. 5',
            'telepon' => '081234567890',
            'pekerjaan' => 'Karyawan Swasta',
            'perusahaan' => 'PT Maju Bersama',
        ];

        $response = $this->post(route('register.store'), $payload);

        $response->assertRedirect(route('member.dashboard'));

        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'role' => 'member',
        ]);

        $user = User::where('email', 'ahmad@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'nik' => '3201234567890001',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function registration_fails_with_invalid_nik_or_mismatched_password()
    {
        $payload = [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch',
            'nik' => '123', // Invalid size (must be 16)
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jl. Melati No. 5',
            'telepon' => '081234567890',
            'pekerjaan' => 'Karyawan',
            'perusahaan' => 'PT ABC',
        ];

        $response = $this->post(route('register.store'), $payload);
        $response->assertSessionHasErrors(['password', 'nik']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('members', 0);
    }

    // -------------------------------------------------------------
    // 2. MEMBER VERIFICATION TESTS
    // -------------------------------------------------------------

    /** @test */
    public function admin_can_verify_and_approve_pending_member()
    {
        $admin = $this->createAdmin();
        $pendingMember = $this->createMember(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post(route('admin.members.review', $pendingMember->id), [
                'action' => 'approve',
                'catatan' => 'Semua dokumen valid.',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('members', [
            'id' => $pendingMember->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('membership_verifications', [
            'member_id' => $pendingMember->id,
            'admin_id' => $admin->id,
            'status_keputusan' => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_reject_pending_member_with_catatan()
    {
        $admin = $this->createAdmin();
        $pendingMember = $this->createMember(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post(route('admin.members.review', $pendingMember->id), [
                'action' => 'reject',
                'catatan' => 'Data KTP buram dan tidak terbaca.',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('members', [
            'id' => $pendingMember->id,
            'status' => 'rejected',
        ]);
        $this->assertDatabaseHas('membership_verifications', [
            'member_id' => $pendingMember->id,
            'admin_id' => $admin->id,
            'status_keputusan' => 'rejected',
            'catatan' => 'Data KTP buram dan tidak terbaca.',
        ]);
    }

    /** @test */
    public function normal_member_cannot_access_admin_review_endpoints()
    {
        $memberA = $this->createMember();
        $memberB = $this->createMember(['status' => 'pending']);

        // Cannot view admin review form
        $responseView = $this->actingAs($memberA->user)
            ->get(route('admin.members.review', $memberB->id));
        $responseView->assertStatus(403);

        // Cannot submit review decision
        $responsePost = $this->actingAs($memberA->user)
            ->post(route('admin.members.review', $memberB->id), [
                'action' => 'approve',
            ]);
        $responsePost->assertStatus(403);
    }

    // -------------------------------------------------------------
    // 3. MEMBER PROFILE TESTS (OWNERSHIP & IDOR PROTECTION)
    // -------------------------------------------------------------

    /** @test */
    public function member_can_view_own_profile_and_edit_form()
    {
        $member = $this->createMember();

        $response = $this->actingAs($member->user)->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertSee($member->user->name);
        $response->assertSee($member->nik);
        $response->assertSee($member->telepon);
        $response->assertSee($member->pekerjaan);
    }

    /** @test */
    public function member_can_update_own_safe_profile_fields()
    {
        $member = $this->createMember();

        $payload = [
            'name' => 'Nama Baru Anggota',
            'tanggal_lahir' => '1993-08-20',
            'alamat' => 'Alamat Baru No. 99',
            'telepon' => '089912345678',
            'pekerjaan' => 'PNS',
            'perusahaan' => 'Dinas Pendidikan',
            // Attempt malicious modifications
            'nik' => '9999999999999999',
            'role' => 'admin',
            'status' => 'active',
        ];

        $response = $this->actingAs($member->user)
            ->put(route('member.profile.update'), $payload);

        $response->assertRedirect(route('member.profile'));
        $response->assertSessionHas('success');

        // Safe fields updated
        $this->assertDatabaseHas('users', [
            'id' => $member->user_id,
            'name' => 'Nama Baru Anggota',
            'role' => 'member', // Unchanged!
        ]);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'nik' => $member->nik, // NIK remained unchanged!
            'alamat' => 'Alamat Baru No. 99',
            'telepon' => '089912345678',
            'pekerjaan' => 'PNS',
            'perusahaan' => 'Dinas Pendidikan',
        ]);
    }

    // -------------------------------------------------------------
    // 4. LOAN ELIGIBILITY UX & BACKEND ENFORCEMENT
    // -------------------------------------------------------------

    /** @test */
    public function unverified_pending_member_is_blocked_with_exact_verification_message()
    {
        $pendingMember = $this->createMember(['status' => 'pending']);
        $koperasiService = new KoperasiService();

        $eligibility = $koperasiService->checkPinjamanEligibility($pendingMember->id);

        $this->assertFalse($eligibility['eligible']);
        $this->assertStringContainsString('akun belum diverifikasi', $eligibility['message']);
    }

    /** @test */
    public function verified_member_without_simpanan_pokok_is_blocked_with_pokok_message()
    {
        $activeMember = $this->createMember(['status' => 'active']);
        $koperasiService = new KoperasiService();

        $eligibility = $koperasiService->checkPinjamanEligibility($activeMember->id);

        $this->assertFalse($eligibility['eligible']);
        $this->assertStringContainsString('Simpanan Pokok belum dibayar', $eligibility['message']);
    }

    /** @test */
    public function verified_member_with_pokok_paid_but_wajib_current_month_unpaid_is_blocked()
    {
        $activeMember = $this->createMember(['status' => 'active']);

        Saving::create([
            'member_id' => $activeMember->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        $koperasiService = new KoperasiService();
        $eligibility = $koperasiService->checkPinjamanEligibility($activeMember->id);

        $this->assertFalse($eligibility['eligible']);
        $this->assertStringContainsString('Simpanan Wajib bulan berjalan', $eligibility['message']);
    }

    /** @test */
    public function verified_member_with_all_savings_fulfilled_is_eligible()
    {
        $activeMember = $this->createMember(['status' => 'active']);

        Saving::create([
            'member_id' => $activeMember->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        Saving::create([
            'member_id' => $activeMember->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        $koperasiService = new KoperasiService();
        $eligibility = $koperasiService->checkPinjamanEligibility($activeMember->id);

        $this->assertTrue($eligibility['eligible']);
    }

    // -------------------------------------------------------------
    // 5. LOAN SUBMISSION & REQUIRED DOCUMENTS
    // -------------------------------------------------------------

    /** @test */
    public function loan_submission_requires_all_mandatory_documents()
    {
        Storage::fake();
        $member = $this->createMember(['status' => 'active']);

        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'pokok', 'nominal' => 55000, 'status' => 'paid']);
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'wajib', 'periode' => date('Y-m-01'), 'nominal' => 100000, 'status' => 'paid']);

        // Missing documents
        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionHasErrors(['ktp', 'bukti_penghasilan', 'rekening_bank']);
        $this->assertDatabaseCount('loans', 0);
    }

    /** @test */
    public function modal_usaha_purpose_requires_bukti_usaha()
    {
        Storage::fake();
        $member = $this->createMember(['status' => 'active']);

        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'pokok', 'nominal' => 55000, 'status' => 'paid']);
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'wajib', 'periode' => date('Y-m-01'), 'nominal' => 100000, 'status' => 'paid']);

        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Modal Usaha',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
            // Missing bukti_usaha
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionHasErrors(['bukti_usaha']);
        $this->assertDatabaseCount('loans', 0);
    }

    /** @test */
    public function invalid_file_type_and_oversized_file_are_rejected()
    {
        Storage::fake();
        $member = $this->createMember(['status' => 'active']);

        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'pokok', 'nominal' => 55000, 'status' => 'paid']);
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'wajib', 'periode' => date('Y-m-01'), 'nominal' => 100000, 'status' => 'paid']);

        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'ktp' => UploadedFile::fake()->create('malicious.exe', 500, 'application/x-msdownload'), // invalid type
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 6000, 'application/pdf'), // > 5MB
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionHasErrors(['ktp', 'bukti_penghasilan']);
        $this->assertDatabaseCount('loans', 0);
    }

    /** @test */
    public function successful_loan_submission_stores_loan_and_documents_atomically()
    {
        Storage::fake();
        $member = $this->createMember(['status' => 'active']);

        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'pokok', 'nominal' => 55000, 'status' => 'paid']);
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'wajib', 'periode' => date('Y-m-01'), 'nominal' => 100000, 'status' => 'paid']);

        $payload = [
            'jumlah_pinjaman' => 15000000,
            'tenor' => 18,
            'tujuan_pinjaman' => 'Modal Usaha',
            'bank_name' => 'Mandiri',
            'bank_account_number' => '987654321',
            'bank_account_owner' => 'Ahmad',
            'keterangan' => 'Untuk ekspansi usaha',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.png', 500, 'image/png'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
            'bukti_usaha' => UploadedFile::fake()->create('surat_usaha.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertRedirect(route('member.loans.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'jumlah_pinjaman' => 15000000,
            'tenor' => 18,
            'persen_jasa' => 8.50,
            'status' => 'submitted',
        ]);

        $loan = Loan::where('member_id', $member->id)->first();

        // 4 documents must be created
        $this->assertDatabaseCount('loan_documents', 4);
        $this->assertDatabaseHas('loan_documents', ['loan_id' => $loan->id, 'jenis_dokumen' => 'ktp', 'status_verifikasi' => 'pending']);
        $this->assertDatabaseHas('loan_documents', ['loan_id' => $loan->id, 'jenis_dokumen' => 'bukti_penghasilan', 'status_verifikasi' => 'pending']);
        $this->assertDatabaseHas('loan_documents', ['loan_id' => $loan->id, 'jenis_dokumen' => 'rekening_bank', 'status_verifikasi' => 'pending']);
        $this->assertDatabaseHas('loan_documents', ['loan_id' => $loan->id, 'jenis_dokumen' => 'bukti_usaha', 'status_verifikasi' => 'pending']);
    }

    // -------------------------------------------------------------
    // 6. LOAN DETAIL, IDOR & DOCUMENT ACCESS CONTROL
    // -------------------------------------------------------------

    /** @test */
    public function member_cannot_access_another_members_loan_detail()
    {
        $memberA = $this->createMember();
        $memberB = $this->createMember();

        $loanB = Loan::create([
            'member_id' => $memberB->id,
            'nomor_pinjaman' => 'KPN-TEST-001',
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'persen_jasa' => 7.00,
            'total_jasa' => 700000,
            'total_kewajiban' => 10700000,
            'angsuran_per_bulan' => 891667,
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'B',
            'status' => 'submitted',
        ]);

        // Member A requests Member B's loan
        $response = $this->actingAs($memberA->user)->get(route('member.loans.show', $loanB->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function member_cannot_access_another_members_loan_documents()
    {
        Storage::fake();
        $memberA = $this->createMember();
        $memberB = $this->createMember();

        $loanB = Loan::create([
            'member_id' => $memberB->id,
            'nomor_pinjaman' => 'KPN-TEST-002',
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'persen_jasa' => 7.00,
            'total_jasa' => 700000,
            'total_kewajiban' => 10700000,
            'angsuran_per_bulan' => 891667,
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'B',
            'status' => 'submitted',
        ]);

        $filePath = 'loan-documents/' . $loanB->id . '/ktp_test.pdf';
        Storage::put($filePath, 'sample content');

        $docB = LoanDocument::create([
            'loan_id' => $loanB->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => $filePath,
            'status_verifikasi' => 'pending',
        ]);

        // Member A requests Member B's document
        $response = $this->actingAs($memberA->user)
            ->get(route('member.loans.documents.show', ['loan' => $loanB->id, 'document' => $docB->id]));

        $response->assertStatus(403);
    }

    /** @test */
    public function rejected_loan_displays_rejection_reason_to_member()
    {
        $member = $this->createMember();

        $rejectedLoan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-TEST-REJECT',
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'persen_jasa' => 7.00,
            'total_jasa' => 700000,
            'total_kewajiban' => 10700000,
            'angsuran_per_bulan' => 891667,
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'Owner',
            'status' => 'rejected',
            'rejection_reason' => 'Kapasitas penghasilan tidak mencukupi untuk angsuran per bulan.',
        ]);

        $response = $this->actingAs($member->user)->get(route('member.loans.show', $rejectedLoan->id));

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Pinjaman Ditolak');
        $response->assertSee('Kapasitas penghasilan tidak mencukupi untuk angsuran per bulan.');
    }
}
