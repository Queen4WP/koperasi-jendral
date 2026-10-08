<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KOSPIN ENTERPRISE SYSTEM - Sistem Informasi & Operasional Koperasi Modern</title>
  
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <!-- CSS Design Tokens & Thermal Printing -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/thermal-print.css">

  <!-- Chart.js 4.x CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .spa-view {
      display: none;
      animation: fadeIn 0.25s ease;
    }
    .spa-view.active {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .pos-tabs-nav {
      display: flex;
      gap: 8px;
      margin-bottom: 20px;
      background: var(--bg-canvas);
      padding: 6px;
      border-radius: 12px;
      border: 1px solid var(--border-subtle);
    }
    .pos-tab-btn {
      flex: 1;
      padding: 10px 14px;
      border-radius: 8px;
      border: none;
      background: transparent;
      color: var(--text-secondary);
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s;
    }
    .pos-tab-btn.active {
      background: var(--bg-surface-elevated);
      color: var(--emerald-500);
      border: 1px solid rgba(16, 185, 129, 0.3);
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .pos-radio-box {
      display: flex;
      gap: 16px;
      margin-top: 8px;
    }
    .pos-radio-label {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      background: var(--bg-surface-elevated);
      border: 1px solid var(--border-card);
      border-radius: 8px;
      cursor: pointer;
      font-size: 13px;
      font-weight: 500;
    }
    .filter-pills { display: flex; gap: 8px; }
    .filter-status-btn {
      padding: 6px 14px;
      border-radius: 20px;
      border: 1px solid var(--border-subtle);
      background: var(--bg-canvas);
      color: var(--text-secondary);
      font-size: 12.5px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .filter-status-btn.active {
      background: var(--emerald-500);
      color: #0A1128;
      border-color: var(--emerald-500);
    }
    .qris-modal-box { text-align: center; padding: 20px; }
    .qris-qr-sim {
      width: 200px;
      height: 200px;
      margin: 16px auto;
      background: #FFFFFF;
      padding: 12px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }
  </style>
</head>
<body>

  <div class="app-container">
    
    <!-- ==================== 1. SIDEBAR NAVIGASI ==================== -->
    <aside class="sidebar" id="appSidebar">
      <div class="sidebar-header">
        <a href="index.html" style="display: flex; align-items: center; text-decoration: none;">
          <img src="assets/img/logo-ksp-core.svg" alt="Logo KSP Core Enterprise" class="brand-logo">
        </a>
      </div>

      <nav class="sidebar-menu">
        <div class="menu-category">Menu Utama & Monitoring</div>
        
        <a href="#dashboard" class="nav-link active" data-view="dashboard" onclick="KspRouter.switchView('dashboard')">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
            <span>Dashboard Eksekutif</span>
          </div>
        </a>

        <a href="#kasir" class="nav-link" data-view="kasir" onclick="KspRouter.switchView('kasir')">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-cash-register"></i></span>
            <span>Kasir Cepat (POS)</span>
          </div>
          <span class="hotkey-badge">F2</span>
        </a>

        <a href="#master" class="nav-link" data-view="master" onclick="KspRouter.switchView('master')">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
            <span>Master Data Anggota</span>
          </div>
        </a>

        <div class="menu-category">Operasional Keuangan</div>

        <a href="#pinjaman" class="nav-link" data-view="pinjaman" onclick="KspRouter.switchView('pinjaman')">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-file-signature"></i></span>
            <span>Pengajuan Pinjaman</span>
          </div>
          <span class="hotkey-badge">F3</span>
        </a>

        <a href="#simpanan" class="nav-link" data-view="simpanan" onclick="KspRouter.switchView('simpanan')">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-vault"></i></span>
            <span>Buku Simpanan (4 Pos)</span>
          </div>
        </a>

        <a href="#laporan" class="nav-link" data-view="laporan" onclick="KspRouter.switchView('laporan')">
          <div class="nav-label">
            <span class="nav-icon"><i class="fa-solid fa-book-journal-whills"></i></span>
            <span>Buku Kas & Jurnal</span>
          </div>
          <span class="hotkey-badge">F9</span>
        </a>

        <div class="menu-category">Layanan Self-Service</div>

        <a href="#portal" class="nav-link" data-view="portal" onclick="KspRouter.switchView('portal')">
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
          <!-- Global Quick Search Trigger [Ctrl + K] -->
          <button class="global-search-btn" onclick="KspApp.openGlobalSearch()">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Cari NIK / Nama Anggota...</span>
            <span class="hotkey-badge" style="margin-left: auto;">Ctrl + K</span>
          </button>
        </div>

        <div class="topbar-right">
          <!-- Realtime Liquidity / Kas Brankas Badge -->
          <div class="brankas-pill" title="Saldo Kas Brankas Pusat Saat Ini">
            <i class="fa-solid fa-landmark text-emerald" style="font-size: 16px;"></i>
            <div>
              <div class="brankas-label">Kas Brankas Pusat</div>
              <div class="brankas-value" id="topbarBrankasValue">Rp 850.000.000</div>
            </div>
          </div>

          <!-- Direct POS Button -->
          <button onclick="KspRouter.switchView('kasir')" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-cash-register"></i> Kasir POS [F2]
          </button>

          <!-- Hotkey Reference -->
          <button class="btn btn-secondary btn-sm" onclick="alert('HOTKEYS CEPAT:\n[F2] Terminal Kasir POS\n[F3] Modul Pinjaman 5C\n[F5] Dividen SHU\n[F9] Jurnal Umum\n[Ctrl + K] Cari Anggota\n[Enter] Cetak Struk 80mm')" title="Bantuan Hotkeys">
            <i class="fa-solid fa-keyboard text-emerald"></i>
          </button>
        </div>
      </header>

      <!-- Dynamic Content Injection Area -->
      <section class="page-body" id="spaContentArea">
        
        <!-- ================= VIEW 1: DASHBOARD EKSEKUTIF ================= -->
        <div class="spa-view active" id="view-dashboard">
          
          <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
              <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading); letter-spacing: -0.5px;">
                Dashboard Eksekutif & Manajemen Likuiditas
              </h1>
              <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">
                Ringkasan real-time kesehatan permodalan (CAR), rasio kredit (NPL), dan arus kas operasional koperasi.
              </p>
            </div>
            <div style="display: flex; gap: 10px;">
              <span class="badge success" style="padding: 6px 12px; font-size: 12px;">
                <i class="fa-solid fa-shield-halved"></i> Audit OJK: Sehat & Patuh
              </span>
            </div>
          </div>

          <!-- 4 KPI Metrics Card Grid -->
          <div class="grid-cols-4" style="margin-bottom: 24px;">
            <div class="kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Portofolio Pinjaman</span>
                <div class="kpi-icon emerald"><i class="fa-solid fa-hand-holding-dollar"></i></div>
              </div>
              <div class="kpi-value">Rp 14.850.000.000</div>
              <div class="kpi-trend">
                <span class="trend-badge up"><i class="fa-solid fa-arrow-up"></i> +4.8%</span>
                <span>vs bulan lalu</span>
              </div>
            </div>

            <div class="kpi-card blue">
              <div class="kpi-header">
                <span class="kpi-title">Simpanan Terhimpun</span>
                <div class="kpi-icon blue"><i class="fa-solid fa-piggy-bank"></i></div>
              </div>
              <div class="kpi-value">Rp 18.250.000.000</div>
              <div class="kpi-trend">
                <span class="trend-badge up"><i class="fa-solid fa-arrow-up"></i> +6.2%</span>
                <span>4 Pos Rekening</span>
              </div>
            </div>

            <div class="kpi-card">
              <div class="kpi-header">
                <span class="kpi-title">Rasio CAR (Modal Min.)</span>
                <div class="kpi-icon emerald"><i class="fa-solid fa-chart-pie"></i></div>
              </div>
              <div class="kpi-value" style="color: var(--emerald-500);">24.85%</div>
              <div class="kpi-trend">
                <span class="badge success">Sangat Sehat</span>
                <span>Min. Standar 8.0%</span>
              </div>
            </div>

            <div class="kpi-card warning">
              <div class="kpi-header">
                <span class="kpi-title">NPL Gross (Kredit Macet)</span>
                <div class="kpi-icon amber"><i class="fa-solid fa-triangle-exclamation"></i></div>
              </div>
              <div class="kpi-value" style="color: var(--amber-500);">1.82%</div>
              <div class="kpi-trend">
                <span class="trend-badge up"><i class="fa-solid fa-check"></i> Aman</span>
                <span>Batas Maks. 5.0%</span>
              </div>
            </div>
          </div>

          <!-- Charts Section (2 Columns) -->
          <div class="grid-cols-2" style="margin-bottom: 24px;">
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">
                    <i class="fa-solid fa-chart-column text-emerald"></i>
                    <span>Arus Likuiditas 6 Bulan Terakhir</span>
                  </div>
                  <div class="card-subtitle">Komparasi Kas Masuk (Setoran & Angsuran) vs Kas Keluar</div>
                </div>
                <span class="badge info">Net Positif</span>
              </div>
              <div style="height: 280px; position: relative;">
                <canvas id="chartLiquidityFlow"></canvas>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">
                    <i class="fa-solid fa-chart-pie text-emerald"></i>
                    <span>Distribusi Portofolio Simpanan</span>
                  </div>
                  <div class="card-subtitle">Porsi Simpanan Pokok, Wajib, SiSuka, & Sijaka Berjangka ARO</div>
                </div>
                <span class="badge success">Total Rp 18.250.000.000</span>
              </div>
              <div style="height: 280px; position: relative;">
                <canvas id="chartSavingsPortfolio"></canvas>
              </div>
            </div>
          </div>

          <!-- EWS & Antrean Komite -->
          <div class="grid-cols-2" style="margin-bottom: 24px;">
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">
                    <i class="fa-solid fa-bell text-amber"></i>
                    <span>Early Warning System (EWS Debitur)</span>
                  </div>
                  <div class="card-subtitle">Pengawasan pinjaman jatuh tempo & debitur Kolektibilitas 2 (DPK)</div>
                </div>
                <span class="badge warning">Perhatian Khusus</span>
              </div>
              <div class="table-container">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Debitur</th>
                      <th>No. Pinjaman</th>
                      <th>Tagihan</th>
                      <th>Status Kol</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div style="font-weight: 600; color: var(--text-heading);">Agus Pratama</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Percetakan Sablon</div>
                      </td>
                      <td class="font-mono" style="font-size: 12px;">PJ-2024-0744</td>
                      <td>
                        <div class="font-mono" style="color: var(--rose-500); font-weight: 700;">Rp 2.408.333</div>
                        <div style="font-size: 11px; color: var(--rose-500);">Lewat 14 Hari</div>
                      </td>
                      <td><span class="badge warning">Kol 2 (DPK)</span></td>
                      <td>
                        <button onclick="KspToast.info('WhatsApp Peringatan Terkirim ke 0819-2233-4455')" class="btn btn-secondary btn-sm">
                          <i class="fa-brands fa-whatsapp text-emerald"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div style="font-weight: 600; color: var(--text-heading);">Siti Rahmawati</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Distributor Hijab</div>
                      </td>
                      <td class="font-mono" style="font-size: 12px;">PJ-2024-0902</td>
                      <td>
                        <div class="font-mono" style="color: var(--amber-500); font-weight: 700;">Rp 1.951.666</div>
                        <div style="font-size: 11px; color: var(--amber-500);">JT 28 Okt</div>
                      </td>
                      <td><span class="badge success">Kol 1 (Lancar)</span></td>
                      <td>
                        <button onclick="KspToast.info('Reminder Jatuh Tempo H-4 Terkirim')" class="btn btn-secondary btn-sm">
                          <i class="fa-regular fa-bell"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">
                    <i class="fa-solid fa-user-check text-emerald"></i>
                    <span>Antrean Persetujuan Komite Kredit</span>
                  </div>
                  <div class="card-subtitle">Pengajuan menunggu keputusan komite kredit</div>
                </div>
                <button onclick="KspRouter.switchView('pinjaman')" class="btn btn-secondary btn-sm">Buka Wizard [F3]</button>
              </div>
              <div style="padding: 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-card); border-radius: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                  <div>
                    <div style="font-weight: 700; color: var(--text-heading);">Nurul Hidayah</div>
                    <div style="font-size: 11px; color: var(--text-muted);">Catering Nasi Box Bu Nurul</div>
                  </div>
                  <span class="badge warning">Menunggu Sign-off</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 12px; margin-bottom: 12px; background: var(--bg-canvas); padding: 10px; border-radius: 8px;">
                  <div><span style="color: var(--text-muted);">Plafon:</span><div class="font-mono text-emerald" style="font-weight: 700;">Rp 40.000.000</div></div>
                  <div><span style="color: var(--text-muted);">Score 5C:</span><div class="font-mono" style="font-weight: 700;">715 / 1000</div></div>
                  <div><span style="color: var(--text-muted);">LTV:</span><div class="font-mono text-emerald" style="font-weight: 700;">16.0% (Aman)</div></div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                  <button onclick="KspToast.warning('Berkas dikembalikan ke Analis')" class="btn btn-secondary btn-sm">Revisi</button>
                  <button onclick="KspDataStore.updateLoanStatus('PJ-2024-1011', 'Disetujui', 40000000); KspToast.success('Plafon Rp 40.000.000 DISETUJUI KOMITE!');" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-check-double"></i> Setujui Plafon
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- ================= VIEW 2: TERMINAL KASIR (POS) ================= -->
        <div class="spa-view" id="view-kasir">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
              <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading);">
                <i class="fa-solid fa-cash-register text-emerald" style="margin-right: 8px;"></i>
                Terminal Kasir Transaksi Cepat (POS)
              </h1>
              <p style="color: var(--text-muted); font-size: 13px;">Kalkulasi kembalian otomatis & cetak live struk thermal 80mm ESC/POS.</p>
            </div>
            <span class="hotkey-badge" style="font-size: 12px; padding: 6px 12px;">Hotkey Kasir [F2]</span>
          </div>

          <div style="display: grid; grid-template-columns: 1.25fr 0.75fr; gap: 24px; align-items: start;">
            <div>
              <div class="card" style="margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 12px;">
                  <label class="form-label">Pilih / Scan Barcode Anggota</label>
                  <div style="display: flex; gap: 10px;">
                    <select id="kasirMemberSelect" class="form-control" style="font-weight: 600;"></select>
                    <button onclick="KspApp.openGlobalSearch()" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                  </div>
                </div>
                <div id="kasirMemberPreview"></div>
              </div>

              <div class="card">
                <div class="pos-tabs-nav">
                  <button type="button" class="pos-tab-btn active" data-tab="setor" onclick="KspKasir.switchTab('setor')">
                    <i class="fa-solid fa-arrow-down"></i> Setor Simpanan
                  </button>
                  <button type="button" class="pos-tab-btn" data-tab="tarik" onclick="KspKasir.switchTab('tarik')">
                    <i class="fa-solid fa-arrow-up"></i> Tarik Tunai
                  </button>
                  <button type="button" class="pos-tab-btn" data-tab="angsuran" onclick="KspKasir.switchTab('angsuran')">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Bayar Angsuran
                  </button>
                </div>

                <div id="panelSetor">
                  <div class="form-group">
                    <label class="form-label">Pilih Pos Rekening Simpanan</label>
                    <div class="pos-radio-box">
                      <label class="pos-radio-label">
                        <input type="radio" name="jenisSimpananPos" value="Sukarela" checked>
                        <span>Simpanan Sukarela (SiSuka)</span>
                      </label>
                      <label class="pos-radio-label">
                        <input type="radio" name="jenisSimpananPos" value="Wajib">
                        <span>Simpanan Wajib (Rp 100.000)</span>
                      </label>
                      <label class="pos-radio-label">
                        <input type="radio" name="jenisSimpananPos" value="Pokok">
                        <span>Simpanan Pokok</span>
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Nominal Setoran (Rp)</label>
                    <div style="position: relative;">
                      <span style="position: absolute; left: 16px; top: 12px; font-weight: 700; color: var(--emerald-500); font-family: 'JetBrains Mono';">Rp</span>
                      <input type="text" id="inputSetorNominal" class="form-control font-mono" style="padding-left: 46px; font-size: 18px; font-weight: 700;" placeholder="0">
                    </div>
                  </div>
                </div>

                <div id="panelTarik" style="display: none;">
                  <div class="form-group">
                    <label class="form-label">Nominal Penarikan Tunai SiSuka (Rp)</label>
                    <div style="position: relative;">
                      <span style="position: absolute; left: 16px; top: 12px; font-weight: 700; color: var(--rose-500); font-family: 'JetBrains Mono';">Rp</span>
                      <input type="text" id="inputTarikNominal" class="form-control font-mono" style="padding-left: 46px; font-size: 18px; font-weight: 700;" placeholder="0">
                    </div>
                  </div>
                </div>

                <div id="panelAngsuran" style="display: none;">
                  <div id="kasirLoanInfo" style="margin-bottom: 16px;"></div>
                </div>

                <!-- Quick Cash Presets -->
                <div style="border-top: 1px solid var(--border-subtle); padding-top: 18px; margin-top: 18px;">
                  <label class="form-label">Tombol Pecahan Cepat (Quick Cash Presets)</label>
                  <div class="cash-preset-grid">
                    <button type="button" class="cash-preset-btn" onclick="KspKasir.setQuickCash(50000)">Rp 50.000</button>
                    <button type="button" class="cash-preset-btn" onclick="KspKasir.setQuickCash(100000)">Rp 100.000</button>
                    <button type="button" class="cash-preset-btn" onclick="KspKasir.setQuickCash(200000)">Rp 200.000</button>
                    <button type="button" class="cash-preset-btn" onclick="KspKasir.setQuickCash(500000)">Rp 500.000</button>
                    <button type="button" class="cash-preset-btn" onclick="KspKasir.setQuickCash(1000000)">Rp 1.000.000</button>
                    <button type="button" class="cash-preset-btn" onclick="KspKasir.setQuickCash(2000000)">Rp 2.000.000</button>
                  </div>
                  <button type="button" class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 8px; font-weight: 700;" onclick="KspKasir.setQuickCash('pas')">
                    <i class="fa-solid fa-coins text-emerald"></i> Set Uang Pas (Uang Diterima = Total Transaksi)
                  </button>

                  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px;">
                    <div class="form-group" style="margin-bottom: 0;">
                      <label class="form-label">Uang Diterima Kasir</label>
                      <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 11px; font-weight: 700; color: var(--text-secondary); font-family: 'JetBrains Mono';">Rp</span>
                        <input type="text" id="inputCashTendered" class="form-control font-mono" style="padding-left: 42px; font-size: 16px; font-weight: 700;" placeholder="0">
                      </div>
                    </div>
                    <div style="background: var(--bg-canvas); padding: 14px 18px; border-radius: 10px; border: 1px solid var(--border-card);">
                      <div style="font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Kembalian (Change)</div>
                      <div id="kasirChangeAmount" class="font-mono" style="font-size: 20px; font-weight: 800; color: var(--emerald-500); margin-top: 4px;">Rp 0</div>
                    </div>
                  </div>

                  <div style="margin-top: 24px;">
                    <button type="button" onclick="KspKasir.executeTransactionAndPrint()" class="btn btn-primary btn-lg" style="width: 100%; font-size: 16px; font-weight: 800;">
                      <i class="fa-solid fa-print"></i> EKSEKUSI & CETAK STRUK 80MM [ENTER]
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Live Slip Preview -->
            <div>
              <div class="card" style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                  <div style="font-weight: 700; color: var(--text-heading); font-size: 14px;">
                    <i class="fa-solid fa-receipt text-emerald" style="margin-right: 6px;"></i> Live Preview Struk 80mm
                  </div>
                  <span class="badge success">ESC/POS</span>
                </div>
                <div id="thermalSlipContainer" class="thermal-slip-preview"></div>
                <div style="margin-top: 16px; text-align: center;">
                  <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="width: 100%;">
                    <i class="fa-solid fa-up-right-from-square"></i> Test Cetak Thermal
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= VIEW 3: MASTER DATA ANGGOTA ================= -->
        <div class="spa-view" id="view-master">
          <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
              <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading);">Master Data Keanggotaan & Rekening</h1>
              <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Kelola KYC anggota, grade loyalitas, konsolidasi simpanan 4 pos, dan rekam jejak pinjaman.</p>
            </div>
            <div style="display: flex; gap: 10px;">
              <button onclick="KspMasterData.exportToCSV()" class="btn btn-secondary btn-sm"><i class="fa-solid fa-file-csv text-emerald"></i> Export CSV</button>
              <button onclick="KspMasterData.openRegisterModal()" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Anggota Baru</button>
            </div>
          </div>

          <div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
              <div class="filter-pills">
                <button type="button" class="filter-status-btn active" data-status="Semua">Semua Anggota</button>
                <button type="button" class="filter-status-btn" data-status="Aktif">Aktif</button>
                <button type="button" class="filter-status-btn" data-status="Luar Biasa">Luar Biasa</button>
                <button type="button" class="filter-status-btn" data-status="Menunggu Verifikasi">Verifikasi</button>
              </div>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div style="position: relative; width: 280px;">
                  <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: var(--text-muted);"></i>
                  <input type="text" id="searchAnggotaInput" class="form-control" style="padding-left: 36px;" placeholder="Filter NIK, Nama, Unit Usaha...">
                </div>
                <span id="totalAnggotaCount" class="badge info" style="padding: 8px 12px; font-size: 12px;"></span>
              </div>
            </div>
          </div>

          <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container" style="border: none;">
              <table class="table">
                <thead>
                  <tr>
                    <th>No. Anggota / Tgl</th>
                    <th>Identitas Anggota & NIK</th>
                    <th>Unit Usaha & Kontak</th>
                    <th>Grade</th>
                    <th>Total Simpanan</th>
                    <th>Pinjaman Aktif</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                  </tr>
                </thead>
                <tbody id="tableAnggotaBody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ================= VIEW 4: PINJAMAN & WIZARD 5C ================= -->
        <div class="spa-view" id="view-pinjaman">
          <div style="margin-bottom: 24px;">
            <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading);">Workflow Pembiayaan Pinjaman & Komite Kredit</h1>
            <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">5 Tahap Penilaian Pembiayaan: Plafon, Taksasi Agunan LTV, Scoring 5C & DSR, Komite Sign-off, dan Pencairan Dana.</p>
          </div>

          <div class="card" style="padding: 24px 32px; margin-bottom: 24px;">
            <div class="stepper-nav">
              <div class="stepper-step active" data-step="1" onclick="LoanWizard.goToStep(1)"><div class="step-circle">1</div><div class="step-label">Plafon</div></div>
              <div class="stepper-step" data-step="2" onclick="LoanWizard.goToStep(2)"><div class="step-circle">2</div><div class="step-label">Agunan LTV</div></div>
              <div class="stepper-step" data-step="3" onclick="LoanWizard.goToStep(3)"><div class="step-circle">3</div><div class="step-label">Scoring 5C & DSR</div></div>
              <div class="stepper-step" data-step="4" onclick="LoanWizard.goToStep(4)"><div class="step-circle">4</div><div class="step-label">Komite Sign-off</div></div>
              <div class="stepper-step" data-step="5" onclick="LoanWizard.goToStep(5)"><div class="step-circle">5</div><div class="step-label">Pencairan & SPK</div></div>
            </div>
          </div>

          <div class="card">
            <!-- Step 1 -->
            <div class="wizard-step-content active" id="stepContent1">
              <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Langkah 1: Input Pengajuan & Nominal Plafon</h3>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                  <label class="form-label">Pilih Anggota Pemohon *</label>
                  <select id="loanMemberSelect" class="form-control" onchange="LoanWizard.calculateInstallment()"></select>
                </div>
                <div class="form-group">
                  <label class="form-label">Sektor Penggunaan</label>
                  <select class="form-control"><option>Modal Kerja UMKM (Produktif)</option><option>Investasi Peralatan</option></select>
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                  <label class="form-label">Nominal Plafon (Rp) *</label>
                  <input type="text" id="loanPlafonInput" class="form-control font-mono" value="50.000.000" oninput="LoanWizard.calculateInstallment()">
                </div>
                <div class="form-group">
                  <label class="form-label">Tenor (Bulan) *</label>
                  <select id="loanTenorSelect" class="form-control font-mono" onchange="LoanWizard.calculateInstallment()">
                    <option value="12">12 Bulan</option>
                    <option value="24" selected>24 Bulan</option>
                    <option value="36">36 Bulan</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Bunga Flat / Bln (%)</label>
                  <input type="text" id="loanBungaInput" class="form-control font-mono" value="0.90" readonly>
                </div>
              </div>
              <div style="background: var(--bg-surface-elevated); padding: 16px; border-radius: 12px; margin-top: 10px;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; font-size: 12.5px;">
                  <div><span style="color: var(--text-muted);">Pokok Bulanan:</span><div id="simPokokBulanan" class="font-mono text-emerald" style="font-weight: 700;">Rp 2.083.333</div></div>
                  <div><span style="color: var(--text-muted);">Bunga Bulanan:</span><div id="simBungaBulanan" class="font-mono text-amber" style="font-weight: 700;">Rp 450.000</div></div>
                  <div><span style="color: var(--text-muted);">Total Angsuran/Bln:</span><div id="simTotalAngsuran" class="font-mono text-emerald" style="font-weight: 800; font-size: 16px;">Rp 2.533.333</div></div>
                  <div><span style="color: var(--text-muted);">Total Kewajiban:</span><div id="simTotalKewajiban" class="font-mono" style="font-weight: 700;">Rp 60.800.000</div></div>
                </div>
              </div>
              <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn btn-primary" onclick="LoanWizard.nextStep()">Lanjut ke Taksasi Agunan <i class="fa-solid fa-arrow-right"></i></button>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="wizard-step-content" id="stepContent2">
              <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Langkah 2: Verifikasi & Taksasi Agunan</h3>
              <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div class="form-group">
                  <label class="form-label">Jenis Agunan</label>
                  <select id="agunanJenis" class="form-control" onchange="LoanWizard.calculateLTV()"><option>BPKB Kendaraan Mobil</option><option>Sertifikat Tanah SHM</option></select>
                </div>
                <div class="form-group">
                  <label class="form-label">Deskripsi Objek</label>
                  <input type="text" id="agunanDetail" class="form-control" value="Toyota Avanza 1.3 G M/T 2019">
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                  <label class="form-label">Nilai Taksasi Pasar (Rp)</label>
                  <input type="text" id="agunanPasar" class="form-control font-mono" value="140.000.000" oninput="LoanWizard.calculateLTV()">
                </div>
                <div class="form-group">
                  <label class="form-label">Nilai Likuidasi (Rp)</label>
                  <input type="text" id="agunanLikuidasi" class="form-control font-mono" value="110.000.000" readonly>
                </div>
                <div class="form-group">
                  <label class="form-label">Rasio LTV</label>
                  <div id="agunanLtvBadge" class="font-mono" style="padding: 10px; background: rgba(16, 185, 129, 0.15); border-radius: 8px; font-weight: 800; color: var(--emerald-500);">35.71% (Aman)</div>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                <button type="button" class="btn btn-secondary" onclick="LoanWizard.prevStep()"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                <button type="button" class="btn btn-primary" onclick="LoanWizard.nextStep()">Lanjut ke Scoring 5C <i class="fa-solid fa-arrow-right"></i></button>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="wizard-step-content" id="stepContent3">
              <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Langkah 3: Mesin Scoring 5C & Analisis DSR</h3>
              <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div>
                  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                      <label class="form-label">Omzet Bersih / Bln (Rp)</label>
                      <input type="text" id="dsrIncomeInput" class="form-control font-mono" value="12.000.000" oninput="LoanWizard.calculateDSR()">
                    </div>
                    <div class="form-group">
                      <label class="form-label">Kewajiban Lain (Rp)</label>
                      <input type="text" id="dsrOtherDebtInput" class="form-control font-mono" value="500.000" oninput="LoanWizard.calculateDSR()">
                    </div>
                  </div>
                  <div style="background: var(--bg-canvas); padding: 14px; border-radius: 10px; border: 1px solid var(--border-card);">
                    <div style="font-size: 12px; font-weight: 700; margin-bottom: 8px;">Matriks Penilaian 5C:</div>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; font-size: 11px; text-align: center;">
                      <div style="background: var(--bg-surface); padding: 6px; border-radius: 4px;"><div>Char</div><b class="text-emerald">90</b></div>
                      <div style="background: var(--bg-surface); padding: 6px; border-radius: 4px;"><div>Capac</div><b class="text-emerald">85</b></div>
                      <div style="background: var(--bg-surface); padding: 6px; border-radius: 4px;"><div>Capit</div><b class="text-emerald">80</b></div>
                      <div style="background: var(--bg-surface); padding: 6px; border-radius: 4px;"><div>Collat</div><b class="text-emerald">95</b></div>
                      <div style="background: var(--bg-surface); padding: 6px; border-radius: 4px;"><div>Cond</div><b class="text-emerald">82</b></div>
                    </div>
                  </div>
                </div>
                <div style="text-align: center; background: var(--bg-surface-elevated); padding: 20px; border-radius: 12px; border: 1px solid var(--border-card);">
                  <div style="font-size: 11px; color: var(--text-muted);">Credit Score (5C)</div>
                  <div class="font-mono" style="font-size: 32px; font-weight: 800; color: var(--emerald-500);">845 <span style="font-size: 12px; color: var(--text-muted);">/1000</span></div>
                  <div style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">DSR Ratio: <b class="font-mono text-emerald" id="dsrRatioBadge">25.28%</b></div>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                <button type="button" class="btn btn-secondary" onclick="LoanWizard.prevStep()"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                <button type="button" class="btn btn-primary" onclick="LoanWizard.nextStep()">Lanjut ke Komite <i class="fa-solid fa-arrow-right"></i></button>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="wizard-step-content" id="stepContent4">
              <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Langkah 4: Sign-off Komite Kredit</h3>
              <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px;">
                <div style="padding: 14px; background: var(--bg-surface-elevated); border-radius: 10px; border: 1px solid var(--border-card);">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span class="badge info">Analis</span><span class="badge success">ACC</span></div>
                  <div style="font-weight: 700;">Budi Santoso, SE</div>
                </div>
                <div style="padding: 14px; background: var(--bg-surface-elevated); border-radius: 10px; border: 1px solid var(--border-card);">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span class="badge info">Manajer</span><span class="badge success">ACC</span></div>
                  <div style="font-weight: 700;">H. Mulyadi Kartono</div>
                </div>
                <div style="padding: 14px; background: var(--bg-surface-elevated); border-radius: 10px; border: 1px solid var(--border-card);">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span class="badge info">Ketua Komite</span><span class="badge warning" id="badgeKetuaKomite">Pending</span></div>
                  <div style="font-weight: 700;">Drs. H. Arifin Siregar</div>
                  <button type="button" id="btnSignOffKetua" onclick="LoanWizard.signOffKetua()" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 8px;">Beri Putusan Setuju</button>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                <button type="button" class="btn btn-secondary" onclick="LoanWizard.prevStep()"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                <button type="button" class="btn btn-primary" onclick="LoanWizard.nextStep()">Lanjut ke SPK & Pencairan <i class="fa-solid fa-arrow-right"></i></button>
              </div>
            </div>

            <!-- Step 5 -->
            <div class="wizard-step-content" id="stepContent5">
              <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Langkah 5: Penerbitan SPK & Pencairan Dana Kas</h3>
              <div style="border: 2px dashed rgba(16, 185, 129, 0.4); border-radius: 14px; padding: 24px; background: var(--bg-canvas); margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                  <div>
                    <div style="font-size: 11px; color: var(--emerald-500); font-weight: 700;">SURAT PERJANJIAN KREDIT (SPK)</div>
                    <div class="font-mono" style="font-size: 18px; font-weight: 800;">SPK/KOSPIN/2024/X-00891</div>
                  </div>
                  <span class="badge success">Disetujui 100%</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; font-size: 13px;">
                  <div><span style="color: var(--text-muted);">Debitur:</span><div style="font-weight: 700;" id="spkDebiturName">Hendra Wijaya</div></div>
                  <div><span style="color: var(--text-muted);">Plafon Disetujui:</span><div class="font-mono text-emerald" style="font-weight: 800; font-size: 16px;" id="spkPlafonFinal">Rp 50.000.000</div></div>
                  <div><span style="color: var(--text-muted);">Tenor:</span><div class="font-mono" style="font-weight: 700;">24 Bulan @ 0.90% Flat</div></div>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-secondary" onclick="LoanWizard.prevStep()"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                <button type="button" class="btn btn-primary btn-lg" onclick="LoanWizard.executeDisbursement()"><i class="fa-solid fa-vault"></i> EKSEKUSI PENCAIRAN DANA KAS</button>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= VIEW 5: BUKU SIMPANAN ================= -->
        <div class="spa-view" id="view-simpanan">
          <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
              <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading);">Buku Simpanan & Mutasi Rekening</h1>
              <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">4 Pos Rekening: Simpanan Pokok, Wajib, Sukarela (SiSuka), dan Deposito Sijaka ARO.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <select id="simpananMemberSelect" class="form-control" style="width: 260px; font-weight: 600;" onchange="KspSimpanan.loadMemberSavings(this.value)"></select>
            </div>
          </div>

          <div class="grid-cols-4" style="margin-bottom: 24px;">
            <div class="kpi-card">
              <div class="kpi-header"><span class="kpi-title">1. Simpanan Pokok</span><div class="kpi-icon emerald"><i class="fa-solid fa-gem"></i></div></div>
              <div class="kpi-value" id="valSimpananPokok">Rp 1.000.000</div>
              <div class="kpi-trend"><span class="badge success">Modal Dasar</span></div>
            </div>
            <div class="kpi-card blue">
              <div class="kpi-header"><span class="kpi-title">2. Simpanan Wajib</span><div class="kpi-icon blue"><i class="fa-solid fa-calendar-check"></i></div></div>
              <div class="kpi-value" id="valSimpananWajib">Rp 14.500.000</div>
              <div class="kpi-trend"><span class="badge info">Bagi Hasil 4.5%</span></div>
            </div>
            <div class="kpi-card">
              <div class="kpi-header"><span class="kpi-title">3. Sukarela (SiSuka)</span><div class="kpi-icon emerald"><i class="fa-solid fa-wallet"></i></div></div>
              <div class="kpi-value" id="valSimpananSukarela">Rp 28.750.000</div>
              <div class="kpi-trend"><span class="badge success">Bagi Hasil 6.0%</span></div>
            </div>
            <div class="kpi-card warning">
              <div class="kpi-header"><span class="kpi-title">4. Sijaka ARO</span><div class="kpi-icon amber"><i class="fa-solid fa-certificate"></i></div></div>
              <div class="kpi-value" id="valSimpananSijaka">Rp 50.000.000</div>
              <div class="kpi-trend"><span class="badge warning" id="jtSijaka">JT: 15 Jun 2025</span></div>
            </div>
          </div>

          <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
              <h3 style="font-size: 15px; font-weight: 700; color: var(--text-heading);">Riwayat Mutasi Debet & Kredit Rekening</h3>
              <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="fa-solid fa-print"></i> Cetak Rekening Koran</button>
            </div>
            <div class="table-container" style="border: none;">
              <table class="table">
                <thead>
                  <tr>
                    <th>Waktu</th>
                    <th>No. Referensi</th>
                    <th>Pos</th>
                    <th>Uraian</th>
                    <th>Debet</th>
                    <th>Kredit</th>
                    <th style="text-align: right;">Saldo Kumulatif</th>
                  </tr>
                </thead>
                <tbody id="tableMutasiBody">
                  <tr>
                    <td class="font-mono">2024-10-24 09:45</td>
                    <td class="font-mono text-emerald" style="font-weight: 700;">TRX-20241024-0081</td>
                    <td><span class="badge success">SiSuka</span></td>
                    <td>Setoran Kasir POS Loket 1</td>
                    <td class="font-mono text-muted">-</td>
                    <td class="font-mono text-emerald" style="font-weight: 700;">+ Rp 5.000.000</td>
                    <td class="font-mono" style="text-align: right; font-weight: 800;">Rp 94.250.000</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ================= VIEW 6: LAPORAN & JURNAL ================= -->
        <div class="spa-view" id="view-laporan">
          <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
              <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading);">Buku Kas, Jurnal Umum & Pelaporan</h1>
              <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Double-entry balanced accounting, rekonsiliasi kas bank, dan simulasi dividen SHU.</p>
            </div>
            <span class="badge success" style="padding: 6px 12px;"><i class="fa-solid fa-scale-balanced"></i> DEBET = KREDIT SEIMBANG</span>
          </div>

          <div class="laporan-tabs">
            <button type="button" class="lap-tab-btn active" id="tabBtnJurnal" onclick="KspLaporan.switchTab('jurnal')">1. Jurnal Umum [F9]</button>
            <button type="button" class="lap-tab-btn" onclick="KspLaporan.switchTab('rekonsiliasi')">2. Rekonsiliasi Kas & Bank</button>
            <button type="button" class="lap-tab-btn" onclick="KspLaporan.switchTab('shu')">3. Simulasi Dividen SHU [F5]</button>
          </div>

          <div id="panelJurnal" class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between;">
              <div style="font-weight: 700;">Buku Jurnal Umum Berpasangan</div>
              <div style="font-size: 12.5px;">Debet: <b class="font-mono text-emerald" id="totalDebetJurnal">Rp 0</b> | Kredit: <b class="font-mono text-emerald" id="totalKreditJurnal">Rp 0</b></div>
            </div>
            <div class="table-container" style="border: none;">
              <table class="table">
                <thead>
                  <tr>
                    <th>Tanggal / ID</th>
                    <th>No. Bukti</th>
                    <th>Kode Akun & Keterangan</th>
                    <th style="text-align: right;">Debet</th>
                    <th style="text-align: right;">Kredit</th>
                  </tr>
                </thead>
                <tbody id="tableJurnalBody"></tbody>
              </table>
            </div>
          </div>

          <div id="panelRekonsiliasi" style="display: none;">
            <div class="grid-cols-4" style="margin-bottom: 24px;">
              <div class="kpi-card"><div class="kpi-title">Kas Teller</div><div class="kpi-value font-mono">Rp 142.500.000</div></div>
              <div class="kpi-card blue"><div class="kpi-title">Kas Brankas</div><div class="kpi-value font-mono">Rp 850.000.000</div></div>
              <div class="kpi-card"><div class="kpi-title">Giro Mandiri</div><div class="kpi-value font-mono">Rp 2.450.000.000</div></div>
              <div class="kpi-card"><div class="kpi-title">Giro BNI</div><div class="kpi-value font-mono">Rp 1.820.000.000</div></div>
            </div>
            <div class="card" style="text-align: center; padding: 24px;">
              <div style="font-size: 12px; color: var(--text-muted);">Saldo Kas Fisik (Cash Count) vs Saldo Buku Kas:</div>
              <div class="font-mono text-emerald" style="font-size: 26px; font-weight: 800; margin: 6px 0;">Rp 992.500.000 (Selisih: Rp 0 / NIHIL)</div>
              <span class="badge success">Rekonsiliasi Harian Terverifikasi</span>
            </div>
          </div>

          <div id="panelSHU" style="display: none;">
            <div class="grid-cols-2">
              <div class="card">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Parameter SHU Tahun Berjalan</h3>
                <div class="form-group">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <label class="form-label" style="margin-bottom: 0;">Estimasi Total SHU:</label>
                    <span id="labelShuTotal" class="font-mono text-emerald" style="font-weight: 800;">Rp 890.000.000</span>
                  </div>
                  <input type="range" id="sliderTotalShu" min="300000000" max="2500000000" step="10000000" value="890000000" style="width: 100%; accent-color: var(--emerald-500);" oninput="KspLaporan.calculateSHU()">
                </div>
                <div style="font-size: 12px; color: var(--text-muted); line-height: 1.8; margin-top: 14px;">
                  <div>• Dana Cadangan (30%): <b class="font-mono text-emerald" id="cadanganShuVal">Rp 267.000.000</b></div>
                  <div>• Jasa Modal / Simpanan (40%): <b class="font-mono text-emerald" id="jasaModalVal">Rp 142.400.000</b></div>
                  <div>• Jasa Usaha / Pinjaman (60%): <b class="font-mono text-emerald" id="jasaUsahaVal">Rp 213.600.000</b></div>
                </div>
              </div>
              <div class="card" style="text-align: center;">
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Estimasi Dividen SHU Anggota (Hendra Wijaya):</div>
                <div id="simulasiShuAnggota" class="font-mono text-emerald" style="font-size: 32px; font-weight: 800; margin: 10px 0;">Rp 4.735.200</div>
                <span class="badge success">Siap Dibagikan Pada RAT 2025</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= VIEW 7: PORTAL MANDIRI ANGGOTA ================= -->
        <div class="spa-view" id="view-portal">
          <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
              <h1 style="font-size: 22px; font-weight: 800; color: var(--text-heading);">Portal Layanan Mandiri Anggota</h1>
              <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Self-service simpanan, pembayaran angsuran pinjaman via QRIS/VA, dan simulator pembiayaan.</p>
            </div>
            <span class="badge platinum" style="padding: 6px 12px;"><i class="fa-solid fa-crown"></i> Membership: PLATINUM</span>
          </div>

          <div class="grid-cols-2" style="margin-bottom: 24px;">
            <div class="digital-card">
              <div style="display: flex; justify-content: space-between; align-items: center; z-index: 2;">
                <img src="assets/img/logo-ksp-core.svg" alt="KSP Logo" style="height: 32px;">
                <div class="card-chip"></div>
              </div>
              <div style="z-index: 2;">
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: var(--text-muted);">NOMOR REKENING ANGGOTA</div>
                <div class="font-mono" style="font-size: 20px; font-weight: 700; letter-spacing: 3px; color: #FFFFFF;">KSP-0029-1488-0003</div>
              </div>
              <div style="display: flex; justify-content: space-between; align-items: flex-end; z-index: 2;">
                <div>
                  <div style="font-size: 10px; color: var(--text-muted);">NAMA PEMILIK</div>
                  <div style="font-size: 15px; font-weight: 800; color: #FFFFFF;">HENDRA WIJAYA</div>
                </div>
                <span class="badge platinum">PLATINUM</span>
              </div>
            </div>

            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total Aset Simpanan Terkonsolidasi</div>
                <div class="font-mono text-emerald" style="font-size: 32px; font-weight: 800; margin: 6px 0;">Rp 94.250.000</div>
                <div style="font-size: 12px; color: var(--text-secondary);">Bagi hasil berjalan bulan ini: <b class="font-mono text-emerald">+ Rp 428.500</b></div>
              </div>
              <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button onclick="KspRouter.switchView('simpanan')" class="btn btn-secondary btn-sm" style="flex: 1;"><i class="fa-solid fa-file-invoice"></i> Rekening Koran</button>
                <button onclick="KspPortal.openPaymentModal()" class="btn btn-primary btn-sm" style="flex: 1;"><i class="fa-solid fa-qrcode"></i> Bayar QRIS</button>
              </div>
            </div>
          </div>

          <div class="grid-cols-2">
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title"><i class="fa-solid fa-hand-holding-dollar text-emerald"></i> Pinjaman Produktif (#PJ-2024-0891)</div>
                  <div class="card-subtitle">Plafon Rp 50.000.000 • Tenor 24 Bulan</div>
                </div>
                <span class="badge success">Kol 1 (Lancar)</span>
              </div>
              <div style="margin-bottom: 14px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                  <span>Angsuran: 9 dari 24 Bulan</span>
                  <span class="font-mono text-emerald" style="font-weight: 700;">37.5% Terbayar</span>
                </div>
                <div style="width: 100%; height: 8px; background: var(--bg-canvas); border-radius: 9999px; overflow: hidden;">
                  <div style="width: 37.5%; height: 100%; background: var(--emerald-500);"></div>
                </div>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 12.5px;">
                <div>Sisa Baki Debet: <b class="font-mono text-rose-500" style="color: var(--rose-500);">Rp 32.500.000</b></div>
                <div>Bulan Ini (JT 05 Nov): <b class="font-mono text-emerald">Rp 2.533.333</b></div>
              </div>
              <button onclick="KspPortal.openPaymentModal()" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-bolt"></i> Bayar via QRIS / Virtual Account</button>
            </div>

            <div class="card">
              <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-calculator text-emerald"></i> Simulasi Pinjaman Baru</div>
              </div>
              <div class="form-group">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                  <label class="form-label" style="margin-bottom: 0;">Plafon:</label>
                  <span id="sliderPlafonVal" class="font-mono text-emerald" style="font-weight: 800;">Rp 30.000.000</span>
                </div>
                <input type="range" id="sliderPlafon" min="5000000" max="100000000" step="5000000" value="30000000" style="width: 100%; accent-color: var(--emerald-500);" oninput="KspPortal.calculateSimulation()">
              </div>
              <div class="form-group" style="margin-top: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                  <label class="form-label" style="margin-bottom: 0;">Tenor:</label>
                  <span id="sliderTenorVal" class="font-mono text-emerald" style="font-weight: 800;">12 Bulan</span>
                </div>
                <input type="range" id="sliderTenor" min="6" max="36" step="6" value="12" style="width: 100%; accent-color: var(--emerald-500);" oninput="KspPortal.calculateSimulation()">
              </div>
              <div style="background: var(--bg-canvas); padding: 12px 16px; border-radius: 8px; margin-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                <div><div style="font-size: 11px; color: var(--text-muted);">Estimasi Angsuran / Bln:</div><div id="simOutputAngsuran" class="font-mono text-emerald" style="font-size: 18px; font-weight: 800;">Rp 2.770.000</div></div>
                <button onclick="KspRouter.switchView('pinjaman')" class="btn btn-secondary btn-sm">Ajukan Pinjaman</button>
              </div>
            </div>
          </div>
        </div>

      </section>

    </main>
  </div>

  <!-- Modal Global Search (Ctrl + K) -->
  <div class="modal-backdrop" id="modalGlobalSearch" onclick="if(event.target === this) KspApp.closeAllModals()">
    <div class="modal-container" style="max-width: 580px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-magnifying-glass text-emerald" style="font-size: 16px;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-heading);">Pencarian Cepat Anggota</h3>
        </div>
        <span class="hotkey-badge">ESC</span>
      </div>
      <div class="modal-body">
        <input type="text" id="inputGlobalSearch" class="form-control" placeholder="Ketik NIK, Nama Anggota, atau Unit Usaha..." autocomplete="off">
        <div id="globalSearchResults" style="margin-top: 16px; max-height: 360px; overflow-y: auto;"></div>
      </div>
    </div>
  </div>

  <!-- Modal Registrasi Anggota Baru (KYC) -->
  <div class="modal-backdrop" id="modalRegisterAnggota" onclick="if(event.target === this) KspApp.closeAllModals()">
    <div class="modal-container">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-user-plus text-emerald" style="font-size: 18px;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-heading);">Registrasi Anggota Baru (Form KYC)</h3>
        </div>
        <button onclick="KspApp.closeAllModals()" class="btn btn-secondary btn-sm"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <form id="formRegisterAnggota">
        <div class="modal-body">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div class="form-group">
              <label class="form-label">Nomor Induk Kependudukan (NIK) *</label>
              <input type="text" id="regNik" class="form-control font-mono" maxlength="16" placeholder="16 Digit Angka NIK KTP" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama Lengkap (Sesuai KTP) *</label>
              <input type="text" id="regNama" class="form-control" placeholder="Contoh: Hendra Wijaya" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Alamat Lengkap Domisili *</label>
            <input type="text" id="regAlamat" class="form-control" placeholder="Jalan, Kelurahan, Kota" required>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div class="form-group">
              <label class="form-label">Nomor Telepon / WhatsApp *</label>
              <input type="text" id="regNoHp" class="form-control" placeholder="0812-xxxx-xxxx" required>
            </div>
            <div class="form-group">
              <label class="form-label">Unit Usaha / Usaha Anggota</label>
              <input type="text" id="regUnitUsaha" class="form-control" placeholder="Contoh: Toko Sembako">
            </div>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div class="form-group">
              <label class="form-label">Grade</label>
              <select id="regGrade" class="form-control">
                <option value="Silver">Silver</option>
                <option value="Gold">Gold</option>
                <option value="Platinum">Platinum</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Setoran Pokok Awal (Rp)</label>
              <input type="text" id="regSetoranPokok" class="form-control font-mono" value="1.000.000">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" onclick="KspApp.closeAllModals()" class="btn btn-secondary">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Simpan & Daftarkan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detail Anggota -->
  <div class="modal-backdrop" id="modalDetailAnggota" onclick="if(event.target === this) KspApp.closeAllModals()">
    <div class="modal-container" style="max-width: 680px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-id-card text-emerald" style="font-size: 18px;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-heading);">Profil Anggota & Portofolio Rekening</h3>
        </div>
        <button onclick="KspApp.closeAllModals()" class="btn btn-secondary btn-sm"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body" id="modalDetailAnggotaBody"></div>
      <div class="modal-footer">
        <button type="button" onclick="KspApp.closeAllModals()" class="btn btn-secondary">Tutup</button>
      </div>
    </div>
  </div>

  <!-- Modal QRIS Payment -->
  <div class="modal-backdrop" id="modalPaymentQRIS" onclick="if(event.target === this) KspApp.closeAllModals()">
    <div class="modal-container" style="max-width: 500px;">
      <div class="modal-header">
        <div style="display: flex; align-items: center; gap: 10px;">
          <i class="fa-solid fa-qrcode text-emerald" style="font-size: 18px;"></i>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--text-heading);">Pembayaran Digital QRIS & Virtual Account</h3>
        </div>
        <button onclick="KspApp.closeAllModals()" class="btn btn-secondary btn-sm"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body qris-modal-box">
        <div style="font-size: 12px; color: var(--text-muted);">Total Tagihan Angsuran Ke-9:</div>
        <div class="font-mono text-emerald" style="font-size: 26px; font-weight: 800; margin: 4px 0 14px 0;">Rp 2.533.333</div>
        <div class="qris-qr-sim">
          <svg viewBox="0 0 100 100" width="160" height="160" fill="#000">
            <rect x="5" y="5" width="26" height="26" fill="none" stroke="#000" stroke-width="4"/>
            <rect x="11" y="11" width="14" height="14"/>
            <rect x="69" y="5" width="26" height="26" fill="none" stroke="#000" stroke-width="4"/>
            <rect x="75" y="11" width="14" height="14"/>
            <rect x="5" y="69" width="26" height="26" fill="none" stroke="#000" stroke-width="4"/>
            <rect x="11" y="75" width="14" height="14"/>
            <rect x="42" y="42" width="16" height="16" fill="#10B981"/>
          </svg>
        </div>
        <div style="font-size: 12px; color: var(--text-muted);">Pindai via BCA, Mandiri, BRI, QRIS atau e-Wallet</div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="KspApp.closeAllModals()" class="btn btn-secondary">Tutup</button>
        <button type="button" onclick="KspToast.success('Pembayaran Angsuran Rp 2.533.333 BERHASIL DIVERIFIKASI!'); KspApp.closeAllModals();" class="btn btn-primary">Konfirmasi Bayar</button>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/js/mock-data.js"></script>
  <script src="assets/js/chart-config.js"></script>
  <script src="assets/js/kasir.js"></script>
  <script src="assets/js/master-data.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- Single Page Application Integrated Router -->
  <script>
    const KspRouter = {
      currentView: 'dashboard',

      switchView: function(viewId, params = {}) {
        this.currentView = viewId;

        // Update nav links
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
          link.classList.toggle('active', link.dataset.view === viewId);
        });

        // Hide all views & show active
        document.querySelectorAll('.spa-view').forEach(v => v.classList.remove('active'));
        const targetView = document.getElementById(`view-${viewId}`);
        if (targetView) targetView.classList.add('active');

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Trigger view-specific re-renders
        if (viewId === 'dashboard') {
          setTimeout(() => KspCharts.initDashboardCharts(), 50);
        } else if (viewId === 'kasir') {
          KspKasir.init();
          if (params.selectMember) {
            const sel = document.getElementById('kasirMemberSelect');
            if (sel) {
              sel.value = params.selectMember;
              KspKasir.onMemberSelect(params.selectMember);
            }
          }
        } else if (viewId === 'master') {
          KspMasterData.init();
        } else if (viewId === 'pinjaman') {
          if (typeof LoanWizard !== 'undefined') LoanWizard.init();
        } else if (viewId === 'simpanan') {
          if (typeof KspSimpanan !== 'undefined') KspSimpanan.init();
        } else if (viewId === 'laporan') {
          if (typeof KspLaporan !== 'undefined') KspLaporan.init();
        } else if (viewId === 'portal') {
          if (typeof KspPortal !== 'undefined') KspPortal.init();
        }

        KspApp.updateLiquidityBadge();
      }
    };

    // Override LoanWizard, KspSimpanan, KspLaporan, KspPortal handlers for SPA Hub
    const LoanWizard = {
      currentStep: 1,
      init: function() {
        this.populateMembers();
        this.calculateInstallment();
        this.calculateLTV();
        this.calculateDSR();
      },
      populateMembers: function() {
        const sel = document.getElementById('loanMemberSelect');
        if (!sel) return;
        const data = KspDataStore.get();
        sel.innerHTML = data.anggota.map(a => `<option value="${a.id_anggota}">${a.id_anggota} - ${a.nama_lengkap} (${a.unit_usaha})</option>`).join('');
      },
      goToStep: function(step) {
        this.currentStep = step;
        document.querySelectorAll('#view-pinjaman .stepper-step').forEach(st => {
          const s = parseInt(st.dataset.step);
          st.classList.toggle('active', s === step);
          st.classList.toggle('completed', s < step);
        });
        document.querySelectorAll('#view-pinjaman .wizard-step-content').forEach((c, idx) => {
          c.classList.toggle('active', (idx + 1) === step);
        });
      },
      nextStep: function() { if (this.currentStep < 5) this.goToStep(this.currentStep + 1); },
      prevStep: function() { if (this.currentStep > 1) this.goToStep(this.currentStep - 1); },
      calculateInstallment: function() {
        const plafonInput = document.getElementById('loanPlafonInput');
        const tenorSelect = document.getElementById('loanTenorSelect');
        if (!plafonInput || !tenorSelect) return;
        const plafon = parseFloat(plafonInput.value.replace(/[^0-9]/g, '')) || 50000000;
        const tenor = parseInt(tenorSelect.value) || 24;
        const rate = 0.90;
        const pokokBulan = Math.round(plafon / tenor);
        const bungaBulan = Math.round(plafon * (rate / 100));
        const total = pokokBulan + bungaBulan;
        document.getElementById('simPokokBulanan').innerText = KspDataStore.formatRupiah(pokokBulan);
        document.getElementById('simBungaBulanan').innerText = KspDataStore.formatRupiah(bungaBulan);
        document.getElementById('simTotalAngsuran').innerText = KspDataStore.formatRupiah(total);
        document.getElementById('simTotalKewajiban').innerText = KspDataStore.formatRupiah(total * tenor);
        if (document.getElementById('spkPlafonFinal')) document.getElementById('spkPlafonFinal').innerText = KspDataStore.formatRupiah(plafon);
      },
      calculateLTV: function() {
        const plafon = parseFloat(document.getElementById('loanPlafonInput').value.replace(/[^0-9]/g, '')) || 50000000;
        const pasar = parseFloat(document.getElementById('agunanPasar').value.replace(/[^0-9]/g, '')) || 140000000;
        document.getElementById('agunanLikuidasi').value = Math.round(pasar * 0.8).toLocaleString('id-ID');
        const ltv = ((plafon / pasar) * 100).toFixed(2);
        document.getElementById('agunanLtvBadge').innerText = `${ltv}% (Aman)`;
      },
      calculateDSR: function() {
        const income = parseFloat(document.getElementById('dsrIncomeInput').value.replace(/[^0-9]/g, '')) || 12000000;
        const other = parseFloat(document.getElementById('dsrOtherDebtInput').value.replace(/[^0-9]/g, '')) || 500000;
        const ksp = parseFloat(document.getElementById('simTotalAngsuran').innerText.replace(/[^0-9]/g, '')) || 2533333;
        const dsr = (((other + ksp) / income) * 100).toFixed(2);
        document.getElementById('dsrRatioBadge').innerText = `${dsr}%`;
      },
      signOffKetua: function() {
        document.getElementById('badgeKetuaKomite').className = 'badge success';
        document.getElementById('badgeKetuaKomite').innerHTML = '<i class="fa-solid fa-check"></i> DISETUJUI';
        document.getElementById('btnSignOffKetua').disabled = true;
        document.getElementById('btnSignOffKetua').innerText = 'Ditandatangani Ketua';
        KspToast.success('Ketua Komite menyetujui pengajuan pembiayaan!');
      },
      executeDisbursement: function() {
        KspToast.success('Plafon Pinjaman BERHASIL DICAIRKAN ke Kasir!');
        setTimeout(() => KspRouter.switchView('dashboard'), 1200);
      }
    };

    const KspSimpanan = {
      init: function() {
        const sel = document.getElementById('simpananMemberSelect');
        if (!sel) return;
        const data = KspDataStore.get();
        sel.innerHTML = data.anggota.map(a => `<option value="${a.id_anggota}">${a.id_anggota} - ${a.nama_lengkap}</option>`).join('');
        this.loadMemberSavings('KSP-002914');
      },
      loadMemberSavings: function(id_anggota) {
        const data = KspDataStore.get();
        const accs = data.simpanan.filter(s => s.id_anggota === id_anggota);
        document.getElementById('valSimpananPokok').innerText = KspDataStore.formatRupiah(accs.find(s=>s.jenis_simpanan==='Pokok')?.saldo_aktif || 1000000);
        document.getElementById('valSimpananWajib').innerText = KspDataStore.formatRupiah(accs.find(s=>s.jenis_simpanan==='Wajib')?.saldo_aktif || 0);
        document.getElementById('valSimpananSukarela').innerText = KspDataStore.formatRupiah(accs.find(s=>s.jenis_simpanan==='Sukarela')?.saldo_aktif || 0);
        document.getElementById('valSimpananSijaka').innerText = KspDataStore.formatRupiah(accs.find(s=>s.jenis_simpanan==='Berjangka')?.saldo_aktif || 0);
      }
    };

    const KspLaporan = {
      init: function() {
        this.renderJurnal();
        this.calculateSHU();
      },
      switchTab: function(tabName) {
        document.querySelectorAll('.laporan-tabs .lap-tab-btn').forEach(b => b.classList.toggle('active', b.textContent.toLowerCase().includes(tabName)));
        document.getElementById('panelJurnal').style.display = tabName === 'jurnal' ? 'block' : 'none';
        document.getElementById('panelRekonsiliasi').style.display = tabName === 'rekonsiliasi' ? 'block' : 'none';
        document.getElementById('panelSHU').style.display = tabName === 'shu' ? 'block' : 'none';
      },
      renderJurnal: function() {
        const tbody = document.getElementById('tableJurnalBody');
        if (!tbody) return;
        const data = KspDataStore.get();
        let d = 0, k = 0, html = '';
        data.jurnal_umum.forEach(j => {
          j.items.forEach((item, idx) => {
            d += item.debet; k += item.kredit;
            html += `
              <tr>
                <td class="font-mono" style="font-size:12px;">${idx === 0 ? j.tanggal : ''}</td>
                <td class="font-mono text-emerald" style="font-size:12px;">${idx === 0 ? j.no_bukti : ''}</td>
                <td style="${item.kredit > 0 ? 'padding-left:20px;' : ''}"><span class="font-mono text-muted">[${item.kode_akun}]</span> ${item.nama_akun}</td>
                <td class="font-mono" style="text-align:right;">${item.debet > 0 ? KspDataStore.formatRupiah(item.debet) : '-'}</td>
                <td class="font-mono" style="text-align:right;">${item.kredit > 0 ? KspDataStore.formatRupiah(item.kredit) : '-'}</td>
              </tr>
            `;
          });
        });
        tbody.innerHTML = html;
        document.getElementById('totalDebetJurnal').innerText = KspDataStore.formatRupiah(d);
        document.getElementById('totalKreditJurnal').innerText = KspDataStore.formatRupiah(k);
      },
      calculateSHU: function() {
        const total = parseFloat(document.getElementById('sliderTotalShu').value);
        document.getElementById('labelShuTotal').innerText = KspDataStore.formatRupiah(total);
        document.getElementById('cadanganShuVal').innerText = KspDataStore.formatRupiah(total * 0.3);
        document.getElementById('jasaModalVal').innerText = KspDataStore.formatRupiah(total * 0.4 * 0.4);
        document.getElementById('jasaUsahaVal').innerText = KspDataStore.formatRupiah(total * 0.4 * 0.6);
        document.getElementById('simulasiShuAnggota').innerText = KspDataStore.formatRupiah(Math.round(4735200 * (total / 890000000)));
      }
    };

    const KspPortal = {
      init: function() { this.calculateSimulation(); },
      openPaymentModal: function() { document.getElementById('modalPaymentQRIS').classList.add('active'); },
      calculateSimulation: function() {
        const p = parseFloat(document.getElementById('sliderPlafon').value);
        const t = parseInt(document.getElementById('sliderTenor').value);
        document.getElementById('sliderPlafonVal').innerText = KspDataStore.formatRupiah(p);
        document.getElementById('sliderTenorVal').innerText = `${t} Bulan`;
        const angs = Math.round(p / t) + Math.round(p * 0.009);
        document.getElementById('simOutputAngsuran').innerText = KspDataStore.formatRupiah(angs);
      }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
      KspCharts.initDashboardCharts();
      KspApp.updateLiquidityBadge();

      // Check URL hash for routing (e.g. #kasir)
      const hash = window.location.hash.replace('#', '');
      if (hash && document.getElementById(`view-${hash}`)) {
        KspRouter.switchView(hash);
      }
    });
  </script>
</body>
</html>
