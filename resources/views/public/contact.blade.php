@extends('layouts.public')

@section('title', 'Kontak & Pusat Bantuan KOPIN — Koperasi Simpan Pinjam')
@section('meta_description', 'Hubungi layanan anggota KOPIN untuk informasi keanggotaan, simpanan, dan pinjaman. Kunjungi kantor kami atau kirim pesan melalui portal resmi.')

@section('content')

<!-- Header Banner -->
<section style="background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%); padding: 56px 24px; border-bottom: 1px solid var(--border-subtle);">
    <div class="section-container" style="text-align: center; max-width: 800px;">
        <span class="section-tag">Pusat Informasi</span>
        <h1 class="section-title" style="font-size: 38px; margin-bottom: 16px;">
            Hubungi Layanan KOPIN
        </h1>
        <p class="section-subtitle">
            Tim layanan dan pengurus KOPIN siap membantu menjawab pertanyaan seputar keanggotaan, simpanan, dan prosedur pinjaman koperasi.
        </p>
    </div>
</section>

<!-- Konten Kontak -->
<section class="section-padding" style="background: #FFFFFF;">
    <div class="section-container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: flex-start; max-width: 1050px; margin: 0 auto;">
            
            <!-- Info Kantor & Jam Operasional -->
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--text-heading); margin-bottom: 16px;">
                    Kantor Layanan Operasional
                </h2>
                <p style="font-size: 14.5px; line-height: 1.7; color: var(--text-secondary); margin-bottom: 32px;">
                    Anggota dapat berkonsultasi, mengurus verifikasi dokumen fisik, maupun melakukan transaksi penyetoran kasir secara langsung pada hari dan jam kerja.
                </p>

                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <strong style="color: var(--text-heading); font-size: 15px; display: block; margin-bottom: 4px;">Alamat Kantor:</strong>
                            <p style="margin: 0; font-size: 13.5px; color: var(--text-secondary); line-height: 1.5;">
                                <strong>Gedung Koperasi KOPIN</strong><br>
                                Kawasan Usaha Terpadu, Jakarta, Indonesia<br>
                                <small style="color: var(--text-muted);">(Alamat representatif konfigurasi sistem)</small>
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <strong style="color: var(--text-heading); font-size: 15px; display: block; margin-bottom: 4px;">Jam Pelayanan Anggota:</strong>
                            <p style="margin: 0; font-size: 13.5px; color: var(--text-secondary); line-height: 1.5;">
                                <strong>Senin – Jumat:</strong> 08:00 – 16:00 WIB<br>
                                <strong>Sabtu, Minggu & Hari Libur Nasional:</strong> Tutup
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <strong style="color: var(--text-heading); font-size: 15px; display: block; margin-bottom: 4px;">Email Pelayanan:</strong>
                            <p style="margin: 0; font-size: 13.5px; color: var(--text-secondary);">
                                layanan@kopin.id / info@kopin.id
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <strong style="color: var(--text-heading); font-size: 15px; display: block; margin-bottom: 4px;">Telepon & Helpdesk:</strong>
                            <p style="margin: 0; font-size: 13.5px; color: var(--text-secondary);">
                                (021) 5000-KOPIN / +62 812-XXXX-XXXX
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Kontak Pertanyaan -->
            <div class="card card-elevated" style="padding: 32px; border-top: 4px solid var(--emerald-600);">
                <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 800; color: var(--text-heading);">
                    Kirim Pesan Pertanyaan
                </h3>
                <p style="font-size: 13.5px; color: var(--text-secondary); margin-bottom: 24px;">
                    Isi formulir di bawah ini untuk pertanyaan atau kendala seputar layanan KOPIN.
                </p>

                <div id="contactSuccessAlert" class="alert alert-success" style="display: none;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> Pesan Anda telah diterima oleh helpdesk KOPIN. Petugas kami akan menghubungi Anda pada hari kerja.
                </div>

                <form id="contactForm" onsubmit="event.preventDefault(); document.getElementById('contactSuccessAlert').style.display = 'block'; this.reset();">
                    <div class="form-group">
                        <label for="contact_name" class="form-label">Nama Lengkap:</label>
                        <input type="text" id="contact_name" class="form-control" placeholder="Nama Anda" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_email" class="form-label">Alamat Email:</label>
                        <input type="email" id="contact_email" class="form-control" placeholder="email@contoh.com" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_subject" class="form-label">Perihal Pertanyaan:</label>
                        <select id="contact_subject" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="keanggotaan">Informasi Keanggotaan Baru</option>
                            <option value="simpanan">Simpanan Pokok & Wajib</option>
                            <option value="pinjaman">Pengajuan Pinjaman & Syarat</option>
                            <option value="teknis">Bantuan Akun Portal Anggota</option>
                            <option value="lainnya">Pertanyaan Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact_message" class="form-label">Isi Pesan:</label>
                        <textarea id="contact_message" class="form-control" rows="4" placeholder="Tuliskan pertanyaan atau kendala Anda..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Pesan ke Helpdesk
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

@endsection
