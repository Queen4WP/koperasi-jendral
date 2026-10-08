@extends('layouts.public')

@section('title', 'KOPIN — Koperasi Simpan Pinjam Modern, Amanah & Terpercaya')
@section('meta_description', 'KOPIN menyediakan layanan simpanan dan pinjaman anggota dengan proses transparan, bunga terjangkau, dan tata kelola profesional untuk karyawan dan masyarakat.')

@section('content')

<!-- ==================== 1. HERO SECTION ==================== -->
<section class="hero-section" aria-label="Hero Banner">
    <div class="hero-container">
        <div>
            <div class="hero-pill">
                <i class="fa-solid fa-certificate" style="color: var(--emerald-600);"></i>
                Lembaga Koperasi Simpan Pinjam Indonesia
            </div>
            <h1 class="hero-title">
                Solusi Simpan Pinjam Amanah untuk Kebutuhan Anda
            </h1>
            <p class="hero-lead">
                KOPIN hadir membangun kemandirian finansial anggota melalui layanan simpanan dan pinjaman yang berkeadilan, transparan, dan berlandaskan semangat gotong royong bagi karyawan dan masyarakat.
            </p>
            
            <div class="hero-actions">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-gauge"></i> Masuk Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('member.loans.create') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Ajukan Pinjaman Sekarang
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-user-plus"></i> Ajukan Pinjaman
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk Anggota
                    </a>
                @endauth
                <a href="#simulasi" class="btn btn-outline-emerald btn-lg">
                    <i class="fa-solid fa-calculator"></i> Simulasi Angsuran
                </a>
            </div>

            <div class="hero-trust-row">
                <div class="trust-item">
                    <span class="trust-value">Rp55.000</span>
                    <span class="trust-label">Simpanan Pokok Terjangkau</span>
                </div>
                <div class="trust-item">
                    <span class="trust-value">s/d Rp50 Juta</span>
                    <span class="trust-label">Plafon Pinjaman Anggota</span>
                </div>
                <div class="trust-item">
                    <span class="trust-value">Mulai 5.0%</span>
                    <span class="trust-label">Jasa Pinjaman Transparan</span>
                </div>
            </div>
        </div>

        <!-- Hero Card Graphic / Overview -->
        <div>
            <div class="card card-elevated" style="padding: 32px; background: #FFFFFF; border-top: 4px solid var(--emerald-600);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--emerald-600); letter-spacing: 0.05em;">Layanan Unggulan</span>
                        <h3 style="margin: 4px 0 0 0; font-size: 20px; font-weight: 800; color: var(--text-heading);">Portal Terpadu KOPIN</h3>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 12px; border-radius: 8px; background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <i class="fa-solid fa-vault" style="color: #10B981; font-size: 18px; margin-top: 2px;"></i>
                        <div>
                            <strong style="font-size: 13.5px; color: var(--text-heading); display: block;">Simpanan Pokok & Wajib</strong>
                            <span style="font-size: 12.5px; color: var(--text-secondary);">Pencatatan saldo simpanan tercatat rapi dan dapat dipantau setiap saat.</span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 12px; border-radius: 8px; background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <i class="fa-solid fa-file-invoice-dollar" style="color: #059669; font-size: 18px; margin-top: 2px;"></i>
                        <div>
                            <strong style="font-size: 13.5px; color: var(--text-heading); display: block;">Pengajuan Pinjaman Online</strong>
                            <span style="font-size: 12.5px; color: var(--text-secondary);">Unggah dokumen langsung dari perangkat Anda tanpa antre di kantor.</span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 12px; border-radius: 8px; background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <i class="fa-solid fa-calendar-check" style="color: #3B82F6; font-size: 18px; margin-top: 2px;"></i>
                        <div>
                            <strong style="font-size: 13.5px; color: var(--text-heading); display: block;">Jadwal Angsuran Akurat</strong>
                            <span style="font-size: 12.5px; color: var(--text-secondary);">Jatuh tempo dihitung presisi berbasis tanggal pencairan aktual.</span>
                        </div>
                    </div>
                </div>

                <div style="padding-top: 16px; border-top: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                    <span style="color: var(--text-muted);"><i class="fa-solid fa-users"></i> Anggota Karyawan & Umum</span>
                    <a href="{{ route('public.about') }}" style="color: var(--emerald-600); font-weight: 600; text-decoration: none;">Pelajari Selengkapnya &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== 2. TENTANG KOPIN INTRODUCTION ==================== -->
