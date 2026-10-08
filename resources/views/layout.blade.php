<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KOSPIN ENTERPRISE SYSTEM - Master Layout Template</title>
  
  <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 Pro / Free CDN Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <!-- Custom Design Tokens & Slicing CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/thermal-print.css">
</head>
<body>

  <div class="app-container">
    
    <!-- ==================== 1. SIDEBAR NAVIGASI ==================== -->
    <aside class="sidebar" id="appSidebar">
      <div class="sidebar-header">
        <a href="index.html" style="display: flex; align-items: center; text-decoration: none;">
          <img src="assets/img/logo-ksp-core.svg" alt="Logo KOSPIN Enterprise" class="brand-logo">
        </a>
      </div>

      <nav class="sidebar-menu">
        <div class="menu-category">Menu Utama & Monitoring</div>
        
        <a href="pages/dashboard.html" class="nav-link active" data-page="dashboard">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
            <span>Dashboard Eksekutif</span>
          </div>
        </a>

        <a href="pages/kasir-pos.html" class="nav-link" data-page="kasir">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span>
            <span>Kasir Cepat (POS)</span>
          </div>
          <span class="hotkey-badge">F2</span>
        </a>

        <a href="pages/data-master.html" class="nav-link" data-page="master">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
            <span>Master Data Anggota</span>
          </div>
        </a>

        <div class="menu-category">Operasional Keuangan</div>

        <a href="pages/form-pinjaman.html" class="nav-link" data-page="pinjaman">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-file-signature"></i></span>
            <span>Pengajuan Pinjaman</span>
          </div>
          <span class="hotkey-badge">F3</span>
        </a>

        <a href="pages/simpanan.html" class="nav-link" data-page="simpanan">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-vault"></i></span>
            <span>Buku Simpanan (4 Pos)</span>
          </div>
        </a>

        <a href="pages/laporan.html" class="nav-link" data-page="laporan">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-book-journal-whills"></i></span>
            <span>Buku Kas & Jurnal</span>
          </div>
          <span class="hotkey-badge">F9</span>
        </a>

        <div class="menu-category">Layanan Self-Service</div>

        <a href="pages/portal-anggota.html" class="nav-link" data-page="portal">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-id-card-clip"></i></span>
            <span>Portal Anggota</span>
          </div>
        </a>
      </nav>

      <!-- Teller Operator Session Footer -->
      <div class="sidebar-footer">
        <div class="teller-card">
          <img src="assets/img/avatar-teller.svg" alt="Avatar Operator" class="teller-avatar">
          <div class="teller-info">
            <div class="teller-name">Siti Rahmawati</div>
            <div class="teller-role">Teller Shift Pagi (Online)</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- ==================== 2. MAIN APP CONTENT ==================== -->
    <main class="main-content">
      
      <!-- Topbar Header -->
      <header class="topbar">
        <div class="topbar-left">
          <button id="sidebarToggleBtn" class="btn btn-secondary btn-sm" style="display: none;" title="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
          </button>
          
          <!-- Global Quick Search Trigger [Ctrl + K] -->
          <button class="global-search-btn" onclick="KspApp.openGlobalSearch()">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Cari NIK / Nama Anggota...</span>
            <span class="hotkey-badge" style="margin-left: auto;">Ctrl + K</span>
          </button>
        </div>

        <div class="topbar-right">
          <!-- Realtime Liquidity / Kas Brankas Badge -->
          <div class="brankas-pill" title="Saldo Likuid Kas Brankas Pusat Saat Ini">
            <i class="fa-solid fa-landmark text-emerald" style="font-size: 16px;"></i>
            <div>
              <div class="brankas-label">Kas Brankas Pusat</div>
              <div class="brankas-value" id="topbarBrankasValue">Rp 850.000.000</div>
            </div>
          </div>

          <!-- Early Warning System (EWS) Alert -->
          <button class="btn btn-secondary btn-sm" style="position: relative;" onclick="KspApp.navigateTo('dashboard')" title="Notifikasi EWS">
            <i class="fa-solid fa-bell" style="color: var(--amber-500);"></i>
            <span style="position: absolute; top: -4px; right: -4px; width: 10px; height: 10px; border-radius: 50%; background: var(--rose-500); border: 2px solid var(--bg-surface);"></span>
          </button>

          <!-- Hotkey Reference Bar Trigger -->
          <button class="btn btn-secondary btn-sm" onclick="alert('Hotkeys Navigasi Cepat:\n[F2] Kasir POS\n[F3] Pinjaman & Scoring\n[F5] Laporan & Dividen SHU\n[F9] Jurnal Umum\n[Ctrl + K] Pencarian Global\n[Enter] Cetak Struk')" title="Bantuan Keyboard Shortcuts">
            <i class="fa-solid fa-keyboard text-emerald"></i>
          </button>
        </div>
      </header>

      <!-- Page Content Insertion Point -->
      <section class="page-body" id="pageContentContainer">
        <!-- Konten spesifik halaman akan diinjeksi atau dirender di sini -->
        <div class="card" style="text-align: center; padding: 60px 20px;">
          <i class="fa-solid fa-cubes text-emerald" style="font-size: 48px; margin-bottom: 16px;"></i>
          <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 8px;">KOSPIN ENTERPRISE SYSTEM</h2>
          <p style="color: var(--text-secondary); max-width: 540px; margin: 0 auto 24px auto;">
            Template Master Layout ini menyediakan arsitektur UI berstandar lembaga keuangan mikro dengan kinerja tinggi, hotkey navigasi kasir terpadu, dan palet otoritatif finansial modern.
          </p>
          <div style="display: inline-flex; gap: 12px;">
            <a href="pages/dashboard.html" class="btn btn-primary"><i class="fa-solid fa-gauge"></i> Masuk ke Dashboard</a>
            <a href="pages/kasir-pos.html" class="btn btn-secondary"><i class="fa-solid fa-cash-register"></i> Terminal Kasir</a>
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ==================== 3. MODAL GLOBAL SEARCH (Ctrl + K) ==================== -->
  <div class="modal-backdrop" id="modalGlobalSearch" onclick="if(event.target === this) KspApp.closeAllModals()">
    <div class="modal-container" style="max-width: 580px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-magnifying-glass text-emerald" style="font-size: 16px;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-heading);">Pencarian Cepat Anggota</h3>
        </div>
        <span class="hotkey-badge">ESC untuk tutup</span>
      </div>
      <div class="modal-body" style="padding-bottom: 12px;">
        <input type="text" id="inputGlobalSearch" class="form-control" placeholder="Ketik NIK, Nama Anggota, atau Unit Usaha..." autocomplete="off">
        <div id="globalSearchResults" style="margin-top: 16px; max-height: 360px; overflow-y: auto;">
          <!-- Search results injected by main.js -->
        </div>
      </div>
    </div>
  </div>

  <!-- Core Scripts -->
  <script src="assets/js/mock-data.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
