@extends('layouts.public')

@section('title', 'Alur & Cara Kerja KOPIN — 8 Langkah Layanan Koperasi Simpan Pinjam')
@section('meta_description', 'Pahami 8 tahapan alur layanan KOPIN mulai dari registrasi anggota, verifikasi berkas, pemenuhan simpanan, hingga pengajuan dan pencairan pinjaman.')

@section('content')

<!-- Header Banner -->
<section style="background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%); padding: 56px 24px; border-bottom: 1px solid var(--border-subtle);">
    <div class="section-container" style="text-align: center; max-width: 800px;">
        <span class="section-tag">Transparansi Operasional</span>
        <h1 class="section-title" style="font-size: 38px; margin-bottom: 16px;">
            Alur & Cara Kerja Layanan KOPIN
        </h1>
        <p class="section-subtitle">
            KOPIN menerapkan tata kelola yang teratur, aman, dan berintegritas. Setiap pengajuan dan transaksi melalui verifikasi resmi oleh pengurus dan administrator koperasi.
        </p>
    </div>
</section>

<!-- 8 Tahapan Alur Detail -->
<section class="section-padding" style="background: #FFFFFF;">
    <div class="section-container" style="max-width: 900px;">
        <div style="display: flex; flex-direction: column; gap: 32px;">
            
            <!-- Step 1 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid var(--emerald-600);">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">1</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Pendaftaran Anggota Baru Secara Online
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Calon anggota mengisi formulir pendaftaran melalui portal KOPIN dengan melampirkan identitas KTP yang sah, nomor telepon aktif, email, serta informasi pekerjaan. Akun akan terdaftar dengan status peninjauan (<em>pending</em>).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid var(--emerald-600);">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">2</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Verifikasi Data Calon Anggota oleh Admin
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Administrator KOPIN melakukan verifikasi kelengkapan dan keabsahan berkas identitas calon anggota. Verifikasi ini dilakukan secara cermat guna menjaga keamanan dan kepatuhan sistem keanggotaan koperasi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid var(--emerald-600);">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">3</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Pemenuhan Kewajiban Simpanan Anggota
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Anggota menyetorkan Simpanan Pokok (Rp 55.000, 1x) dan membayar Simpanan Wajib bulanan (Rp 100.000/bln). Bukti pembayaran diunggah melalui portal dan diverifikasi oleh admin. Kepatuhan simpanan ini menjadi syarat mutlak untuk memperoleh status keanggotaan aktif.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid var(--emerald-600);">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0;">4</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Pengajuan Pinjaman & Unggah Dokumen Pendukung
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Anggota yang memenuhi syarat kelayakan memilih jumlah pinjaman (Rp 5jt – Rp 50jt), jangka waktu tenor (6, 12, 18, atau 24 bulan), mencantumkan nomor rekening bank atas nama sendiri, serta mengunggah dokumen persyaratan (KTP, Slip Gaji/Rekening Koran, Rekening Bank, dan Bukti Usaha bila tujuan Modal Usaha).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid #F59E0B;">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0; background: #F59E0B;">5</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Review Kelayakan & Dokumen oleh Administrator
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Pengajuan masuk ke status <em>under review</em>. Petugas Admin meneliti keabsahan dokumen, riwayat simpanan, serta kapasitas pembiayaan anggota. <strong>Persetujuan tidak dilakukan secara otomatis</strong>, melainkan melalui evaluasi manual yang objektif.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 6 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid #10B981;">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0; background: #10B981;">6</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Persetujuan Resmi Pinjaman (Approved)
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Jika seluruh dokumen dan syarat kelayakan terpenuhi, Administrator memberikan persetujuan resmi (status berubah menjadi <em>Approved</em>). Anggota dapat melihat status persetujuan langsung di portal pribadinya.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 7 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid #059669;">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0; background: #059669;">7</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Pencairan Dana (Disbursement) & Penerbitan Jadwal Angsuran
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Admin mencairkan dana sesuai nominal yang diajukan ke rekening bank anggota dan mencatat nomor referensi transaksi. Pinjaman otomatis bertransisi menjadi <strong>Active</strong> dan sistem secara otomatis menerbitkan jadwal angsuran resmi dengan jatuh tempo berbasis tanggal pencairan aktual.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 8 -->
            <div class="card card-elevated" style="padding: 28px; border-left: 5px solid #3B82F6;">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <div class="step-number-badge" style="margin-bottom: 0; width: 44px; height: 44px; font-size: 18px; flex-shrink: 0; background: #3B82F6;">8</div>
                    <div>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: var(--text-heading);">
                            Pembayaran Angsuran Bulanan Hingga Lunas
                        </h3>
                        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin: 0;">
                            Anggota membayar angsuran setiap bulan sebelum tanggal jatuh tempo melalui transfer bank atau kasir koperasi. Setiap pembayaran diverifikasi oleh admin hingga pinjaman tercatat lunas penuh (<em>Paid Off</em>).
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Note on Discretion -->
        <div class="alert alert-info" style="margin-top: 48px;">
            <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i>
            <strong>Pemberitahuan Penting:</strong> KOPIN tidak menjanjikan pencairan instan tanpa audit. Seluruh proses review dan pencairan dikendalikan secara ketat oleh pengurus dan administrator demi menjamin integritas finansial dan keselamatan aset anggota bersama.
        </div>
    </div>
</section>

@endsection
