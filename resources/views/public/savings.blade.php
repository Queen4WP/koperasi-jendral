@extends('layouts.public')

@section('title', 'Produk Simpanan KOPIN — Simpanan Pokok & Simpanan Wajib Anggota')
@section('meta_description', 'Pelajari produk Simpanan Pokok Rp55.000 dan Simpanan Wajib Rp100.000/bulan di KOPIN. Ketahui kewajiban simpanan sebagai syarat pengajuan pinjaman.')

@section('content')

<!-- Header Banner -->
<section style="background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%); padding: 56px 24px; border-bottom: 1px solid var(--border-subtle);">
    <div class="section-container" style="text-align: center; max-width: 800px;">
        <span class="section-tag">Ketentuan Finansial</span>
        <h1 class="section-title" style="font-size: 38px; margin-bottom: 16px;">
            Produk Simpanan Koperasi KOPIN
        </h1>
        <p class="section-subtitle">
            Simpanan anggota adalah pilar utama modal koperasi untuk membangun ketahanan ekonomi bersama secara amanah dan berkelanjutan.
        </p>
    </div>
</section>

<!-- Detail Produk Simpanan -->
<section class="section-padding" style="background: #FFFFFF;">
    <div class="section-container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 64px;">
            <!-- Simpanan Pokok Card -->
            <div class="card card-elevated" style="padding: 36px; border-top: 4px solid var(--emerald-600);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="badge badge-emerald">Dibayarkan 1x di Awal</span>
                    <i class="fa-solid fa-piggy-bank" style="font-size: 28px; color: var(--emerald-600);"></i>
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px;">
                    Simpanan Pokok
                </h2>
                <p style="font-size: 14.5px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
                    Simpanan Pokok adalah sejumlah uang yang wajib disetorkan oleh anggota kepada koperasi pada saat pertama kali mendaftar sebagai anggota resmi KOPIN.
                </p>

                <div style="padding: 20px; background: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 24px;">
                    <div style="font-size: 12.5px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Nominal Ketetapan:</div>
                    <div style="font-size: 32px; font-weight: 800; color: var(--emerald-700); margin: 4px 0;" class="tabular-nums">Rp 55.000</div>
                    <div style="font-size: 12.5px; color: var(--text-secondary);">Hanya satu kali selama menjadi anggota aktif</div>
                </div>

                <h4 style="font-size: 14px; font-weight: 700; color: var(--text-heading); margin-bottom: 12px;">Ketentuan & Karakteristik:</h4>
                <ul style="list-style: none; padding: 0; margin: 0 0 24px 0; font-size: 13.5px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li><i class="fa-solid fa-circle-check" style="color: var(--emerald-600); margin-right: 8px;"></i> <strong>Status Keanggotaan:</strong> Menjadi syarat mutlak verifikasi dan penerbitan nomor anggota koperasi.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: var(--emerald-600); margin-right: 8px;"></i> <strong>Sifat Dana:</strong> Mengendap sebagai ekuitas modal dasar koperasi selama yang bersangkutan menjadi anggota.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: var(--emerald-600); margin-right: 8px;"></i> <strong>Pencatatan Buku:</strong> Langsung tercatat di buku simpanan digital setelah bukti setoran diverifikasi admin.</li>
                </ul>
            </div>

            <!-- Simpanan Wajib Card -->
            <div class="card card-elevated" style="padding: 36px; border-top: 4px solid #3B82F6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="badge badge-info">Iuran Rutin Bulanan</span>
                    <i class="fa-solid fa-wallet" style="font-size: 28px; color: #3B82F6;"></i>
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px;">
                    Simpanan Wajib
                </h2>
                <p style="font-size: 14.5px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
                    Simpanan Wajib adalah sejumlah simpanan tertentu yang harus disetorkan secara rutin setiap bulan oleh seluruh anggota yang terdaftar di KOPIN.
                </p>

                <div style="padding: 20px; background: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 24px;">
                    <div style="font-size: 12.5px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Nominal Bulanan:</div>
                    <div style="font-size: 32px; font-weight: 800; color: #1D4ED8; margin: 4px 0;" class="tabular-nums">Rp 100.000</div>
                    <div style="font-size: 12.5px; color: var(--text-secondary);">Disetorkan setiap bulan secara tertib</div>
                </div>

                <h4 style="font-size: 14px; font-weight: 700; color: var(--text-heading); margin-bottom: 12px;">Ketentuan & Karakteristik:</h4>
                <ul style="list-style: none; padding: 0; margin: 0 0 24px 0; font-size: 13.5px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> <strong>Kewajiban Rutin:</strong> Dicatat sebagai tagihan kewajiban simpanan bulanan di akun portal anggota.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> <strong>Syarat Pinjaman:</strong> Kepatuhan pelunasan simpanan wajib menentukan kelayakan pengajuan pinjaman baru.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> <strong>Akumulasi Modal:</strong> Total saldo simpanan wajib terakumulasi dan dapat dipantau riwayatnya secara real-time.</li>
                </ul>
            </div>
        </div>

        <!-- Ketentuan Kepatuhan Simpanan & Pinjaman -->
        <div class="card" style="padding: 32px; background: #F8FAFC; border: 1px solid var(--border-card); margin-bottom: 48px;">
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                        Hubungan Kepatuhan Simpanan dan Kelayakan Pengajuan Pinjaman
                    </h3>
                    <p style="font-size: 14px; line-height: 1.7; color: var(--text-secondary); margin-bottom: 12px;">
                        KOPIN menerapkan prinsip keadilan: pinjaman anggota bersumber dari dana simpanan bersama. Oleh karena itu, sistem KOPIN secara otomatis memverifikasi bahwa:
                    </p>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13.5px; color: var(--text-secondary); line-height: 1.6;">
                        <li>Anggota telah melunasi <strong>Simpanan Pokok (Rp 55.000)</strong> dan akun telah berstatus <em>Active</em>.</li>
                        <li>Anggota tidak memiliki tunggakan pada <strong>Simpanan Wajib (Rp 100.000/bln)</strong> yang telah tercatat sebagai kewajiban dalam sistem.</li>
                        <li>Anggota tidak sedang memiliki pinjaman aktif lain yang belum lunas.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Cara Penyetoran Simpanan -->
        <div class="section-header" style="margin-bottom: 32px;">
            <span class="section-tag">Metode Pembayaran</span>
            <h2 class="section-title">Cara Menyetor Simpanan</h2>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px; max-width: 900px; margin: 0 auto;">
            <div class="card" style="padding: 24px; border-left: 4px solid var(--emerald-600);">
                <h4 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 700; color: var(--text-heading);">
                    <i class="fa-solid fa-mobile-screen" style="color: var(--emerald-600); margin-right: 8px;"></i> 1. Transfer Bank (Online)
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; margin: 0 0 12px 0;">
                    Transfer ke rekening resmi koperasi yang tertera di portal anggota, lalu unggah bukti transfer ke sistem untuk diverifikasi oleh admin.
                </p>
                <span class="badge badge-emerald">Tersedia di Portal Anggota</span>
            </div>

            <div class="card" style="padding: 24px; border-left: 4px solid #3B82F6;">
                <h4 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 700; color: var(--text-heading);">
                    <i class="fa-solid fa-building-user" style="color: #3B82F6; margin-right: 8px;"></i> 2. Kasir Kantor Koperasi (Offline)
                </h4>
                <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; margin: 0 0 12px 0;">
                    Datang langsung ke kantor operasional KOPIN pada jam kerja untuk melakukan pembayaran tunai atau debit kepada petugas teller kasir kami.
                </p>
                <span class="badge badge-info">Kuitansi Langsung Tercatat</span>
            </div>
        </div>
    </div>
</section>

@endsection
