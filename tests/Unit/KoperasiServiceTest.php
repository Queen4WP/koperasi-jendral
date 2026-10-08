<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\KoperasiService;
use Exception;
use Illuminate\Support\Facades\DB;

class KoperasiServiceTest extends TestCase
{
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
}
