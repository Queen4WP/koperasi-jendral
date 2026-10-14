@extends('layouts.app')

@section('title', 'Pinjaman Modal Karyawan — KOPIN Enterprise')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-hand-holding-dollar" style="color: #4f46e5; margin-right: 8px;"></i>
                Pinjaman Modal Karyawan (Internal Staff)
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Fasilitas pinjaman modal berbunga rendah dengan persetujuan Finance dan pencairan tunai di loket Kasir.
            </p>
        </div>
        <div>
            @if(!$activeLoan)
                <a href="{{ route('karyawan.loans.create') }}" class="btn btn-primary" style="background: #4f46e5; border-color: #4338ca; padding: 10px 18px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-plus-circle"></i> Ajukan Pinjaman Modal
                </a>
            @else
                <button type="button" class="btn btn-secondary" disabled style="opacity: 0.6; cursor: not-allowed; padding: 10px 18px; font-weight: 700; font-size: 13px;">
                    <i class="fa-solid fa-lock"></i> Pinjaman Berjalan Aktif
                </button>
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

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 18px; color: #dc2626;"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

<!-- WORKFLOW STEP BANNER -->
<div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;">
    <div style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 14px;">
        <i class="fa-solid fa-route" style="color: #4f46e5;"></i> Alur Operasional Pinjaman Modal Karyawan:
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
            <div style="font-size: 11px; font-weight: 800; color: #4f46e5; margin-bottom: 2px;">TAHAP 1: KARYAWAN</div>
            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Pengajuan Modal</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Input nominal & tenor via portal</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
            <div style="font-size: 11px; font-weight: 800; color: #2563eb; margin-bottom: 2px;">TAHAP 2: FINANCE</div>
            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Persetujuan Treasury</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Validasi cadangan kas 10% & approval</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
            <div style="font-size: 11px; font-weight: 800; color: #059669; margin-bottom: 2px;">TAHAP 3: KASIR</div>
            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Serah Terima Uang Tunai</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Karyawan tanda tangan BKK di loket kasir</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
            <div style="font-size: 11px; font-weight: 800; color: #d97706; margin-bottom: 2px;">TAHAP 4: PAYROLL</div>
            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Potong Gaji Bulanan</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Angsuran otomatis dari slip gaji</div>
        </div>
    </div>
</div>

<!-- KARTU STATUS PINJAMAN AKTIF (JIKA ADA) -->
@if($activeLoan)
<div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); border-radius: 18px; padding: 24px; color: #FFFFFF; box-shadow: 0 10px 25px rgba(67, 56, 202, 0.25); margin-bottom: 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
        <div>
            <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                Pinjaman Modal Berjalan
            </span>
            <h2 style="font-size: 20px; font-weight: 800; margin: 8px 0 0 0;">{{ $activeLoan->nomor_pinjaman }}</h2>
        </div>
        <div>
            @if(in_array($activeLoan->status, ['submitted', 'under_review']))
                <span style="background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-clock"></i> MENUNGGU PERSETUJUAN FINANCE
                </span>
            @elseif($activeLoan->status === 'approved')
                <span style="background: #dbeafe; color: #1e40af; padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; animation: pulse 2s infinite;">
                    <i class="fa-solid fa-circle-check"></i> DISETUJUI FINANCE — SIAP AMBIL DI KASIR!
                </span>
            @elseif(in_array($activeLoan->status, ['disbursed', 'active']))
                <span style="background: #d1fae5; color: #065f46; padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-money-bill-transfer"></i> AKTIF BERJALAN (PAYROLL CUT)
                </span>
            @endif
        </div>
    </div>

    @if($activeLoan->status === 'approved')
        <div style="background: rgba(59, 130, 246, 0.25); border: 1px solid rgba(147, 197, 253, 0.5); padding: 14px 18px; border-radius: 12px; margin-bottom: 16px; font-size: 13px; line-height: 1.5;">
            <i class="fa-solid fa-bell" style="color: #93c5fd; margin-right: 6px;"></i>
            <strong>Instruksi Pencairan:</strong> Finance telah menyetujui pinjaman modal Anda. Silakan menuju ke <strong>Loket Kasir Kantor KOPIN</strong> untuk menandatangani Bukti Kas Keluar (BKK) dan menerima uang tunai fisik sejumlah <strong>Rp {{ number_format($activeLoan->jumlah_pinjaman, 0, ',', '.') }}</strong>.
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
        <div style="background: rgba(255,255,255,0.12); padding: 14px 16px; border-radius: 10px;">
            <div style="font-size: 11px; opacity: 0.85;">Plafon Pinjaman</div>
            <div style="font-size: 20px; font-weight: 800; color: #93c5fd; margin-top: 2px;">
                Rp {{ number_format($activeLoan->jumlah_pinjaman, 0, ',', '.') }}
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 14px 16px; border-radius: 10px;">
            <div style="font-size: 11px; opacity: 0.85;">Tenor Pinjaman</div>
            <div style="font-size: 20px; font-weight: 800; color: #FFFFFF; margin-top: 2px;">
                {{ $activeLoan->tenor }} Bulan
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 14px 16px; border-radius: 10px;">
            <div style="font-size: 11px; opacity: 0.85;">Angsuran Bulanan (Payroll)</div>
            <div style="font-size: 20px; font-weight: 800; color: #86efac; margin-top: 2px;">
                Rp {{ number_format($activeLoan->angsuran_per_bulan, 0, ',', '.') }}
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 14px 16px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <a href="{{ route('karyawan.loans.show', $activeLoan->id) }}" style="background: #FFFFFF; color: #312e81; font-weight: 800; font-size: 12px; padding: 10px 16px; border-radius: 8px; text-decoration: none; width: 100%; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                Lihat Detail & Jadwal <i class="fa-solid fa-arrow-right" style="margin-left: 4px;"></i>
            </a>
        </div>
    </div>