<section class="section-padding" id="tentang" style="background: #FFFFFF;">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Mengenal KOPIN</span>
            <h2 class="section-title">Koperasi Simpan Pinjam yang Mengutamakan Kesejahteraan Anggota</h2>
            <p class="section-subtitle">
                KOPIN berdiri atas prinsip kekeluargaan untuk memberikan akses permodalan yang sehat dan wadah menabung yang aman bagi seluruh anggota koperasi.
            </p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="feature-title">Asas Gotong Royong</h3>
                <p class="feature-desc">
                    Modal simpanan dihimpun dari anggota dan disalurkan kembali kepada anggota yang membutuhkan pembiayaan dengan tata kelola adil.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="feature-title">Jasa Pinjaman Ringan</h3>
                <p class="feature-desc">
                    Tingkat jasa terkunci mulai dari 5.0% flat transparan, melindungi anggota dari jeratan bunga memberatkan di luar koperasi.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-laptop-file"></i>
                </div>
                <h3 class="feature-title">Pengelolaan Digital Terpadu</h3>
                <p class="feature-desc">
                    Setiap anggota memiliki akun portal pribadi untuk memantau buku simpanan, status pengajuan, serta jadwal angsuran secara berkala.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon-box">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <h3 class="feature-title">Tanpa Biaya Siluman</h3>
                <p class="feature-desc">
                    Seluruh kewajiban dihitung secara terbuka sejak awal tanpa potongan administrasi misterius atau denda tak beralasan.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== 3. PRODUK SIMPANAN ==================== -->
<section class="section-padding" style="background: #F8FAFC; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Produk Keuangan</span>
            <h2 class="section-title">Simpanan Koperasi KOPIN</h2>
            <p class="section-subtitle">
                Bangun komitmen kebersamaan dan penuhi syarat kelayakan pembiayaan melalui simpanan resmi anggota.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; max-width: 900px; margin: 0 auto 40px auto;">
            <!-- Simpanan Pokok -->
            <div class="card card-elevated" style="padding: 32px; border-top: 4px solid var(--emerald-600);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="badge badge-emerald">Wajib Saat Registrasi</span>
                    <i class="fa-solid fa-piggy-bank" style="font-size: 24px; color: var(--emerald-600);"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px;">Simpanan Pokok</h3>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                    Simpanan awal sebagai bukti sah komitmen keanggotaan koperasi KOPIN.
                </p>
                <div style="margin-bottom: 24px; padding: 16px; background: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
                    <span style="font-size: 12px; color: var(--text-muted); display: block;">Besaran Simpanan:</span>
                    <span style="font-size: 26px; font-weight: 800; color: var(--emerald-700);">Rp 55.000</span>
                    <span style="font-size: 12px; color: var(--text-muted); display: block; margin-top: 4px;">Dibayarkan 1 (satu) kali di awal</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 24px 0; font-size: 13.5px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li><i class="fa-solid fa-check" style="color: var(--emerald-600); margin-right: 8px;"></i> Syarat aktivasi akun keanggotaan</li>
                    <li><i class="fa-solid fa-check" style="color: var(--emerald-600); margin-right: 8px;"></i> Dicatat resmi dalam buku simpanan</li>
                    <li><i class="fa-solid fa-check" style="color: var(--emerald-600); margin-right: 8px;"></i> Mengendap selama menjadi anggota aktif</li>
                </ul>
                <a href="{{ route('public.savings') }}" class="btn btn-outline-emerald" style="width: 100%;">
                    Pelajari Simpanan Pokok &rarr;
                </a>
            </div>

            <!-- Simpanan Wajib -->
            <div class="card card-elevated" style="padding: 32px; border-top: 4px solid #3B82F6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="badge badge-info">Iuran Bulanan Anggota</span>
                    <i class="fa-solid fa-coins" style="font-size: 24px; color: #3B82F6;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: var(--text-heading); margin-bottom: 8px;">Simpanan Wajib</h3>
                <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                    Iuran bulanan anggota untuk memperkuat modal kerja koperasi bersama.
                </p>
                <div style="margin-bottom: 24px; padding: 16px; background: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
                    <span style="font-size: 12px; color: var(--text-muted); display: block;">Besaran Iuran:</span>
                    <span style="font-size: 26px; font-weight: 800; color: #1D4ED8;">Rp 100.000</span>
                    <span style="font-size: 12px; color: var(--text-muted); display: block; margin-top: 4px;">Per bulan secara berkala</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 24px 0; font-size: 13.5px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li><i class="fa-solid fa-check" style="color: #3B82F6; margin-right: 8px;"></i> Membentuk saldo simpanan akumulatif</li>
                    <li><i class="fa-solid fa-check" style="color: #3B82F6; margin-right: 8px;"></i> Syarat kelayakan pengajuan pinjaman</li>
                    <li><i class="fa-solid fa-check" style="color: #3B82F6; margin-right: 8px;"></i> Pembayaran mudah via transfer / kantor</li>
                </ul>
                <a href="{{ route('public.savings') }}" class="btn btn-outline" style="width: 100%; border-color: #3B82F6; color: #1D4ED8;">
                    Pelajari Simpanan Wajib &rarr;
                </a>
            </div>
        </div>

        <div style="text-align: center;">
            <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 16px;">
                <i class="fa-solid fa-circle-info" style="color: var(--emerald-600);"></i> Status kepatuhan simpanan diverifikasi oleh sistem sebelum anggota dapat mengajukan pinjaman.
            </p>
            <a href="{{ route('public.savings') }}" class="btn btn-primary">
                Baca Ketentuan Simpanan Lengkap
            </a>
        </div>
    </div>
