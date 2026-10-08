<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
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
    public function dashboard_loads_successfully_without_database_field_errors()
    {
        $member = $this->createMember();
        $user = $member->user;

        // Add savings
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

        // Add active loan with installments
        $loan = Loan::create([
            'member_id' => $member->id,
            'nomor_pinjaman' => 'KPN-20261008-TEST1',
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'persen_jasa' => 7,
            'total_jasa' => 700000,
            'total_kewajiban' => 10700000,
            'angsuran_per_bulan' => 891666.67,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_owner' => 'Budi',
            'tujuan_pinjaman' => 'Modal Usaha',
            'status' => 'active',
        ]);

        $dueDate = date('Y-m-d', strtotime('+1 month'));

        LoanInstallment::create([
            'loan_id' => $loan->id,
            'angsuran_ke' => 1,
            'jatuh_tempo' => $dueDate,
            'pokok_angsuran' => 833333.33,
            'jasa_angsuran' => 58333.34,
            'total_angsuran' => 891666.67,
            'denda' => 0,
            'total_tagihan' => 891666.67,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($user)->get(route('member.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Anggota');
        $response->assertSee('Rp 55.000,00');
        $response->assertSee('Rp 100.000,00');
        $response->assertSee('891.666,67');
        $response->assertSee(date('d M Y', strtotime($dueDate)));
    }
}
