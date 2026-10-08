<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\KoperasiService;
use App\Services\LoanService;
use App\Services\SavingsService;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanInstallment;
use Exception;
use RuntimeException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected KoperasiService $koperasiService;
    protected LoanService $loanService;
    protected SavingsService $savingsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->koperasiService = new KoperasiService();
        $this->loanService = new LoanService($this->koperasiService);
        $this->savingsService = new SavingsService();
    }

    protected function createEligibleMember(): Member
    {
        $user = User::factory()->create();
        $member = Member::create([
            'user_id' => $user->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Merdeka No. 1',
            'telepon' => '08123456789',
            'pekerjaan' => 'Karyawan',
            'perusahaan' => 'PT Maju Bersama',
            'status' => 'active',
        ]);

        // Simpanan Pokok
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'pokok',
            'nominal' => 55000,
            'status' => 'paid',
        ]);

        // Simpanan Wajib bulan ini
        Saving::create([
            'member_id' => $member->id,
            'jenis_simpanan' => 'wajib',
            'periode' => date('Y-m-01'),
            'nominal' => 100000,
            'status' => 'paid',
        ]);

        return $member;
    }

    /** @test */
    public function it_validates_loan_amount_boundaries_accurately()
    {
        // 4.999.999 -> reject
        try {
            $this->koperasiService->calculateLoanInterestAndInstallment(4999999, 12);
            $this->fail('Expected exception for loan amount below Rp5.000.000');
        } catch (Exception $e) {
            $this->assertStringContainsString('di luar batas pengajuan', $e->getMessage());
        }

        // 5.000.000 -> accept
        $resMin = $this->koperasiService->calculateLoanInterestAndInstallment(5000000, 12);
        $this->assertEquals(5000000, $resMin['total_kewajiban'] - $resMin['total_jasa']);

        // 50.000.000 -> accept
        $resMax = $this->koperasiService->calculateLoanInterestAndInstallment(50000000, 12);
        $this->assertEquals(50000000, $resMax['total_kewajiban'] - $resMax['total_jasa']);

        // 50.000.001 -> reject
        try {
            $this->koperasiService->calculateLoanInterestAndInstallment(50000001, 12);
            $this->fail('Expected exception for loan amount above Rp50.000.000');
        } catch (Exception $e) {
            $this->assertStringContainsString('di luar batas pengajuan', $e->getMessage());
        }
    }

    /** @test */
    public function it_validates_discrete_tenors_and_rejects_arbitrary_tenors()
    {
        // 6, 12, 18, 24 -> accept
        foreach ([6, 12, 18, 24] as $tenor) {
            $calc = $this->koperasiService->calculateLoanInterestAndInstallment(10000000, $tenor);
            $this->assertIsArray($calc);
        }

        // Arbitrary tenors -> reject
        foreach ([1, 3, 5, 10, 15, 20, 36, 48] as $invalidTenor) {
            try {
                $this->koperasiService->calculateLoanInterestAndInstallment(10000000, $invalidTenor);
                $this->fail("Expected exception for invalid tenor {$invalidTenor}");
            } catch (Exception $e) {
                $this->assertStringContainsString('Tenor tidak valid', $e->getMessage());
            }
        }
    }

    /** @test */
    public function it_verifies_exact_rate_matrix_mapping()
    {
        $this->assertEquals(5.0, KoperasiService::getRatePercent(6));
        $this->assertEquals(7.0, KoperasiService::getRatePercent(12));
        $this->assertEquals(8.5, KoperasiService::getRatePercent(18));
        $this->assertEquals(10.0, KoperasiService::getRatePercent(24));

        $this->assertEquals(0.05, KoperasiService::getRateDecimal(6));
        $this->assertEquals(0.07, KoperasiService::getRateDecimal(12));
        $this->assertEquals(0.085, KoperasiService::getRateDecimal(18));
        $this->assertEquals(0.10, KoperasiService::getRateDecimal(24));
    }

    /** @test */
    public function it_neutralizes_client_rate_and_financial_manipulation()
    {
        $member = $this->createEligibleMember();

        // Client attempts to inject persen_jasa = 1% on 12 months (official is 7%)
        $loan = $this->loanService->submitLoan(
            $member->id,
            10000000,
            12,
            1, // Malicious rate attempt
            'Modal Usaha',
            'BCA',
            '1234567890',
            'Budi Santoso',
            'Catatan'
        );

        // Server must enforce official 7% rate
        $this->assertEquals(7.00, (float)$loan->persen_jasa);
        $this->assertEquals(700000.00, (float)$loan->total_jasa);
        $this->assertEquals(10700000.00, (float)$loan->total_kewajiban);
        $this->assertEquals(891667.00, round((float)$loan->angsuran_per_bulan));
    }

    /** @test */
    public function it_verifies_financial_calculation_and_installment_invariants()
    {
        $member = $this->createEligibleMember();

        $loan = $this->loanService->submitLoan(
            $member->id,
            10000000,
            12,
            null,
            'Modal Usaha',
            'BCA',
            '1234567890',
            'Budi Santoso'
        );

        $this->assertEquals(10000000.00, (float)$loan->jumlah_pinjaman);
        $this->assertEquals(700000.00, (float)$loan->total_jasa);
        $this->assertEquals(10700000.00, (float)$loan->total_kewajiban);

        // Disburse & activate loan to generate installments
        $loan->status = 'disbursed';
        $loan->save();
        $this->loanService->activateLoan($loan->id);

        $installments = LoanInstallment::where('loan_id', $loan->id)->get();
        $this->assertCount(12, $installments);

        $sumPokok = round($installments->sum('pokok_angsuran'), 2);
        $sumJasa = round($installments->sum('jasa_angsuran'), 2);
        $sumTotal = round($installments->sum('total_angsuran'), 2);

        // Verify Invariants:
        // 1. SUM(pokok_angsuran) = jumlah_pinjaman
        $this->assertEquals((float)$loan->jumlah_pinjaman, $sumPokok);

        // 2. SUM(jasa_angsuran) = total_jasa
        $this->assertEquals((float)$loan->total_jasa, $sumJasa);

        // 3. SUM(total_angsuran) = total_kewajiban
        $this->assertEquals((float)$loan->total_kewajiban, $sumTotal);

        // 4. Per installment total_angsuran = pokok_angsuran + jasa_angsuran
        foreach ($installments as $inst) {
            $this->assertEquals(
                (float)$inst->total_angsuran,
                round((float)$inst->pokok_angsuran + (float)$inst->jasa_angsuran, 2)
            );
            $this->assertEquals(0, (float)$inst->denda);
        }
    }

    /** @test */
    public function it_guarantees_zero_rounding_discrepancy_with_terminal_adjustment()
    {
        $member = $this->createEligibleMember();

        // 10.000.000 with 12 months generates 891.666,666... per month
        $loan = $this->loanService->submitLoan(
            $member->id,
            10000000,
            12,
            null,
            'Investasi',
            'BCA',
            '1234567890',
            'Budi'
        );

        $loan->status = 'disbursed';
        $loan->save();
        $this->loanService->activateLoan($loan->id);

        $installments = LoanInstallment::where('loan_id', $loan->id)->orderBy('angsuran_ke')->get();

        $sumTotal = 0;
        foreach ($installments as $inst) {
            $sumTotal += (float)$inst->total_angsuran;
        }

        $this->assertEquals(10700000.00, round($sumTotal, 2));
    }

    /** @test */
    public function it_enforces_trusted_savings_amounts_and_prevents_arbitrary_nominal()
    {
        $member = $this->createEligibleMember();

        // Attempting to create Simpanan Pokok with arbitrary amount (Rp1) must fail
        $newMemberUser = User::factory()->create();
        $newMember = Member::create([
            'user_id' => $newMemberUser->id,
            'nik' => (string) rand(1000000000000000, 9999999999999999),
            'tanggal_lahir' => '1995-01-01',
            'alamat' => 'Jl. Kebon Jeruk',
            'telepon' => '08123456788',
            'pekerjaan' => 'Wiraswasta',
            'perusahaan' => 'Mandiri',
            'status' => 'active',
        ]);

        try {
            $this->savingsService->createPokok($newMember->id, 1);
            $this->fail('Expected exception when creating Simpanan Pokok with arbitrary nominal');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('55.000', $e->getMessage());
        }

        // Creating with null or 55.000 must succeed and persist exactly 55000
        $pokok = $this->savingsService->createPokok($newMember->id);
        $this->assertEquals(55000.00, (float)$pokok->nominal);

        // Attempting to create Simpanan Wajib with arbitrary amount (Rp10.000) must fail
        try {
            $this->savingsService->createWajib($newMember->id, 10000, '2026-11-01');
            $this->fail('Expected exception when creating Simpanan Wajib with arbitrary nominal');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('100.000', $e->getMessage());
        }

        // Creating with null or 100.000 must succeed and persist exactly 100000
        $wajib = $this->savingsService->createWajib($newMember->id, null, '2026-11-01');
        $this->assertEquals(100000.00, (float)$wajib->nominal);
    }
}
