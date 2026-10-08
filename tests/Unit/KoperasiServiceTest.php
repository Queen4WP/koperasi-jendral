<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\KoperasiService;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KoperasiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KoperasiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KoperasiService();
    }

    /** @test */
    public function it_calculates_loan_interest_and_installment_correctly()
    {
        // Pinjaman 10jt, tenor 24 bulan
        // Jasa = 10% = 1.000.000
        // Total = 11.000.000
        // Angsuran = 458.333
        
        $result = $this->service->calculateLoanInterestAndInstallment(10000000, 24);
        
        $this->assertEquals(0.10, $result['persen_jasa']);
        $this->assertEquals(1000000, $result['total_jasa']);
        $this->assertEquals(11000000, $result['total_kewajiban']);
        $this->assertEquals(458333, $result['angsuran_per_bulan']);
        
        // Tenor 6 bulan
        $result2 = $this->service->calculateLoanInterestAndInstallment(5000000, 6);
        $this->assertEquals(0.05, $result2['persen_jasa']);
    }
    
    /** @test */
    public function it_throws_exception_if_loan_amount_is_out_of_bounds()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('di luar batas pengajuan');
        
        $this->service->calculateLoanInterestAndInstallment(4000000, 12); // Kurang dari 5 juta
    }

    /** @test */
    public function it_calculates_penalty_correctly()
    {
        $this->assertEquals(0, $this->service->calculateDenda(5));
        $this->assertEquals(0, $this->service->calculateDenda(6));
        $this->assertEquals(10000, $this->service->calculateDenda(7));
        $this->assertEquals(10000, $this->service->calculateDenda(10));
        $this->assertEquals(20000, $this->service->calculateDenda(14));
        $this->assertEquals(40000, $this->service->calculateDenda(28));
    }

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
    public function it_rejects_eligibility_if_simpanan_pokok_not_paid()
    {
        $member = $this->createMember();

        $result = $this->service->checkPinjamanEligibility($member->id);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('Simpanan Pokok belum dibayar penuh', $result['message']);
    }

    /** @test */
    public function it_rejects_eligibility_if_simpanan_pokok_is_less_than_55000()
    {
        $member = $this->createMember();

        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 50000,
            'status' => 'paid',
        ]);

        $result = $this->service->checkPinjamanEligibility($member->id);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('Simpanan Pokok belum dibayar penuh', $result['message']);
    }

    /** @test */
    public function it_rejects_eligibility_if_current_month_simpanan_wajib_is_not_paid()
    {
        $member = $this->createMember();

        // Simpanan Pokok lunas
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Simpanan Wajib bulan ini masih unpaid
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'unpaid',
        ]);

        $result = $this->service->checkPinjamanEligibility($member->id);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('Simpanan Wajib bulan berjalan belum dibayar penuh', $result['message']);
    }

    /** @test */
    public function it_rejects_eligibility_if_there_are_mandatory_savings_arrears()
    {
        $member = $this->createMember();

        // Simpanan Pokok lunas
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Simpanan Wajib bulan lalu nunggak (overdue)
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01', strtotime('-1 month')),
            'nominal' => 100000,
            'status' => 'overdue',
        ]);

        // Simpanan Wajib bulan ini lunas
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        $result = $this->service->checkPinjamanEligibility($member->id);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('masih terdapat tunggakan Simpanan Wajib', $result['message']);
    }

    /** @test */
    public function it_approves_eligibility_when_pokok_and_current_wajib_are_paid_with_no_arrears()
    {
        $member = $this->createMember();

        // Simpanan Pokok lunas
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Simpanan Wajib bulan berjalan lunas
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        $result = $this->service->checkPinjamanEligibility($member->id);

        $this->assertTrue($result['eligible']);
        $this->assertStringContainsString('Memenuhi syarat pengajuan pinjaman', $result['message']);
    }
}