</div>
@endif

<!-- DAFTAR RIWAYAT PINJAMAN KARYAWAN -->
<div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #4f46e5; margin-right: 6px;"></i>
            Riwayat Pinjaman Modal Karyawan
        </h3>
        <span style="font-size: 12px; color: #64748b;">Total: {{ $loans->count() }} Pengajuan</span>
    </div>

    @if($loans->isEmpty())
        <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
            <i class="fa-solid fa-file-invoice-dollar" style="font-size: 40px; margin-bottom: 12px; opacity: 0.4;"></i>
            <p style="font-size: 14px; margin: 0 0 12px 0;">Belum ada riwayat pengajuan pinjaman modal.</p>
            <a href="{{ route('karyawan.loans.create') }}" class="btn btn-primary btn-sm" style="background: #4f46e5; border-color: #4338ca;">
                Ajukan Pinjaman Modal Pertama
            </a>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b;">
                        <th style="padding: 10px 12px;">No. Pinjaman</th>
                        <th style="padding: 10px 12px;">Tanggal</th>
                        <th style="padding: 10px 12px;">Plafon</th>
                        <th style="padding: 10px 12px;">Tenor</th>
                        <th style="padding: 10px 12px;">Angsuran / Bln</th>
                        <th style="padding: 10px 12px;">Status</th>
                        <th style="padding: 10px 12px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $l)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: 700; color: #0f172a;">
                            {{ $l->nomor_pinjaman }}
                            <div style="font-size: 11px; color: #64748b; font-weight: normal;">{{ $l->tujuan_pinjaman }}</div>
                        </td>
                        <td style="padding: 12px; color: #64748b;">
                            {{ $l->created_at->format('d/m/Y') }}
                        </td>
                        <td style="padding: 12px; font-weight: 800; color: #0f172a;">
                            Rp {{ number_format($l->jumlah_pinjaman, 0, ',', '.') }}
                        </td>
                        <td style="padding: 12px; color: #475569;">
                            {{ $l->tenor }} Bln
                        </td>
                        <td style="padding: 12px; font-weight: 700; color: #059669;">
                            Rp {{ number_format($l->angsuran_per_bulan, 0, ',', '.') }}
                        </td>
                        <td style="padding: 12px;">
                            @if(in_array($l->status, ['submitted', 'under_review']))
                                <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                    Menunggu Finance
                                </span>
                            @elseif($l->status === 'approved')
                                <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                    Disetujui (Ambil di Kasir)
                                </span>
                            @elseif(in_array($l->status, ['disbursed', 'active']))
                                <span style="background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                    Aktif Berjalan
                                </span>
                            @elseif($l->status === 'paid_off')
                                <span style="background: #e2e8f0; color: #334155; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                    Lunas
                                </span>
                            @elseif($l->status === 'rejected')
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                    Ditolak
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="{{ route('karyawan.loans.show', $l->id) }}" style="display: inline-block; background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-decoration: none;">
                                Detail <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
