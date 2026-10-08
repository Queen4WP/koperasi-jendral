@extends('layouts.public')

@section('title', 'Tentang KOPIN — Profil, Visi, Misi & Asas Koperasi Simpan Pinjam')
@section('meta_description', 'Kenali KOPIN, Koperasi Simpan Pinjam berbadan hukum yang melayani karyawan dan masyarakat umum dengan asas gotong royong dan tata kelola transparan.')

@section('content')

<!-- Header Banner -->
<section style="background: linear-gradient(180deg, #FFFFFF 0%, #F1F5F9 100%); padding: 56px 24px; border-bottom: 1px solid var(--border-subtle);">
    <div class="section-container" style="text-align: center; max-width: 800px;">
        <span class="section-tag">Profil Institusi</span>
        <h1 class="section-title" style="font-size: 38px; margin-bottom: 16px;">
            Mengenal Koperasi Simpan Pinjam KOPIN
        </h1>
        <p class="section-subtitle">
            Wadah ekonomi gotong royong yang hadir untuk memberikan solusi simpanan terpercaya dan pembiayaan pinjaman yang adil bagi seluruh anggota.
        </p>
    </div>
</section>

<!-- Konten Profil & Nilai -->
<section class="section-padding" style="background: #FFFFFF;">
    <div class="section-container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; margin-bottom: 64px;">
            <div>
                <span class="section-tag">Jati Diri Koperasi</span>
                <h2 style="font-size: 28px; font-weight: 800; color: var(--text-heading); margin-bottom: 16px;">
                    Dari Anggota, Oleh Anggota, Untuk Kemakmuran Anggota
                </h2>
                <p style="font-size: 15px; line-height: 1.7; color: var(--text-secondary); margin-bottom: 16px;">
                    <strong>KOPIN</strong> merupakan Koperasi Simpan Pinjam yang didirikan atas dasar kebersamaan dan kekeluargaan. Kami meyakini bahwa kemandirian ekonomi dapat dicapai ketika masyarakat dan karyawan bersatu menghimpun kekuatan modal melalui simpanan yang disiplin dan penyaluran pinjaman yang produktif.
                </p>
                <p style="font-size: 15px; line-height: 1.7; color: var(--text-secondary);">
                    Berbeda dengan institusi komersial umum, KOPIN tidak berorientasi pada keuntungan sepihak, melainkan fokus pada kemudahan akses keuangan anggota, perlindungan dari beban bunga yang memberatkan, serta transparansi mutlak di setiap tahapan pencatatan.
                </p>
            </div>
            
            <div class="card card-elevated" style="padding: 32px; border-left: 4px solid var(--emerald-600); background: #F8FAFC;">
                <h3 style="font-size: 18px; font-weight: 800; color: var(--text-heading); margin-bottom: 16px;">
                    <i class="fa-solid fa-users-gear" style="color: var(--emerald-600); margin-right: 8px;"></i> Siapa yang Dapat Menjadi Anggota?
                </h3>
                <div style="display: flex; flex-direction: column; gap: 16px; font-size: 14px; color: var(--text-secondary);">
                    <div style="display: flex; gap: 12px;">
                        <i class="fa-solid fa-id-badge" style="color: var(--emerald-600); font-size: 18px; margin-top: 2px;"></i>
                        <div>
                            <strong style="color: var(--text-heading); display: block;">Karyawan Perusahaan & Mitra</strong>
                            <span>Pegawai dan staf yang ingin mengelola keuangan, menabung teratur, dan mengakses pembiayaan kebutuhan keluarga.</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <i class="fa-solid fa-shop" style="color: var(--emerald-600); font-size: 18px; margin-top: 2px;"></i>
                        <div>
                            <strong style="color: var(--text-heading); display: block;">Masyarakat Umum & Pelaku Usaha</strong>
                            <span>Warga negara Indonesia yang memiliki usaha produktif atau penghasilan mandiri yang membutuhkan permodalan sehat.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visi & Misi -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 64px;">
            <div class="card" style="padding: 36px; border-top: 4px solid var(--emerald-600);">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(5,150,105,0.1); color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 20px;">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 800; color: var(--text-heading); margin-bottom: 12px;">Visi KOPIN</h3>
                <p style="font-size: 14.5px; line-height: 1.7; color: var(--text-secondary); margin: 0;">
                    Menjadi koperasi simpan pinjam terkemuka di Indonesia yang modern, terpercaya, dan berdaya saing tinggi dalam memperkuat ketahanan ekonomi anggota melalui tata kelola berintegritas dan teknologi digital.
                </p>
            </div>

            <div class="card" style="padding: 36px; border-top: 4px solid #3B82F6;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59,130,246,0.1); color: #3B82F6; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 20px;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 800; color: var(--text-heading); margin-bottom: 12px;">Misi KOPIN</h3>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px; line-height: 1.5;">
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> Mengembangkan budaya menabung yang teratur dan amanah bagi anggota.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> Menyalurkan pembiayaan dengan persyaratan mudah dan jasa pinjaman adil.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> Menerapkan keterbukaan pelaporan keuangan yang akuntabel dan profesional.</li>
                    <li><i class="fa-solid fa-circle-check" style="color: #3B82F6; margin-right: 8px;"></i> Menghadirkan portal layanan digital yang ramah bagi pengguna setiap waktu.</li>
                </ul>
            </div>
        </div>

        <!-- 4 Pilar Nilai Koperasi -->
        <div class="section-header" style="margin-bottom: 40px;">
            <span class="section-tag">Prinsip Kerja</span>
            <h2 class="section-title">Nilai-Nilai Utama KOPIN</h2>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon-box"><i class="fa-solid fa-heart-pulse"></i></div>
                <h4 class="feature-title">Kekeluargaan</h4>
                <p class="feature-desc">Mengedepankan empati dan musyawarah dalam melayani setiap kebutuhan dan solusi kendala anggota.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon-box"><i class="fa-solid fa-book-open"></i></div>
                <h4 class="feature-title">Transparansi</h4>
                <p class="feature-desc">Semua formula perhitungan simpanan, jasa pinjaman, dan tanggal jatuh tempo dapat diaudit langsung oleh anggota.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon-box"><i class="fa-solid fa-award"></i></div>
                <h4 class="feature-title">Integritas</h4>
                <p class="feature-desc">Pengelolaan dana simpanan dijalankan dengan tata kelola verifikasi ketat demi menjamin keamanan aset bersama.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon-box"><i class="fa-solid fa-hand-holding-heart"></i></div>
                <h4 class="feature-title">Pemberdayaan</h4>
                <p class="feature-desc">Membantu anggota mencapai kesejahteraan mandiri melalui pembiayaan modal kerja dan kebutuhan primer.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background: #F8FAFC; padding: 50px 24px; text-align: center; border-top: 1px solid var(--border-subtle);">
    <div style="max-width: 600px; margin: 0 auto;">
        <h3 style="font-size: 22px; font-weight: 800; color: var(--text-heading); margin-bottom: 12px;">Ingin Menjadi Anggota KOPIN?</h3>
        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 24px;">Pendaftaran dilakukan secara online dengan melampirkan data identitas diri yang valid.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Keanggotaan Baru</a>
    </div>
</section>

@endsection
