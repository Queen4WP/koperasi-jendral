/* ==========================================================================
   KSP CORE ENTERPRISE SYSTEM - MAIN APPLICATION HUB
   Router SPA, Keyboard Shortcuts (F2, F3, F5, F9, Ctrl+K), Modals & Toasts
   ========================================================================== */

// Global Toast Manager
const KspToast = {
  show: function(message, type = 'success', duration = 3500) {
    let container = document.getElementById('globalToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'globalToastContainer';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    let icon = 'fa-circle-check';
    if (type === 'warning') icon = 'fa-triangle-exclamation';
    if (type === 'danger') icon = 'fa-circle-exclamation';
    if (type === 'info') icon = 'fa-circle-info';

    toast.innerHTML = `
      <i class="fa-solid ${icon}"></i>
      <span style="font-size: 13px; font-weight: 500;">${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  },

  success: function(msg) { this.show(msg, 'success'); },
  warning: function(msg) { this.show(msg, 'warning'); },
  danger: function(msg) { this.show(msg, 'danger'); },
  info: function(msg) { this.show(msg, 'info'); }
};

// Global Hotkeys Listener
document.addEventListener('keydown', function(e) {
  // Ctrl + K -> Global Search
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    KspApp.openGlobalSearch();
    return;
  }

  // F2 -> Kasir POS
  if (e.key === 'F2') {
    e.preventDefault();
    KspApp.navigateTo('kasir');
    KspToast.info("Navigasi Cepat [F2]: Terminal Kasir POS");
    return;
  }

  // F3 -> Modul Pinjaman & Scoring 5C
  if (e.key === 'F3') {
    e.preventDefault();
    KspApp.navigateTo('pinjaman');
    KspToast.info("Navigasi Cepat [F3]: Formulir Pinjaman & Scoring 5C");
    return;
  }

  // F5 -> SHU & Laporan (Prevent browser refresh only if desirable, but allow standard if Shift is pressed)
  if (e.key === 'F5' && !e.shiftKey) {
    e.preventDefault();
    KspApp.navigateTo('laporan');
    KspToast.info("Navigasi Cepat [F5]: Buku Kas, Jurnal & Simulasi SHU");
    return;
  }

  // F9 -> Jurnal Kas Besar
  if (e.key === 'F9') {
    e.preventDefault();
    KspApp.navigateTo('laporan');
    // Scroll to journal tab if in laporan
    const journalTab = document.getElementById('tabBtnJurnal');
    if (journalTab) journalTab.click();
    KspToast.info("Navigasi Cepat [F9]: Jurnal Umum Berpasangan");
    return;
  }

  // Escape -> Close Modals
  if (e.key === 'Escape') {
    KspApp.closeAllModals();
  }
});

// Main App Controller
const KspApp = {
  currentPage: 'dashboard',

  init: function() {
    this.updateLiquidityBadge();
    this.setupGlobalSearchModal();
    this.highlightActiveNavLink();
  },

  updateLiquidityBadge: function() {
    const data = KspDataStore.get();
    const el = document.getElementById('topbarBrankasValue');
    if (el) {
      el.innerText = KspDataStore.formatRupiah(data.financials.kas_brankas);
    }
  },

  openGlobalSearch: function() {
    const modal = document.getElementById('modalGlobalSearch');
    if (modal) {
      modal.classList.add('active');
      const input = document.getElementById('inputGlobalSearch');
      if (input) {
        input.value = '';
        input.focus();
        this.renderGlobalSearchResults('');
      }
    }
  },

  closeAllModals: function() {
    document.querySelectorAll('.modal-backdrop').forEach(m => m.classList.remove('active'));
  },

  setupGlobalSearchModal: function() {
    const input = document.getElementById('inputGlobalSearch');
    if (input) {
      input.addEventListener('input', (e) => {
        this.renderGlobalSearchResults(e.target.value.trim().toLowerCase());
      });
    }
  },

  renderGlobalSearchResults: function(query) {
    const resultBox = document.getElementById('globalSearchResults');
    if (!resultBox) return;

    const data = KspDataStore.get();
    let filtered = data.anggota;
    if (query) {
      filtered = data.anggota.filter(a => 
        a.nama_lengkap.toLowerCase().includes(query) ||
        a.nik.includes(query) ||
        a.id_anggota.toLowerCase().includes(query) ||
        a.unit_usaha.toLowerCase().includes(query)
      );
    }

    if (filtered.length === 0) {
      resultBox.innerHTML = `
        <div style="text-align: center; padding: 24px; color: var(--text-muted);">
          <i class="fa-solid fa-user-slash" style="font-size: 24px; margin-bottom: 8px;"></i>
          <p>Tidak ditemukan anggota dengan kata kunci "${query}"</p>
        </div>
      `;
      return;
    }

    resultBox.innerHTML = filtered.map(a => `
      <div onclick="KspApp.selectAnggotaFromSearch('${a.id_anggota}')" style="
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-radius: 10px; cursor: pointer;
        background: var(--bg-canvas); margin-bottom: 8px; border: 1px solid var(--border-subtle);
        transition: all 0.15s;
      " onmouseover="this.style.borderColor='var(--emerald-500)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-surface-elevated); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--emerald-500);">
            ${a.nama_lengkap.charAt(0)}
          </div>
          <div>
            <div style="font-weight: 600; color: var(--text-heading); font-size: 13.5px;">${a.nama_lengkap}</div>
            <div style="font-size: 11.5px; color: var(--text-muted);">NIK: <span class="font-mono">${a.nik}</span> • ${a.unit_usaha}</div>
          </div>
        </div>
        <div style="text-align: right;">
          <span class="badge ${a.grade_keanggotaan.toLowerCase()}">${a.grade_keanggotaan}</span>
          <div style="font-size: 10px; color: var(--text-muted); margin-top: 3px; font-family: 'JetBrains Mono';">${a.id_anggota}</div>
        </div>
      </div>
    `).join('');
  },

  selectAnggotaFromSearch: function(id_anggota) {
    this.closeAllModals();
    // If Kasir POS is open, select the member
    if (typeof KspKasir !== 'undefined' && document.getElementById('kasirMemberSelect')) {
      document.getElementById('kasirMemberSelect').value = id_anggota;
      KspKasir.onMemberSelect(id_anggota);
    } else {
      // Navigate to kasir with preselection or data master
      this.navigateTo('kasir', { selectMember: id_anggota });
    }
  },

  navigateTo: function(pageKey, params = {}) {
    const isSinglePageShell = document.getElementById('spaContentArea') !== null;
    if (isSinglePageShell) {
      if (typeof KspRouter !== 'undefined') {
        KspRouter.loadView(pageKey, params);
      }
    } else {
      // In standalone page mode
      const pageMap = {
        'dashboard': 'dashboard.html',
        'kasir': 'kasir-pos.html',
        'master': 'data-master.html',
        'pinjaman': 'form-pinjaman.html',
        'simpanan': 'simpanan.html',
        'laporan': 'laporan.html',
        'portal': 'portal-anggota.html'
      };
      const target = pageMap[pageKey] || 'dashboard.html';
      // Adjust path if already in pages/ or at root
      const isInPagesDir = window.location.pathname.includes('/pages/');
      const url = isInPagesDir ? target : `pages/${target}`;
      window.location.href = url;
    }
  },

  highlightActiveNavLink: function() {
    const path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (href && path.endsWith(href)) {
        link.classList.add('active');
      }
    });
  }
};

document.addEventListener('DOMContentLoaded', () => {
  KspApp.init();
});
