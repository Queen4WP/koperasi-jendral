@extends('layouts.app')

@section('title', 'Loket Kasir — Transaksi Kas & Layanan Fisik KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-cash-register" style="color: #059669; margin-right: 8px;"></i>
                Dashboard Loket Kasir
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Layanan Setoran Tunai, Penarikan, Pencairan Pinjaman, & Rekapitulasi Kas Harian (Closing 16:00)
            </p>
        </div>
        <div>
            <span style="background: #ecfdf5; color: #047857; padding: 8px 14px; border-radius: 999px; font-weight: 700; font-size: 13px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-money-bill-wave"></i> Loket Kas Tunai Aktif
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

<!-- ARUS KAS LOKET HARI INI -->
<div style="background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f766e 100%); border-radius: 18px; padding: 26px; color: #FFFFFF; box-shadow: 0 10px 25px rgba(6, 78, 59, 0.25); margin-bottom: 28px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                Posisi Kas Fisik Hari Ini
            </span>
            <h2 style="font-size: 20px; font-weight: 800; margin: 8px 0 0 0;">Laci Kasir: {{ auth('kasir')->user()->name }}</h2>
        </div>
        <div style="font-size: 13px; opacity: 0.9;">
            <i class="fa-solid fa-calendar-day"></i> {{ \Carbon\Carbon::parse($dailySummary['tanggal'])->format('d F Y') }}
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 4px;">Total Kas Masuk (Hari Ini)</div>
            <div style="font-size: 22px; font-weight: 800; color: #86efac;">+ Rp {{ number_format($dailySummary['total_kas_masuk'], 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.85; margin-top: 4px;">
                Angsuran: Rp {{ number_format($dailySummary['angsuran_masuk'], 0, ',', '.') }}<br>
                Simpanan: Rp {{ number_format($dailySummary['simpanan_masuk'], 0, ',', '.') }}
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.12); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px);">
            <div style="font-size: 12px; opacity: 0.85; margin-bottom: 4px;">Total Kas Keluar (Hari Ini)</div>
            <div style="font-size: 22px; font-weight: 800; color: #fca5a5;">- Rp {{ number_format($dailySummary['total_kas_keluar'], 0, ',', '.') }}</div>
            <div style="font-size: 11px; opacity: 0.85; margin-top: 4px;">
                Pencairan: Rp {{ number_format($dailySummary['pinjaman_keluar'], 0, ',', '.') }}<br>
                Penarikan: Rp {{ number_format($dailySummary['penarikan_keluar'], 0, ',', '.') }}
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.18); padding: 18px; border-radius: 12px; backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.3); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">Saldo Kas Fisik Siap Diserahkan</div>
                <div style="font-size: 24px; font-weight: 800; color: #fef08a;">Rp {{ number_format($dailySummary['saldo_fisik_tersedia'], 0, ',', '.') }}</div>
                <div style="font-size: 11px; opacity: 0.8; margin-top: 4px;">Wajib diserahterimakan jam 16:00</div>
            </div>

            @if($dailySummary['closing'])
                <div style="margin-top: 14px; font-size: 11px; font-weight: 700; background: rgba(16, 185, 129, 0.25); border: 1px solid #10b981; color: #a7f3d0; padding: 6px 12px; border-radius: 8px; text-align: center;">
                    <i class="fa-solid fa-check-circle"></i> Closing Kas Hari Ini Telah Disubmit
                </div>
            @elseif($dailySummary['saldo_fisik_tersedia'] > 0)
                <a href="#closingKasirSection" onclick="setTimeout(() => { document.getElementById('inputFisikDiserahkan')?.focus(); document.getElementById('inputFisikDiserahkan')?.select(); }, 100);" style="margin-top: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #fef08a; color: #854d0e; padding: 10px 14px; border-radius: 8px; font-size: 12px; font-weight: 800; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.15); transition: all 0.2s;">
                    <i class="fa-solid fa-arrow-down"></i> Serahkan Kas ke Finance Sekarang
                </a>
            @endif
        </div>
    </div>

    @if($dailySummary['total_kas_keluar'] > $dailySummary['total_kas_masuk'])
        <div style="margin-top: 16px; background: rgba(0,0,0,0.22); border-left: 4px solid #fef08a; padding: 12px 16px; border-radius: 8px; font-size: 12px; display: flex; align-items: flex-start; gap: 10px;">
            <i class="fa-solid fa-circle-info" style="color: #fef08a; font-size: 16px; margin-top: 2px;"></i>
            <div>
                <strong>Informasi Posisi Kas Fisik:</strong> Total pencairan tunai (<strong>Rp {{ number_format($dailySummary['total_kas_keluar'], 0, ',', '.') }}</strong>) lebih besar dibanding penerimaan tunai loket hari ini (<strong>Rp {{ number_format($dailySummary['total_kas_masuk'], 0, ',', '.') }}</strong>). Seluruh uang angsuran dan simpanan masuk telah terserap untuk pembayaran pencairan pinjaman anggota, sehingga sisa kas fisik netto di laci loket saat ini bernilai <strong>Rp 0</strong>.
            </div>
        </div>
    @endif
