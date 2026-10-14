@extends('layouts.app')

@section('title', 'Portal Anggota Karyawan — KOPIN Enterprise')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-id-badge" style="color: #4f46e5; margin-right: 8px;"></i>
                Portal Anggota Karyawan (Internal Staff)
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Mekanisme Simpanan Sukarela & Angsuran Pinjaman Otomatis Potong Gaji Bulanan (Payroll Deduction)
            </p>
        </div>
        <div>
            <span style="background: #eef2ff; color: #4338ca; padding: 8px 14px; border-radius: 999px; font-weight: 700; font-size: 13px; border: 1px solid #c7d2fe; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-user-tie"></i> Karyawan KSP KOPIN
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px;">
        <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 18px;"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

<!-- HERO CARD: SALDO SIMPANAN SUKARELA KARYAWAN -->
<div style="background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #3b82f6 100%); border-radius: 18px; padding: 26px; color: #FFFFFF; box-shadow: 0 10px 25px rgba(67, 56, 202, 0.25); margin-bottom: 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                Tabungan Sukarela Payroll
            </span>
            <h2 style="font-size: 20px; font-weight: 800; margin: 8px 0 0 0;">{{ $user->name }}</h2>
        </div>
        <div style="font-size: 13px; opacity: 0.9;">
            <i class="fa-solid fa-building-user"></i> Staf Karyawan KOPIN
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 4px;">Total Simpanan Sukarela Terkumpul</div>
            <div style="font-size: 24px; font-weight: 800; color: #93c5fd;">Rp {{ number_format($totalSukarela, 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">Dipupuk otomatis dari potong gaji bulanan</div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 4px;">Nominal Potong Gaji Sukarela/Bulan</div>
            <div style="font-size: 24px; font-weight: 800; color: #86efac;">
                Rp {{ number_format($payrollSetting->nominal_sukarela_bulanan, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">
                Status Potong Gaji: {{ $payrollSetting->is_active ? 'AKTIF' : 'NONAKTIF' }}
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 4px;">Kewajiban Pokok/Wajib</div>
            <div style="font-size: 20px; font-weight: 800; color: #fde047;">BEBAS (RP 0)</div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">Karyawan dikecualikan dari Simpanan Pokok & Wajib</div>
        </div>
    </div>
</div>

<!-- GRID: PENGATURAN POTONG GAJI & PROYEKSI RESIGN SETTLEMENT -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px;">
    <!-- 1. PENGATURAN POTONG GAJI SIMPANAN SUKARELA -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Pengaturan Potong Gaji</h3>
                <div style="font-size: 12px; color: #64748b;">Simpanan Sukarela Bulanan</div>
            </div>
        </div>

        <form action="{{ route('karyawan.payroll_setting.update') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Nominal Potong Gaji Sukarela (Rp / Bulan)
                </label>
                <input type="number" name="nominal_sukarela_bulanan" value="{{ old('nominal_sukarela_bulanan', (int)$payrollSetting->nominal_sukarela_bulanan) }}" required min="0" step="10000" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-size: 15px; font-weight: 700; box-sizing: border-box;">
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                    Nominal ini akan otomatis dipotong dari gaji setiap akhir bulan.
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #334155; font-weight: 600; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ $payrollSetting->is_active ? 'checked' : '' }} style="transform: scale(1.2);">
                    <span>Aktifkan pemotongan otomatis bulanan</span>
                </label>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background: #4f46e5; color: #FFFFFF; border: none; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                    <i class="fa-solid fa-save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>

        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed #e2e8f0;">
            <form action="{{ route('karyawan.simulate_payroll_cut') }}" method="POST">
                @csrf
                <button type="submit" style="width: 100%; background: #059669; color: #FFFFFF; border: none; padding: 10px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
                    <i class="fa-solid fa-play"></i> Simulasi Eksekusi Potong Gaji Bulan Ini
                </button>
            </form>
        </div>
    </div>

    <!-- 2. PROYEKSI SETTLEMENT SAAT RESIGN (PRD RULE) -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Proyeksi Settlement Resign</h3>
                <div style="font-size: 12px; color: #64748b;">Perhitungan Hak Bersih Pengunduran Diri</div>
            </div>
        </div>

        <p style="font-size: 12px; color: #64748b; line-height: 1.5; margin: 0 0 16px 0;">
            SOP Karyawan KOPIN: Jika karyawan mengundurkan diri (resign), seluruh saldo simpanan sukarela dicairkan penuh dikurangi sisa kewajiban pinjaman yang belum lunas.
        </p>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; font-size: 13px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: #64748b;">Total Simpanan Sukarela:</span>
                <strong style="color: #059669;">Rp {{ number_format($resignationSummary['total_sukarela'], 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: #64748b;">Sisa Hutang Pinjaman:</span>
                <strong style="color: #dc2626;">- Rp {{ number_format($resignationSummary['total_sisa_pinjaman'], 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 2px dashed #cbd5e1; font-size: 15px; margin-top: 10px;">
                <span style="font-weight: 800; color: #0f172a;">Hak Bersih Diterima:</span>
                <strong style="font-weight: 800; color: {{ $resignationSummary['net_payout'] >= 0 ? '#166534' : '#991b1b' }};">
                    Rp {{ number_format($resignationSummary['net_payout'], 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <div style="font-size: 11px; color: #64748b; margin-top: 12px;">
            @if($resignationSummary['is_surplus'])
                <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> Saldo simpanan sukarela surplus menutup sisa kewajiban.
            @else
                <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626;"></i> Sisa kewajiban pinjaman melebihi tabungan sukarela.
            @endif
        </div>
    </div>
</div>

<!-- SECTION 3: PINJAMAN KARYAWAN & RIWAYAT POTONG GAJI -->
<div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #4f46e5; margin-right: 6px;"></i>
            Riwayat Pemotongan Gaji Otomatis Terakhir
        </h3>
        <span style="font-size: 12px; color: #64748b;">Simpanan Sukarela & Angsuran</span>
    </div>

    @if($recentPayrollDeductions->isEmpty())
        <div style="text-align: center; padding: 30px; color: #94a3b8; font-size: 13px;">
            Belum ada catatan pemotongan payroll otomatis. Silakan jalankan simulasi potong gaji.
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b;">
                        <th style="padding: 10px 12px;">No. Referensi</th>
                        <th style="padding: 10px 12px;">Tanggal</th>
                        <th style="padding: 10px 12px;">Keterangan</th>
                        <th style="padding: 10px 12px; text-align: right;">Nominal Dipotong</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPayrollDeductions as $trx)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 12px; font-weight: 700; color: #0f172a;">{{ $trx->nomor_referensi }}</td>
                        <td style="padding: 10px 12px; color: #64748b;">{{ $trx->paid_at ? $trx->paid_at->format('d/m/Y H:i') : '-' }}</td>
                        <td style="padding: 10px 12px; color: #475569;">{{ $trx->notes }}</td>
                        <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: #059669;">
                            Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
