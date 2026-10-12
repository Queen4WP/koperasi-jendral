<?php

namespace Tests\Feature;

use App\Constants\NotificationType;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\Notification;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\User;
use App\Services\DisbursementService;
use App\Services\LoanPaymentService;
use App\Services\LoanService;
use App\Services\MemberService;
use App\Services\NotificationService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class Phase7Gate4NotificationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected MemberService $memberService;
    protected LoanService $loanService;
    protected DisbursementService $disbursementService;
    protected SavingsService $savingsService;
    protected LoanPaymentService $loanPaymentService;
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->memberService = app(MemberService::class);
        $this->loanService = app(LoanService::class);
        $this->disbursementService = app(DisbursementService::class);
        $this->savingsService = app(SavingsService::class);
        $this->loanPaymentService = app(LoanPaymentService::class);
        $this->notificationService = app(NotificationService::class);
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    protected function createMember(array $userOverrides = [], array $memberOverrides = []): Member
    {
        $user = User::factory()->create(array_merge([
            'role' => 'member',
        ], $userOverrides));

        return Member::create(array_merge([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1995-05-20',
            'alamat' => 'Jl. Kebon Jeruk No. 12',
            'telepon' => '081234567890',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'CV Mandiri Sejahtera',
            'status' => 'active',
        ], $memberOverrides));
    }

    protected function createLoanWithDocs(Member $member, string $status = 'under_review'): Loan
    {
        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'jumlah_pinjaman' => 10000000.00,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Renovasi Rumah',
            'keterangan' => 'Renovasi atap',
            'persen_jasa' => 7.00,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'angsuran_per_bulan' => 891667.00,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'status' => $status,
            'submitted_at' => now(),
            'reviewed_at' => $status !== 'submitted' ? now() : null,
        ]);

        foreach (['ktp', 'bukti_penghasilan', 'rekening_bank'] as $jenis) {
            LoanDocument::create([
                'loan_id' => $loan->id,
                'jenis_dokumen' => $jenis,
                'file_path' => "loan-documents/{$loan->id}/{$jenis}.pdf",
                'status_verifikasi' => 'sesuai',
                'catatan' => 'OK',
            ]);
        }

        return $loan;
    }

    // =========================================================================
    // SECTION A: MEMBER VERIFICATION NOTIFICATIONS
    // =========================================================================

    /** @test */
    public function it_creates_notification_when_membership_verification_is_approved()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember([], ['status' => 'pending']);

        $this->assertEquals(0, Notification::where('user_id', $member->user_id)->count());

        $this->memberService->approveMembership($member->id, $admin->id, 'Dokumen lengkap dan valid');

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => 'active',
        ]);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::MEMBER_VERIFICATION_APPROVED, $notification->type);
        $this->assertEquals('Verifikasi Keanggotaan Disetujui', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($member->id, $notification->data['member_id']);
        $this->assertFalse($notification->is_read);
    }

    /** @test */
    public function it_creates_notification_when_membership_verification_is_rejected()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember([], ['status' => 'pending']);

        $rejectionReason = 'Foto KTP buram dan tidak terbaca jelas.';
        $this->memberService->rejectMembership($member->id, $admin->id, $rejectionReason);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => 'rejected',
        ]);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::MEMBER_VERIFICATION_REJECTED, $notification->type);
        $this->assertEquals('Verifikasi Keanggotaan Ditolak', $notification->title);
        $this->assertStringContainsString($rejectionReason, $notification->message);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($rejectionReason, $notification->data['catatan']);
        $this->assertEquals($member->id, $notification->data['member_id']);
    }

    // =========================================================================
    // SECTION B: LOAN LIFECYCLE NOTIFICATIONS
    // =========================================================================

    /** @test */
    public function it_creates_notification_when_loan_review_starts()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'submitted');

        $this->loanService->startReview($loan->id, $admin->id);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'under_review',
        ]);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_REVIEW_STARTED, $notification->type);
        $this->assertEquals('Pengajuan Pinjaman Ditinjau', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($loan->id, $notification->data['loan_id']);
        $this->assertEquals($loan->nomor_pinjaman, $notification->data['nomor_pinjaman']);
    }

    /** @test */
    public function it_creates_notification_when_loan_is_approved()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'under_review');

        $this->loanService->approveLoan($loan->id, $admin->id);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'approved',
        ]);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_APPROVED, $notification->type);
        $this->assertEquals('Pengajuan Pinjaman Disetujui', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($loan->id, $notification->data['loan_id']);
        $this->assertEquals(10000000.00, (float) $notification->data['jumlah_pinjaman']);
    }

    /** @test */
    public function it_creates_notification_when_loan_is_rejected()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'under_review');

        $reason = 'Kapasitas bayar tidak mencukupi untuk nominal yang diajukan.';
        $this->loanService->rejectLoan($loan->id, $admin->id, $reason);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_REJECTED, $notification->type);
        $this->assertEquals('Pengajuan Pinjaman Ditolak', $notification->title);
        $this->assertStringContainsString($reason, $notification->message);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($reason, $notification->data['rejection_reason']);
    }

    // =========================================================================
    // SECTION C: LOAN DISBURSEMENT NOTIFICATIONS
    // =========================================================================

    /** @test */
    public function it_creates_notification_when_loan_is_disbursed()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'approved');

        $tanggal = '2026-10-10';
        $ref = 'TRX-DISB-998811';
        $bukti = 'transfers/bukti_tf_998811.pdf';

        $disbursement = $this->disbursementService->disburseLoan(
            $loan->id,
            $admin->id,
            10000000.00,
            $tanggal,
            $bukti,
            $ref
        );

        $this->assertInstanceOf(Disbursement::class, $disbursement);
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'disbursed',
        ]);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_DISBURSED, $notification->type);
        $this->assertEquals('Pinjaman Telah Dicairkan', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($loan->id, $notification->data['loan_id']);
        $this->assertEquals(10000000.00, (float) $notification->data['nominal']);
        $this->assertEquals($tanggal, $notification->data['tanggal_pencairan']);

        // Security check: sensitive transfer evidence file must NEVER be in notification payload
        $this->assertArrayNotHasKey('bukti_transfer', $notification->data);
    }

    // =========================================================================
    // SECTION D: SAVINGS PAYMENT NOTIFICATIONS
    // =========================================================================

    /** @test */
    public function it_creates_notification_when_online_savings_payment_is_approved()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'periode' => null,
            'nominal' => 55000.00,
            'status' => 'unpaid',
        ]);

        $transaction = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'admin_id' => null,
            'nominal_bayar' => 55000.00,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-ONL-123456',
            'bukti_pembayaran' => 'proofs/saving_123.jpg',
            'status' => 'pending',
        ]);

        $this->savingsService->markPaymentSuccess($transaction->id, $admin->id);

        $this->assertDatabaseHas('savings', ['id' => $saving->id, 'status' => 'paid']);
        $this->assertDatabaseHas('savings_transactions', ['id' => $transaction->id, 'status' => 'success']);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::SAVINGS_PAYMENT_APPROVED, $notification->type);
        $this->assertEquals('Pembayaran Simpanan Disetujui', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($transaction->id, $notification->data['transaction_id']);
        $this->assertEquals(55000.00, (float) $notification->data['nominal']);
    }

    /** @test */
    public function it_creates_notification_when_online_savings_payment_is_rejected()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'periode' => null,
            'nominal' => 55000.00,
            'status' => 'unpaid',
        ]);

        $transaction = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'admin_id' => null,
            'nominal_bayar' => 55000.00,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-ONL-789012',
            'bukti_pembayaran' => 'proofs/saving_789.jpg',
            'status' => 'pending',
        ]);

        $catatan = 'Bukti transfer mutasi rekening tidak valid.';
        $this->savingsService->markPaymentFailed($transaction->id, $admin->id, $catatan);

        $this->assertDatabaseHas('savings', ['id' => $saving->id, 'status' => 'unpaid']);
        $this->assertDatabaseHas('savings_transactions', ['id' => $transaction->id, 'status' => 'failed']);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::SAVINGS_PAYMENT_REJECTED, $notification->type);
        $this->assertEquals('Pembayaran Simpanan Ditolak', $notification->title);
        $this->assertStringContainsString($catatan, $notification->message);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($catatan, $notification->data['rejection_reason']);
    }

    /** @test */
    public function it_creates_notification_when_admin_records_direct_office_savings_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => '2026-10-01',
            'nominal' => 100000.00,
            'status' => 'unpaid',
        ]);

        $transaction = $this->savingsService->recordOfficePayment($saving->id, $admin->id, 'Setor tunai kasir 1');

        $this->assertInstanceOf(SavingsTransaction::class, $transaction);
        $this->assertDatabaseHas('savings', ['id' => $saving->id, 'status' => 'paid']);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::SAVINGS_PAYMENT_APPROVED, $notification->type);
        $this->assertEquals('Pembayaran Simpanan Kantor Berhasil', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($transaction->id, $notification->data['transaction_id']);
        $this->assertEquals('kantor', $notification->data['metode']);
        $this->assertEquals(100000.00, (float) $notification->data['nominal']);
    }

    // =========================================================================
    // SECTION E: LOAN INSTALLMENT PAYMENT NOTIFICATIONS
    // =========================================================================

    /** @test */
    public function it_creates_notification_when_online_installment_payment_is_approved()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'active');

        $installment = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => '2026-11-10',
            'pokok_angsuran' => 833333.00,
            'jasa_angsuran' => 58334.00,
            'total_angsuran' => 891667.00,
            'denda' => 0.00,
            'total_tagihan' => 891667.00,
            'status' => 'unpaid',
        ]);

        $payment = LoanPayment::create([
            'installment_id' => $installment->id,
            'admin_id' => null,
            'nominal_bayar' => 891667.00,
            'denda_dibayar' => 0.00,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-ONL-112233',
            'status' => 'pending',
        ]);

        $this->loanPaymentService->markPaymentSuccess($payment->id, $admin->id);

        $this->assertDatabaseHas('loan_installments', ['id' => $installment->id, 'status' => 'paid']);
        $this->assertDatabaseHas('loan_payments', ['id' => $payment->id, 'status' => 'success']);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_PAYMENT_APPROVED, $notification->type);
        $this->assertEquals('Pembayaran Angsuran Disetujui', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($payment->id, $notification->data['payment_id']);
        $this->assertEquals(1, $notification->data['angsuran_ke']);
        $this->assertEquals(891667.00, (float) $notification->data['nominal']);
    }

    /** @test */
    public function it_creates_notification_when_online_installment_payment_is_rejected()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'active');

        $installment = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => '2026-11-10',
            'pokok_angsuran' => 833333.00,
            'jasa_angsuran' => 58334.00,
            'total_angsuran' => 891667.00,
            'denda' => 0.00,
            'total_tagihan' => 891667.00,
            'status' => 'unpaid',
        ]);

        $payment = LoanPayment::create([
            'installment_id' => $installment->id,
            'admin_id' => null,
            'nominal_bayar' => 891667.00,
            'denda_dibayar' => 0.00,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-ONL-445566',
            'status' => 'pending',
        ]);

        $catatan = 'Nominal transfer tidak sesuai dengan tagihan.';
        $this->loanPaymentService->markPaymentFailed($payment->id, $admin->id, $catatan);

        $this->assertDatabaseHas('loan_installments', ['id' => $installment->id, 'status' => 'unpaid']);
        $this->assertDatabaseHas('loan_payments', ['id' => $payment->id, 'status' => 'failed']);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_PAYMENT_REJECTED, $notification->type);
        $this->assertEquals('Pembayaran Angsuran Ditolak', $notification->title);
        $this->assertStringContainsString($catatan, $notification->message);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($catatan, $notification->data['rejection_reason']);
    }

    /** @test */
    public function it_creates_notification_when_admin_records_direct_office_installment_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'active');

        $installment = LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 2,
            'jatuh_tempo' => '2026-12-10',
            'pokok_angsuran' => 833333.00,
            'jasa_angsuran' => 58334.00,
            'total_angsuran' => 891667.00,
            'denda' => 0.00,
            'total_tagihan' => 891667.00,
            'status' => 'unpaid',
        ]);

        $payment = $this->loanPaymentService->recordOfficePayment($installment->id, $admin->id, 'Pembayaran kasir loket');

        $this->assertInstanceOf(LoanPayment::class, $payment);
        $this->assertDatabaseHas('loan_installments', ['id' => $installment->id, 'status' => 'paid']);

        $notification = Notification::where('user_id', $member->user_id)->first();
        $this->assertNotNull($notification);
        $this->assertEquals(NotificationType::LOAN_PAYMENT_APPROVED, $notification->type);
        $this->assertEquals('Pembayaran Angsuran Kantor Berhasil', $notification->title);
        $this->assertEquals($member->user_id, $notification->user_id);
        $this->assertEquals($payment->id, $notification->data['payment_id']);
        $this->assertEquals(2, $notification->data['angsuran_ke']);
        $this->assertEquals('kantor', $notification->data['metode']);
    }

    // =========================================================================
    // SECTION F: FAILURE, ROLLBACK & IDEMPOTENCY / DEDUPLICATION
    // =========================================================================

    /** @test */
    public function no_notification_is_created_when_business_operation_fails()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember([], ['status' => 'active']);

        // Approving an already active member must fail and NOT generate a notification
        $this->expectException(RuntimeException::class);

        try {
            $this->memberService->approveMembership($member->id, $admin->id);
        } finally {
            $this->assertEquals(0, Notification::where('user_id', $member->user_id)->count());
        }
    }

    /** @test */
    public function no_duplicate_notifications_on_repeated_state_transition_attempt()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithDocs($member, 'submitted');

        // First attempt succeeds and generates 1 notification
        $this->loanService->startReview($loan->id, $admin->id);
        $this->assertEquals(1, Notification::where('user_id', $member->user_id)->count());

        // Second attempt to startReview must fail due to state lock & validation
        try {
            $this->loanService->startReview($loan->id, $admin->id);
            $this->fail('Expected exception was not thrown on duplicate startReview.');
        } catch (RuntimeException $e) {
            // Expected
        }

        // Must still have exactly 1 notification, no duplicates
        $this->assertEquals(1, Notification::where('user_id', $member->user_id)->count());
    }

    /** @test */
    public function transaction_rollback_rolls_back_both_business_change_and_notification()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember([], ['status' => 'pending']);

        // Mock NotificationService to fail inside the transaction
        $failingNotificationService = $this->createMock(NotificationService::class);
        $failingNotificationService->method('send')
            ->willThrowException(new RuntimeException('Simulated notification dispatch failure'));

        $memberServiceWithFault = new MemberService(
            app(\App\Services\ActivityLogService::class),
            $failingNotificationService
        );

        try {
            $memberServiceWithFault->approveMembership($member->id, $admin->id);
            $this->fail('Expected transaction failure was not thrown.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Simulated notification dispatch failure', $e->getMessage());
        }

        // Verify member status is NOT modified (rolled back to pending)
        $member->refresh();
        $this->assertEquals('pending', $member->status);

        // Verify no notification persisted
        $this->assertEquals(0, Notification::where('user_id', $member->user_id)->count());
    }

    // =========================================================================
    // SECTION G: RECIPIENT ISOLATION & TENANT / IDOR PRIVACY
    // =========================================================================

    /** @test */
    public function notifications_are_strictly_isolated_to_the_target_member()
    {
        $admin = $this->createAdmin();
        $memberA = $this->createMember([], ['status' => 'pending']);
        $memberB = $this->createMember([], ['status' => 'pending']);

        $this->memberService->approveMembership($memberA->id, $admin->id);

        // Member A receives exactly 1 notification
        $this->assertEquals(1, Notification::where('user_id', $memberA->user_id)->count());

        // Member B must have ZERO notifications
        $this->assertEquals(0, Notification::where('user_id', $memberB->user_id)->count());

        // Verify notification user_id strictly matches Member A
        $notif = Notification::where('user_id', $memberA->user_id)->first();
        $this->assertEquals($memberA->user_id, $notif->user_id);
        $this->assertNotEquals($memberB->user_id, $notif->user_id);
    }
}
