<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use App\Models\Disbursement;
use App\Services\LoanService;
use App\Services\DisbursementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;

class Phase5Gate1FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected DisbursementService $disbursementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loanService = app(LoanService::class);
        $this->disbursementService = app(DisbursementService::class);
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
            'alamat' => 'Jl. Merdeka No. 1',
            'telepon' => '08123456789',
            'pekerjaan' => 'Karyawan',
            'perusahaan' => 'PT Maju Bersama',
            'status' => 'active',
        ], $overrides));
    }

    protected function createApprovedLoan(Member $member, int $tenor = 6, float $amount = 10000000): Loan
    {
        return Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-TEST-' . uniqid(),
            'jumlah_pinjaman' => $amount,
            'tenor' => $tenor,
            'tujuan_pinjaman' => 'Modal Usaha',
            'keterangan' => 'Pengajuan test',
            'persen_jasa' => 5.00,
            'total_jasa' => 500000.00,
            'total_kewajiban' => 10500000.00,
            'angsuran_per_bulan' => 1750000.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function disbursement_date_becomes_basis_of_installment_due_dates()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 12000000);

        // Disburse on October 15, 2026
        $disbursementDate = '2026-10-15';
        $this->disbursementService->disburseLoan(
            $loan->id,
            $this->createAdmin()->id,
            12000000,
            $disbursementDate,
            null,
            'TRF-20261015-001'
        );

        $this->loanService->activateLoan($loan->id, $disbursementDate);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $this->assertCount(6, $installments);
        $this->assertEquals('2026-11-15', $installments[0]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2026-12-15', $installments[1]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-01-15', $installments[2]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-02-15', $installments[3]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-03-15', $installments[4]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-04-15', $installments[5]->jatuh_tempo->format('Y-m-d'));
    }

    /** @test */
    public function disbursement_on_january_31_does_not_overflow_months()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 12000000);

        // Disburse on January 31, 2027 (non-leap year)
        $disbursementDate = '2027-01-31';
        $this->disbursementService->disburseLoan(
            $loan->id,
            $this->createAdmin()->id,
            12000000,
            $disbursementDate,
            null,
            'TRF-20270131-001'
        );

        $this->loanService->activateLoan($loan->id, $disbursementDate);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $this->assertCount(6, $installments);
        // Month 1: 28 Feb 2027 (not March!)
        $this->assertEquals('2027-02-28', $installments[0]->jatuh_tempo->format('Y-m-d'));
        // Month 2: 31 Mar 2027
        $this->assertEquals('2027-03-31', $installments[1]->jatuh_tempo->format('Y-m-d'));
        // Month 3: 30 Apr 2027
        $this->assertEquals('2027-04-30', $installments[2]->jatuh_tempo->format('Y-m-d'));
        // Month 4: 31 May 2027
        $this->assertEquals('2027-05-31', $installments[3]->jatuh_tempo->format('Y-m-d'));
        // Month 5: 30 Jun 2027
        $this->assertEquals('2027-06-30', $installments[4]->jatuh_tempo->format('Y-m-d'));
        // Month 6: 31 Jul 2027
        $this->assertEquals('2027-07-31', $installments[5]->jatuh_tempo->format('Y-m-d'));
    }

    /** @test */
    public function disbursement_on_january_31_handles_leap_year_february_29()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 12000000);

        // Disburse on January 31, 2028 (leap year)
        $disbursementDate = '2028-01-31';
        $this->disbursementService->disburseLoan(
            $loan->id,
            $this->createAdmin()->id,
            12000000,
            $disbursementDate,
            null,
            'TRF-20280131-001'
        );

        $this->loanService->activateLoan($loan->id, $disbursementDate);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $this->assertCount(6, $installments);
        // Month 1 in leap year must be Feb 29!
        $this->assertEquals('2028-02-29', $installments[0]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2028-03-31', $installments[1]->jatuh_tempo->format('Y-m-d'));
    }

    /** @test */
    public function generating_installments_without_valid_disbursement_date_throws_exception()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000);
        $loan->status = 'active';
        $loan->save();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tanggal pencairan tidak valid untuk pembuatan angsuran.');

        // Calling generateInstallments directly with no date and no Disbursement record
        $this->loanService->generateInstallments($loan->id, null);

        // Assert zero installments created
        $this->assertDatabaseMissing('loan_installments', ['loan_id' => $loan->id]);
    }

    /** @test */
    public function scoped_route_binding_blocks_access_to_documents_belonging_to_another_loan()
    {
        $admin = $this->createAdmin();

        $member1 = $this->createMember();
        $loan1 = $this->createApprovedLoan($member1);
        $doc1 = LoanDocument::create([
            'loan_id' => $loan1->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => 'loan-documents/' . $loan1->id . '/ktp.pdf',
            'status_verifikasi' => 'pending',
        ]);

        $member2 = $this->createMember();
        $loan2 = $this->createApprovedLoan($member2);
        $doc2 = LoanDocument::create([
            'loan_id' => $loan2->id,
            'jenis_dokumen' => 'ktp',
            'file_path' => 'loan-documents/' . $loan2->id . '/ktp.pdf',
            'status_verifikasi' => 'pending',
        ]);

        // Attempting to verify doc2 using loan1's route must return 404 Not Found!
        $response = $this->actingAs($admin)->post(
            route('admin.loans.documents.verify', ['loan' => $loan1->id, 'document' => $doc2->id])
        );

        $response->assertStatus(404);

        // Attempting to reject doc1 using loan2's route must return 404 Not Found!
        $responseReject = $this->actingAs($admin)->post(
            route('admin.loans.documents.reject', ['loan' => $loan2->id, 'document' => $doc1->id]),
            ['catatan' => 'Catatan penolakan test']
        );

        $responseReject->assertStatus(404);
    }

    /** @test */
    public function disbursement_requires_reference_number_and_persists_in_database()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 12, 10000000);

        // Validation failure when nomor_referensi is missing
        $responseFail = $this->actingAs($admin)->post(
            route('admin.loans.disburse.store', $loan->id),
            [
                'nominal' => 10000000,
                'tanggal_pencairan' => '2026-10-15',
                // 'nomor_referensi' missing
            ]
        );

        $responseFail->assertSessionHasErrors('nomor_referensi');
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);

        // Successful disbursement with nomor_referensi
        $responseSuccess = $this->actingAs($admin)->post(
            route('admin.loans.disburse.store', $loan->id),
            [
                'nominal' => 10000000,
                'tanggal_pencairan' => '2026-10-15',
                'nomor_referensi' => 'TRF-BCA-20261015-099',
                'bukti_transfer' => 'Ref-Optional-String',
            ]
        );

        $responseSuccess->assertSessionHasNoErrors();
        $responseSuccess->assertSessionHas('success');

        $this->assertDatabaseHas('disbursements', [
            'loan_id' => $loan->id,
            'admin_id' => $admin->id,
            'nominal' => 10000000.00,
            'nomor_referensi' => 'TRF-BCA-20261015-099',
            'bukti_transfer' => 'Ref-Optional-String',
        ]);

        $loan->refresh();
        $this->assertEquals('active', $loan->status);
        $this->assertCount(12, $loan->installments);
        $this->assertEquals('2026-11-15', $loan->installments[0]->jatuh_tempo->format('Y-m-d'));
    }
}
