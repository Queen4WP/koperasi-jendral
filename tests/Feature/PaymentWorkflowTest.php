<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\SavingsTransaction;
use App\Models\LoanPayment;
use App\Services\SavingsService;
use App\Services\LoanPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    protected function createMember(array $overrides = []): Member
    {
        $user = User::factory()->create([
            'role' => 'member',
        ]);

        return Member::create(array_merge([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1992-05-15',
            'alamat' => 'Jl. Mawar No. 12, Surabaya',
            'telepon' => '081234567890',
            'pekerjaan' => 'Karyawan',
            'perusahaan' => 'PT Maju Bersama',
            'status' => 'active',
        ], $overrides));
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    protected function createLoanWithInstallments(Member $member, int $months = 3): Loan
    {
        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'LP-' . strtoupper(uniqid()),
            'jumlah_pinjaman' => 3000000,
            'tenor' => $months,
            'tujuan_pinjaman' => 'Modal Usaha',
            'persen_jasa' => 7.0,
            'total_jasa' => 210000,
            'total_kewajiban' => 3210000,
            'angsuran_per_bulan' => 1070000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Member Test',
            'status' => 'active',
            'submitted_at' => now(),
            'reviewed_at' => now(),
            'approved_at' => now(),
            'disbursed_at' => now(),
        ]);

        for ($i = 1; $i <= $months; $i++) {
            LoanInstallment::create([
                'loan_id' => $loan->id,
                'angsuran_ke' => $i,
                'jatuh_tempo' => now()->addMonths($i)->format('Y-m-d'),
                'pokok_angsuran' => 1000000,
                'jasa_angsuran' => 70000,
                'total_angsuran' => 1070000,
                'denda' => 0,
                'total_tagihan' => 1070000,
                'status' => 'unpaid',
            ]);
        }

        return $loan;
    }

    // -------------------------------------------------------------
    // SCENARIO 1: Online savings payment -> pending
    // -------------------------------------------------------------
    /** @test */
    public function online_savings_payment_creates_pending_status_with_uploaded_proof()
    {
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'periode' => null,
            'nominal' => 55000,
            'status' => 'unpaid',
            'jatuh_tempo' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $file = UploadedFile::fake()->create('bukti_transfer.jpg', 200, 'image/jpeg');

        $response = $this->actingAs($member->user)->post(route('member.savings.pay', $saving->id), [
            'bukti_pembayaran' => $file,
            'catatan' => 'Transfer via Mandiri',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('savings_transactions', [
            'saving_id' => $saving->id,
            'status' => 'pending',
            'metode_pembayaran' => 'online',
            'nominal_bayar' => 55000.00,
        ]);

        $transaction = SavingsTransaction::where('saving_id', $saving->id)->first();
        $this->assertNotNull($transaction->bukti_pembayaran);
        Storage::disk('local')->assertExists($transaction->bukti_pembayaran);

        // Saving itself must remain unpaid until approved
        $this->assertEquals('unpaid', $saving->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 2: Online loan payment -> pending
    // -------------------------------------------------------------
    /** @test */
    public function online_loan_payment_creates_pending_status_with_uploaded_proof()
    {
        $member = $this->createMember();
        $loan = $this->createLoanWithInstallments($member);
        $installment = $loan->installments()->first();

        $file = UploadedFile::fake()->create('bukti_angsuran.png', 300, 'image/png');

        $response = $this->actingAs($member->user)->post(route('member.loan-payments.store', $installment->id), [
            'bukti_pembayaran' => $file,
            'catatan' => 'Transfer via BCA',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_payments', [
            'installment_id' => $installment->id,
            'status' => 'pending',
            'metode_pembayaran' => 'online',
            'nominal_bayar' => 1070000.00,
        ]);

        $payment = LoanPayment::where('installment_id', $installment->id)->first();
        $this->assertNotNull($payment->bukti_pembayaran);
        Storage::disk('local')->assertExists($payment->bukti_pembayaran);

        // Installment itself must remain unpaid until approved
        $this->assertEquals('unpaid', $installment->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 3: Member cannot self-approve
    // -------------------------------------------------------------
    /** @test */
    public function member_cannot_self_approve_payment()
    {
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);
        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-TEST01',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($member->user)->post(route('admin.payments.savings.approve', $trx->id));
        $response->assertStatus(403);
        $this->assertEquals('pending', $trx->fresh()->status);
        $this->assertEquals('unpaid', $saving->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 4: Member cannot manipulate payment amount
    // -------------------------------------------------------------
    /** @test */
    public function member_cannot_manipulate_payment_amount()
    {
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => now()->format('Y-m-d'),
            'nominal' => 100000,
            'status' => 'unpaid',
        ]);

        $file = UploadedFile::fake()->create('bukti.jpg', 100);

        // Attempting to send manipulated nominal_bayar = 100
        $this->actingAs($member->user)->post(route('member.savings.pay', $saving->id), [
            'nominal_bayar' => 100,
            'bukti_pembayaran' => $file,
        ]);

        $trx = SavingsTransaction::where('saving_id', $saving->id)->first();
        // Authoritative nominal from saving must be enforced
        $this->assertEquals(100000.00, (float) $trx->nominal_bayar);

        // Same for loan installment
        $loan = $this->createLoanWithInstallments($member);
        $installment = $loan->installments()->first();

        $this->actingAs($member->user)->post(route('member.loan-payments.store', $installment->id), [
            'nominal_bayar' => 500,
            'bukti_pembayaran' => $file,
        ]);

        $pay = LoanPayment::where('installment_id', $installment->id)->first();
        $this->assertEquals(1070000.00, (float) $pay->nominal_bayar);
    }

    // -------------------------------------------------------------
    // SCENARIO 5: Admin can approve savings payment
    // -------------------------------------------------------------
    /** @test */
    public function admin_can_approve_savings_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);
        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-APPROVE',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.savings.approve', $trx->id));
        $response->assertRedirect(route('admin.payments.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('success', $trx->fresh()->status);
        $this->assertEquals('paid', $saving->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 6: Admin can reject savings payment
    // -------------------------------------------------------------
    /** @test */
    public function admin_can_reject_savings_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);
        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-REJECT',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.savings.reject', $trx->id), [
            'catatan' => 'Bukti mutasi tidak valid',
        ]);

        $response->assertRedirect(route('admin.payments.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('failed', $trx->fresh()->status);
        $this->assertEquals('Bukti mutasi tidak valid', $trx->fresh()->catatan);
        // Saving remains unpaid
        $this->assertEquals('unpaid', $saving->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 7: Admin can approve loan payment
    // -------------------------------------------------------------
    /** @test */
    public function admin_can_approve_loan_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithInstallments($member, 1);
        $installment = $loan->installments()->first();

        $payment = LoanPayment::create([
            'installment_id' => $installment->id,
            'nominal_bayar' => 1070000,
            'denda_dibayar' => 0,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-APPROVE',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.loans.approve', $payment->id));
        $response->assertRedirect(route('admin.payments.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('success', $payment->fresh()->status);
        $this->assertEquals('paid', $installment->fresh()->status);
        // Loan should transition to paid_off because all installments are paid
        $this->assertEquals('paid_off', $loan->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 8: Admin can reject loan payment
    // -------------------------------------------------------------
    /** @test */
    public function admin_can_reject_loan_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $loan = $this->createLoanWithInstallments($member);
        $installment = $loan->installments()->first();

        $payment = LoanPayment::create([
            'installment_id' => $installment->id,
            'nominal_bayar' => 1070000,
            'denda_dibayar' => 0,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'PAY-REJECT',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.loans.reject', $payment->id), [
            'catatan' => 'Dana belum masuk ke rekening koperasi',
        ]);

        $response->assertRedirect(route('admin.payments.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('failed', $payment->fresh()->status);
        $this->assertEquals('Dana belum masuk ke rekening koperasi', $payment->fresh()->catatan);
        // Installment remains unpaid
        $this->assertEquals('unpaid', $installment->fresh()->status);
    }

    // -------------------------------------------------------------
    // SCENARIO 9: Admin can record office payment
    // -------------------------------------------------------------
    /** @test */
    public function admin_can_record_office_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);

        // Office payment for saving
        $responseSaving = $this->actingAs($admin)->post(route('admin.savings.office_pay', $saving->id), [
            'catatan' => 'Diterima tunai di loket kasir',
        ]);
        $responseSaving->assertRedirect();

        $this->assertEquals('paid', $saving->fresh()->status);
        $this->assertDatabaseHas('savings_transactions', [
            'saving_id' => $saving->id,
            'metode_pembayaran' => 'kantor',
            'status' => 'success',
            'admin_id' => $admin->id,
        ]);

        // Office payment for loan installment
        $loan = $this->createLoanWithInstallments($member);
        $installment = $loan->installments()->first();

        $responseInstallment = $this->actingAs($admin)->post(route('admin.installments.office_pay', $installment->id), [
            'catatan' => 'Diterima di loket kasir',
        ]);
        $responseInstallment->assertRedirect();

        $this->assertEquals('paid', $installment->fresh()->status);
        $this->assertDatabaseHas('loan_payments', [
            'installment_id' => $installment->id,
            'metode_pembayaran' => 'kantor',
            'status' => 'success',
            'admin_id' => $admin->id,
        ]);
    }

    // -------------------------------------------------------------
    // SCENARIO 10: Non-admin cannot access Admin payment operations
    // -------------------------------------------------------------
    /** @test */
    public function non_admin_cannot_access_admin_payment_operations()
    {
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);
        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-NA',
            'status' => 'pending',
        ]);

        // Member cannot access index
        $this->actingAs($member->user)->get(route('admin.payments.index'))->assertStatus(403);

        // Member cannot review
        $this->actingAs($member->user)->get(route('admin.payments.savings.show', $trx->id))->assertStatus(403);

        // Member cannot approve
        $this->actingAs($member->user)->post(route('admin.payments.savings.approve', $trx->id))->assertStatus(403);

        // Member cannot office-pay
        $this->actingAs($member->user)->post(route('admin.savings.office_pay', $saving->id))->assertStatus(403);
    }

    // -------------------------------------------------------------
    // SCENARIO 11: Member cannot access another member's payment/proof
    // -------------------------------------------------------------
    /** @test */
    public function member_cannot_access_another_members_payment_or_proof()
    {
        $memberA = $this->createMember();
        $memberB = $this->createMember();

        $savingA = Saving::create([
            'member_id' => $memberA->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);

        $path = 'payment-proofs/savings/' . $savingA->id . '/proofA.jpg';
        Storage::disk('local')->put($path, 'sample proof content');

        $trxA = SavingsTransaction::create([
            'saving_id' => $savingA->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-A',
            'bukti_pembayaran' => $path,
            'status' => 'pending',
        ]);

        // Member B cannot view Member A's proof
        $response = $this->actingAs($memberB->user)->get(route('payments.savings.proof', $trxA->id));
        $response->assertStatus(403);

        // Member B cannot pay Member A's saving
        $file = UploadedFile::fake()->create('proof.jpg', 100);
        $payResponse = $this->actingAs($memberB->user)->post(route('member.savings.pay', $savingA->id), [
            'bukti_pembayaran' => $file,
        ]);
        $payResponse->assertStatus(403);

        // But Member A can view their own proof
        $ownerResponse = $this->actingAs($memberA->user)->get(route('payments.savings.proof', $trxA->id));
        $ownerResponse->assertStatus(200);

        // Admin can also view Member A's proof
        $admin = $this->createAdmin();
        $adminResponse = $this->actingAs($admin)->get(route('payments.savings.proof', $trxA->id));
        $adminResponse->assertStatus(200);
    }

    // -------------------------------------------------------------
    // SCENARIO 12: Duplicate pending payment is prevented
    // -------------------------------------------------------------
    /** @test */
    public function duplicate_pending_payment_is_prevented()
    {
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);

        $file1 = UploadedFile::fake()->create('proof1.jpg', 100);
        $file2 = UploadedFile::fake()->create('proof2.jpg', 100);

        // First payment attempt succeeds into pending
        $this->actingAs($member->user)->post(route('member.savings.pay', $saving->id), [
            'bukti_pembayaran' => $file1,
        ]);

        $this->assertEquals(1, SavingsTransaction::where('saving_id', $saving->id)->count());

        // Second payment attempt while first is pending must be blocked with error
        $response2 = $this->actingAs($member->user)->post(route('member.savings.pay', $saving->id), [
            'bukti_pembayaran' => $file2,
        ]);

        $response2->assertSessionHas('error');
        $this->assertEquals(1, SavingsTransaction::where('saving_id', $saving->id)->count());
    }

    // -------------------------------------------------------------
    // SCENARIO 13: Payment proof is stored privately
    // -------------------------------------------------------------
    /** @test */
    public function payment_proof_is_stored_privately()
    {
        $member = $this->createMember();
        $saving = Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'unpaid',
        ]);

        $file = UploadedFile::fake()->create('private_proof.jpg', 150);

        $this->actingAs($member->user)->post(route('member.savings.pay', $saving->id), [
            'bukti_pembayaran' => $file,
        ]);

        $trx = SavingsTransaction::where('saving_id', $saving->id)->first();
        $this->assertNotNull($trx->bukti_pembayaran);

        // Check it exists on the local private disk
        Storage::disk('local')->assertExists($trx->bukti_pembayaran);

        // Check it is NOT stored in public disk
        Storage::disk('public')->assertMissing($trx->bukti_pembayaran);
    }
}
