@extends('layouts.app')

@section('title', 'Detail Pinjaman Modal ' . $loan->nomor_pinjaman . ' — KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="margin-bottom: 6px;">
                <a href="{{ route('karyawan.loans.index') }}" style="color: #64748b; font-size: 13px; text-decoration: none; font-weight: 600;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pinjaman
                </a>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-file-contract" style="color: #4f46e5; margin-right: 8px;"></i>
                Rincian Pinjaman Modal: {{ $loan->nomor_pinjaman }}
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Pelacakan status persetujuan Finance, serah terima fisik uang di Kasir, dan jadwal angsuran payroll.
            </p>
        </div>
        <div>
            @if(in_array($loan->status, ['submitted', 'under_review']))
                <span style="background: #fef3c7; color: #92400e; padding: 8px 16px; border-radius: 999px; font-weight: 800; font-size: 13px; border: 1px solid #fde68a; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-clock"></i> MENUNGGU PERSETUJUAN FINANCE
                </span>
            @elseif($loan->status === 'approved')
                <span style="background: #dbeafe; color: #1e40af; padding: 8px 16px; border-radius: 999px; font-weight: 800; font-size: 13px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-check"></i> DISETUJUI — SIAP AMBIL DI KASIR
                </span>
            @elseif(in_array($loan->status, ['disbursed', 'active']))
                <span style="background: #ecfdf5; color: #047857; padding: 8px 16px; border-radius: 999px; font-weight: 800; font-size: 13px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-money-bill-transfer"></i> AKTIF BERJALAN (PAYROLL CUT)
                </span>
            @elseif($loan->status === 'paid_off')
                <span style="background: #f1f5f9; color: #334155; padding: 8px 16px; border-radius: 999px; font-weight: 800; font-size: 13px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-flag-checkered"></i> LUNAS
                </span>
            @elseif($loan->status === 'rejected')
                <span style="background: #fef2f2; color: #991b1b; padding: 8px 16px; border-radius: 999px; font-weight: 800; font-size: 13px; border: 1px solid #fecaca; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-xmark"></i> DITOLAK
                </span>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px;">
        <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #059669;"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

<!-- LIVE STATUS PROGRESS STEPPER -->
<div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;">
    <div style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px;">
        Pelacakan Siklus Pinjaman Modal:
    </div>

    @php
        $step1 = true; // submitted
        $step2 = in_array($loan->status, ['approved', 'disbursed', 'active', 'paid_off']); // finance approved
        $step3 = in_array($loan->status, ['disbursed', 'active', 'paid_off']); // cashier cash disbursed
        $step4 = $loan->status === 'paid_off'; // paid off
    @endphp

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; position: relative;">
        <!-- STEP 1 -->
        <div style="border-top: 4px solid #4f46e5; padding-top: 10px;">
            <div style="font-size: 11px; font-weight: 800; color: #4f46e5;">LANGKAH 1</div>
            <div style="font-size: 14px; font-weight: 700; color: #0f172a;">Pengajuan Karyawan</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ $loan->submitted_at ? $loan->submitted_at->format('d/m/Y H:i') : '-' }}</div>
        </div>

        <!-- STEP 2 -->
        <div style="border-top: 4px solid {{ $step2 ? '#2563eb' : '#e2e8f0' }}; padding-top: 10px;">
            <div style="font-size: 11px; font-weight: 800; color: {{ $step2 ? '#2563eb' : '#94a3b8' }};">LANGKAH 2</div>
            <div style="font-size: 14px; font-weight: 700; color: {{ $step2 ? '#0f172a' : '#94a3b8' }};">Persetujuan Finance</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                @if($loan->approved_at)
                    {{ $loan->approved_at->format('d/m/Y H:i') }} (Disetujui)
                @else
                    Sedang dievaluasi
                @endif
            </div>
        </div>

        <!-- STEP 3 -->
        <div style="border-top: 4px solid {{ $step3 ? '#059669' : ($loan->status === 'approved' ? '#f59e0b' : '#e2e8f0') }}; padding-top: 10px;">
            <div style="font-size: 11px; font-weight: 800; color: {{ $step3 ? '#059669' : ($loan->status === 'approved' ? '#f59e0b' : '#94a3b8') }};">LANGKAH 3</div>
            <div style="font-size: 14px; font-weight: 700; color: {{ $step3 ? '#0f172a' : ($loan->status === 'approved' ? '#d97706' : '#94a3b8') }};">Pencairan Kasir (Fisik)</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                @if($loan->disbursed_at)
                    {{ $loan->disbursed_at->format('d/m/Y H:i') }} (Uang Diterima)
                @elseif($loan->status === 'approved')
                    <span style="color: #d97706; font-weight: 700;">Siap Ambil di Loket Kasir!</span>
                @else
                    Menunggu approval Finance
                @endif
            </div>
        </div>

        <!-- STEP 4 -->
        <div style="border-top: 4px solid {{ $step4 ? '#10b981' : '#e2e8f0' }}; padding-top: 10px;">
            <div style="font-size: 11px; font-weight: 800; color: {{ $step4 ? '#10b981' : '#94a3b8' }};">LANGKAH 4</div>
            <div style="font-size: 14px; font-weight: 700; color: {{ $step4 ? '#0f172a' : '#94a3b8' }};">Cicilan Slip Gaji</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                {{ $loan->installments->where('status', 'paid')->count() }} dari {{ $loan->tenor }} Angsuran Lunas
            </div>
        </div>
    </div>
