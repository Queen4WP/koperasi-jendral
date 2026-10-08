<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
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
use RuntimeException;

class Phase5Gate5DisbursementTest extends TestCase
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

    protected function createApprovedLoan(Member $member, int $tenor = 12, float $amount = 10000000.00): Loan
    {
        $calc = $this->koperasiService->calculateLoanInterestAndInstallment($amount, $tenor);

        return Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-APP-' . uniqid(),
            'jumlah_pinjaman' => $amount,
            'tenor' => $tenor,
            'tujuan_pinjaman' => 'Modal Usaha',
            'keterangan' => 'Pengembangan usaha',
            'persen_jasa' => $calc['rate_percent'],
            'total_jasa' => $calc['total_jasa'],
            'total_kewajiban' => $calc['total_kewajiban'],
            'angsuran_per_bulan' => $calc['angsuran_per_bulan'],
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => $member->user->name,
            'status' => 'approved',
            'submitted_at' => now()->subDays(2),
            'reviewed_at' => now()->subDay(),
            'approved_at' => now()->subDay(),
        ]);
    }

    protected function createLoanWithStatus(Member $member, string $status): Loan
    {
        $loan = $this->createApprovedLoan($member);
        $loan->status = $status;
        $loan->save();
        return $loan;
    }

    // ==========================================
    // A. AUTHORIZATION (Tests 1 - 4)
    // ==========================================

    public function test_1_admin_can_access_disbursement_action()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $response = $this->actingAs($admin)->get(route('admin.loans.disburse', $loan->id));

        $response->assertStatus(200);
        $response->assertSee('Proses Pencairan Pinjaman');
        $response->assertSee($loan->member->user->name);
        $response->assertSee(number_format($loan->jumlah_pinjaman, 2, ',', '.'));
    }

    public function test_2_member_cannot_disburse()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $response = $this->actingAs($member->user)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-TEST-001',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    public function test_3_guest_cannot_disburse()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $response = $this->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-TEST-001',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    public function test_4_member_cannot_manipulate_another_loans_disbursement_endpoint()
    {
        $member1 = $this->createMember();
        $loan1 = $this->createApprovedLoan($member1);
        $member2 = $this->createMember();

        $responseGet = $this->actingAs($member2->user)->get(route('admin.loans.disburse', $loan1->id));
        $responseGet->assertStatus(403);

        $responsePost = $this->actingAs($member2->user)->post(route('admin.loans.disburse.store', $loan1->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-TEST-001',
        ]);
        $responsePost->assertStatus(403);
    }

    // ==========================================
    // B. STATE GUARDS (Tests 5 - 10)
    // ==========================================

    public function test_5_approved_loan_can_be_disbursed()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-OK-001',
            'bukti_transfer' => 'Ref-Disb-01',
        ]);

        $response->assertSessionHas('success');
        $loan->refresh();
        $this->assertEquals('active', $loan->status);
        $this->assertDatabaseHas('disbursements', ['loan_id' => $loan->id]);
    }

    public function test_6_submitted_loan_cannot_be_disbursed()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithStatus($member, 'submitted');

        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-FAIL-001',
        ]);

        $response->assertSessionHas('error');
        $loan->refresh();
        $this->assertEquals('submitted', $loan->status);
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    public function test_7_under_review_loan_cannot_be_disbursed()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithStatus($member, 'under_review');

        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-FAIL-002',
        ]);

        $response->assertSessionHas('error');
        $loan->refresh();
        $this->assertEquals('under_review', $loan->status);
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    public function test_8_rejected_loan_cannot_be_disbursed()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithStatus($member, 'rejected');

        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-FAIL-003',
        ]);

        $response->assertSessionHas('error');
        $loan->refresh();
        $this->assertEquals('rejected', $loan->status);
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    public function test_9_active_loan_cannot_be_disbursed_again()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        // First disbursement: successful
        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-FIRST-001',
        ]);

        $loan->refresh();
        $this->assertEquals('active', $loan->status);

        // Second disbursement attempt
        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-16',
            'nomor_referensi' => 'TRF-DUPLICATE-002',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, Disbursement::where('loan_id', $loan->id)->count());
    }

    public function test_10_paid_off_loan_cannot_be_disbursed()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithStatus($member, 'paid_off');

        $response = $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-FAIL-004',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('disbursements', ['loan_id' => $loan->id]);
    }

    // ==========================================
    // C. DISBURSEMENT (Tests 11 - 16)
    // ==========================================

    public function test_11_successful_disbursement_creates_exactly_one_disbursement()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-ONCE-001',
        ]);

        $this->assertEquals(1, Disbursement::where('loan_id', $loan->id)->count());
    }

    public function test_12_loan_becomes_disbursed_then_active()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        // Direct service step verification: approved -> disbursed
        $this->disbursementService->disburseLoan($loan->id, $admin->id, 10000000, '2026-10-15', null, 'TRF-STEP-001');
        $loan->refresh();
        $this->assertEquals('disbursed', $loan->status);
        $this->assertNotNull($loan->disbursed_at);

        // Direct service step verification: disbursed -> active
        $this->loanService->activateLoan($loan->id, '2026-10-15');
        $loan->refresh();
        $this->assertEquals('active', $loan->status);
    }

    public function test_13_required_reference_is_stored()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-BCA-20261015-XYZ',
            'bukti_transfer' => 'Catatan transfer kantor cabang',
        ]);

        $this->assertDatabaseHas('disbursements', [
            'loan_id' => $loan->id,
            'nomor_referensi' => 'TRF-BCA-20261015-XYZ',
            'bukti_transfer' => 'Catatan transfer kantor cabang',
            'admin_id' => $admin->id,
        ]);
    }

    public function test_14_disbursement_amount_comes_from_authoritative_loan_amount()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 12, 15000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-AUTH-001',
        ]);

        $disbursement = Disbursement::where('loan_id', $loan->id)->first();
        $this->assertNotNull($disbursement);
        $this->assertEquals(15000000.00, (float)$disbursement->nominal);
    }

    public function test_15_client_cannot_override_disbursement_amount()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 12, 20000000.00);

        // Malicious client payload passes nominal Rp 500.000 instead of 20M
        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 500000.00,
            'amount' => 500000.00,
            'total' => 500000.00,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-ATTACK-001',
        ]);

        $disbursement = Disbursement::where('loan_id', $loan->id)->first();
        $this->assertNotNull($disbursement);
        $this->assertEquals(20000000.00, (float)$disbursement->nominal);
        $this->assertDatabaseMissing('disbursements', [
            'loan_id' => $loan->id,
            'nominal' => 500000.00,
        ]);
    }

    public function test_16_client_cannot_override_loan_financial_values()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 12, 10000000.00);

        $originalObligation = $loan->total_kewajiban;
        $originalService = $loan->total_jasa;

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'total_kewajiban' => 1000.00,
            'total_jasa' => 100.00,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-FIN-001',
        ]);

        $loan->refresh();
        $this->assertEquals($originalObligation, $loan->total_kewajiban);
        $this->assertEquals($originalService, $loan->total_jasa);
    }

    // ==========================================
    // D. ACTIVATION (Tests 17 - 19)
    // ==========================================

    public function test_17_successful_gate_5_flow_results_in_active()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-ACTIVE-001',
        ]);

        $loan->refresh();
        $this->assertEquals('active', $loan->status);
    }

    public function test_18_activation_cannot_happen_without_valid_disbursement()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Loan belum dicairkan.');

        // Attempting to activate an approved loan directly throws exception
        $this->loanService->activateLoan($loan->id, '2026-10-15');
    }

    public function test_19_approved_loan_does_not_become_active_without_disbursement()
    {
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member);

        $loan->refresh();
        $this->assertEquals('approved', $loan->status);
        $this->assertNotEquals('active', $loan->status);
        $this->assertDatabaseMissing('loan_installments', ['loan_id' => $loan->id]);
    }

    // ==========================================
    // E. INSTALLMENTS (Tests 20 - 33)
    // ==========================================

    public function test_20_correct_installment_count_for_tenor_6()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-T6-001',
        ]);

        $this->assertCount(6, LoanInstallment::where('loan_id', $loan->id)->get());
    }

    public function test_21_correct_installment_count_for_tenor_12()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 12, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-T12-001',
        ]);

        $this->assertCount(12, LoanInstallment::where('loan_id', $loan->id)->get());
    }

    public function test_22_correct_installment_count_for_tenor_18()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 18, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-T18-001',
        ]);

        $this->assertCount(18, LoanInstallment::where('loan_id', $loan->id)->get());
    }

    public function test_23_correct_installment_count_for_tenor_24()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 24, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-T24-001',
        ]);

        $this->assertCount(24, LoanInstallment::where('loan_id', $loan->id)->get());
    }

    public function test_24_installments_use_actual_disbursement_date()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-05-10',
            'nomor_referensi' => 'TRF-DATE-001',
        ]);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $this->assertEquals('2026-06-10', $installments[0]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2026-07-10', $installments[1]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2026-08-10', $installments[2]->jatuh_tempo->format('Y-m-d'));
    }

    public function test_25_january_31_no_overflow_behavior_works()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2027-01-31',
            'nomor_referensi' => 'TRF-JAN31-001',
        ]);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $this->assertEquals('2027-02-28', $installments[0]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-03-31', $installments[1]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-04-30', $installments[2]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-05-31', $installments[3]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-06-30', $installments[4]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2027-07-31', $installments[5]->jatuh_tempo->format('Y-m-d'));
    }

    public function test_26_leap_year_february_behavior_works()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2028-01-31',
            'nomor_referensi' => 'TRF-LEAP-001',
        ]);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $this->assertEquals('2028-02-29', $installments[0]->jatuh_tempo->format('Y-m-d'));
        $this->assertEquals('2028-03-31', $installments[1]->jatuh_tempo->format('Y-m-d'));
    }

    public function test_27_installment_total_equals_loan_total_obligation()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        // Odd amount to test rounding distribution
        $loan = $this->createApprovedLoan($member, 18, 17500000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-ROUND-001',
        ]);

        $sumTotal = LoanInstallment::where('loan_id', $loan->id)->sum('total_angsuran');
        $this->assertEquals((float)$loan->total_kewajiban, round((float)$sumTotal, 2));
    }

    public function test_28_principal_totals_equal_loan_amount()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 18, 17500000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-POKOK-001',
        ]);

        $sumPokok = LoanInstallment::where('loan_id', $loan->id)->sum('pokok_angsuran');
        $this->assertEquals((float)$loan->jumlah_pinjaman, round((float)$sumPokok, 2));
    }

    public function test_29_service_totals_equal_loan_total_service()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 18, 17500000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-JASA-001',
        ]);

        $sumJasa = LoanInstallment::where('loan_id', $loan->id)->sum('jasa_angsuran');
        $this->assertEquals((float)$loan->total_jasa, round((float)$sumJasa, 2));
    }

    public function test_30_denda_is_zero_for_newly_generated_installments()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 12, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-DENDA-001',
        ]);

        $installments = LoanInstallment::where('loan_id', $loan->id)->get();
        foreach ($installments as $inst) {
            $this->assertEquals(0, (float)$inst->denda);
        }
    }

    public function test_31_installments_start_in_correct_unpaid_state()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-UNPAID-001',
        ]);

        $installments = LoanInstallment::where('loan_id', $loan->id)->get();
        foreach ($installments as $inst) {
            $this->assertEquals('unpaid', $inst->status);
        }
    }

    public function test_32_duplicate_generation_is_prevented()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-DUPGEN-001',
        ]);

        // Attempting to call generateInstallments again on active loan
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Angsuran sudah dibuat sebelumnya.');

        $this->loanService->generateInstallments($loan->id, '2026-10-15');
    }

    public function test_33_duplicate_disbursement_is_prevented()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->disbursementService->disburseLoan($loan->id, $admin->id, 10000000, '2026-10-15', null, 'TRF-DUP-001');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tidak bisa mencairkan loan dengan status disbursed.');

        // Second direct call fails safely
        $this->disbursementService->disburseLoan($loan->id, $admin->id, 10000000, '2026-10-15', null, 'TRF-DUP-002');
    }

    // ==========================================
    // F. MEMBER ACCESS (Tests 34 - 35)
    // ==========================================

    public function test_34_member_can_view_own_generated_installments()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-MEMVIEW-001',
        ]);

        $response = $this->actingAs($member->user)->get(route('member.loans.show', $loan->id));

        $response->assertStatus(200);
        $response->assertSee('Jadwal Angsuran');
        $response->assertSee('Bayar Online');
    }

    public function test_35_member_cannot_view_another_members_installments()
    {
        $admin = $this->createAdmin();
        $member1 = $this->createMember();
        $loan1 = $this->createApprovedLoan($member1, 6, 10000000.00);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan1->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-IDOR-001',
        ]);

        $member2 = $this->createMember();

        $response = $this->actingAs($member2->user)->get(route('member.loans.show', $loan1->id));

        $response->assertStatus(403);
    }

    // ==========================================
    // G. REGRESSION & INTEGRITY (Tests 36 - 40)
    // ==========================================

    public function test_36_existing_gate_1_to_4_functionality_remains_intact()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        // Create submitted loan
        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-REG-' . uniqid(),
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Pendidikan',
            'persen_jasa' => 7.00,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'status' => 'submitted',
        ]);

        // Start Review
        $resStart = $this->actingAs($admin)->post(route('admin.loans.start_review', $loan->id));
        $resStart->assertSessionHas('success');
        $loan->refresh();
        $this->assertEquals('under_review', $loan->status);

        // Reject with reason
        $resReject = $this->actingAs($admin)->post(route('admin.loans.reject', $loan->id), [
            'rejection_reason' => 'Data tidak sesuai',
        ]);
        $resReject->assertSessionHas('success');
        $loan->refresh();
        $this->assertEquals('rejected', $loan->status);
    }

    public function test_37_financial_rate_matrix_remains_intact()
    {
        $matrix = [
            6 => 5.0,
            12 => 7.0,
            18 => 8.5,
            24 => 10.0,
        ];

        foreach ($matrix as $tenor => $rate) {
            $this->assertEquals($rate, KoperasiService::getRatePercent($tenor));
            $calc = $this->koperasiService->calculateLoanInterestAndInstallment(10000000, $tenor);
            $this->assertEquals($rate, $calc['rate_percent']);
        }
    }

    public function test_38_loan_eligibility_remains_intact()
    {
        $member = $this->createMember();
        // Ineligible because no savings paid
        $eligibility = $this->koperasiService->checkPinjamanEligibility($member->id);
        $this->assertFalse($eligibility['eligible']);

        // Fulfill Pokok & Wajib
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

        $eligibilityAfter = $this->koperasiService->checkPinjamanEligibility($member->id);
        $this->assertTrue($eligibilityAfter['eligible']);
    }

    public function test_39_required_document_rules_remain_intact()
    {
        $member = $this->createMember();
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'pokok', 'nominal' => 55000, 'status' => 'paid']);
        Saving::create(['member_id' => $member->id, 'jenis_simpanan' => 'wajib', 'periode' => date('Y-m-01'), 'nominal' => 100000, 'status' => 'paid']);

        Storage::fake();

        // Modal Usaha missing bukti_usaha fails
        $resModal = $this->actingAs($member->user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Modal Usaha',
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'Member Test',
            'ktp' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'bukti_penghasilan' => UploadedFile::fake()->create('slip.pdf', 500, 'application/pdf'),
            'rekening_bank' => UploadedFile::fake()->create('bank.pdf', 500, 'application/pdf'),
        ]);
        $resModal->assertSessionHasErrors('bukti_usaha');
    }

    public function test_40_existing_payment_behavior_remains_intact()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createApprovedLoan($member, 6, 10000000.00);

        // Disburse
        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRF-PAYTEST-001',
        ]);

        $firstInstallment = LoanInstallment::where('loan_id', $loan->id)->first();
        $this->assertEquals('unpaid', $firstInstallment->status);

        Storage::fake();
        // Member uploads payment proof
        $resPay = $this->actingAs($member->user)->post(route('member.loan-payments.store', $firstInstallment->id), [
            'bukti_pembayaran' => UploadedFile::fake()->create('bukti.jpg', 500, 'image/jpeg'),
            'catatan' => 'Bayar angsuran 1',
        ]);

        $resPay->assertSessionHas('success');
        $this->assertDatabaseHas('loan_payments', [
            'installment_id' => $firstInstallment->id,
            'status' => 'pending',
            'metode_pembayaran' => 'online',
        ]);
    }
}
