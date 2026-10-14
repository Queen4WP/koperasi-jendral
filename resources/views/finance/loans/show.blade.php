@extends('layouts.app')

@section('title', 'Evaluasi Pinjaman ' . $loan->nomor_pinjaman . ' — Finance POV')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="{{ route('finance.dashboard') }}" style="color: #64748b; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Finance
            </a>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0;">
                Evaluasi Pinjaman #{{ $loan->nomor_pinjaman }}
            </h1>
        </div>
        <div>
            <span style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 999px;">
                Status: {{ strtoupper($loan->status) }}
            </span>
        </div>
    </div>
</div>

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px;">
    <!-- KOLOM KIRI: DATA PENGAJUAN & SIMULASI 10% CADANGAN -->
    <div>
        <!-- KARTU SIMULASI 10% POCKET RESERVE -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 22px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator" style="color: #2563eb;"></i>
                Simulasi Kepatuhan Likuiditas Cadangan 10% (Pocket Reserve Rule)
            </h3>

            <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                    <div>
                        <span style="color: #64748b;">Pocket Kas Likuid Saat Ini:</span>
                        <div style="font-size: 16px; font-weight: 700; color: #0f172a;">Rp {{ number_format($reserveCheck['current_pocket'], 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="color: #64748b;">Nominal Pengajuan Pinjaman:</span>
                        <div style="font-size: 16px; font-weight: 800; color: #2563eb;">Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="color: #64748b;">Syarat Minimum 10% Cadangan:</span>
                        <div style="font-size: 16px; font-weight: 700; color: #b45309;">Rp {{ number_format($reserveCheck['minimum_reserve'], 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span style="color: #64748b;">Sisa Pocket Kas Pasca-Pencairan:</span>
                        <div style="font-size: 16px; font-weight: 800; color: {{ $reserveCheck['allowed'] ? '#16a34a' : '#dc2626' }};">
                            Rp {{ number_format($reserveCheck['pocket_after'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            @if($reserveCheck['allowed'])
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 10px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #16a34a;"></i>
                    <div>
                        <strong>Likuiditas Memenuhi Syarat:</strong> Kas koperasi masih menyisakan lebih dari 10% total simpanan wajib setelah pinjaman ini dicairkan. Aman untuk disetujui.
                    </div>
                </div>
            @else
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 13px; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px; color: #dc2626; margin-top: 2px;"></i>
                    <div>
                        <strong>PERINGATAN DEFISIT CADANGAN:</strong>
                        <div>{{ $reserveCheck['reason'] }}</div>
                        <div style="margin-top: 4px; font-size: 12px; color: #7f1d1d;">
                            Alasan transparansi ini akan otomatis diinformasikan kepada anggota jika pinjaman ditolak.
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- DETAIL PINJAMAN -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 16px 0;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: #475569;"></i>
                Rincian Pinjaman & Angsuran
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13px;">
                <div>
                    <span style="color: #64748b;">Jumlah Pinjaman Pokok:</span>
                    <div style="font-size: 16px; font-weight: 700; color: #0f172a;">Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</div>
                </div>
                <div>
                    <span style="color: #64748b;">Tenor Waktu:</span>
                    <div style="font-size: 16px; font-weight: 700; color: #0f172a;">{{ $loan->tenor }} Bulan</div>
                </div>
                <div>
                    <span style="color: #64748b;">Bunga Jasa Koperasi:</span>
                    <div style="font-size: 14px; font-weight: 600; color: #475569;">{{ $loan->persen_jasa }}% (Rp {{ number_format($loan->total_jasa, 0, ',', '.') }})</div>
                </div>
                <div>
                    <span style="color: #64748b;">Angsuran Per Bulan:</span>
                    <div style="font-size: 15px; font-weight: 700; color: #0f172a;">Rp {{ number_format($loan->angsuran_per_bulan, 0, ',', '.') }} / bln</div>
                </div>
                <div style="grid-column: span 2;">
                    <span style="color: #64748b;">Tujuan Pembiayaan:</span>
                    <div style="font-size: 14px; font-weight: 600; color: #1e293b; background: #f8fafc; padding: 10px; border-radius: 8px; margin-top: 4px;">
                        {{ $loan->tujuan_pinjaman }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: DATA ANGGOTA & KEPUTUSAN FINANCE -->
    <div>
        <!-- DATA ANGGOTA & SALDO SIMPANAN -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 22px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 14px 0;">
                <i class="fa-solid fa-user-check" style="color: #475569;"></i>
                Profil Pemohon Pinjaman
            </h3>

            <div style="font-size: 13px; line-height: 1.6; color: #334155;">
                <div><strong>Nama:</strong> {{ $loan->member->user->name }}</div>
                <div><strong>NIK:</strong> {{ $loan->member->nik }}</div>
                <div><strong>Pekerjaan:</strong> {{ $loan->member->pekerjaan }}</div>
                <div><strong>Telepon:</strong> {{ $loan->member->telepon }}</div>

                <div style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed #e2e8f0;">
                    <span style="color: #64748b;">Total Saldo Simpanan Anggota:</span>
                    <div style="font-size: 18px; font-weight: 800; color: #059669;">
                        Rp {{ number_format($memberSavingsTotal, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                        Plafon pinjaman dibatasi oleh kapasitas simpanan yang dimiliki anggota.
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM KEPUTUSAN FINANCE -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 16px 0;">
                <i class="fa-solid fa-gavel" style="color: #2563eb;"></i>
                Keputusan Finance
            </h3>

            <!-- APPROVE FORM -->
            <form action="{{ route('finance.loans.approve', $loan->id) }}" method="POST" style="margin-bottom: 20px;">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Catatan Persetujuan (Note Approval Finance)
                    </label>
                    <textarea name="approval_note" rows="3" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; box-sizing: border-box;" placeholder="Contoh: Disetujui finance. Likuiditas pocket mencukupi di atas rasio 10%."></textarea>
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                        * Catatan ini akan dikirim ke Kasir dan muncul di live tracking anggota.
                    </div>
                </div>

                <button type="submit" style="width: 100%; background: #059669; color: #FFFFFF; border: none; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" {{ !$reserveCheck['allowed'] ? 'onclick="return confirm(\'PERINGATAN: Likuiditas cadangan di bawah 10%. Apakah Anda yakin ingin memaksakan persetujuan?\')"' : '' }}>
                    <i class="fa-solid fa-check-circle"></i> SETUJUI PINJAMAN (APPROVE)
                </button>
            </form>

            <div style="position: relative; text-align: center; margin: 18px 0;">
                <hr style="border: none; border-top: 1px solid #e2e8f0;">
                <span style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: #FFFFFF; padding: 0 10px; font-size: 11px; color: #94a3b8; font-weight: 700;">ATAU</span>
            </div>

            <!-- REJECT FORM -->
            <form action="{{ route('finance.loans.reject', $loan->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #991b1b; margin-bottom: 6px;">
                        Alasan Penolakan (Transparansi Alasan Reject)
                    </label>
                    <textarea name="rejection_reason" rows="3" required style="width: 100%; border: 1px solid #fca5a5; border-radius: 8px; padding: 10px; font-size: 13px; box-sizing: border-box;" placeholder="Sebutkan alasan penolakan secara jelas dan transparan...">{{ !$reserveCheck['allowed'] ? $reserveCheck['reason'] : '' }}</textarea>
                </div>

                <button type="submit" style="width: 100%; background: #dc2626; color: #FFFFFF; border: none; padding: 12px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-ban"></i> TOLAK PENGAJUAN (REJECT)
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
