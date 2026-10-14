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
    
    <!-- SweetAlert2 (Local Asset with CDN Fallback) -->
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script>
        if (typeof Swal === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
        }

        // Global Logout Confirmation
        function triggerLogoutConfirmation(formElement, role = '', name = '') {
            if (!formElement) return;

            if (typeof Swal === 'undefined') {
                if (confirm('Apakah Anda yakin ingin keluar dari sistem KOPIN?')) {
                    formElement.submit();
                }
                return;
            }

            const roleDisplay = role ? role : 'Akun';
            const nameDisplay = name ? ` (${name})` : '';

            Swal.fire({
                title: 'Konfirmasi Keluar',
                html: `Apakah Anda yakin ingin mengakhiri sesi <strong>${roleDisplay}${nameDisplay}</strong>?<br><span style="font-size: 13px; color: #64748B; margin-top: 6px; display: inline-block;">Pastikan seluruh transaksi dan input aktif telah tersimpan dengan aman.</span>`,
                icon: 'warning',
                iconColor: '#F59E0B',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa-solid fa-right-from-bracket"></i> Ya, Keluar Sekarang',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'kopin-swal-popup',
                    confirmButton: 'kopin-swal-confirm',
                    cancelButton: 'kopin-swal-cancel',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengakhiri Sesi...',
                        text: 'Mohon tunggu sebentar, Anda sedang dialihkan keluar...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    formElement.submit();
                }
            });
        }
    </script>

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

        /* SweetAlert2 Enterprise Styling */
        .kopin-swal-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 20px !important;
            padding: 24px !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35) !important;
            border: 1px solid #E2E8F0 !important;
        }
        .kopin-swal-confirm {
            border-radius: 10px !important;
            padding: 10px 20px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
        }
        .kopin-swal-cancel {
            border-radius: 10px !important;
            padding: 10px 20px !important;
            font-weight: 700 !important;
            font-size: 13px !important;
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
                @php
                    $user = auth('kasir')->user() 
                        ?? auth('finance')->user() 
                        ?? auth('karyawan')->user() 
                        ?? auth('admin')->user() 
                        ?? auth('web')->user() 
                        ?? auth()->user();

                    $currentUser = $user;
                    $role = $user ? $user->role : 'member';

                    $isKasirPortal = ($role === 'kasir') || request()->is('kasir*');
                    $isFinancePortal = ($role === 'finance') || request()->is('finance*');
                    $isKaryawanPortal = ($role === 'karyawan') || request()->is('karyawan*');
                    $isAdminPortal = ($role === 'admin') || request()->is('admin*');

                    if ($isKasirPortal) {
                        $logoutRoute = route('kasir.logout');
                    } elseif ($isFinancePortal) {
                        $logoutRoute = route('finance.logout');
                    } elseif ($isKaryawanPortal) {
                        $logoutRoute = route('karyawan.logout');
                    } elseif ($isAdminPortal) {
                        $logoutRoute = route('admin.logout');
                    } else {
                        $logoutRoute = route('logout');
                    }
                @endphp

                @if($currentUser)
                    @if($isKasirPortal)
                        <div class="menu-category">Menu Loket Kasir</div>
                        <a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span><span>Dashboard Loket</span></div>
                        </a>
                        <a href="{{ route('kasir.savings.deposit') }}" class="nav-link {{ request()->routeIs('kasir.savings.deposit') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-circle-down"></i></span><span>Setoran Tunai Simpanan</span></div>
                        </a>
                        <a href="{{ route('kasir.savings.withdraw') }}" class="nav-link {{ request()->routeIs('kasir.savings.withdraw') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-circle-up"></i></span><span>Penarikan Tunai (BKK)</span></div>
                        </a>
                        <a href="{{ route('kasir.loans.installment') }}" class="nav-link {{ request()->routeIs('kasir.loans.installment') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><span>Bayar Angsuran Tunai</span></div>
                        </a>
                    @elseif($isFinancePortal)
                        <div class="menu-category">Menu Finance & Treasury</div>
                        <a href="{{ route('finance.dashboard') }}" class="nav-link {{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span><span>Dashboard & Cadangan 10%</span></div>
                        </a>
                        <a href="{{ route('finance.settings.loan_limits') }}" class="nav-link {{ request()->routeIs('finance.settings.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-sliders"></i></span><span>Pengaturan Plafon Pinjaman</span></div>
                        </a>
                    @elseif($isKaryawanPortal)
                        <div class="menu-category">Menu Anggota Karyawan</div>
                        <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-wallet"></i></span><span>Dashboard Payroll</span></div>
                        </a>
                        <a href="{{ route('karyawan.loans.index') }}" class="nav-link {{ request()->routeIs('karyawan.loans.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><span>Pinjaman Karyawan</span></div>
                        </a>
                    @elseif($isAdminPortal)
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
                        <a href="{{ route('admin.activity_logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity_logs.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span><span>Audit Log</span></div>
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
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-vault"></i></span><span>Buku Tabungan</span></div>
                        </a>
                        <a href="{{ route('member.loans.index') }}" class="nav-link {{ request()->routeIs('member.loans.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><span>Kartu Pinjaman</span></div>
                        </a>
                    @endif

                    <div class="menu-category">Tautan Publik & Portal</div>
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
                @endif
            </nav>

            <div class="sidebar-footer">
                @if($currentUser)
                <div class="teller-card">
                    <div class="teller-info">
                        <div class="teller-name">{{ $currentUser->name }}</div>
                        <div class="teller-role">{{ ucfirst($currentUser->role) }} KOPIN</div>
                    </div>
                </div>
                <form id="appSidebarLogoutForm" method="POST" action="{{ $logoutRoute }}" style="margin-top: 10px;">
                    @csrf
                    <button type="button" id="btnLogoutSidebar" onclick="triggerLogoutConfirmation(document.getElementById('appSidebarLogoutForm'), '{{ ucfirst($currentUser->role) }}', '{{ $currentUser->name }}')" class="btn btn-secondary js-logout-trigger" data-role="{{ ucfirst($currentUser->role) }}" data-name="{{ $currentUser->name }}" style="width: 100%; text-align: left; background: rgba(220, 38, 38, 0.15); color: #FCA5A5; border: 1px solid rgba(220, 38, 38, 0.4); cursor: pointer; transition: all 0.2s;">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
                @endif
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

    <!-- Logout Form Submit Interceptor -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[action*="logout"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!form.dataset.confirmed) {
                        e.preventDefault();
                        const btn = form.querySelector('.js-logout-trigger') || form.querySelector('button');
                        const role = btn?.dataset.role || '';
                        const name = btn?.dataset.name || '';
                        triggerLogoutConfirmation(form, role, name);
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