</section>

<!-- ==================== 4. PRODUK PINJAMAN & SIMULATOR ==================== -->
<section class="section-padding" id="simulasi" style="background: #FFFFFF;">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Pembiayaan Anggota</span>
            <h2 class="section-title">Pinjaman Transparan & Simulasi Angsuran</h2>
            <p class="section-subtitle">
                KOPIN menyediakan fasilitas pinjaman bagi anggota dengan plafon Rp 5.000.000 hingga Rp 50.000.000 dengan pilihan tenor 6, 12, 18, dan 24 bulan.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: flex-start; max-width: 1100px; margin: 0 auto;">
            <!-- Informasi Ketentuan Pinjaman -->
            <div>
                <h3 style="font-size: 22px; font-weight: 800; color: var(--text-heading); margin-bottom: 16px;">
                    Ketentuan Pinjaman KOPIN
                </h3>
                <p style="font-size: 14.5px; line-height: 1.6; color: var(--text-secondary); margin-bottom: 24px;">
                    Pinjaman ditujukan untuk mendukung kebutuhan produktif maupun esensial anggota. Jasa pinjaman bersifat tetap (flat) sesuai tenor yang dipilih.
                </p>

                <!-- Matriks Jasa -->
                <div class="card" style="padding: 20px; margin-bottom: 24px; background: #F8FAFC;">
                    <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: var(--text-heading);">Matriks Jasa Pinjaman Resmi:</h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; font-size: 13.5px;">
                        <div style="padding: 10px; background: #FFFFFF; border-radius: 6px; border: 1px solid #E2E8F0;">
                            <span style="color: var(--text-muted); display: block; font-size: 12px;">Tenor 6 Bulan:</span>
                            <strong style="color: var(--emerald-700); font-size: 16px;">5.0% flat</strong>
                        </div>
                        <div style="padding: 10px; background: #FFFFFF; border-radius: 6px; border: 1px solid #E2E8F0;">
                            <span style="color: var(--text-muted); display: block; font-size: 12px;">Tenor 12 Bulan:</span>
                            <strong style="color: var(--emerald-700); font-size: 16px;">7.0% flat</strong>
                        </div>
                        <div style="padding: 10px; background: #FFFFFF; border-radius: 6px; border: 1px solid #E2E8F0;">
                            <span style="color: var(--text-muted); display: block; font-size: 12px;">Tenor 18 Bulan:</span>
                            <strong style="color: var(--emerald-700); font-size: 16px;">8.5% flat</strong>
                        </div>
                        <div style="padding: 10px; background: #FFFFFF; border-radius: 6px; border: 1px solid #E2E8F0;">
                            <span style="color: var(--text-muted); display: block; font-size: 12px;">Tenor 24 Bulan:</span>
                            <strong style="color: var(--emerald-700); font-size: 16px;">10.0% flat</strong>
                        </div>
                    </div>
                </div>

                <!-- Tujuan Pinjaman -->
                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-heading); margin-bottom: 12px;">Tujuan Pinjaman yang Didukung:</h4>
                <ul style="list-style: none; padding: 0; margin: 0 0 24px 0; font-size: 13.5px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 8px;">
                    <li><i class="fa-solid fa-briefcase" style="color: var(--emerald-600); width: 20px;"></i> <strong>Modal Usaha</strong> (Wajib melampirkan Bukti Usaha)</li>
                    <li><i class="fa-solid fa-graduation-cap" style="color: var(--emerald-600); width: 20px;"></i> <strong>Pendidikan</strong> (Biaya sekolah/kuliah)</li>
                    <li><i class="fa-solid fa-basket-shopping" style="color: var(--emerald-600); width: 20px;"></i> <strong>Kebutuhan Rumah Tangga</strong> (Kebutuhan pokok keluarga)</li>
                    <li><i class="fa-solid fa-house" style="color: var(--emerald-600); width: 20px;"></i> <strong>Renovasi Rumah</strong> (Perbaikan tempat tinggal)</li>
                    <li><i class="fa-solid fa-circle-question" style="color: var(--emerald-600); width: 20px;"></i> <strong>Keperluan Lainnya</strong> (Keperluan mendesak yang sah)</li>
                </ul>

                <a href="{{ route('public.loans') }}" class="btn btn-outline-emerald">
                    Rincian Lengkap Pinjaman &rarr;
                </a>
            </div>

            <!-- Kalkulator Simulasi Interaktif -->
            <div class="calc-card" id="loanCalculator">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                        <i class="fa-solid fa-calculator" style="color: var(--emerald-600); margin-right: 6px;"></i> Simulasi Angsuran
                    </h3>
                    <span class="badge badge-emerald">Kalkulator Interaktif</span>
                </div>

                <!-- Input Nominal Pinjaman -->
                <div class="calc-range-wrapper">
                    <div class="calc-range-labels">
                        <label for="simNominalInput" style="color: var(--text-heading);">Jumlah Pinjaman:</label>
                        <strong style="color: var(--emerald-700); font-size: 16px;" id="simNominalDisplay">Rp 10.000.000</strong>
                    </div>
                    <input type="range" class="calc-range-slider" id="simNominalRange" min="5000000" max="50000000" step="500000" value="10000000">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                        <span>Rp 5.000.000</span>
                        <span>Rp 50.000.000</span>
                    </div>
                </div>

                <!-- Pilihan Tenor -->
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-weight: 600; font-size: 13.5px; color: var(--text-heading); margin-bottom: 8px;">Pilih Jangka Waktu (Tenor):</label>
                    <div class="calc-tenor-group" id="tenorButtonsGroup">
                        <button type="button" class="calc-tenor-btn" data-tenor="6" data-rate="5.0">6 Bulan<br><small style="color: var(--text-muted);">Jasa 5%</small></button>
                        <button type="button" class="calc-tenor-btn active" data-tenor="12" data-rate="7.0">12 Bulan<br><small style="color: #FFFFFF;">Jasa 7%</small></button>
                        <button type="button" class="calc-tenor-btn" data-tenor="18" data-rate="8.5">18 Bulan<br><small style="color: var(--text-muted);">Jasa 8.5%</small></button>
                        <button type="button" class="calc-tenor-btn" data-tenor="24" data-rate="10.0">24 Bulan<br><small style="color: var(--text-muted);">Jasa 10%</small></button>
                    </div>
                </div>

                <!-- Ringkasan Hasil Simulasi -->
                <div class="calc-summary-box">
                    <div class="calc-summary-row">
                        <span>Pokok Pinjaman:</span>
                        <strong class="tabular-nums" id="resPokok">Rp 10.000.000</strong>
                    </div>
                    <div class="calc-summary-row">
                        <span>Tingkat Jasa Koperasi:</span>
                        <span id="resRatePercent">7.0%</span>
                    </div>
                    <div class="calc-summary-row">
                        <span>Total Jasa Koperasi:</span>
                        <strong class="tabular-nums" id="resTotalJasa">Rp 700.000</strong>
                    </div>
                    <div class="calc-summary-row">
                        <span>Total Kewajiban Pembayaran:</span>
                        <strong class="tabular-nums" id="resTotalKewajiban">Rp 10.700.000</strong>
                    </div>
                    <div class="calc-summary-row total-row">
                        <span>Estimasi Angsuran / Bulan:</span>
                        <span class="tabular-nums" style="font-size: 20px;" id="resAngsuranBulan">Rp 891.667</span>
                    </div>
                </div>

                <p style="font-size: 11.5px; color: var(--text-muted); line-height: 1.5; margin-top: 14px; margin-bottom: 20px;">
                    * <em>Simulasi bersifat estimasi. Nilai angsuran akhir mengikuti perhitungan sistem dan dapat mengalami penyesuaian pada angsuran terakhir karena pembulatan. Jadwal resmi ditetapkan setelah proses verifikasi dan persetujuan pinjaman.</em>
                </p>

                @auth
                    @if(auth()->user()->role === 'member')
                        <a href="{{ route('member.loans.create') }}" class="btn btn-primary" style="width: 100%;">
                            Lanjutkan Pengajuan Pinjaman &rarr;
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="width: 100%;">
                            Buka Dashboard Admin &rarr;
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">
                        Daftar Sebagai Anggota & Ajukan Pinjaman &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- ==================== 5. ALUR & CARA KERJA ==================== -->
