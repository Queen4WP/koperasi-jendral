@extends('layouts.public')

@section('title', 'Produk Pinjaman KOPIN — Plafon, Tenor, Suku Jasa & Simulasi Angsuran')
@section('meta_description', 'Pelajari fasilitas pinjaman anggota KOPIN dari Rp 5.000.000 hingga Rp 50.000.000 dengan jasa transparan 5% - 10%. Hitung estimasi angsuran bulanan Anda.')

@section('content')

<!-- Header Banner -->
<section style="background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%); padding: 56px 24px; border-bottom: 1px solid var(--border-subtle);">
    <div class="section-container" style="text-align: center; max-width: 800px;">
        <span class="section-tag">Pembiayaan Anggota</span>
        <h1 class="section-title" style="font-size: 38px; margin-bottom: 16px;">
            Fasilitas Pinjaman Koperasi KOPIN
        </h1>
        <p class="section-subtitle">
            Solusi pembiayaan yang mudah, adil, dan transparan untuk mendukung pertumbuhan usaha produktif dan kebutuhan keluarga anggota.
        </p>
    </div>
</section>

<!-- Detail Pinjaman & Matriks -->
<section class="section-padding" style="background: #FFFFFF;">
    <div class="section-container">
        <!-- 3 Highlight Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 56px;">
            <div class="card card-elevated" style="padding: 28px; border-top: 4px solid var(--emerald-600);">
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Plafon Pinjaman</div>
                <div style="font-size: 24px; font-weight: 800; color: var(--emerald-700); margin: 6px 0;">Rp 5 Jt – Rp 50 Jt</div>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Fleksibel disesuaikan dengan kapasitas dan riwayat kepatuhan anggota.</p>
            </div>

            <div class="card card-elevated" style="padding: 28px; border-top: 4px solid #3B82F6;">
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Jangka Waktu (Tenor)</div>
                <div style="font-size: 24px; font-weight: 800; color: #1D4ED8; margin: 6px 0;">6, 12, 18, 24 Bulan</div>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Pilihan tenor tetap bulanan dengan jadwal angsuran pasti.</p>
            </div>

            <div class="card card-elevated" style="padding: 28px; border-top: 4px solid #F59E0B;">
                <div style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Tingkat Jasa Koperasi</div>
                <div style="font-size: 24px; font-weight: 800; color: #B45309; margin: 6px 0;">5.0% – 10.0% Flat</div>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Jasa pinjaman terkunci dan dihitung transparan tanpa biaya tersembunyi.</p>
            </div>
        </div>

        <!-- Matriks Tenor dan Tarif Jasa Resmi -->
        <div style="margin-bottom: 64px;">
            <div class="section-header" style="margin-bottom: 32px;">
                <span class="section-tag">Ketentuan Tarif</span>
                <h2 class="section-title">Matriks Tenor & Tarif Jasa Pinjaman</h2>
                <p class="section-subtitle">Tingkat jasa ditentukan secara baku berdasarkan tenor yang dipilih oleh anggota.</p>
            </div>

            <div class="table-responsive" style="max-width: 800px; margin: 0 auto; border-radius: 10px; border: 1px solid #E2E8F0; overflow: hidden;">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Jangka Waktu</th>
                            <th style="width: 25%;">Tingkat Jasa (Flat)</th>
                            <th style="width: 50%;">Keterangan & Peruntukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>6 Bulan</strong></td>
                            <td><span class="badge badge-emerald">5.0%</span></td>
                            <td>Pembiayaan jangka pendek, perputaran kas cepat</td>
                        </tr>
                        <tr>
                            <td><strong>12 Bulan</strong></td>
                            <td><span class="badge badge-emerald">7.0%</span></td>
                            <td>Pilihan terpopuler untuk kebutuhan tahunan anggota</td>
                        </tr>
                        <tr>
                            <td><strong>18 Bulan</strong></td>
                            <td><span class="badge badge-emerald">8.5%</span></td>
                            <td>Pembiayaan modal usaha menengah dan renovasi</td>
                        </tr>
                        <tr>
                            <td><strong>24 Bulan</strong></td>
                            <td><span class="badge badge-emerald">10.0%</span></td>
                            <td>Tenor maksimal dengan beban angsuran bulanan paling ringan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Simulasi Angsuran Pinjaman -->
        <div id="simulasi" style="max-width: 900px; margin: 0 auto 64px auto;">
            <div class="calc-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <span class="badge badge-emerald" style="margin-bottom: 6px;">Kalkulator Informatif</span>
                        <h3 style="margin: 0; font-size: 22px; font-weight: 800; color: var(--text-heading);">
                            Simulasi Perhitungan Angsuran KOPIN
                        </h3>
                    </div>
                    <div style="font-size: 28px; color: var(--emerald-600);">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                </div>

                <!-- Range Slider -->
                <div class="calc-range-wrapper">
                    <div class="calc-range-labels">
                        <label for="simNominalRange" style="color: var(--text-heading);">Pilih Jumlah Pinjaman:</label>
                        <strong style="color: var(--emerald-700); font-size: 18px;" id="simNominalDisplay">Rp 10.000.000</strong>
                    </div>
                    <input type="range" class="calc-range-slider" id="simNominalRange" min="5000000" max="50000000" step="500000" value="10000000">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                        <span>Min: Rp 5.000.000</span>
                        <span>Maks: Rp 50.000.000</span>
                    </div>
                </div>

                <!-- Tenor Selector -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: var(--text-heading); margin-bottom: 8px;">Pilih Tenor:</label>
                    <div class="calc-tenor-group" id="tenorButtonsGroup">
                        <button type="button" class="calc-tenor-btn" data-tenor="6" data-rate="5.0">6 Bulan (5%)</button>
                        <button type="button" class="calc-tenor-btn active" data-tenor="12" data-rate="7.0">12 Bulan (7%)</button>
                        <button type="button" class="calc-tenor-btn" data-tenor="18" data-rate="8.5">18 Bulan (8.5%)</button>
                        <button type="button" class="calc-tenor-btn" data-tenor="24" data-rate="10.0">24 Bulan (10%)</button>
                    </div>
                </div>

                <!-- Hasil Perhitungan -->
                <div class="calc-summary-box">
                    <div class="calc-summary-row">
                        <span>Jumlah Pokok:</span>
                        <strong class="tabular-nums" id="resPokok">Rp 10.000.000</strong>
                    </div>
                    <div class="calc-summary-row">
                        <span>Jasa Pinjaman:</span>
                        <span id="resRatePercent">7.0%</span>
                    </div>
                    <div class="calc-summary-row">
                        <span>Total Jasa:</span>
                        <strong class="tabular-nums" id="resTotalJasa">Rp 700.000</strong>
                    </div>
                    <div class="calc-summary-row">
                        <span>Total Kewajiban:</span>
                        <strong class="tabular-nums" id="resTotalKewajiban">Rp 10.700.000</strong>
                    </div>
                    <div class="calc-summary-row total-row">
                        <span>Estimasi Angsuran / Bulan:</span>
                        <span class="tabular-nums" style="font-size: 22px;" id="resAngsuranBulan">Rp 891.667</span>
                    </div>
                </div>

                <div class="alert alert-info" style="margin-top: 20px; margin-bottom: 20px; font-size: 12.5px;">
                    <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i>
                    <strong>Catatan Simulasi:</strong> Simulasi bersifat estimasi. Nilai angsuran akhir mengikuti perhitungan sistem dan dapat mengalami penyesuaian pada angsuran terakhir karena pembulatan. Besaran resmi serta jadwal angsuran final ditetapkan saat pinjaman disetujui dan dicairkan oleh Administrator KOPIN.
                </div>

                @auth
                    @if(auth()->user()->role === 'member')
                        <a href="{{ route('member.loans.create') }}" class="btn btn-primary btn-lg" style="width: 100%;">
                            <i class="fa-solid fa-file-signature"></i> Masuk Form Pengajuan Pinjaman
                        </a>
                    @else
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-primary btn-lg" style="width: 100%;">
                            <i class="fa-solid fa-gauge"></i> Kelola Pinjaman (Admin)
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="width: 100%;">
                        <i class="fa-solid fa-user-plus"></i> Daftar Anggota untuk Mengajukan Pinjaman
                    </a>
                @endauth
            </div>
        </div>

        <!-- 5 Tujuan Pinjaman yang Didukung -->
        <div style="margin-bottom: 64px;">
            <div class="section-header" style="margin-bottom: 32px;">
                <span class="section-tag">Peruntukan Pinjaman</span>
                <h2 class="section-title">Tujuan Pinjaman yang Didukung</h2>
                <p class="section-subtitle">KOPIN mendukung berbagai kebutuhan finansial yang produktif dan esensial.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon-box" style="background: rgba(245,158,11,0.1); color: #D97706;"><i class="fa-solid fa-briefcase"></i></div>
                    <h4 class="feature-title">Modal Usaha</h4>
                    <p class="feature-desc">Tambahan modal kerja, pembelian stok barang, atau perluasan sarana usaha. <em>Wajib melampirkan Bukti Usaha (SKU/Foto Tempat Usaha).</em></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-box"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h4 class="feature-title">Pendidikan</h4>
                    <p class="feature-desc">Pembiayaan uang pangkal sekolah, SPP semester, buku pelajaran, atau perlengkapan pendidikan anggota dan keluarga.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-box"><i class="fa-solid fa-house-chimney"></i></div>
                    <h4 class="feature-title">Kebutuhan Rumah Tangga</h4>
                    <p class="feature-desc">Pemenuhan kebutuhan pokok keluarga, peralatan esensial, atau keperluan darurat rumah tangga anggota.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-box"><i class="fa-solid fa-hammer"></i></div>
                    <h4 class="feature-title">Renovasi Rumah</h4>
                    <p class="feature-desc">Perbaikan atap, renovasi sanitasi, penambahan ruang, atau pemeliharaan tempat tinggal anggota agar lebih layak.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-box"><i class="fa-solid fa-layer-group"></i></div>
                    <h4 class="feature-title">Keperluan Lainnya</h4>
                    <p class="feature-desc">Keperluan produktif atau esensial lain yang sah dan dapat dipertanggungjawabkan dalam pengajuan resmi.</p>
                </div>
            </div>
        </div>

        <!-- Dokumen Persyaratan -->
        <div class="card" style="padding: 32px; background: #F8FAFC; border: 1px solid var(--border-subtle);">
            <h3 style="font-size: 20px; font-weight: 800; color: var(--text-heading); margin-top: 0; margin-bottom: 16px;">
                <i class="fa-solid fa-file-circle-check" style="color: var(--emerald-600); margin-right: 8px;"></i>
                Syarat & Dokumen Wajib Pengajuan Pinjaman
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; font-size: 13.5px;">
                <div>
                    <strong>1. Identitas KTP:</strong>
                    <p style="color: var(--text-secondary); margin: 4px 0 0 0;">KTP WNI asli yang masih berlaku, foto jelas dan tidak buram.</p>
                </div>
                <div>
                    <strong>2. Bukti Penghasilan:</strong>
                    <p style="color: var(--text-secondary); margin: 4px 0 0 0;">Slip gaji resmi karyawan atau mutasi rekening koran 3 bulan terakhir.</p>
                </div>
                <div>
                    <strong>3. Rekening Bank:</strong>
                    <p style="color: var(--text-secondary); margin: 4px 0 0 0;">Buku tabungan rekening bank aktif atas nama anggota sendiri.</p>
                </div>
                <div>
                    <strong>4. Bukti Usaha (Khusus):</strong>
                    <p style="color: var(--text-secondary); margin: 4px 0 0 0;">Surat Keterangan Usaha / foto fisik usaha untuk pengajuan Modal Usaha.</p>
                </div>
            </div>
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
                tenorBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                selectedTenor = parseInt(this.getAttribute('data-tenor')) || 12;
                selectedRate = parseFloat(this.getAttribute('data-rate')) || 7.0;

                recalculate();
            });
        });

        recalculate();
    });
</script>
@endpush
