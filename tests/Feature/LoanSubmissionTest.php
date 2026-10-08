<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function createMember(array $overrides = []): Member
    {
        $user = User::factory()->create();
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

    /** @test */
    public function ineligible_member_cannot_submit_loan_application()
    {
        $member = $this->createMember();
        $user = $member->user;

        // Member has not paid Simpanan Pokok or Simpanan Wajib
        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'persen_jasa' => 7,
            'tujuan_pinjaman' => 'Modal Usaha',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'keterangan' => 'Pengajuan mendesak',
        ];

        $response = $this->actingAs($user)
            ->from(route('member.loans.create'))
            ->post(route('member.loans.store'), $payload);

        $response->assertRedirect(route('member.loans.create'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('loans', 0);
    }

    /** @test */
    public function eligible_member_can_submit_loan_and_is_redirected_to_member_loans_index()
    {
        $member = $this->createMember();
        $user = $member->user;

        // Fulfill Simpanan Pokok
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Fulfill Simpanan Wajib current month
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'persen_jasa' => 7,
            'tujuan_pinjaman' => 'Modal Usaha',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
            'keterangan' => 'Pengajuan modal usaha',
        ];

        $response = $this->actingAs($user)
            ->post(route('member.loans.store'), $payload);

        $response->assertRedirect(route('member.loans.index'));
        $response->assertSessionHas('success', 'Pengajuan pinjaman berhasil disubmit.');

        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'status' => 'submitted',
        ]);
    }
}
