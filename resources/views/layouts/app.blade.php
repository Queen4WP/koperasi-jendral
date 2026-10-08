<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KOPIN - Sistem Informasi Koperasi')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .sidebar.mobile-open {
            transform: translateX(0) !important;
            display: flex !important;
            position: fixed !important;
            z-index: 100 !important;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar-toggle-btn {
                display: block !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="app-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="appSidebar">
            <div class="sidebar-header" style="padding: 16px 20px;">
                <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: #FFFFFF; color: var(--emerald-600); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <h2 style="color: #FFFFFF; margin: 0; font-size: 20px; font-weight: 800; line-height: 1;">KOPIN</h2>
                        <span style="font-size: 10px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Koperasi Simpan Pinjam</span>
                    </div>
                </a>
            </div>

            <nav class="sidebar-menu" aria-label="Menu Navigasi Portal">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <div class="menu-category">Menu Administrator</div>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-gauge"></i></span><span>Dashboard Admin</span></div>
                        </a>
                        <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-users"></i></span><span>Manajemen Anggota</span></div>
                        </a>
                        <a href="{{ route('admin.savings.index') }}" class="nav-link {{ request()->routeIs('admin.savings.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-vault"></i></span><span>Manajemen Simpanan</span></div>
                        </a>
                        <a href="{{ route('admin.loans.index') }}" class="nav-link {{ request()->routeIs('admin.loans.*') && request('status') !== 'approved' && !request()->routeIs('admin.loans.disburse*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-file-signature"></i></span><span>Review Pinjaman</span></div>
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-receipt"></i></span><span>Verifikasi Pembayaran</span></div>
                        </a>
                        <a href="{{ route('admin.loans.index', ['status' => 'approved']) }}" class="nav-link {{ request('status') === 'approved' || request()->routeIs('admin.loans.disburse*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-money-bill-transfer"></i></span><span>Pencairan Pinjaman</span></div>
                        </a>
                    @else
                        <div class="menu-category">Menu Anggota</div>
                        <a href="{{ route('member.dashboard') }}" class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span><span>Dashboard</span></div>
                        </a>
                        <a href="{{ route('member.profile') }}" class="nav-link {{ request()->routeIs('member.profile*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-user"></i></span><span>Profil Saya</span></div>
                        </a>
                        <a href="{{ route('member.savings.index') }}" class="nav-link {{ request()->routeIs('member.savings.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-vault"></i></span><span>Simpanan Saya</span></div>
                        </a>
                        <a href="{{ route('member.loans.index') }}" class="nav-link {{ request()->routeIs('member.loans.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><span>Pinjaman Saya</span></div>
                        </a>
                    @endif

                    <div class="menu-category">Tautan Publik</div>
                    <a href="{{ route('home') }}" class="nav-link" target="_blank" rel="noopener">
                        <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-globe"></i></span><span>Website KOPIN</span></div>
                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px; opacity: 0.7;"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link">
                        <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-right-to-bracket"></i></span><span>Masuk</span></div>
                    </a>
                    <a href="{{ route('home') }}" class="nav-link">
                        <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-arrow-left"></i></span><span>Kembali ke Website</span></div>
                    </a>
                @endauth
            </nav>

            <div class="sidebar-footer">
                @auth
                <div class="teller-card">
                    <div class="teller-info">
                        <div class="teller-name">{{ auth()->user()->name }}</div>
                        <div class="teller-role">{{ ucfirst(auth()->user()->role) }} KOPIN</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="width: 100%; text-align: left; background: rgba(220, 38, 38, 0.15); color: #FCA5A5; border: 1px solid rgba(220, 38, 38, 0.4);">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
                @endauth
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" style="display: none; background: transparent; border: none; color: #FFFFFF; font-size: 20px; cursor: pointer; padding: 6px; margin-right: 12px;">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h3 style="margin: 0; color: #FFFFFF; font-weight: 700; font-size: 18px; letter-spacing: -0.01em;">
                        @yield('header_title', 'Dashboard')
                    </h3>
                </div>
                <div class="topbar-right">
                    @auth
                        <div style="color: rgba(255,255,255,0.9); font-size: 13px; display: flex; align-items: center; gap: 8px;">
                            <span class="badge" style="background: rgba(255,255,255,0.2); color: #FFFFFF;">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                    @endauth
                </div>
            </header>

            <section class="page-body" id="pageContentContainer" style="padding: 28px;">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <!-- Responsive Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebar = document.getElementById('appSidebar');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) {
                        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                            sidebar.classList.remove('mobile-open');
                        }
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
