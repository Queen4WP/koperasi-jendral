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
use RuntimeException;
use Tests\TestCase;

class Phase7Gate2ActivityLogHardeningTest extends TestCase
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

    protected function createAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge(['role' => 'admin'], $overrides));
    }

    protected function createMember(array $overrides = []): Member
    {
        $user = User::factory()->create(['role' => 'member']);
        return Member::create(array_merge([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Pahlawan No. 12',
            'telepon' => '081234567899',
            'pekerjaan' => 'Pedagang',
            'perusahaan' => 'UD Makmur Sentosa',
            'status' => 'pending',
        ], $overrides));
    }

    protected function createLoan(Member $member, array $overrides = []): Loan
    {
        return Loan::create(array_merge([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LOAN-AUDIT-' . uniqid(),
            'jumlah_pinjaman' => 5000000.00,
            'tenor' => 12,
            'persen_jasa' => 7.0,
            'total_jasa' => 350000.00,
            'total_kewajiban' => 5350000.00,
            'angsuran_per_bulan' => 445833.00,
            'tujuan_pinjaman' => 'Pendidikan',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Audit Member',
            'status' => 'submitted',
        ], $overrides));
    }

    protected function createSaving(Member $member, array $overrides = []): Saving
    {
        return Saving::create(array_merge([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 50000.00,
            'status' => 'unpaid',
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
    // 1. DUPLICATE PROTECTION AUDIT
    // ==========================================

    /** @test */
    public function test_1_repeated_member_approval_is_blocked_and_does_not_create_duplicate_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        // First approval
        $this->memberService->approveMembership($member->id, $admin->id, 'Disetujui pertama kali');
        $this->assertEquals(1, ActivityLog::where('activity', 'member.verified')->count());

        // Repeated approval attempt
        try {
            $this->memberService->approveMembership($member->id, $admin->id, 'Percobaan kedua');
            $this->fail('Repeated member approval should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Tidak dapat menyetujui member dengan status active', $e->getMessage());
        }

        // Duplicate log MUST NOT exist
        $this->assertEquals(1, ActivityLog::where('activity', 'member.verified')->count());
    }

    /** @test */
    public function test_2_repeated_member_rejection_is_blocked_and_does_not_create_duplicate_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        // First rejection
        $this->memberService->rejectMembership($member->id, $admin->id, 'Data tidak valid');
        $this->assertEquals(1, ActivityLog::where('activity', 'member.rejected')->count());

        // Repeated rejection attempt
        try {
            $this->memberService->rejectMembership($member->id, $admin->id, 'Tolak kedua');
            $this->fail('Repeated member rejection should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Tidak dapat menolak member dengan status rejected', $e->getMessage());
        }

        // Duplicate log MUST NOT exist
        $this->assertEquals(1, ActivityLog::where('activity', 'member.rejected')->count());
    }

    /** @test */
    public function test_3_repeated_loan_approval_is_blocked_and_does_not_create_duplicate_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'active']);

        $loan = $this->createLoan($member, ['status' => 'submitted']);
        $this->attachVerifiedDocuments($loan);

        $this->loanService->startReview($loan->id, $admin->id);
        $this->loanService->approveLoan($loan->id, $admin->id);

        $this->assertEquals(1, ActivityLog::where('activity', 'loan.approved')->count());

        // Repeated approval attempt
        try {
            $this->loanService->approveLoan($loan->id, $admin->id);
            $this->fail('Repeated loan approval should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Tidak bisa menyetujui loan dengan status approved', $e->getMessage());
        }

        // Duplicate log MUST NOT exist
        $this->assertEquals(1, ActivityLog::where('activity', 'loan.approved')->count());
    }

    /** @test */
    public function test_4_repeated_disbursement_is_blocked_and_does_not_create_duplicate_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'active']);

        $loan = $this->createLoan($member, ['status' => 'approved', 'jumlah_pinjaman' => 3000000]);

        $this->disbursementService->disburseLoan(
            $loan->id,
            $admin->id,
            3000000,
            now()->toDateString(),
            'transfer.jpg',
            'REF-DISB-001'
        );

        $this->assertEquals(1, ActivityLog::where('activity', 'loan.disbursed')->count());

        // Repeated disbursement attempt
        try {
            $this->disbursementService->disburseLoan(
                $loan->id,
                $admin->id,
                3000000,
                now()->toDateString(),
                'transfer2.jpg',
                'REF-DISB-002'
            );
            $this->fail('Repeated loan disbursement should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertTrue(
                str_contains($e->getMessage(), 'disbursed') || str_contains($e->getMessage(), 'sudah dicairkan')
            );
        }

        // Duplicate log MUST NOT exist
        $this->assertEquals(1, ActivityLog::where('activity', 'loan.disbursed')->count());
    }

    /** @test */
    public function test_5_repeated_savings_payment_verification_is_blocked_and_does_not_create_duplicate_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'active']);

        $saving = $this->createSaving($member, ['nominal' => 50000]);

        $transaction = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 50000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-AUDIT-001',
            'status' => 'pending',
        ]);

        $this->savingsService->markPaymentSuccess($transaction->id, $admin->id);
        $this->assertEquals(1, ActivityLog::where('activity', 'savings_payment.approved')->count());

        // Repeated verify attempt
        try {
            $this->savingsService->markPaymentSuccess($transaction->id, $admin->id);
            $this->fail('Repeated savings verification should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Transaksi tidak dalam status pending', $e->getMessage());
        }

        // Duplicate log MUST NOT exist
        $this->assertEquals(1, ActivityLog::where('activity', 'savings_payment.approved')->count());
    }

    /** @test */
    public function test_6_repeated_loan_payment_verification_is_blocked_and_does_not_create_duplicate_logs()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'active']);

        $loan = $this->createLoan($member, ['status' => 'active']);

        $installment = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => now()->addMonth()->toDateString(),
            'pokok_angsuran' => 100000,
            'jasa_angsuran' => 10000,
            'total_angsuran' => 110000,
            'denda' => 0,
            'total_tagihan' => 110000,
            'status' => 'unpaid',
        ]);

        $payment = LoanPayment::create([
            'installment_id' => $installment->id,
            'nominal_bayar' => 110000,
            'denda_dibayar' => 0,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-AUDIT-001',
            'status' => 'pending',
        ]);

        $this->loanPaymentService->markPaymentSuccess($payment->id, $admin->id);
        $this->assertEquals(1, ActivityLog::where('activity', 'loan_payment.approved')->count());

        // Repeated payment approval attempt
        try {
            $this->loanPaymentService->markPaymentSuccess($payment->id, $admin->id);
            $this->fail('Repeated loan payment approval should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Payment tidak dalam status pending', $e->getMessage());
        }

        // Duplicate log MUST NOT exist
        $this->assertEquals(1, ActivityLog::where('activity', 'loan_payment.approved')->count());
    }

    // ==========================================
    // 2. ACTOR INTEGRITY AUDIT
    // ==========================================

    /** @test */
    public function test_7_client_cannot_spoof_actor_id_via_http_request()
    {
        $adminA = $this->createAdmin(['name' => 'Admin Authoritative']);
        $impersonatedUser = $this->createAdmin(['name' => 'Admin Impersonated']);
        $member = $this->createMember(['status' => 'pending']);

        // Admin A makes request trying to send spoofed actor_id/user_id/admin_id in payload
        $this->actingAs($adminA);
        $response = $this->post(route('admin.members.review.store', $member->id), [
            'action' => 'approve',
            'catatan' => 'Verifikasi sah oleh admin terautentikasi',
            'actor_id' => $impersonatedUser->id,
            'user_id' => $impersonatedUser->id,
            'admin_id' => $impersonatedUser->id,
        ]);

        $response->assertSessionHasNoErrors();

        $log = ActivityLog::where('activity', 'member.verified')->first();
        $this->assertNotNull($log);
        // The actor MUST be Admin A, never the spoofed ID
        $this->assertEquals($adminA->id, $log->user_id);
        $this->assertNotEquals($impersonatedUser->id, $log->user_id);
    }

    /** @test */
    public function test_8_correct_authenticated_actor_is_authoritatively_stored()
    {
        $admin1 = $this->createAdmin(['name' => 'Admin One']);
        $admin2 = $this->createAdmin(['name' => 'Admin Two']);

        $member1 = $this->createMember(['status' => 'pending']);
        $member2 = $this->createMember(['status' => 'pending']);

        $this->memberService->approveMembership($member1->id, $admin1->id, 'Disetujui Admin 1');
        $this->memberService->approveMembership($member2->id, $admin2->id, 'Disetujui Admin 2');

        $log1 = ActivityLog::where('activity', 'member.verified')->where('subject_id', $member1->id)->first();
        $log2 = ActivityLog::where('activity', 'member.verified')->where('subject_id', $member2->id)->first();

        $this->assertEquals($admin1->id, $log1->user_id);
        $this->assertEquals($admin2->id, $log2->user_id);
    }

    // ==========================================
    // 3. SUBJECT INTEGRITY AUDIT
    // ==========================================

    /** @test */
    public function test_9_polymorphic_subject_integrity_and_isolation()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'active']);

        $loan = $this->createLoan($member, ['status' => 'submitted', 'tujuan_pinjaman' => 'Pendidikan']);
        $saving = $this->createSaving($member, ['nominal' => 200000]);

        $savingsTx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 200000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-SUBJ-001',
            'status' => 'pending',
        ]);

        $this->loanService->startReview($loan->id, $admin->id);
        $this->savingsService->markPaymentSuccess($savingsTx->id, $admin->id);

        $loanLog = ActivityLog::where('activity', 'loan.review_started')->first();
        $savingLog = ActivityLog::where('activity', 'savings_payment.approved')->first();

        $this->assertEquals(Loan::class, $loanLog->subject_type);
        $this->assertEquals($loan->id, $loanLog->subject_id);
        $this->assertInstanceOf(Loan::class, $loanLog->subject);

        $this->assertEquals(SavingsTransaction::class, $savingLog->subject_type);
        $this->assertEquals($savingsTx->id, $savingLog->subject_id);
        $this->assertInstanceOf(SavingsTransaction::class, $savingLog->subject);

        // Cross-contamination verification: loanLog has nothing to do with savingsTx
        $this->assertNotEquals($loanLog->subject_type, $savingLog->subject_type);
    }

    // ==========================================
    // 4. METADATA & PII PROTECTION AUDIT
    // ==========================================

    /** @test */
    public function test_10_pii_nik_is_not_stored_in_member_activity_metadata_or_fallback_description()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        // Test approve
        $this->memberService->approveMembership($member->id, $admin->id, 'Disetujui aman');
        $approvedLog = ActivityLog::where('activity', 'member.verified')->first();

        $this->assertArrayNotHasKey('nik', $approvedLog->data);
        $this->assertArrayHasKey('member_id', $approvedLog->data);
        $this->assertStringNotContainsString($member->nik, $approvedLog->keterangan);

        // Test reject
        $member2 = $this->createMember(['status' => 'pending']);
        $this->memberService->rejectMembership($member2->id, $admin->id, 'Alasan penolakan dokumen');
        $rejectedLog = ActivityLog::where('activity', 'member.rejected')->first();

        $this->assertArrayNotHasKey('nik', $rejectedLog->data);
        $this->assertArrayHasKey('member_id', $rejectedLog->data);
        $this->assertStringNotContainsString($member2->nik, $rejectedLog->keterangan);

        // Test resubmit
        $this->memberService->resubmitMembership($member2->id, [
            'name' => 'Nama Baru Anggota',
            'nik' => $member2->nik,
            'tanggal_lahir' => '1995-02-02',
            'alamat' => 'Alamat Baru',
            'telepon' => '081299887766',
            'pekerjaan' => 'PNS',
            'perusahaan' => 'Dinas',
        ]);
        $resubmitLog = ActivityLog::where('activity', 'member.resubmitted')->first();

        $this->assertArrayNotHasKey('nik', $resubmitLog->data);
        $this->assertArrayHasKey('member_id', $resubmitLog->data);
        $this->assertStringNotContainsString($member2->nik, $resubmitLog->keterangan);
    }

    /** @test */
    public function test_11_sensitive_top_level_and_nested_keys_and_token_suffixes_are_scrubbed()
    {
        $admin = $this->createAdmin();

        $payload = [
            'safe_key' => 'Nilai Aman',
            'password' => 'secret123',
            'current_password' => 'secretOld',
            'authorization' => 'Bearer eyJhbGciOi...',
            'session_id' => 'sess_abcdef',
            'credential' => 'master_cred',
            'nik' => '3201010101010001',
            'ktp' => 'base64rawdata',
            'file_ktp' => '/uploads/ktp.jpg',
            'slip_gaji' => '/uploads/slip.pdf',
            'bukti_transfer' => '/uploads/tf.jpg',
            'bank_account' => '1234567890',
            'no_rek' => '987654321',
            'cvv' => '123',
            'pin' => '123456',
            'custom_api_secret' => 'super_secret',
            'auth_token' => 'token_value',
            'user_password' => 'another_pass',
            'nested_group' => [
                'nested_safe' => 'Aman',
                'secret' => 'nested_secret',
                'access_token' => 'nested_token',
                'deeply_nested' => [
                    'session' => 'deep_session',
                    'card_number' => '4111222233334444',
                    'deep_safe' => 'Deep Safe Value',
                ],
            ],
        ];

        $log = $this->activityLogService->record(
            'security.sanitization_test',
            null,
            'Uji pembersihan data sensitif multi-tingkat',
            $payload,
            $admin->id
        );

        $sanitized = $log->data;

        // Safe fields preserved
        $this->assertEquals('Nilai Aman', $sanitized['safe_key']);
        $this->assertEquals('Aman', $sanitized['nested_group']['nested_safe']);
        $this->assertEquals('Deep Safe Value', $sanitized['nested_group']['deeply_nested']['deep_safe']);

        // Sensitive top-level fields stripped
        $this->assertArrayNotHasKey('password', $sanitized);
        $this->assertArrayNotHasKey('current_password', $sanitized);
        $this->assertArrayNotHasKey('authorization', $sanitized);
        $this->assertArrayNotHasKey('session_id', $sanitized);
        $this->assertArrayNotHasKey('credential', $sanitized);
        $this->assertArrayNotHasKey('nik', $sanitized);
        $this->assertArrayNotHasKey('ktp', $sanitized);
        $this->assertArrayNotHasKey('file_ktp', $sanitized);
        $this->assertArrayNotHasKey('slip_gaji', $sanitized);
        $this->assertArrayNotHasKey('bukti_transfer', $sanitized);
        $this->assertArrayNotHasKey('bank_account', $sanitized);
        $this->assertArrayNotHasKey('no_rek', $sanitized);
        $this->assertArrayNotHasKey('cvv', $sanitized);
        $this->assertArrayNotHasKey('pin', $sanitized);
        $this->assertArrayNotHasKey('custom_api_secret', $sanitized);
        $this->assertArrayNotHasKey('auth_token', $sanitized);
        $this->assertArrayNotHasKey('user_password', $sanitized);

        // Sensitive nested fields stripped
        $this->assertArrayNotHasKey('secret', $sanitized['nested_group']);
        $this->assertArrayNotHasKey('access_token', $sanitized['nested_group']);
        $this->assertArrayNotHasKey('session', $sanitized['nested_group']['deeply_nested']);
        $this->assertArrayNotHasKey('card_number', $sanitized['nested_group']['deeply_nested']);
    }

    // ==========================================
    // 5. TRANSACTION INTEGRITY AUDIT
    // ==========================================

    /** @test */
    public function test_12_failed_business_operation_rolls_back_and_leaves_no_false_success_log()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        $initialLogsCount = ActivityLog::count();

        // Simulate an operation that encounters an unhandled exception inside transaction
        try {
            DB::transaction(function () use ($member, $admin) {
                $this->memberService->approveMembership($member->id, $admin->id, 'Disetujui sebelum crash');

                // Forced failure after the service call
                throw new \Exception('Database write failure during downstream step.');
            });
            $this->fail('Transaction should have thrown Exception.');
        } catch (\Exception $e) {
            $this->assertEquals('Database write failure during downstream step.', $e->getMessage());
        }

        // Verify member status was rolled back
        $member->refresh();
        $this->assertEquals('pending', $member->status);

        // Verify NO false activity log remains
        $this->assertEquals($initialLogsCount, ActivityLog::count());
        $this->assertDatabaseMissing('activity_logs', [
            'activity' => 'member.verified',
            'subject_id' => $member->id,
        ]);
    }

    // ==========================================
    // 6. ACCESS CONTROL AUDIT
    // ==========================================

    /** @test */
    public function test_13_guest_and_member_blocked_from_admin_activity_log_viewer()
    {
        // 1. Guest blocked
        $guestResponse = $this->get(route('admin.activity_logs.index'));
        $guestResponse->assertRedirect(route('login'));

        // 2. Member blocked
        $member = $this->createMember();
        $this->actingAs($member->user);
        $memberResponse = $this->get(route('admin.activity_logs.index'));
        $memberResponse->assertStatus(403);
    }

    /** @test */
    public function test_14_admin_can_access_viewer_with_sanitized_display_and_no_mutation_endpoints()
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $this->activityLogService->record(
            'loan.approved',
            null,
            'Pinjaman disetujui untuk audit',
            ['nominal' => 5000000, 'loan_id' => 99]
        );

        $response = $this->get(route('admin.activity_logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Audit Log & Riwayat Aktivitas', false);
        $response->assertSee('loan.approved');
        $response->assertSee('Pinjaman disetujui untuk audit');

        // Search functionality
        $searchResponse = $this->get(route('admin.activity_logs.index', ['search' => 'audit']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('loan.approved');

        // Confirm no mutation buttons or forms exist for activity logs
        $response->assertDontSee('admin.activity_logs.update');
        $response->assertDontSee('admin.activity_logs.delete');
        $response->assertDontSee('admin.activity_logs.destroy');
    }

    // ==========================================
    // 7. IMMUTABILITY AUDIT
    // ==========================================

    /** @test */
    public function test_15_activity_log_model_level_immutability_enforced()
    {
        $admin = $this->createAdmin();
        $log = $this->activityLogService->record(
            'system.immutable_check',
            null,
            'Log audit asli',
            ['version' => 1],
            $admin->id
        );

        // Attempt direct Eloquent update
        try {
            $log->update(['keterangan' => 'Manipulated description']);
            $this->fail('Updating activity log should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('cannot be updated', $e->getMessage());
        }

        // Attempt direct Eloquent delete
        try {
            $log->delete();
            $this->fail('Deleting activity log should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('cannot be deleted', $e->getMessage());
        }

        // Verify record remains untouched in DB
        $freshLog = ActivityLog::find($log->id);
        $this->assertNotNull($freshLog);
        $this->assertEquals('Log audit asli', $freshLog->keterangan);
    }
}
