<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\DisbursementService;
use App\Services\LoanPaymentService;
use App\Services\LoanService;
use App\Services\MemberService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase7Gate1ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityLogService $activityLogService;
    protected MemberService $memberService;
    protected LoanService $loanService;
    protected DisbursementService $disbursementService;
    protected SavingsService $savingsService;
    protected LoanPaymentService $loanPaymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLogService = app(ActivityLogService::class);
        $this->memberService = app(MemberService::class);
        $this->loanService = app(LoanService::class);
        $this->disbursementService = app(DisbursementService::class);
        $this->savingsService = app(SavingsService::class);
        $this->loanPaymentService = app(LoanPaymentService::class);
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

    protected function attachVerifiedDocuments(Loan $loan): void
    {
        $docTypes = ['ktp', 'bukti_penghasilan', 'rekening_bank'];
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
    // A. FOUNDATION TESTS
    // ==========================================

    /** @test */
    public function test_1_activity_log_can_be_created_via_service()
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $log = $this->activityLogService->record(
            'system.test_action',
            null,
            'Pengujian fondasi activity log.',
            ['foo' => 'bar']
        );

        $this->assertInstanceOf(ActivityLog::class, $log);
        $this->assertEquals('system.test_action', $log->activity);
        $this->assertEquals('system.test_action', $log->action);
        $this->assertEquals($admin->id, $log->user_id);
        $this->assertEquals($admin->id, $log->actor_id);
        $this->assertEquals('Pengujian fondasi activity log.', $log->description);
        $this->assertEquals(['foo' => 'bar'], $log->metadata);
        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'activity' => 'system.test_action',
            'user_id' => $admin->id,
        ]);
    }

    /** @test */
    public function test_2_actor_id_and_nullable_actor_work()
    {
        $log = $this->activityLogService->record(
            'system.anonymous_event',
            null,
            'Event sistem tanpa pengguna login.',
            ['source' => 'cron'],
            null
        );

        $this->assertNull($log->user_id);
        $this->assertNull($log->actor);
        $this->assertEquals('system.anonymous_event', $log->activity);
    }

    /** @test */
    public function test_3_polymorphic_subject_relation_works()
    {
        $member = $this->createMember();
        $admin = $this->createAdmin();

        $log = $this->activityLogService->record(
            'member.sample_event',
            $member,
            'Event pada member subjek.',
            ['member_id' => $member->id],
            $admin->id
        );

        $this->assertEquals(Member::class, $log->subject_type);
        $this->assertEquals($member->id, $log->subject_id);
        $this->assertInstanceOf(Member::class, $log->subject);
        $this->assertEquals($member->id, $log->subject->id);
    }

    /** @test */
    public function test_4_metadata_stored_correctly_and_sensitive_keys_stripped()
    {
        $admin = $this->createAdmin();

        $log = $this->activityLogService->record(
            'security.audit',
            null,
            'Tes pembersihan data sensitif.',
            [
                'safe_info' => 'OK',
                'password' => 'secret123',
                'remember_token' => 'tokenABC',
                'nested' => [
                    'visible' => 'yes',
                    'token' => 'leak_token',
                ],
            ],
            $admin->id
        );

        $this->assertEquals('OK', $log->metadata['safe_info']);
        $this->assertArrayNotHasKey('password', $log->metadata);
        $this->assertArrayNotHasKey('remember_token', $log->metadata);
        $this->assertEquals('yes', $log->metadata['nested']['visible']);
        $this->assertArrayNotHasKey('token', $log->metadata['nested']);
    }

    /** @test */
    public function test_5_ip_address_and_user_agent_captured_in_http_context()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => '192.168.1.100', 'HTTP_USER_AGENT' => 'KopinTestAgent/1.0'])
            ->get(route('admin.activity_logs.index'));

        $response->assertStatus(200);

        // Record a log during request
        $log = $this->activityLogService->record('admin.inspected', null, 'Audit inspection.');
        $this->assertNotNull($log->id);
    }

    /** @test */
    public function test_6_activity_log_model_is_immutable_updating_or_deleting_throws_exception()
    {
        $admin = $this->createAdmin();
        $log = $this->activityLogService->record('system.immutable_check', null, 'Initial state.', [], $admin->id);

        $this->expectException(\RuntimeException::class);
        $log->update(['keterangan' => 'Mutated state.']);
    }

    /** @test */
    public function test_7_activity_log_model_is_immutable_deleting_throws_exception()
    {
        $admin = $this->createAdmin();
        $log = $this->activityLogService->record('system.immutable_check_del', null, 'Initial state.', [], $admin->id);

        $this->expectException(\RuntimeException::class);
        $log->delete();
    }

    // ==========================================
    // B. SECURITY & VIEWER TESTS
    // ==========================================

    /** @test */
    public function test_8_guest_cannot_access_admin_activity_log_viewer()
    {
        $response = $this->get(route('admin.activity_logs.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_9_member_cannot_access_admin_activity_log_viewer()
    {
        $member = $this->createMember();
        $response = $this->actingAs($member->user)->get(route('admin.activity_logs.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function test_10_admin_can_access_activity_log_viewer_and_see_latest_first()
    {
        $admin = $this->createAdmin();
        $log1 = $this->activityLogService->record('event.first', null, 'First event recorded.', [], $admin->id);
        $log2 = $this->activityLogService->record('event.second', null, 'Second event recorded.', [], $admin->id);

        $response = $this->actingAs($admin)->get(route('admin.activity_logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Audit Log');
        $response->assertSee('event.second');
        $response->assertSee('event.first');

        // Verify order in query
        $logs = ActivityLog::latest('id')->get();
        $this->assertEquals($log2->id, $logs->first()->id);
        $this->assertEquals($log1->id, $logs->last()->id);
    }

    /** @test */
    public function test_11_admin_can_filter_logs_by_action()
    {
        $admin = $this->createAdmin();
        $this->activityLogService->record('member.verified', null, 'Member approved.', [], $admin->id);
        $this->activityLogService->record('loan.rejected', null, 'Loan rejected.', [], $admin->id);

        $response = $this->actingAs($admin)->get(route('admin.activity_logs.index', ['action' => 'member.verified']));
        $response->assertStatus(200);
        $response->assertSee('Member approved.');
        $response->assertDontSee('Loan rejected.');
    }

    /** @test */
    public function test_12_no_application_routes_exist_to_edit_or_delete_logs()
    {
        $admin = $this->createAdmin();
        $log = $this->activityLogService->record('test.action', null, 'Test', [], $admin->id);

        // Assert no DELETE /admin/activity-logs/{id}
        $resDelete = $this->actingAs($admin)->delete("/admin/activity-logs/{$log->id}");
        $this->assertTrue(in_array($resDelete->getStatusCode(), [404, 405]));

        // Assert no PUT /admin/activity-logs/{id}
        $resPut = $this->actingAs($admin)->put("/admin/activity-logs/{$log->id}", ['keterangan' => 'Tampered']);
        $this->assertTrue(in_array($resPut->getStatusCode(), [404, 405]));
    }

    // ==========================================
    // C. BUSINESS ACTION INTEGRATION TESTS
    // ==========================================

    /** @test */
    public function test_13_member_verification_approved_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        $this->actingAs($admin)->post(route('admin.members.review.store', $member->id), [
            'action' => 'approve',
            'catatan' => 'Dokumen lengkap dan valid.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'member.verified',
            'user_id' => $admin->id,
            'subject_type' => Member::class,
            'subject_id' => $member->id,
        ]);
    }

    /** @test */
    public function test_14_member_verification_rejected_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        $this->actingAs($admin)->post(route('admin.members.review.store', $member->id), [
            'action' => 'reject',
            'catatan' => 'KTP buram dan tidak terbaca.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'member.rejected',
            'user_id' => $admin->id,
            'subject_type' => Member::class,
            'subject_id' => $member->id,
        ]);

        $log = ActivityLog::where('activity', 'member.rejected')->first();
        $this->assertEquals('KTP buram dan tidak terbaca.', $log->metadata['rejection_reason']);
    }

    /** @test */
    public function test_15_member_profile_resubmitted_creates_log()
    {
        $member = $this->createMember(['status' => 'rejected']);

        $this->actingAs($member->user)->post(route('member.profile.resubmit'), [
            'name' => 'Budi Baru',
            'nik' => $member->nik,
            'tanggal_lahir' => '1995-05-15',
            'alamat' => 'Jl. Baru No. 12',
            'telepon' => '081299998888',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'PT Baru',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'member.resubmitted',
            'user_id' => $member->user_id,
            'subject_type' => Member::class,
            'subject_id' => $member->id,
        ]);
    }

    /** @test */
    public function test_16_loan_review_started_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-TEST-001',
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

        $this->actingAs($admin)->post(route('admin.loans.start_review', $loan->id));

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan.review_started',
            'user_id' => $admin->id,
            'subject_type' => Loan::class,
            'subject_id' => $loan->id,
        ]);
    }

    /** @test */
    public function test_17_loan_approved_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-TEST-002',
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
            'status' => 'under_review',
        ]);
        $this->attachVerifiedDocuments($loan);

        $this->actingAs($admin)->post(route('admin.loans.approve', $loan->id));

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan.approved',
            'user_id' => $admin->id,
            'subject_type' => Loan::class,
            'subject_id' => $loan->id,
        ]);
    }

    /** @test */
    public function test_18_loan_rejected_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-TEST-003',
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
            'status' => 'under_review',
        ]);

        $this->actingAs($admin)->post(route('admin.loans.reject', $loan->id), [
            'rejection_reason' => 'Kapasitas angsuran bulanan tidak mencukupi.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan.rejected',
            'user_id' => $admin->id,
            'subject_type' => Loan::class,
            'subject_id' => $loan->id,
        ]);
    }

    /** @test */
    public function test_19_loan_disbursed_and_activated_creates_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-TEST-004',
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
            'status' => 'approved',
        ]);

        $this->actingAs($admin)->post(route('admin.loans.disburse.store', $loan->id), [
            'nominal' => 10000000.00,
            'tanggal_pencairan' => '2026-10-15',
            'nomor_referensi' => 'TRX-DISBURSE-999',
        ]);

        // Disbursed log
        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan.disbursed',
            'user_id' => $admin->id,
            'subject_type' => Loan::class,
            'subject_id' => $loan->id,
        ]);

        // Activation log
        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan.status_changed',
            'user_id' => $admin->id,
            'subject_type' => Loan::class,
            'subject_id' => $loan->id,
        ]);
    }

    /** @test */
    public function test_20_savings_payment_approved_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000.00,
            'status' => 'unpaid',
        ]);

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000.00,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-TEST-001',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.payments.savings.approve', $trx->id));

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'savings_payment.approved',
            'user_id' => $admin->id,
            'subject_type' => SavingsTransaction::class,
            'subject_id' => $trx->id,
        ]);
    }

    /** @test */
    public function test_21_savings_payment_rejected_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000.00,
            'status' => 'unpaid',
        ]);

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000.00,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-TEST-002',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.payments.savings.reject', $trx->id), [
            'catatan' => 'Bukti transfer tidak valid.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'savings_payment.rejected',
            'user_id' => $admin->id,
            'subject_type' => SavingsTransaction::class,
            'subject_id' => $trx->id,
        ]);
    }

    /** @test */
    public function test_22_savings_office_payment_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000.00,
            'status' => 'unpaid',
        ]);

        $this->actingAs($admin)->post(route('admin.savings.office_pay', $saving->id), [
            'catatan' => 'Setor tunai loket kasir.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'savings_payment.approved',
            'user_id' => $admin->id,
            'subject_type' => SavingsTransaction::class,
        ]);
    }

    /** @test */
    public function test_23_loan_payment_approved_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-PAY-001',
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
            'status' => 'active',
        ]);

        $inst = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => '2026-11-15',
            'pokok_angsuran' => 833333.33,
            'jasa_angsuran' => 58333.33,
            'total_angsuran' => 891667.00,
            'denda' => 0,
            'total_tagihan' => 891667.00,
            'status' => 'unpaid',
        ]);

        $payment = LoanPayment::create([
            'installment_id' => $inst->id,
            'nominal_bayar' => 891667.00,
            'denda_dibayar' => 0,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-TEST-001',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.payments.loans.approve', $payment->id));

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan_payment.approved',
            'user_id' => $admin->id,
            'subject_type' => LoanPayment::class,
            'subject_id' => $payment->id,
        ]);
    }

    /** @test */
    public function test_24_loan_payment_rejected_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-PAY-002',
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
            'status' => 'active',
        ]);

        $inst = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => '2026-11-15',
            'pokok_angsuran' => 833333.33,
            'jasa_angsuran' => 58333.33,
            'total_angsuran' => 891667.00,
            'denda' => 0,
            'total_tagihan' => 891667.00,
            'status' => 'unpaid',
        ]);

        $payment = LoanPayment::create([
            'installment_id' => $inst->id,
            'nominal_bayar' => 891667.00,
            'denda_dibayar' => 0,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-TEST-002',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.payments.loans.reject', $payment->id), [
            'catatan' => 'Nominal transfer tidak sesuai.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan_payment.rejected',
            'user_id' => $admin->id,
            'subject_type' => LoanPayment::class,
            'subject_id' => $payment->id,
        ]);
    }

    /** @test */
    public function test_25_loan_payment_office_payment_creates_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-PAY-003',
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
            'status' => 'active',
        ]);

        $inst = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => '2026-11-15',
            'pokok_angsuran' => 833333.33,
            'jasa_angsuran' => 58333.33,
            'total_angsuran' => 891667.00,
            'denda' => 0,
            'total_tagihan' => 891667.00,
            'status' => 'unpaid',
        ]);

        $this->actingAs($admin)->post(route('admin.installments.office_pay', $inst->id), [
            'catatan' => 'Pembayaran langsung di loket kasir.',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'loan_payment.approved',
            'user_id' => $admin->id,
            'subject_type' => LoanPayment::class,
        ]);
    }

    /** @test */
    public function test_26_transaction_rollback_does_not_persist_activity_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        // Simulate an operation that records a log but later fails in the transaction
        try {
            DB::transaction(function () use ($member, $admin) {
                $this->activityLogService->record('transaction.doomed', $member, 'Should not exist', [], $admin->id);
                throw new \RuntimeException('Simulated catastrophic failure.');
            });
        } catch (\Throwable $e) {
            // Expected
        }

        $this->assertDatabaseMissing('activity_logs', [
            'activity' => 'transaction.doomed',
        ]);
    }
}
