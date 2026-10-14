@extends('layouts.app')

@section('title', 'Finance & Treasury — Pengawasan Likuiditas 10% KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-chart-pie" style="color: #2563eb; margin-right: 8px;"></i>
                Dashboard Finance & Treasury
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Pengawasan Likuiditas Cadangan 10% Pocket Reserve, Persetujuan Pinjaman, & Rekapitulasi Kas Harian
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <span style="background: #eff6ff; color: #1d4ed8; padding: 8px 14px; border-radius: 999px; font-weight: 700; font-size: 13px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-building-columns"></i> KSP KOPIN Pusat
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

<!-- METRIK CADANGAN 10% POCKET RESERVE -->
<div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #0369a1 100%); border-radius: 18px; padding: 28px; color: #FFFFFF; box-shadow: 0 10px 25px rgba(30, 58, 138, 0.25); margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
        <div>
            <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                Prudential Treasury Reserve
            </span>
            <h2 style="font-size: 22px; font-weight: 800; margin: 8px 0 0 0;">Pengawasan Aturan 10% Pocket Reserve</h2>
        </div>
        <div style="text-align: right;">
            @if($reserveMetrics['is_healthy'])
                <span style="background: #22c55e; color: #FFFFFF; padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-shield-check"></i> LIKUIDITAS AMAN (> 10%)
                </span>
            @else
                <span style="background: #ef4444; color: #FFFFFF; padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> DEFISIT CADANGAN WAKTU NYATA
                </span>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px;">
        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 6px;">Total Pocket Kas Koperasi</div>
            <div style="font-size: 24px; font-weight: 800;">Rp {{ number_format($reserveMetrics['current_pocket'], 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">Kas riil siap pakai saat ini</div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 6px;">Total Simpanan Wajib Seluruh Anggota</div>
            <div style="font-size: 24px; font-weight: 800;">Rp {{ number_format($reserveMetrics['total_simpanan_wajib'], 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">Basis perhitungan batas 10%</div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 6px;">Syarat Cadangan Mengendap (10%)</div>
            <div style="font-size: 24px; font-weight: 800; color: #fde047;">Rp {{ number_format($reserveMetrics['minimum_reserve'], 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">Batas minimal tidak boleh ditembus</div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 6px;">Maksimal Dana Boleh Dipinjamkan</div>
            <div style="font-size: 24px; font-weight: 800; color: #86efac;">Rp {{ number_format($reserveMetrics['max_lendable'], 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.75; margin-top: 4px;">Plafon aman untuk persetujuan pinjaman</div>
        </div>
    </div>

    <!-- Progress bar rasio -->
    <div>
        <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 6px;">
            <span>Rasio Cadangan Pocket Kas: {{ $reserveMetrics['reserve_ratio_percent'] }}% dari Simpanan Wajib</span>
            <span>Syarat Minimal: 10%</span>
        </div>
        <div style="height: 10px; background: rgba(255,255,255,0.25); border-radius: 999px; overflow: hidden;">
            <div style="height: 100%; width: {{ min(100, max(5, $reserveMetrics['reserve_ratio_percent'])) }}%; background: {{ $reserveMetrics['is_healthy'] ? '#4ade80' : '#f87171' }}; border-radius: 999px; transition: width 0.4s ease;"></div>
        </div>
    </div>
</div>

<!-- GRID SECTION: ANTREAN PINJAMAN & CLOSING KASIR -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- KOLOM KIRI: ANTREAN PINJAMAN (POV FINANCE) -->
    <div>
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                <div>
                    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">
                        <i class="fa-solid fa-list-check" style="color: #2563eb; margin-right: 6px;"></i>
                        Antrean Pengajuan Pinjaman (POV Finance)
                    </h3>
                    <p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">
                        Evaluasi tujuan pembiayaan dan validasi syarat cadangan 10% sebelum approval.
                    </p>
                </div>
                <span style="background: #dbeafe; color: #1e40af; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 999px;">
                    {{ $pendingLoans->count() }} Antrean
                </span>
            </div>

            @if($pendingLoans->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                    <i class="fa-solid fa-clipboard-check" style="font-size: 40px; margin-bottom: 12px; opacity: 0.5;"></i>
                    <p style="font-size: 14px; margin: 0;">Tidak ada antrean pinjaman baru. Semua pengajuan telah dievaluasi!</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f5f9; text-align: left; color: #64748b;">
                                <th style="padding: 10px 12px;">No. Pinjaman</th>
                                <th style="padding: 10px 12px;">Anggota</th>
                                <th style="padding: 10px 12px;">Nominal</th>
                                <th style="padding: 10px 12px;">Tenor</th>
                                <th style="padding: 10px 12px;">Tujuan</th>
                                <th style="padding: 10px 12px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingLoans as $loan)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px; font-weight: 700; color: #1e293b;">
                                    {{ $loan->nomor_pinjaman }}
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: normal;">{{ $loan->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 600; color: #0f172a;">{{ $loan->member->user->name }}</div>
                                    <div style="font-size: 11px; color: #64748b;">NIK: {{ $loan->member->nik }}</div>
                                </td>
                                <td style="padding: 12px; font-weight: 800; color: #0f172a;">
                                    Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}
                                </td>
                                <td style="padding: 12px; color: #475569;">
                                    {{ $loan->tenor }} Bulan
                                </td>
                                <td style="padding: 12px; color: #475569; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $loan->tujuan_pinjaman }}
                                </td>
                                <td style="padding: 12px; text-align: right;">
                                    <a href="{{ route('finance.loans.show', $loan->id) }}" style="display: inline-block; background: #2563eb; color: #FFFFFF; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none;">
                                        Review & Putuskan
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- RIWAYAT PINJAMAN YANG TELAH DISETUJUI BESERTA CATATAN FINANCE -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 16px 0;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #059669; margin-right: 6px;"></i>
                Riwayat Persetujuan Pinjaman Terkini
            </h3>
            @if($recentlyApprovedLoans->isEmpty())
                <p style="color: #94a3b8; font-size: 13px; margin: 0;">Belum ada riwayat persetujuan pinjaman.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($recentlyApprovedLoans as $rLoan)
                    <div style="background: #f8fafc; border-radius: 10px; padding: 14px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 14px;">
                                {{ $rLoan->nomor_pinjaman }} &bull; {{ $rLoan->member->user->name }}
                            </div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                                Nominal: <strong>Rp {{ number_format($rLoan->jumlah_pinjaman, 0, ',', '.') }}</strong> | Disetujui: {{ $rLoan->approved_at ? $rLoan->approved_at->format('d/m/Y H:i') : '-' }}
                            </div>
                            <div style="font-size: 12px; color: #1e40af; margin-top: 4px; background: #eff6ff; padding: 4px 8px; border-radius: 6px; display: inline-block;">
                                <i class="fa-solid fa-comment-dots"></i> <strong>Note Finance:</strong> {{ $rLoan->approval_note ?? 'Disetujui' }}
                            </div>
                        </div>
                        <div>
                            @if($rLoan->status === 'approved')
                                <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700;">
                                    Menunggu Kasir (Uang Siap Disiapkan)
                                </span>
                            @elseif(in_array($rLoan->status, ['disbursed', 'active']))
                                <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700;">
                                    DISBURSED (Kas Telah Dicairkan)
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- KOLOM KANAN: SERAH TERIMA KAS KASIR JAM 16:00 (DAILY ZERO-CASH HANDOVER) -->
    <div>
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Closing Kasir Jam 16:00</h3>
                    <div style="font-size: 12px; color: #64748b;">Daily Zero-Cash Handover</div>
                </div>
            </div>

            <p style="font-size: 13px; color: #475569; margin: 0 0 16px 0; line-height: 1.5;">
                Kasir menyerahkan 100% uang fisik di laci kasir setiap jam 16:00. Finance memverifikasi penghitungan fisik uang tunai sebelum dana resmi masuk brankas/bank.
            </p>

            @if($pendingClosings->isEmpty())
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 24px; color: #10b981; margin-bottom: 8px; display: block;"></i>
                    Tidak ada serah terima closing kasir yang pending saat ini.
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach($pendingClosings as $closing)
                    <div style="border: 1px solid #fde68a; background: #fffbeb; border-radius: 12px; padding: 16px;">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #92400e; font-weight: 700; margin-bottom: 8px;">
                            <span>Tanggal: {{ $closing->tanggal_closing->format('d/m/Y') }}</span>
                            <span>Kasir: {{ $closing->kasir->name }}</span>
                        </div>

                        <div style="font-size: 13px; color: #78350f; margin-bottom: 12px;">
                            <div>Total Kas Masuk: <strong>Rp {{ number_format($closing->total_kas_masuk, 0, ',', '.') }}</strong></div>
                            <div>Total Kas Keluar: <strong>Rp {{ number_format($closing->total_kas_keluar, 0, ',', '.') }}</strong></div>
                            <div style="font-size: 15px; font-weight: 800; color: #b45309; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #fcd34d;">
                                Fisik Uang Diserahkan: Rp {{ number_format($closing->total_fisik_diserahkan, 0, ',', '.') }}
                            </div>
                        </div>

                        <form action="{{ route('finance.closings.verify', $closing->id) }}" method="POST">
                            @csrf
                            <input type="text" name="catatan" placeholder="Catatan verifikasi (opsional)" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 12px; margin-bottom: 8px; box-sizing: border-box;">
                            <button type="submit" style="width: 100%; background: #059669; color: #FFFFFF; border: none; padding: 10px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                                <i class="fa-solid fa-check-double"></i> Verifikasi & Terima Uang Fisik
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