<section class="section-padding" style="background: #F8FAFC; border-top: 1px solid var(--border-subtle);">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Transparansi Proses</span>
            <h2 class="section-title">Alur Layanan Koperasi KOPIN</h2>
            <p class="section-subtitle">
                8 langkah mudah dari pendaftaran hingga penyelesaian pinjaman yang aman dan transparan.
            </p>
        </div>

        <div class="step-grid">
            <div class="step-card">
                <div class="step-number-badge">1</div>
                <h4 class="step-title">Daftar Anggota</h4>
                <p class="step-desc">Isi formulir pendaftaran dengan identitas KTP yang valid.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">2</div>
                <h4 class="step-title">Verifikasi Admin</h4>
                <p class="step-desc">Administrator memeriksa keabsahan data identitas Anda.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">3</div>
                <h4 class="step-title">Penuhi Simpanan</h4>
                <p class="step-desc">Bayar Simpanan Pokok (Rp55rb) dan Simpanan Wajib bulanan.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">4</div>
                <h4 class="step-title">Ajukan Pinjaman</h4>
                <p class="step-desc">Pilih jumlah, tenor, tujuan, dan lampirkan dokumen persyaratan.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">5</div>
                <h4 class="step-title">Review Kelayakan</h4>
                <p class="step-desc">Admin melakukan telaah kesesuaian dokumen dan kemampuan bayar.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">6</div>
                <h4 class="step-title">Persetujuan Resmi</h4>
                <p class="step-desc">Pinjaman disetujui (Approved) secara resmi oleh Koperasi.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">7</div>
                <h4 class="step-title">Pencairan Dana</h4>
                <p class="step-desc">Dana ditransfer ke rekening bank anggota & jadwal angsuran aktif.</p>
            </div>

            <div class="step-card">
                <div class="step-number-badge">8</div>
                <h4 class="step-title">Bayar Angsuran</h4>
                <p class="step-desc">Lakukan pembayaran bulanan tepat waktu hingga pinjaman lunas.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('public.how-it-works') }}" class="btn btn-outline">
                Pelajari Detail Alur & Dokumen &rarr;
            </a>
        </div>
    </div>
