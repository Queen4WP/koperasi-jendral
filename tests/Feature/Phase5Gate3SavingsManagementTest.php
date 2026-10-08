<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingsTransaction;
use App\Models\User;
use App\Services\KoperasiService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class Phase5Gate3SavingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    protected function createMember(array $overrides = [], array $userOverrides = []): Member
    {
        $user = User::factory()->create(array_merge([
            'role' => 'member',
        ], $userOverrides));

        return Member::create(array_merge([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1992-05-15',
            'alamat' => 'Jl. Mawar No. 12',
            'telepon' => '081234567890',
            'pekerjaan' => 'Karyawan',
            'perusahaan' => 'PT KOPIN Sejahtera',
            'status' => 'active',
        ], $overrides));
    }

    protected function createSaving(Member $member, string $jenis = 'pokok', array $overrides = []): Saving
    {
        $nominal = ($jenis === 'pokok') ? 55000 : 100000;
        $periode = ($jenis === 'pokok') ? null : ($overrides['periode'] ?? date('Y-m-01'));

        return Saving::create(array_merge([
            'member_id' => $member->id,
            'jenis_simpanan' => $jenis,
            'periode' => $periode,
            'nominal' => $nominal,
            'status' => 'unpaid',
            'jatuh_tempo' => ($jenis === 'wajib') ? date('Y-m-10') : null,
        ], $overrides));
    }

    /** @test */
    public function test_1_admin_can_view_savings_list()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember([], ['name' => 'Budi Santoso']);
        $saving = $this->createSaving($member, 'pokok');

        $response = $this->actingAs($admin)->get(route('admin.savings.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Simpanan Anggota');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Pokok');
        $response->assertSee('55.000');
    }

    /** @test */
    public function test_2_admin_can_filter_savings_by_type()
    {
        $admin = $this->createAdmin();
        $member1 = $this->createMember([], ['name' => 'Anggota Pokok']);
        $savingPokok = $this->createSaving($member1, 'pokok');

        $member2 = $this->createMember([], ['name' => 'Anggota Wajib']);
        $savingWajib = $this->createSaving($member2, 'wajib');

        // Filter Pokok
        $responsePokok = $this->actingAs($admin)->get(route('admin.savings.index', ['jenis' => 'pokok']));
        $responsePokok->assertStatus(200);
        $responsePokok->assertSee('Anggota Pokok');
        $responsePokok->assertDontSee('Anggota Wajib');

        // Filter Wajib
        $responseWajib = $this->actingAs($admin)->get(route('admin.savings.index', ['jenis' => 'wajib']));
        $responseWajib->assertStatus(200);
        $responseWajib->assertSee('Anggota Wajib');
        $responseWajib->assertDontSee('Anggota Pokok');
    }

    /** @test */
    public function test_3_admin_can_filter_by_payment_status()
    {
        $admin = $this->createAdmin();
        $member1 = $this->createMember([], ['name' => 'User Pending']);
        $saving1 = $this->createSaving($member1, 'pokok');
        SavingsTransaction::create([
            'saving_id' => $saving1->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-PENDING-001',
            'status' => 'pending',
        ]);

        $member2 = $this->createMember([], ['name' => 'User Success']);
        $saving2 = $this->createSaving($member2, 'pokok', ['status' => 'paid']);
        SavingsTransaction::create([
            'saving_id' => $saving2->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'kantor',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-SUCCESS-002',
            'status' => 'success',
        ]);

        $responsePending = $this->actingAs($admin)->get(route('admin.savings.index', ['status' => 'pending']));
        $responsePending->assertStatus(200);
        $responsePending->assertSee('User Pending');
        $responsePending->assertDontSee('User Success');

        $responseSuccess = $this->actingAs($admin)->get(route('admin.savings.index', ['status' => 'success']));
        $responseSuccess->assertStatus(200);
        $responseSuccess->assertSee('User Success');
        $responseSuccess->assertDontSee('User Pending');
    }

    /** @test */
    public function test_4_admin_can_filter_by_payment_method()
    {
        $admin = $this->createAdmin();
        $member1 = $this->createMember([], ['name' => 'User Online']);
        $saving1 = $this->createSaving($member1, 'pokok');
        SavingsTransaction::create([
            'saving_id' => $saving1->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-ONL-001',
            'status' => 'pending',
        ]);

        $member2 = $this->createMember([], ['name' => 'User Kantor']);
        $saving2 = $this->createSaving($member2, 'pokok', ['status' => 'paid']);
        SavingsTransaction::create([
            'saving_id' => $saving2->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'kantor',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-KTR-002',
            'status' => 'success',
        ]);

        $responseOnline = $this->actingAs($admin)->get(route('admin.savings.index', ['metode' => 'online']));
        $responseOnline->assertStatus(200);
        $responseOnline->assertSee('User Online');
        $responseOnline->assertDontSee('User Kantor');

        $responseKantor = $this->actingAs($admin)->get(route('admin.savings.index', ['metode' => 'kantor']));
        $responseKantor->assertStatus(200);
        $responseKantor->assertSee('User Kantor');
        $responseKantor->assertDontSee('User Online');
    }

    /** @test */
    public function test_5_admin_can_search_member_savings()
    {
        $admin = $this->createAdmin();
        $memberTarget = $this->createMember(['nik' => '3201999911112222'], ['name' => 'Target Anggota']);
        $savingTarget = $this->createSaving($memberTarget, 'pokok');

        $memberOther = $this->createMember(['nik' => '3201000044445555'], ['name' => 'Other Member']);
        $savingOther = $this->createSaving($memberOther, 'pokok');

        $response = $this->actingAs($admin)->get(route('admin.savings.index', ['search' => 'Target']));
        $response->assertStatus(200);
        $response->assertSee('Target Anggota');
        $response->assertDontSee('Other Member');

        $responseNik = $this->actingAs($admin)->get(route('admin.savings.index', ['search' => '3201999911112222']));
        $responseNik->assertStatus(200);
        $responseNik->assertSee('Target Anggota');
        $responseNik->assertDontSee('Other Member');
    }

    /** @test */
    public function test_6_admin_can_view_savings_detail()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['nik' => '3201888844441111'], ['name' => 'Siti Aminah', 'email' => 'siti@kopin.id']);
        $saving = $this->createSaving($member, 'pokok');

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-TEST-DETAIL-001',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.savings.show', $saving->id));

        $response->assertStatus(200);
        $response->assertSee('Siti Aminah');
        $response->assertSee('3201888844441111');
        $response->assertSee('siti@kopin.id');
        $response->assertSee('55.000');
        $response->assertSee('SAV-TEST-DETAIL-001');
        $response->assertSee('Online');
    }

    /** @test */
    public function test_7_admin_can_approve_pending_online_savings_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-APPROVE-001',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.savings.approve', $trx->id));

        $response->assertSessionHas('success');

        $trx->refresh();
        $saving->refresh();

        $this->assertEquals('success', $trx->status);
        $this->assertEquals($admin->id, $trx->admin_id);
        $this->assertEquals('paid', $saving->status);
    }

    /** @test */
    public function test_8_admin_can_reject_pending_online_payment()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-REJECT-001',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.savings.reject', $trx->id), [
            'catatan' => 'Bukti transfer tidak jelas / nominal salah.',
        ]);

        $response->assertSessionHas('success');

        $trx->refresh();
        $saving->refresh();

        $this->assertEquals('failed', $trx->status);
        $this->assertEquals('Bukti transfer tidak jelas / nominal salah.', $trx->catatan);
        // Saving obligation remains unpaid
        $this->assertEquals('unpaid', $saving->status);
    }

    /** @test */
    public function test_9_cannot_verify_an_already_successful_payment_again()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok', ['status' => 'paid']);

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-ALREADY-SUCCESS',
            'status' => 'success',
            'admin_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.savings.approve', $trx->id));

        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_10_cannot_verify_an_already_failed_payment_as_success()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-ALREADY-FAILED',
            'status' => 'failed',
            'catatan' => 'Ditolak',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.savings.approve', $trx->id));

        $response->assertSessionHas('error');
    }

    /** @test */
    public function test_11_office_payment_uses_kantor_and_success()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        $response = $this->actingAs($admin)->post(route('admin.savings.office_pay', $saving->id), [
            'catatan' => 'Diterima tunai di teller',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('savings_transactions', [
            'saving_id' => $saving->id,
            'admin_id' => $admin->id,
            'metode_pembayaran' => 'kantor',
            'status' => 'success',
            'catatan' => 'Diterima tunai di teller',
        ]);

        $saving->refresh();
        $this->assertEquals('paid', $saving->status);
    }

    /** @test */
    public function test_12_office_payment_cannot_use_client_controlled_arbitrary_nominal_value()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok'); // Nominal is 55000

        // Client attempts to pass arbitrary nominal 1000
        $response = $this->actingAs($admin)->post(route('admin.savings.office_pay', $saving->id), [
            'nominal' => 1000,
            'nominal_bayar' => 1000,
        ]);

        $response->assertSessionHas('success');

        // Server authoritative nominal (55000) was stored, not 1000!
        $this->assertDatabaseHas('savings_transactions', [
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000.00,
            'metode_pembayaran' => 'kantor',
            'status' => 'success',
        ]);
    }

    /** @test */
    public function test_13_partial_savings_payment_is_rejected()
    {
        $savingsService = new SavingsService();
        $member = $this->createMember();

        // Attempting to create Pokok with partial amount Rp 25.000 must be rejected
        $this->expectException(RuntimeException::class);
        $savingsService->createPokok($member->id, 25000);
    }

    /** @test */
    public function test_14_duplicate_simpanan_pokok_successful_payment_is_prevented()
    {
        $savingsService = new SavingsService();
        $member = $this->createMember();

        $pokok = $savingsService->createPokok($member->id);

        // Second attempt to create Pokok for the same member is prevented
        $this->expectException(RuntimeException::class);
        $savingsService->createPokok($member->id);
    }

    /** @test */
    public function test_15_duplicate_pending_payment_attempt_is_prevented()
    {
        Storage::fake();
        $savingsService = new SavingsService();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        // First payment attempt
        $savingsService->createPaymentAttempt($saving->id, 'online', now(), 'proof1.jpg');

        // Second payment attempt while previous is still pending must throw RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Sudah ada pembayaran simpanan yang sedang menunggu verifikasi.');
        $savingsService->createPaymentAttempt($saving->id, 'online', now(), 'proof2.jpg');
    }

    /** @test */
    public function test_16_member_can_see_only_their_own_savings()
    {
        $memberA = $this->createMember([], ['name' => 'Member A']);
        $savingA = $this->createSaving($memberA, 'pokok');

        $memberB = $this->createMember([], ['name' => 'Member B']);
        $savingB = $this->createSaving($memberB, 'wajib');

        $response = $this->actingAs($memberA->user)->get(route('member.savings.index'));

        $response->assertStatus(200);
        $response->assertSee('55.000');
        // Member A's view does not contain Member B's savings obligation
        $response->assertDontSee('100.000');
    }

    /** @test */
    public function test_17_member_cannot_access_admin_savings_management()
    {
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        $responseIndex = $this->actingAs($member->user)->get(route('admin.savings.index'));
        $responseIndex->assertStatus(403);

        $responseShow = $this->actingAs($member->user)->get(route('admin.savings.show', $saving->id));
        $responseShow->assertStatus(403);
    }

    /** @test */
    public function test_18_guest_cannot_access_private_savings_data()
    {
        $response = $this->get(route('member.savings.index'));
        $response->assertRedirect(route('login'));

        $responseAdmin = $this->get(route('admin.savings.index'));
        $responseAdmin->assertRedirect(route('login'));
    }

    /** @test */
    public function test_19_payment_proof_remains_protected()
    {
        Storage::fake();
        $memberA = $this->createMember();
        $memberB = $this->createMember();

        $savingB = $this->createSaving($memberB, 'pokok');
        $proofPath = 'payment-proofs/savings/' . $savingB->id . '/test_proof.jpg';
        Storage::put($proofPath, 'dummy content');

        $trxB = SavingsTransaction::create([
            'saving_id' => $savingB->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-PROOF-TEST',
            'bukti_pembayaran' => $proofPath,
            'status' => 'pending',
        ]);

        // Member A attempts to view Member B's proof -> 403
        $response = $this->actingAs($memberA->user)->get(route('payments.savings.proof', $trxB->id));
        $response->assertStatus(403);

        // Member B can view own proof -> 200
        $responseOwner = $this->actingAs($memberB->user)->get(route('payments.savings.proof', $trxB->id));
        $responseOwner->assertStatus(200);
    }

    /** @test */
    public function test_20_current_month_savings_payment_correctly_affects_existing_eligibility()
    {
        $member = $this->createMember(['status' => 'active']);
        $koperasiService = new KoperasiService();

        // Simpanan Pokok paid
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Simpanan Wajib current month paid
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        $eligibility = $koperasiService->checkPinjamanEligibility($member->id);
        $this->assertTrue($eligibility['eligible']);
    }

    /** @test */
    public function test_21_unpaid_mandatory_savings_creates_the_expected_ineligibility_condition()
    {
        $member = $this->createMember(['status' => 'active']);
        $koperasiService = new KoperasiService();

        // Simpanan Pokok paid
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Current month Wajib is unpaid
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'unpaid',
        ]);

        $eligibility = $koperasiService->checkPinjamanEligibility($member->id);
        $this->assertFalse($eligibility['eligible']);
        $this->assertStringContainsString('Simpanan Wajib bulan berjalan', $eligibility['message']);
    }

    /** @test */
    public function test_22_failed_payment_does_not_incorrectly_mark_the_obligation_as_paid()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();
        $saving = $this->createSaving($member, 'pokok');

        $trx = SavingsTransaction::create([
            'saving_id' => $saving->id,
            'nominal_bayar' => 55000,
            'metode_pembayaran' => 'online',
            'tanggal_bayar' => now(),
            'payment_reference' => 'SAV-FAIL-CHECK',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.savings.reject', $trx->id), [
            'catatan' => 'Ditolak karena tidak sesuai',
        ]);

        $saving->refresh();
        $this->assertEquals('unpaid', $saving->status);
        $this->assertNotEquals('paid', $saving->status);
    }

    /** @test */
    public function test_23_arrears_source_of_truth_is_actual_database_records_not_date_arithmetic()
    {
        $member = $this->createMember(['status' => 'active', 'created_at' => now()->subMonths(10)]);
        $koperasiService = new KoperasiService();

        // 1. Simpanan Pokok paid
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // 2. Current month Simpanan Wajib paid
        $currentMonth = date('Y-m-01');
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => $currentMonth,
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        // Member joined 10 months ago, but NO past records exist in database.
        // Under KOPIN locked business rule, arrears must NOT be derived from (current - join + 1) * 100000.
        // It must come strictly from actual database records.
        $eligibility = $koperasiService->checkPinjamanEligibility($member->id);
        $this->assertTrue($eligibility['eligible'], 'Member should be eligible because no arrears records exist in the database.');

        // Now, add an actual past record with status 'unpaid'
        $pastMonth = date('Y-m-01', strtotime('-2 months'));
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => $pastMonth,
            'nominal' => 100000,
            'status' => 'unpaid',
        ]);

        // Now eligibility MUST be false because an actual past obligation record is unpaid
        $eligibilityWithArrears = $koperasiService->checkPinjamanEligibility($member->id);
        $this->assertFalse($eligibilityWithArrears['eligible']);
        $this->assertStringContainsString('tunggakan Simpanan Wajib', $eligibilityWithArrears['message']);
    }
}
