<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KOPIN — Koperasi Simpan Pinjam Modern & Terpercaya')</title>
    <meta name="description" content="@yield('meta_description', 'KOPIN adalah Koperasi Simpan Pinjam terpercaya yang melayani karyawan dan masyarakat dengan asas gotong royong, transparansi keuangan, dan kemudahan akses digital.')">
    <meta name="keywords" content="koperasi, simpan pinjam, pinjaman koperasi, simpanan pokok, simpanan wajib, kopin, pinjaman dana usaha">
    
    <!-- Open Graph Meta -->
    <meta property="og:title" content="@yield('title', 'KOPIN — Koperasi Simpan Pinjam')">
    <meta property="og:description" content="@yield('meta_description', 'Solusi simpanan dan pinjaman amanah untuk kesejahteraan bersama.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- CSS Core Tokens & Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    @stack('styles')
</head>
<body>

    <!-- ==================== PUBLIC HEADER & NAVBAR ==================== -->
    <header class="site-header" id="siteHeader">
        <div class="site-nav-container">
            <a href="{{ route('home') }}" class="site-brand" aria-label="Beranda KOPIN">
                <div class="brand-badge">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div class="brand-info">
                    <span class="brand-name">KOPIN</span>
                    <span class="brand-tagline">Koperasi Simpan Pinjam</span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav aria-label="Navigasi Utama">
                <ul class="site-nav-links">
                    <li>
                        <a href="{{ route('home') }}" class="site-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                    </li>
                    <li>
                        <a href="{{ route('public.about') }}" class="site-nav-link {{ request()->routeIs('public.about') ? 'active' : '' }}">Tentang Kami</a>
                    </li>
                    <li>
                        <a href="{{ route('public.savings') }}" class="site-nav-link {{ request()->routeIs('public.savings') ? 'active' : '' }}">Simpanan</a>
                    </li>
                    <li>
                        <a href="{{ route('public.loans') }}" class="site-nav-link {{ request()->routeIs('public.loans') ? 'active' : '' }}">Pinjaman</a>
                    </li>
                    <li>
                        <a href="{{ route('public.how-it-works') }}" class="site-nav-link {{ request()->routeIs('public.how-it-works') ? 'active' : '' }}">Cara Kerja</a>
                    </li>
                    <li>
                        <a href="{{ route('public.contact') }}" class="site-nav-link {{ request()->routeIs('public.contact') ? 'active' : '' }}">Kontak</a>
                    </li>
                </ul>
            </nav>

            <!-- Authentication & Action Buttons -->
            <div class="site-nav-actions">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-emerald btn-sm">
                            <i class="fa-solid fa-gauge"></i> Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-emerald btn-sm">
                            <i class="fa-solid fa-user"></i> Portal Anggota
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" title="Keluar">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-user-plus"></i> Daftar Anggota
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Toggle -->
            <button type="button" class="mobile-menu-toggle" id="mobileMenuBtn" aria-label="Buka menu navigasi" aria-expanded="false">
                <i class="fa-solid fa-bars" id="mobileMenuIcon"></i>
            </button>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div class="mobile-nav-drawer" id="mobileNavDrawer">
            <ul class="mobile-nav-list">
                <li><a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('public.about') }}" class="mobile-nav-link {{ request()->routeIs('public.about') ? 'active' : '' }}">Tentang Kami</a></li>
                <li><a href="{{ route('public.savings') }}" class="mobile-nav-link {{ request()->routeIs('public.savings') ? 'active' : '' }}">Simpanan</a></li>
                <li><a href="{{ route('public.loans') }}" class="mobile-nav-link {{ request()->routeIs('public.loans') ? 'active' : '' }}">Pinjaman</a></li>
                <li><a href="{{ route('public.how-it-works') }}" class="mobile-nav-link {{ request()->routeIs('public.how-it-works') ? 'active' : '' }}">Cara Kerja</a></li>
                <li><a href="{{ route('public.contact') }}" class="mobile-nav-link {{ request()->routeIs('public.contact') ? 'active' : '' }}">Kontak</a></li>
            </ul>
            <div style="display: flex; flex-direction: column; gap: 8px; padding-top: 12px; border-top: 1px solid #E2E8F0;">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-gauge"></i> Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('member.dashboard') }}" class="btn btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-user"></i> Portal Anggota
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="width: 100%;">
                            <i class="fa-solid fa-right-from-bracket"></i> Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline" style="width: 100%;">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">
                        <i class="fa-solid fa-user-plus"></i> Daftar Anggota
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main id="mainContent">
        @yield('content')
    </main>

    <!-- ==================== PUBLIC FOOTER ==================== -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Col 1: Identity -->
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                        <div class="brand-badge" style="width: 32px; height: 32px; font-size: 16px;">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <span class="footer-brand-title" style="margin-bottom: 0;">KOPIN</span>
                    </div>
                    <p style="font-size: 13.5px; line-height: 1.6; margin-bottom: 16px;">
                        Koperasi Simpan Pinjam berbadan hukum di Indonesia yang mengedepankan prinsip keterbukaan, keadilan finansial, dan gotong royong untuk kemakmuran bersama karyawan dan masyarakat.
                    </p>
                    <div style="font-size: 12.5px; color: #64748B;">
                        <i class="fa-solid fa-shield-halved" style="color: #10B981;"></i> Tata kelola simpan pinjam akuntabel & aman
                    </div>
                </div>

                <!-- Col 2: Navigasi Informasi -->
                <div>
                    <h4 class="footer-col-title">Informasi Publik</h4>
                    <ul class="footer-link-list">
                        <li><a href="{{ route('home') }}"><i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-right: 6px;"></i> Beranda</a></li>
                        <li><a href="{{ route('public.about') }}"><i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-right: 6px;"></i> Profil KOPIN</a></li>
                        <li><a href="{{ route('public.savings') }}"><i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-right: 6px;"></i> Produk Simpanan</a></li>
                        <li><a href="{{ route('public.loans') }}"><i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-right: 6px;"></i> Produk Pinjaman</a></li>
                        <li><a href="{{ route('public.how-it-works') }}"><i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-right: 6px;"></i> Alur & Cara Kerja</a></li>
                        <li><a href="{{ route('public.contact') }}"><i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-right: 6px;"></i> Hubungi Kami</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan Anggota & Portal Peran -->
                <div>
                    <h4 class="footer-col-title">Portal & Peran KOPIN</h4>
                    <ul class="footer-link-list">
                        <li><a href="{{ route('login') }}"><i class="fa-solid fa-user" style="font-size: 11px; margin-right: 6px;"></i> Portal Anggota (Eksternal)</a></li>
                        <li><a href="{{ route('karyawan.login') }}"><i class="fa-solid fa-id-badge" style="font-size: 11px; margin-right: 6px;"></i> Portal Karyawan (Payroll)</a></li>
                        <li><a href="{{ route('kasir.login') }}"><i class="fa-solid fa-cash-register" style="font-size: 11px; margin-right: 6px;"></i> Loket Kasir (Tunai Fisik)</a></li>
                        <li><a href="{{ route('finance.login') }}"><i class="fa-solid fa-chart-pie" style="font-size: 11px; margin-right: 6px;"></i> Portal Finance (10% Reserve)</a></li>
                        <li><a href="{{ route('admin.login') }}"><i class="fa-solid fa-shield-halved" style="font-size: 11px; margin-right: 6px;"></i> Portal Administrator</a></li>
                    </ul>
                </div>

                <!-- Col 4: Layanan Operasional -->
                <div>
                    <h4 class="footer-col-title">Kantor & Layanan</h4>
                    <p style="font-size: 13px; line-height: 1.6; margin-bottom: 12px;">
                        <strong>Gedung Koperasi KOPIN</strong><br>
                        Kawasan Usaha Terpadu, Jakarta<br>
                        Indonesia <span style="color: #94A3B8; font-size: 11px;">(Simulasi Akademik)</span>
                    </p>
                    <div style="font-size: 13px; line-height: 1.6; display: flex; flex-direction: column; gap: 6px;">
                        <div><i class="fa-solid fa-clock" style="color: #10B981; width: 16px;"></i> Senin – Jumat: 08:00 – 16:00 WIB</div>
                        <div><i class="fa-solid fa-envelope" style="color: #10B981; width: 16px;"></i> layanan@kopin.id <span style="color: #94A3B8; font-size: 11px;">(Simulasi)</span></div>
                        <div><i class="fa-solid fa-phone" style="color: #10B981; width: 16px;"></i> (021) 5000-KOPIN <span style="color: #94A3B8; font-size: 11px;">(Demo)</span></div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; {{ date('Y') }} <strong>KOPIN (Koperasi Simpan Pinjam)</strong>. Hak Cipta Dilindungi Undang-Undang.
                </div>
                <div style="color: #64748B;">
                    Sistem Manajemen Keuangan Koperasi Berbasis Nilai Gotong Royong
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Nav Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileMenuBtn');
            const drawer = document.getElementById('mobileNavDrawer');
            const icon = document.getElementById('mobileMenuIcon');

            if (btn && drawer) {
                btn.addEventListener('click', function () {
                    const isOpen = drawer.classList.contains('active');
                    if (isOpen) {
                        drawer.classList.remove('active');
                        btn.setAttribute('aria-expanded', 'false');
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    } else {
                        drawer.classList.add('active');
                        btn.setAttribute('aria-expanded', 'true');
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