</section>

<!-- ==================== 6. PERSYARATAN DOKUMEN ==================== -->
<section class="section-padding" style="background: #FFFFFF; border-top: 1px solid var(--border-subtle);">
    <div class="section-container">
        <div class="section-header">
            <span class="section-tag">Ketentuan Berkas</span>
            <h2 class="section-title">Dokumen Persyaratan Pengajuan</h2>
            <p class="section-subtitle">
                Persiapkan dokumen berikut untuk kelancaran verifikasi pengajuan pinjaman Anda.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; max-width: 1000px; margin: 0 auto;">
            <div class="card" style="padding: 24px; border-left: 4px solid var(--emerald-600);">
                <div style="font-size: 20px; color: var(--emerald-600); margin-bottom: 12px;"><i class="fa-solid fa-id-card"></i></div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700;">1. KTP Elektronik</h4>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Kartu Tanda Penduduk WNI yang masih berlaku dan terbaca jelas.</p>
            </div>

            <div class="card" style="padding: 24px; border-left: 4px solid var(--emerald-600);">
                <div style="font-size: 20px; color: var(--emerald-600); margin-bottom: 12px;"><i class="fa-solid fa-receipt"></i></div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700;">2. Bukti Penghasilan</h4>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Slip gaji resmi (karyawan) atau mutasi rekening koran 3 bulan terakhir.</p>
            </div>

            <div class="card" style="padding: 24px; border-left: 4px solid var(--emerald-600);">
                <div style="font-size: 20px; color: var(--emerald-600); margin-bottom: 12px;"><i class="fa-solid fa-credit-card"></i></div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700;">3. Rekening Bank</h4>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Buku tabungan bank aktif atas nama pemohon sendiri untuk pencairan.</p>
            </div>

            <div class="card" style="padding: 24px; border-left: 4px solid #F59E0B;">
                <div style="font-size: 20px; color: #F59E0B; margin-bottom: 12px;"><i class="fa-solid fa-store"></i></div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 700;">4. Bukti Usaha (Khusus)</h4>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Surat Keterangan Usaha / foto lokasi usaha (wajib untuk Modal Usaha).</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== 7. CTA BANNER ==================== -->
