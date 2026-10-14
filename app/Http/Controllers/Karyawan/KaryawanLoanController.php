<?php

namespace App\Http\Controllers\Karyawan;

use App\Constants\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class KaryawanLoanController extends Controller
{
    protected ActivityLogService $activityLogService;
    protected NotificationService $notificationService;

    public function __construct(
        ActivityLogService $activityLogService,
        NotificationService $notificationService
    ) {
        $this->activityLogService = $activityLogService;
        $this->notificationService = $notificationService;
    }

    /**
     * Pastikan akun karyawan memiliki record Member aktif.
     */
    protected function getOrCreateMember(User $user): Member
    {
        $member = $user->member;
        if (!$member) {
            $member = Member::create([
                'user_id' => $user->id,
                'nik' => 'KRY-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'tanggal_lahir' => '1995-01-01',
                'alamat' => 'Kantor KSP KOPIN Pusat',
                'telepon' => '08123456789',
                'pekerjaan' => 'Karyawan Internal',
                'perusahaan' => 'KSP KOPIN',
                'status' => 'active',
            ]);
        }
        return $member;
    }

    /**
     * Tampilkan daftar portofolio pinjaman karyawan.
     */
    public function index()
    {
        $user = auth('karyawan')->user();
        $member = $this->getOrCreateMember($user);

        $loans = Loan::with(['installments', 'disbursement', 'finance'])
            ->where('member_id', $member->id)
            ->latest()
            ->get();

        $activeLoan = $loans->first(function ($l) {
            return in_array($l->status, ['submitted', 'under_review', 'approved', 'disbursed', 'active']);
        });

        return view('karyawan.loans.index', compact('user', 'member', 'loans', 'activeLoan'));
    }

    /**
     * Formulir pengajuan pinjaman modal karyawan.
     */
    public function create()
    {
        $user = auth('karyawan')->user();
        $member = $this->getOrCreateMember($user);

        // Single Active Loan Rule (Opsi A - Strict Hard-Block)
        $activeLoan = Loan::where('member_id', $member->id)
            ->whereIn('status', ['submitted', 'under_review', 'approved', 'disbursed', 'active'])
            ->latest()
            ->first();

        if ($activeLoan) {
            $statusLabel = $activeLoan->status === 'approved' 
                ? 'telah disetujui Finance dan siap diambil tunai di Kasir'
                : ($activeLoan->status === 'submitted' ? 'sedang dalam antrean evaluasi Finance' : 'masih aktif berjalan');

            return redirect()->route('karyawan.loans.index')->with(
                'error',
                "Anda masih memiliki pinjaman modal berjalan {$activeLoan->nomor_pinjaman} yang {$statusLabel}. Selesaikan atau tunggu proses pinjaman berjalan sebelum mengajukan pinjaman baru."
            );
        }

        return view('karyawan.loans.create', compact('user', 'member'));
    }

    /**
     * Simpan pengajuan pinjaman modal karyawan ke database.
     */
    public function store(Request $request)
    {
        $user = auth('karyawan')->user();
        $member = $this->getOrCreateMember($user);

        // Sanitasi format rupiah pada input
        $nominalRaw = $request->input('jumlah_pinjaman');
        $nominalClean = (float) preg_replace('/[^0-9]/', '', (string)$nominalRaw);

        $request->merge(['jumlah_pinjaman' => $nominalClean]);

        $request->validate([
            'jumlah_pinjaman' => ['required', 'numeric', 'min:1000000', 'max:20000000'],
            'tenor' => ['required', 'integer', 'in:3,6,10,12,18,24'],
            'tujuan_pinjaman' => ['required', 'string', 'min:5', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'jumlah_pinjaman.min' => 'Batas minimum pinjaman modal karyawan adalah Rp 1.000.000.',
            'jumlah_pinjaman.max' => 'Batas maksimum pinjaman modal karyawan adalah Rp 20.000.000.',
            'tenor.in' => 'Tenor pinjaman yang tersedia: 3, 6, 10, 12, 18, atau 24 bulan.',
            'tujuan_pinjaman.required' => 'Mohon jelaskan tujuan / keperluan pinjaman modal Anda.',
        ]);

        // Cek Single Active Loan
        $activeLoan = Loan::where('member_id', $member->id)
            ->whereIn('status', ['submitted', 'under_review', 'approved', 'disbursed', 'active'])
            ->latest()
            ->first();

        if ($activeLoan) {
            return redirect()->route('karyawan.loans.index')->with(
                'error',
                'Anda masih memiliki pinjaman modal aktif atau sedang dalam peninjauan.'
            );
        }

        try {
            $loan = DB::transaction(function () use ($request, $user, $member, $nominalClean) {
                $tenor = (int) $request->tenor;
                $persenJasa = 5.0; // 5% Flat per tahun (benefit staf internal)

                // Perhitungan kewajiban
                $totalJasa = ($nominalClean * ($persenJasa / 100)) * ($tenor / 12);
                $totalKewajiban = $nominalClean + $totalJasa;
                $angsuranPerBulan = ceil($totalKewajiban / $tenor);

                $loan = Loan::create([
                    'member_id' => $member->id,
                    'nomor_pinjaman' => 'PINJ-KRY-' . date('Ym') . '-' . strtoupper(Str::random(4)),
                    'jumlah_pinjaman' => $nominalClean,
                    'tenor' => $tenor,
                    'tujuan_pinjaman' => $request->tujuan_pinjaman,
                    'keterangan' => $request->keterangan ?? 'Pengajuan Pinjaman Modal Karyawan Internal via Payroll',
                    'persen_jasa' => $persenJasa,
                    'total_jasa' => $totalJasa,
                    'total_kewajiban' => $totalKewajiban,
                    'angsuran_per_bulan' => $angsuranPerBulan,
                    'bank_name' => 'LOKET KASIR KOPIN (Pencairan Kas Fisik Tunai)',
                    'bank_account_number' => 'TUNAI-LOKET',
                    'bank_account_owner' => $user->name,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                // Notifikasi ke seluruh staf Finance
                $financeUsers = User::where('role', 'finance')->get();
                foreach ($financeUsers as $fin) {
                    $this->notificationService->send(
                        $fin->id,
                        NotificationType::LOAN_SUBMITTED,
                        'Pengajuan Pinjaman Karyawan Baru',
                        "Karyawan {$user->name} mengajukan pinjaman modal Rp " . number_format($nominalClean, 0, ',', '.') . " ({$loan->nomor_pinjaman}). Menunggu review Finance.",
                        route('finance.loans.show', $loan->id)
                    );
                }

                // Catat Log Audit
                $this->activityLogService->record(
                    'karyawan.loan_submitted',
                    $loan,
                    "Karyawan {$user->name} mengajukan pinjaman modal Rp " . number_format($nominalClean, 0, ',', '.') . " tenor {$tenor} bulan",
                    [
                        'jumlah_pinjaman' => $nominalClean,
                        'tenor' => $tenor,
                        'angsuran_per_bulan' => $angsuranPerBulan,
                    ],
                    $user->id
                );

                return $loan;
            });

            return redirect()->route('karyawan.loans.show', $loan->id)->with(
                'success',
                "Pengajuan pinjaman modal {$loan->nomor_pinjaman} berhasil dikirim! Silakan menunggu evaluasi & persetujuan Finance sebelum dana tunai dapat dicairkan di loket Kasir."
            );
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail status pinjaman, approval status, dan jadwal angsuran payroll.
     */
    public function show(Loan $loan)
    {
        $user = auth('karyawan')->user();
        $member = $this->getOrCreateMember($user);

        if ($loan->member_id !== $member->id) {
            abort(403, 'Akses tidak diizinkan untuk data pinjaman ini.');
        }

        $loan->load(['installments.payments', 'disbursement.cashier', 'finance']);

        return view('karyawan.loans.show', compact('user', 'member', 'loan'));
    }
}