</div>

<!-- TOMBOL AKSI CEPAT TRANSAKSI LOKET KASIR -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
    <a href="{{ route('kasir.savings.deposit') }}" style="background: #FFFFFF; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; text-decoration: none; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: transform 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-circle-down"></i>
        </div>
        <div>
            <div style="font-weight: 800; font-size: 15px; color: #0f172a;">Setoran Tunai</div>
            <div style="font-size: 12px; color: #64748b;">Simpanan Pokok/Wajib (BKM)</div>
        </div>
    </a>

    <a href="{{ route('kasir.savings.withdraw') }}" style="background: #FFFFFF; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; text-decoration: none; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: transform 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-circle-up"></i>
        </div>
        <div>
            <div style="font-weight: 800; font-size: 15px; color: #0f172a;">Penarikan Tunai</div>
            <div style="font-size: 12px; color: #64748b;">Tarik Simpanan Wajib (BKK)</div>
        </div>
    </a>

    <a href="{{ route('kasir.loans.installment') }}" style="background: #FFFFFF; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; text-decoration: none; display: flex; align-items: center; gap: 14px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: transform 0.2s;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div>
            <div style="font-weight: 800; font-size: 15px; color: #0f172a;">Bayar Angsuran</div>
            <div style="font-size: 12px; color: #64748b;">Cicilan Pinjaman Tunai (BKM)</div>
        </div>
    </a>
</div>

