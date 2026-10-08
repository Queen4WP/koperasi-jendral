<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Services\KoperasiService;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Phase5Gate4LoanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected KoperasiService $koperasiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loanService = app(LoanService::class);
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
            'alamat' => 'Jl. Mawar No. 10',
            'telepon' => '081298765432',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'CV Sumber Rejeki',
            'status' => 'active',
        ], $overrides));
    }

    protected function createSubmittedLoan(Member $member, array $overrides = []): Loan
    {
        return Loan::create(array_merge([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-TEST-' . uniqid(),
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Modal Usaha',
            'keterangan' => 'Pengajuan modal kerja',
            'persen_jasa' => 7.00,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'status' => 'submitted',
            'submitted_at' => now(),
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

    /** @test */
    public function test_1_admin_can_view_loan_list()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member);

        $response = $this->actingAs($admin)->get(route('admin.loans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.loans.index');
        $response->assertSee($loan->nomor_pinjaman);
        $response->assertSee($member->user->name);
        $response->assertSee('Rp ' . number_format($loan->jumlah_pinjaman, 2, ',', '.'));
    }

    /** @test */
    public function test_2_admin_can_search_loans()
    {
        $admin = $this->createAdmin();

        $user1 = User::factory()->create(['name' => 'Alice Pinjaman', 'email' => 'alice@test.com', 'role' => 'member']);
        $member1 = Member::create([
            'user_id' => $user1->id,
            'nik' => '3201123456789001',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jl. Alice',
            'telepon' => '0811111111',
            'pekerjaan' => 'Karyawan',
            'perusahaan' => 'PT Alice',
            'status' => 'active',
        ]);
        $loan1 = $this->createSubmittedLoan($member1, ['nomor_pinjaman' => 'KPN-ALICE-001']);

        $user2 = User::factory()->create(['name' => 'Bob Santoso', 'email' => 'bob@test.com', 'role' => 'member']);
        $member2 = Member::create([
            'user_id' => $user2->id,
            'nik' => '3202999999999002',
            'tanggal_lahir' => '1992-02-02',
            'alamat' => 'Jl. Bob',
            'telepon' => '0822222222',
            'pekerjaan' => 'Pedagang',
            'perusahaan' => 'Toko Bob',
            'status' => 'active',
        ]);
        $loan2 = $this->createSubmittedLoan($member2, ['nomor_pinjaman' => 'KPN-BOB-002']);

        // Search by Member Name
        $resName = $this->actingAs($admin)->get(route('admin.loans.index', ['search' => 'Alice']));
        $resName->assertSee('Alice Pinjaman');
        $resName->assertDontSee('Bob Santoso');

        // Search by NIK
        $resNik = $this->actingAs($admin)->get(route('admin.loans.index', ['search' => '3202999999999002']));
        $resNik->assertSee('Bob Santoso');
        $resNik->assertDontSee('Alice Pinjaman');

        // Search by Email
        $resEmail = $this->actingAs($admin)->get(route('admin.loans.index', ['search' => 'alice@test.com']));
        $resEmail->assertSee('Alice Pinjaman');
        $resEmail->assertDontSee('Bob Santoso');

        // Search by Loan Reference
        $resRef = $this->actingAs($admin)->get(route('admin.loans.index', ['search' => 'KPN-BOB-002']));
        $resRef->assertSee('KPN-BOB-002');
        $resRef->assertDontSee('KPN-ALICE-001');
    }

    /** @test */
    public function test_3_admin_can_filter_loans_by_status()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loanSubmitted = $this->createSubmittedLoan($member, ['nomor_pinjaman' => 'KPN-SUBMITTED-01', 'status' => 'submitted']);
        $loanUnderReview = $this->createSubmittedLoan($member, ['nomor_pinjaman' => 'KPN-REVIEW-02', 'status' => 'under_review']);
        $loanApproved = $this->createSubmittedLoan($member, ['nomor_pinjaman' => 'KPN-APPROVED-03', 'status' => 'approved']);
        $loanRejected = $this->createSubmittedLoan($member, ['nomor_pinjaman' => 'KPN-REJECTED-04', 'status' => 'rejected']);

        // Filter under_review
        $resReview = $this->actingAs($admin)->get(route('admin.loans.index', ['status' => 'under_review']));
        $resReview->assertSee('KPN-REVIEW-02');
        $resReview->assertDontSee('KPN-SUBMITTED-01');
        $resReview->assertDontSee('KPN-APPROVED-03');
        $resReview->assertDontSee('KPN-REJECTED-04');

        // Filter approved
        $resApproved = $this->actingAs($admin)->get(route('admin.loans.index', ['status' => 'approved']));
        $resApproved->assertSee('KPN-APPROVED-03');
        $resApproved->assertDontSee('KPN-SUBMITTED-01');
        $resApproved->assertDontSee('KPN-REVIEW-02');
    }

    /** @test */
    public function test_4_admin_can_view_loan_detail()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['pekerjaan' => 'Arsitek Senior', 'perusahaan' => 'Studio Desain Modern']);
        $loan = $this->createSubmittedLoan($member, ['keterangan' => 'Pengembangan studio cabang baru']);

        $response = $this->actingAs($admin)->get(route('admin.loans.show', $loan->id));

        $response->assertStatus(200);
        $response->assertSee($member->user->name);
        $response->assertSee($member->nik);
        $response->assertSee($member->telepon);
        $response->assertSee('Arsitek Senior');
        $response->assertSee('Studio Desain Modern');
        $response->assertSee($loan->nomor_pinjaman);
        $response->assertSee('Pengembangan studio cabang baru');
    }

    /** @test */
    public function test_5_admin_can_start_review()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member);

        $response = $this->actingAs($admin)->post(route('admin.loans.start_review', $loan->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $loan->refresh();
        $this->assertEquals('under_review', $loan->status);
        $this->assertEquals($admin->id, $loan->reviewed_by);
        $this->assertNotNull($loan->reviewed_at);
    }

    /** @test */
    public function test_6_admin_can_approve_an_under_review_loan()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);
        $this->attachVerifiedDocuments($loan, true);

        $response = $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $loan->refresh();
        $this->assertEquals('approved', $loan->status);
        $this->assertEquals($admin->id, $loan->reviewed_by);
        $this->assertNotNull($loan->approved_at);
    }

    /** @test */
    public function test_7_approved_loan_does_not_become_active()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);
        $this->attachVerifiedDocuments($loan, true);

        $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $loan->refresh();
        $this->assertEquals('approved', $loan->status);
        $this->assertNotEquals('active', $loan->status);
    }

    /** @test */
    public function test_8_approved_loan_does_not_automatically_create_disbursement()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);
        $this->attachVerifiedDocuments($loan, true);

        $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    /** @test */
    public function test_9_approved_loan_does_not_automatically_create_installments()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);
        $this->attachVerifiedDocuments($loan, true);

        $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $this->assertDatabaseMissing('loan_installments', ['loan_id' => $loan->id]);
    }

    /** @test */
    public function test_10_admin_can_reject_an_under_review_loan_with_reason()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);

        $response = $this->actingAs($admin)->post(route('admin.loans.reject', $loan->id), [
            'rejection_reason' => 'Kapasitas angsuran tidak mencukupi berdasarkan slip gaji.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $loan->refresh();
        $this->assertEquals('rejected', $loan->status);
        $this->assertEquals('Kapasitas angsuran tidak mencukupi berdasarkan slip gaji.', $loan->rejection_reason);
        $this->assertEquals($admin->id, $loan->reviewed_by);
        $this->assertNotNull($loan->rejected_at);
    }

    /** @test */
    public function test_11_reject_without_reason_fails_validation()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);

        $response = $this->actingAs($admin)->post(route('admin.loans.reject', $loan->id), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');

        $loan->refresh();
        $this->assertEquals('under_review', $loan->status);
        $this->assertNull($loan->rejection_reason);
    }

    /** @test */
    public function test_12_cannot_approve_a_submitted_loan_without_starting_review()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'submitted']);
        $this->attachVerifiedDocuments($loan, true);

        $response = $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $response->assertSessionHas('error');

        $loan->refresh();
        $this->assertEquals('submitted', $loan->status);
        $this->assertNull($loan->approved_at);
    }

    /** @test */
    public function test_13_cannot_reject_a_submitted_loan_directly()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'submitted']);

        $response = $this->actingAs($admin)->post(route('admin.loans.reject', $loan->id), [
            'rejection_reason' => 'Menolak langsung',
        ]);

        $response->assertSessionHas('error');

        $loan->refresh();
        $this->assertEquals('submitted', $loan->status);
        $this->assertNull($loan->rejection_reason);
    }

    /** @test */
    public function test_14_cannot_approve_an_already_rejected_loan()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, [
            'status' => 'rejected',
            'rejection_reason' => 'Ditolak sebelumnya',
        ]);
        $this->attachVerifiedDocuments($loan, true);

        $response = $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $response->assertSessionHas('error');

        $loan->refresh();
        $this->assertEquals('rejected', $loan->status);
    }

    /** @test */
    public function test_15_cannot_reject_an_already_approved_loan()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'approved', 'approved_at' => now()]);

        $response = $this->actingAs($admin)->post(route('admin.loans.reject', $loan->id), [
            'rejection_reason' => 'Coba tolak setelah approve',
        ]);

        $response->assertSessionHas('error');

        $loan->refresh();
        $this->assertEquals('approved', $loan->status);
    }

    /** @test */
    public function test_16_member_can_see_own_loan_status()
    {
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, ['status' => 'under_review']);

        $response = $this->actingAs($member->user)->get(route('member.loans.show', $loan->id));

        $response->assertStatus(200);
        $response->assertSee('Informasi Pengajuan');
        $response->assertSee($loan->tujuan_pinjaman);
    }

    /** @test */
    public function test_17_member_can_see_own_rejection_reason()
    {
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member, [
            'status' => 'rejected',
            'rejection_reason' => 'Dokumen rekening koran 3 bulan terakhir tidak dilampirkan.',
        ]);

        $response = $this->actingAs($member->user)->get(route('member.loans.show', $loan->id));

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Pinjaman Ditolak');
        $response->assertSee('Dokumen rekening koran 3 bulan terakhir tidak dilampirkan.');
    }

    /** @test */
    public function test_18_member_cannot_see_another_members_loan()
    {
        $member1 = $this->createMember();
        $loan1 = $this->createSubmittedLoan($member1);

        $member2 = $this->createMember();

        $response = $this->actingAs($member2->user)->get(route('member.loans.show', $loan1->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_19_member_cannot_access_admin_loan_management()
    {
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member);

        $resIndex = $this->actingAs($member->user)->get(route('admin.loans.index'));
        $resIndex->assertStatus(403);

        $resShow = $this->actingAs($member->user)->get(route('admin.loans.show', $loan->id));
        $resShow->assertStatus(403);

        $resStart = $this->actingAs($member->user)->post(route('admin.loans.start_review', $loan->id));
        $resStart->assertStatus(403);
    }

    /** @test */
    public function test_20_guest_cannot_access_private_loan_management()
    {
        $member = $this->createMember();
        $loan = $this->createSubmittedLoan($member);

        $resAdmin = $this->get(route('admin.loans.index'));
        $resAdmin->assertRedirect(route('login'));

        $resMember = $this->get(route('member.loans.show', $loan->id));
        $resMember->assertRedirect(route('login'));
    }

    /** @test */
    public function test_21_cross_loan_document_access_returns_404()
    {
        $admin = $this->createAdmin();

        $member1 = $this->createMember();
        $loan1 = $this->createSubmittedLoan($member1);
        $doc1 = LoanDocument::create([
            'loan_id' => $loan1->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => "loan-documents/{$loan1->id}/ktp.pdf",
            'status_verifikasi' => 'pending',
        ]);

        $member2 = $this->createMember();
        $loan2 = $this->createSubmittedLoan($member2);
        $doc2 = LoanDocument::create([
            'loan_id' => $loan2->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => "loan-documents/{$loan2->id}/ktp.pdf",
            'status_verifikasi' => 'pending',
        ]);

        // Access doc2 under loan1 scope
        $resVerify = $this->actingAs($admin)->post(
            route('admin.loans.documents.verify', ['loan' => $loan1->id, 'document' => $doc2->id])
        );
        $resVerify->assertStatus(404);

        $resReject = $this->actingAs($admin)->post(
            route('admin.loans.documents.reject', ['loan' => $loan1->id, 'document' => $doc2->id]),
            ['catatan' => 'Cross loan reject']
        );
        $resReject->assertStatus(404);

        $resShow = $this->actingAs($admin)->get(
            route('member.loans.documents.show', ['loan' => $loan1->id, 'document' => $doc2->id])
        );
        $resShow->assertStatus(404);
    }

    /** @test */
    public function test_22_required_documents_are_enforced_according_to_loan_purpose()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        Storage::fake();

        // Submission with missing ktp and rekening_bank
        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionHasErrors(['ktp', 'rekening_bank']);
    }

    /** @test */
    public function test_23_modal_usaha_requires_business_evidence()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        Storage::fake();

        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Modal Usaha',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
            // 'bukti_usaha' omitted
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionHasErrors('bukti_usaha');
    }

    /** @test */
    public function test_24_non_business_purpose_does_not_incorrectly_require_business_evidence()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        Storage::fake();

        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Renovasi Rumah',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
            // No bukti_usaha
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionDoesntHaveErrors('bukti_usaha');
        $response->assertRedirect(route('member.loans.index'));
        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'tujuan_pinjaman' => 'Renovasi Rumah',
        ]);
    }

    /** @test */
    public function test_25_loan_amount_below_minimum_is_rejected()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 4999999, // below 5.000.000
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
        ]);

        $response->assertSessionHasErrors('jumlah_pinjaman');
    }

    /** @test */
    public function test_26_loan_amount_above_maximum_is_rejected()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 50000001, // above 50.000.000
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
        ]);

        $response->assertSessionHasErrors('jumlah_pinjaman');
    }

    /** @test */
    public function test_27_invalid_tenor_is_rejected()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 9, // not in 6, 12, 18, 24
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
        ]);

        $response->assertSessionHasErrors('tenor');
    }

    /** @test */
    public function test_28_correct_tenor_produces_correct_locked_service_rate()
    {
        $rates = [
            6 => 5.0,
            12 => 7.0,
            18 => 8.5,
            24 => 10.0,
        ];

        foreach ($rates as $tenor => $expectedRate) {
            $this->assertEquals($expectedRate, KoperasiService::getRatePercent($tenor));
            
            $calc = $this->koperasiService->calculateLoanInterestAndInstallment(10000000, $tenor);
            $this->assertEquals($expectedRate, $calc['rate_percent']);
            $this->assertEquals(round(10000000 * ($expectedRate / 100), 2), $calc['total_jasa']);
        }
    }

    /** @test */
    public function test_29_client_cannot_override_service_rate_or_calculated_financial_values()
    {
        $member = $this->createMember();
        $this->makeMemberEligible($member);

        Storage::fake();

        // Client passes forged rate 1.5% and manipulated totals
        $payload = [
            'jumlah_pinjaman' => 20000000,
            'tenor' => 18, // Official rate must be 8.5%
            'persen_jasa' => 1.5,
            'total_jasa' => 300000,
            'total_kewajiban' => 20300000,
            'angsuran_per_bulan' => 1127777,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertRedirect(route('member.loans.index'));

        // Server authoritative verification:
        // 20M * 8.5% = 1.700.000 jasa, 21.700.000 kewajiban
        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'jumlah_pinjaman' => 20000000.00,
            'tenor' => 18,
            'persen_jasa' => 8.50,
            'total_jasa' => 1700000.00,
            'total_kewajiban' => 21700000.00,
        ]);
    }

    /** @test */
    public function test_30_ineligible_member_cannot_submit_a_loan()
    {
        $member = $this->createMember();
        // Member has not fulfilled savings obligations

        Storage::fake();
        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($member->user)->post(route('member.loans.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('loans', ['member_id' => $member->id]);
    }

    protected function makeMemberEligible(Member $member): void
    {
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);
    }
}