</div>

<!-- ALERT PETUNJUK SESUAI STATUS -->
@if($loan->status === 'approved')
    <div style="background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #2563eb; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div style="flex: 1;">
            <h3 style="font-size: 16px; font-weight: 800; color: #1e3a8a; margin: 0 0 6px 0;">
                PINJAMAN DISETUJUI OLEH FINANCE — SILAKAN AMBIL UANG TUNAI DI KASIR!
            </h3>
            <p style="font-size: 13px; color: #1e40af; margin: 0 0 10px 0; line-height: 1.5;">
                Tim Finance telah memverifikasi permohonan Anda dan memastikan cadangan kas mencukupi. Silakan datang ke <strong>Loket Kasir Kantor KOPIN</strong> dengan membawa identitas karyawan untuk menandatangani Bukti Kas Keluar (BKK) dan menerima uang tunai fisik sebesar <strong>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</strong>.
            </p>
            @if($loan->approval_note)
                <div style="background: #dbeafe; padding: 8px 12px; border-radius: 8px; font-size: 12px; color: #1e3a8a;">
                    <strong>Catatan Finance:</strong> {{ $loan->approval_note }}
                </div>
            @endif
        </div>
    </div>
@elseif(in_array($loan->status, ['disbursed', 'active']))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 16px; padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: #059669; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <div style="font-size: 13px; color: #065f46; line-height: 1.5;">
            <strong>Uang Tunai Telah Diterima Fisik di Kasir!</strong><br>
            Pinjaman telah dicairkan pada <strong>{{ $loan->disbursed_at ? $loan->disbursed_at->format('d/m/Y H:i') : '-' }}</strong>
            @if($loan->disbursement)
                &bull; No. Referensi BKK: <strong>{{ $loan->disbursement->nomor_referensi }}</strong>
                &bull; Petugas Kasir: <strong>{{ $loan->disbursement->cashier?->name ?? $loan->disbursement->admin?->name ?? 'Kasir Loket' }}</strong>
            @endif.<br>
            Angsuran bulanan sebesar <strong>Rp {{ number_format($loan->angsuran_per_bulan, 0, ',', '.') }}</strong> akan otomatis dipotong dari slip gaji setiap periode payroll bulanan.
        </div>
    </div>
@endif

