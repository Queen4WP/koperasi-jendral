<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOPIN - Sistem Informasi Koperasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .text-danger { color: #dc3545; font-size: 0.875em; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .btn { padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background-color: var(--emerald-600, #059669); color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
    </style>
</head>
<body>

    <div class="app-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="appSidebar">
            <div class="sidebar-header">
                <a href="/" style="display: flex; align-items: center; text-decoration: none;">
                    <h2 style="color: var(--emerald-500, #10b981); margin: 0;">KOPIN</h2>
                </a>
            </div>

            <nav class="sidebar-menu">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <div class="menu-category">Menu Admin</div>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-gauge"></i></span><span>Dashboard Admin</span></div>
                        </a>
                        <a href="{{ route('admin.members.index') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-users"></i></span><span>Manajemen Anggota</span></div>
                        </a>
                        <a href="{{ route('admin.savings.index') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-vault"></i></span><span>Manajemen Simpanan</span></div>
                        </a>
                        <a href="{{ route('admin.loans.index') }}" class="nav-link {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-file-signature"></i></span><span>Review Pinjaman</span></div>
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-receipt"></i></span><span>Verifikasi Pembayaran</span></div>
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-money-bill-transfer"></i></span><span>Pencairan Pinjaman</span></div>
                        </a>
                    @else
                        <div class="menu-category">Menu Anggota</div>
                        <a href="{{ route('member.dashboard') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span><span>Dashboard</span></div>
                        </a>
                        <a href="{{ route('member.profile') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-user"></i></span><span>Profil Saya</span></div>
                        </a>
                        <a href="{{ route('member.savings.index') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-vault"></i></span><span>Simpanan Saya</span></div>
                        </a>
                        <a href="{{ route('member.loans.index') }}" class="nav-link">
                            <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><span>Pinjaman Saya</span></div>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="nav-link">
                        <div class="nav-label"><span class="nav-icon"><i class="fa-solid fa-right-to-bracket"></i></span><span>Login</span></div>
                    </a>
                @endauth
            </nav>

            <div class="sidebar-footer">
                @auth
                <div class="teller-card">
                    <div class="teller-info">
                        <div class="teller-name">{{ auth()->user()->name }}</div>
                        <div class="teller-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="width: 100%; text-align: left; background: transparent; color: #dc2626; border: 1px solid #dc2626;"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
                @endauth
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h3 style="margin: 0; color: var(--text-heading);">@yield('header_title', 'Dashboard')</h3>
                </div>
            </header>

            <section class="page-body" id="pageContentContainer" style="padding: 24px;">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

</body>
</html>