<!-- SECTION UTAMA: PINJAMAN SIAP DICAIRKAN & CLOSING 16:00 -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- KOLOM KIRI: PINJAMAN SIAP DICAIRKAN (STATUS: APPROVED DARI FINANCE) -->
    <div>
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>
                    <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0;">
                        <i class="fa-solid fa-money-bill-transfer" style="color: #059669; margin-right: 6px;"></i>
                        Pinjaman Siap Dicairkan (Persetujuan Finance)
                    </h3>
                    <p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">
                        Finance telah menyetujui. Siapkan uang tunai dan minta tanda tangan tanda terima fisik pemohon.
                    </p>
                </div>
                <span style="background: #dcfce7; color: #166534; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 999px;">
                    {{ $readyLoans->count() }} Siap Cair
                </span>
            </div>

            @if($readyLoans->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                    <i class="fa-solid fa-inbox" style="font-size: 36px; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p style="font-size: 14px; margin: 0;">Tidak ada pinjaman berstatus APPROVED yang menunggu pencairan kasir.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($readyLoans as $rLoan)
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #fafafa;">
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #0f172a;">
                                {{ $rLoan->nomor_pinjaman }} &bull; {{ $rLoan->member->user->name }}
                            </div>
                            <div style="font-size: 13px; color: #047857; font-weight: 700; margin-top: 4px;">
                                Uang Tunai: Rp {{ number_format($rLoan->jumlah_pinjaman, 0, ',', '.') }}
                            </div>
                            <div style="font-size: 12px; color: #1e40af; background: #eff6ff; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-top: 6px;">
                                <i class="fa-solid fa-comment-dots"></i> Note Finance: {{ $rLoan->approval_note ?? 'Disetujui' }}
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('kasir.loans.disburse', $rLoan->id) }}" style="display: inline-flex; align-items: center; gap: 8px; background: #059669; color: #FFFFFF; font-weight: 700; font-size: 13px; padding: 10px 18px; border-radius: 8px; text-decoration: none;">
                                <i class="fa-solid fa-hand-holding-dollar"></i> Serahkan Uang Tunai di Loket
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- KOLOM KANAN: CLOSING KAS HARIAN JAM 16:00 (DAILY ZERO-CASH HANDOVER) -->
    <div>
        <div id="closingKasirSection" style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); scroll-margin-top: 24px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">Closing Kasir Jam 16:00</h3>
                    <div style="font-size: 12px; color: #64748b;">Daily Zero-Cash Handover</div>
                </div>
            </div>

            <p style="font-size: 12px; color: #475569; line-height: 1.5; margin: 0 0 16px 0;">
                SOP KOPIN: 100% uang fisik diserahkan ke bagian Finance setiap sore. Kasir tidak menyimpan kas semalam.
            </p>

            @if($dailySummary['closing'])
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px; font-size: 13px; color: #166534;">
                    <div style="font-weight: 800; margin-bottom: 4px;">
                        <i class="fa-solid fa-circle-check"></i> Closing Hari Ini Telah Disubmit!
                    </div>
                    <div>Fisik Diserahkan: <strong>Rp {{ number_format($dailySummary['closing']->total_fisik_diserahkan, 0, ',', '.') }}</strong></div>
                    <div>Status: <strong>{{ strtoupper($dailySummary['closing']->status) }}</strong></div>
                    @if($dailySummary['closing']->status === 'pending')
                        <div style="font-size: 11px; color: #854d0e; margin-top: 6px;">
                            <i class="fa-solid fa-clock"></i> Menunggu verifikasi fisik oleh Finance.
                        </div>
                    @else
                        <div style="font-size: 11px; color: #166534; margin-top: 6px;">
                            <i class="fa-solid fa-check-double"></i> Telah diverifikasi oleh Finance!
                        </div>
                    @endif
                </div>
            @else
                <form id="closingKasirForm" action="{{ route('kasir.closing.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Total Uang Fisik Riil Diserahkan (Rp)
                        </label>
                        <input type="number" id="inputFisikDiserahkan" name="total_fisik_diserahkan" value="{{ (int)$dailySummary['saldo_fisik_tersedia'] }}" required min="0" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 15px; font-weight: 800; color: #0f172a; box-sizing: border-box; background: #f8fafc;">
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                            Hitung fisik lembar uang tunai di laci kasir.
                        </div>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Catatan Closing (Opsional)
                        </label>
                        <input type="text" name="catatan" placeholder="Contoh: Pecahan 100rb 7 lbr, 50rb 1 lbr, dst" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px; font-size: 12px; box-sizing: border-box;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: #334155; cursor: pointer; background: #fffbeb; border: 1px solid #fef3c7; padding: 10px; border-radius: 8px;">
                            <input type="checkbox" id="checkKonfirmasiFisik" required style="margin-top: 2px; width: 16px; height: 16px; accent-color: #d97706; cursor: pointer;">
                            <span>Saya telah menghitung uang fisik di laci kasir dan siap menyerahkannya ke Finance.</span>
                        </label>
                    </div>

                    <button type="submit" id="btnSubmitClosing" style="width: 100%; background: #d97706; color: #FFFFFF; border: none; padding: 12px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s;">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i> Serahkan Kas Fisik ke Finance
                    </button>
                </form>

                <script>
                    document.getElementById('closingKasirForm')?.addEventListener('submit', function() {
                        const btn = document.getElementById('btnSubmitClosing');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses Serah Terima...';
                            btn.style.opacity = '0.7';
                        }
                    });
                </script>
            @endif
        </div>
    </div>
</div>
@endsection