<section style="background: linear-gradient(135deg, #065F46 0%, #047857 100%); color: #FFFFFF; padding: 70px 24px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 34px; font-weight: 800; margin-bottom: 16px; color: #FFFFFF;">
            Siap Menjadi Bagian dari Koperasi KOPIN?
        </h2>
        <p style="font-size: 16px; line-height: 1.6; color: #A7F3D0; margin-bottom: 32px;">
            Daftarkan diri Anda hari ini, nikmati kemudahan simpanan gotong royong, dan ajukan pembiayaan usaha dengan aman dan terpercaya.
        </p>
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            @auth
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('member.dashboard') }}" class="btn btn-lg" style="background: #FFFFFF; color: #065F46; font-weight: 700;">
                    Buka Dashboard Saya
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-lg" style="background: #FFFFFF; color: #065F46; font-weight: 700;">
                    Daftar Keanggotaan Baru
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline btn-lg" style="border-color: #A7F3D0; color: #FFFFFF;">
                    Masuk ke Akun
                </a>
            @endauth
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.getElementById('simNominalRange');
        const display = document.getElementById('simNominalDisplay');
        const tenorBtns = document.querySelectorAll('.calc-tenor-btn');
        
        const resPokok = document.getElementById('resPokok');
        const resRatePercent = document.getElementById('resRatePercent');
        const resTotalJasa = document.getElementById('resTotalJasa');
        const resTotalKewajiban = document.getElementById('resTotalKewajiban');
        const resAngsuranBulan = document.getElementById('resAngsuranBulan');

        let selectedTenor = 12;
        let selectedRate = 7.0;

        function formatRupiah(num) {
            return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function recalculate() {
            const nominal = parseFloat(slider.value) || 10000000;
            display.textContent = formatRupiah(nominal);

            const totalJasa = Math.round(nominal * (selectedRate / 100) * 100) / 100;
            const totalKewajiban = Math.round((nominal + totalJasa) * 100) / 100;
            const angsuranPerBulan = Math.round(totalKewajiban / selectedTenor);

            resPokok.textContent = formatRupiah(nominal);
            resRatePercent.textContent = selectedRate.toFixed(1) + '%';
            resTotalJasa.textContent = formatRupiah(totalJasa);
            resTotalKewajiban.textContent = formatRupiah(totalKewajiban);
            resAngsuranBulan.textContent = formatRupiah(angsuranPerBulan);
        }

        if (slider) {
            slider.addEventListener('input', recalculate);
        }

        tenorBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                tenorBtns.forEach(b => {
                    b.classList.remove('active');
                    const small = b.querySelector('small');
                    if (small) small.style.color = 'var(--text-muted)';
                });
                this.classList.add('active');
                const activeSmall = this.querySelector('small');
                if (activeSmall) activeSmall.style.color = '#FFFFFF';

                selectedTenor = parseInt(this.getAttribute('data-tenor')) || 12;
                selectedRate = parseFloat(this.getAttribute('data-rate')) || 7.0;

                recalculate();
            });
        });

        recalculate();
    });
</script>
@endpush