<!-- GRID: INFORMASI PINJAMAN & JADWAL ANGSURAN -->
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
    <!-- INFORMASI PINJAMAN -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 16px 0;">
            <i class="fa-solid fa-receipt" style="color: #4f46e5; margin-right: 6px;"></i>
            Ringkasan Pinjaman
        </h3>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Nomor Pinjaman:</span>
                <strong style="color: #0f172a;">{{ $loan->nomor_pinjaman }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Plafon Disetujui:</span>
                <strong style="color: #0f172a; font-size: 15px;">Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Tenor:</span>
                <strong style="color: #0f172a;">{{ $loan->tenor }} Bulan</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Jasa Koperasi (Flat):</span>
                <strong style="color: #d97706;">{{ $loan->persen_jasa }}% / Tahun</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Total Jasa:</span>
                <strong style="color: #d97706;">Rp {{ number_format($loan->total_jasa, 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Total Pengembalian:</span>
                <strong style="color: #0f172a;">Rp {{ number_format($loan->total_kewajiban, 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Potongan Gaji / Bln:</span>
                <strong style="color: #059669; font-size: 15px;">Rp {{ number_format($loan->angsuran_per_bulan, 0, ',', '.') }}</strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                <span style="color: #64748b;">Metode Pencairan:</span>
                <strong style="color: #0f172a;">Tunai Fisik di Loket Kasir</strong>
            </div>

            <div style="display: flex; justify-content: space-between; padding-top: 4px;">
                <span style="color: #64748b;">Tujuan:</span>
                <strong style="color: #334155; text-align: right;">{{ $loan->tujuan_pinjaman }}</strong>
            </div>
        </div>
    </div>

    <!-- JADWAL ANGSURAN PAYROLL -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;">
                <i class="fa-solid fa-calendar-check" style="color: #4f46e5; margin-right: 6px;"></i>
                Jadwal Pemotongan Angsuran Payroll
            </h3>
            <span style="font-size: 12px; color: #64748b;">Tenor: {{ $loan->tenor }} Bulan</span>
        </div>

        @if($loan->installments->isEmpty())
            <div style="text-align: center; padding: 35px 20px; color: #94a3b8; font-size: 13px;">
                <i class="fa-solid fa-hourglass-start" style="font-size: 32px; margin-bottom: 10px; opacity: 0.5;"></i>
                <div>Jadwal angsuran akan dibuat otomatis segera setelah uang tunai diserahkan oleh Kasir di loket.</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b;">
                            <th style="padding: 10px 12px;">Angsuran Ke</th>
                            <th style="padding: 10px 12px;">Jatuh Tempo</th>
                            <th style="padding: 10px 12px;">Pokok</th>
                            <th style="padding: 10px 12px;">Jasa</th>
                            <th style="padding: 10px 12px;">Total Potong Gaji</th>
                            <th style="padding: 10px 12px; text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->installments as $ins)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px 12px; font-weight: 700; color: #0f172a;">
                                Bulan Ke-{{ $ins->angsuran_ke }}
                            </td>
                            <td style="padding: 10px 12px; color: #64748b;">
                                {{ $ins->jatuh_tempo ? $ins->jatuh_tempo->format('d/m/Y') : '-' }}
                            </td>
                            <td style="padding: 10px 12px; color: #475569;">
                                Rp {{ number_format($ins->angsuran_pokok, 0, ',', '.') }}
                            </td>
                            <td style="padding: 10px 12px; color: #d97706;">
                                Rp {{ number_format($ins->angsuran_jasa, 0, ',', '.') }}
                            </td>
                            <td style="padding: 10px 12px; font-weight: 700; color: #0f172a;">
                                Rp {{ number_format($ins->total_angsuran, 0, ',', '.') }}
                            </td>
                            <td style="padding: 10px 12px; text-align: right;">
                                @if($ins->status === 'paid')
                                    <span style="background: #ecfdf5; color: #047857; padding: 3px 8px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                        <i class="fa-solid fa-check"></i> Terpotong
                                    </span>
                                @else
                                    <span style="background: #f1f5f9; color: #64748b; padding: 3px 8px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                        Menunggu Payroll
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
