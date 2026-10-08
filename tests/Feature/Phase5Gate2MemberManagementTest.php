<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipVerification;
use App\Models\Saving;
use App\Models\User;
use App\Services\KoperasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase5Gate2MemberManagementTest extends TestCase
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
            'alamat' => 'Jl. Anggrek No. 10, Jakarta',
            'telepon' => '081298765432',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'Toko Berkah',
            'status' => 'pending',
        ], $overrides));
    }

    /** @test */
    public function test_1_admin_can_view_member_list()
    {
        $admin = $this->createAdmin();
        $member1 = $this->createMember(['nik' => '3201000000000001'], ['name' => 'Budi Anggota']);
        $member2 = $this->createMember(['nik' => '3201000000000002'], ['name' => 'Siti Anggota']);

        $response = $this->actingAs($admin)->get(route('admin.members.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Anggota Koperasi');
        $response->assertSee('Budi Anggota');
        $response->assertSee('Siti Anggota');
        $response->assertSee('3201000000000001');
    }

    /** @test */
    public function test_2_admin_can_view_member_detail()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember([
            'nik' => '3201999988887777',
            'alamat' => 'Jl. Merdeka No. 45',
            'pekerjaan' => 'Arsitek',
        ], ['name' => 'Doni Arsitek', 'email' => 'doni@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.members.show', $member->id));

        $response->assertStatus(200);
        $response->assertSee('Doni Arsitek');
        $response->assertSee('3201999988887777');
        $response->assertSee('doni@example.com');
        $response->assertSee('Jl. Merdeka No. 45');
        $response->assertSee('Arsitek');
    }

    /** @test */
    public function test_3_admin_can_approve_pending_member()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.members.review.store', $member->id), [
            'action' => 'approve',
            'catatan' => 'Berkas lengkap dan valid.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('membership_verifications', [
            'member_id' => $member->id,
            'admin_id' => $admin->id,
            'status_keputusan' => 'approved',
            'catatan' => 'Berkas lengkap dan valid.',
        ]);
    }

    /** @test */
    public function test_4_admin_can_reject_pending_member_with_a_reason()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.members.review.store', $member->id), [
            'action' => 'reject',
            'catatan' => 'Foto KTP buram dan NIK tidak terbaca.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => 'rejected',
        ]);
        $this->assertDatabaseHas('membership_verifications', [
            'member_id' => $member->id,
            'admin_id' => $admin->id,
            'status_keputusan' => 'rejected',
            'catatan' => 'Foto KTP buram dan NIK tidak terbaca.',
        ]);
    }

    /** @test */
    public function test_5_admin_cannot_reject_without_rejection_reason()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.members.review.store', $member->id), [
            'action' => 'reject',
            'catatan' => '',
        ]);

        $response->assertSessionHasErrors(['catatan']);
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function test_6_rejected_member_can_edit_allowed_fields()
    {
        $member = $this->createMember(['status' => 'rejected']);

        $response = $this->actingAs($member->user)->put(route('member.profile.update'), [
            'name' => 'Nama Perbaikan',
            'tanggal_lahir' => '1995-10-10',
            'alamat' => 'Alamat Perbaikan No. 12',
            'telepon' => '081233334444',
            'pekerjaan' => 'Guru',
            'perusahaan' => 'SMA 1 Jakarta',
        ]);

        $response->assertRedirect(route('member.profile'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $member->user_id,
            'name' => 'Nama Perbaikan',
        ]);
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'alamat' => 'Alamat Perbaikan No. 12',
            'telepon' => '081233334444',
            'pekerjaan' => 'Guru',
            'status' => 'rejected', // Profile update keeps status rejected until resubmission
        ]);
    }

    /** @test */
    public function test_7_rejected_member_can_resubmit_and_status_becomes_pending()
    {
        $admin = $this->createAdmin();
        $member = $this->createMember(['status' => 'rejected']);

        MembershipVerification::create([
            'member_id' => $member->id,
            'admin_id' => $admin->id,
            'status_keputusan' => 'rejected',
            'catatan' => 'NIK salah',
        ]);

        $newNik = '3201888877776666';
        $payload = [
            'nama' => 'Nama Final',
            'nik' => $newNik,
            'tanggal_lahir' => '1994-06-15',
            'alamat' => 'Jl. Sudirman No. 100',
            'telepon' => '081288889999',
            'pekerjaan' => 'Programmer',
            'perusahaan' => 'Tech Corp',
        ];

        $response = $this->actingAs($member->user)->post(route('member.profile.resubmit'), $payload);

        $response->assertRedirect(route('member.profile'));
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertEquals('pending', $member->status);
        $this->assertEquals($newNik, $member->nik);
        $this->assertEquals('Nama Final', $member->user->name);

        // Verification history is preserved, not deleted
        $this->assertDatabaseCount('membership_verifications', 1);
        $this->assertDatabaseHas('membership_verifications', [
            'member_id' => $member->id,
            'status_keputusan' => 'rejected',
            'catatan' => 'NIK salah',
        ]);
    }

    /** @test */
    public function test_8_resubmission_cannot_change_email_or_account_identity()
    {
        $originalEmail = 'original.member@kopin.id';
        $member = $this->createMember(['status' => 'rejected'], ['email' => $originalEmail]);

        $payload = [
            'name' => 'Nama Benar',
            'nik' => '3201777766665555',
            'email' => 'malicious.new.email@kopin.id',
            'role' => 'admin',
            'status' => 'active',
            'tanggal_lahir' => '1991-01-01',
            'alamat' => 'Jl. Thamrin No. 1',
            'telepon' => '081277778888',
            'pekerjaan' => 'Akuntan',
            'perusahaan' => 'PT Keuangan',
        ];

        $response = $this->actingAs($member->user)->post(route('member.profile.resubmit'), $payload);

        $response->assertRedirect(route('member.profile'));

        $member->user->refresh();
        $member->refresh();

        // Email and role are unchanged
        $this->assertEquals($originalEmail, $member->user->email);
        $this->assertEquals('member', $member->user->role);
        // Status is strictly set to pending by server, not client-supplied active
        $this->assertEquals('pending', $member->status);
    }

    /** @test */
    public function test_9_duplicate_nik_is_rejected_on_resubmission()
    {
        $existingMember = $this->createMember(['nik' => '3201111122223333']);
        $rejectedMember = $this->createMember(['status' => 'rejected']);

        $payload = [
            'nama' => 'Nama Budi',
            'nik' => '3201111122223333', // Duplicate of existingMember!
            'tanggal_lahir' => '1992-05-15',
            'alamat' => 'Jl. Anggrek',
            'telepon' => '081298765432',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'Toko',
        ];

        $response = $this->actingAs($rejectedMember->user)->post(route('member.profile.resubmit'), $payload);

        $response->assertSessionHasErrors(['nik']);
        $rejectedMember->refresh();
        $this->assertEquals('rejected', $rejectedMember->status);
    }

    /** @test */
    public function test_10_member_a_cannot_modify_member_b()
    {
        $memberA = $this->createMember(['status' => 'rejected']);
        $memberB = $this->createMember(['status' => 'rejected', 'nik' => '3201555544443333']);

        $payload = [
            'nama' => 'Nama Palsu',
            'nik' => '3201999900001111',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jl. Diretas',
            'telepon' => '089999999999',
            'pekerjaan' => 'Hacker',
            'perusahaan' => 'Dark',
        ];

        // Member A attempts to modify Member B through explicit ID endpoint
        $response = $this->actingAs($memberA->user)->post(route('member.resubmit', $memberB->id), $payload);

        $response->assertStatus(403);

        // Member B data is completely untouched
        $memberB->refresh();
        $this->assertEquals('3201555544443333', $memberB->nik);
        $this->assertNotEquals('Nama Palsu', $memberB->user->name);
        $this->assertEquals('rejected', $memberB->status);
    }

    /** @test */
    public function test_11_member_cannot_access_admin_member_management()
    {
        $member = $this->createMember(['status' => 'active']);
        $otherMember = $this->createMember();

        // Index
        $responseIndex = $this->actingAs($member->user)->get(route('admin.members.index'));
        $responseIndex->assertStatus(403);

        // Show
        $responseShow = $this->actingAs($member->user)->get(route('admin.members.show', $otherMember->id));
        $responseShow->assertStatus(403);

        // Review endpoint
        $responseReview = $this->actingAs($member->user)->get(route('admin.members.review', $otherMember->id));
        $responseReview->assertStatus(403);

        // Guest access
        $this->app['auth']->guard()->logout();
        $responseGuest = $this->get(route('admin.members.index'));
        $responseGuest->assertRedirect(route('login'));
    }

    /** @test */
    public function test_12_pending_or_resubmitted_member_does_not_become_loan_eligible()
    {
        $member = $this->createMember(['status' => 'rejected']);

        // Resubmit -> status transitions to pending
        $payload = [
            'name' => 'Member Perbaikan',
            'nik' => '3201444433332222',
            'tanggal_lahir' => '1992-05-15',
            'alamat' => 'Jl. Anggrek No. 10',
            'telepon' => '081298765432',
            'pekerjaan' => 'PNS',
            'perusahaan' => 'Dinas',
        ];
        $this->actingAs($member->user)->post(route('member.profile.resubmit'), $payload);
        $member->refresh();

        $this->assertEquals('pending', $member->status);

        // Even if savings are paid:
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

        $koperasiService = new KoperasiService();
        $eligibility = $koperasiService->checkPinjamanEligibility($member->id);

        $this->assertFalse($eligibility['eligible']);
        $this->assertStringContainsString('akun belum diverifikasi', $eligibility['message']);
    }
}
