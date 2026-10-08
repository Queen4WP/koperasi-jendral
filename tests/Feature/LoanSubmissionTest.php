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
            'persen_jasa' => 7.00,
            'total_jasa' => 700000.00,
            'total_kewajiban' => 10700000.00,
            'status' => 'submitted',
        ]);
    }

    /** @test */
    public function loan_amount_below_minimum_or_above_maximum_is_rejected()
    {
        $member = $this->createMember();
        $user = $member->user;

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

        // Attempt 4.000.000 (below 5M)
        $responseBelow = $this->actingAs($user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 4000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Modal',
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'Budi',
        ]);
        $responseBelow->assertSessionHasErrors('jumlah_pinjaman');

        // Attempt 60.000.000 (above 50M)
        $responseAbove = $this->actingAs($user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 60000000,
            'tenor' => 12,
            'tujuan_pinjaman' => 'Modal',
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'Budi',
        ]);
        $responseAbove->assertSessionHasErrors('jumlah_pinjaman');
    }

    /** @test */
    public function invalid_tenor_is_rejected_by_form_request()
    {
        $member = $this->createMember();
        $user = $member->user;

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

        // Attempt tenor 5 (not in 6, 12, 18, 24)
        $response = $this->actingAs($user)->post(route('member.loans.store'), [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 5,
            'tujuan_pinjaman' => 'Modal',
            'bank_name' => 'BCA',
            'bank_account_number' => '123',
            'bank_account_owner' => 'Budi',
        ]);

        $response->assertSessionHasErrors('tenor');
    }

    /** @test */
    public function malicious_client_rate_cannot_alter_server_calculation()
    {
        $member = $this->createMember();
        $user = $member->user;

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

        // Client attempts to pass 1% rate on 24 months (official is 10%)
        $payload = [
            'jumlah_pinjaman' => 10000000,
            'tenor' => 24,
            'persen_jasa' => 1.0,
            'total_jasa' => 100000,
            'total_kewajiban' => 10100000,
            'tujuan_pinjaman' => 'Eksploitasi Jasa',
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi Santoso',
        ];

        $response = $this->actingAs($user)->post(route('member.loans.store'), $payload);
        $response->assertRedirect(route('member.loans.index'));

        // Verify that server enforced 10% rate and exact financial totals
        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'jumlah_pinjaman' => 10000000,
            'tenor' => 24,
            'persen_jasa' => 10.00,
            'total_jasa' => 1000000.00,
            'total_kewajiban' => 11000000.00,
        ]);
    }
}
