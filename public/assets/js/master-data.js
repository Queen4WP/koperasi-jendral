/* ==========================================================================
   KSP CORE ENTERPRISE SYSTEM - MASTER DATA KEANGGOTAAN
   CRUD Anggota, Filter Status, Modal KYC Identitas, & Rekap 4 Pos Rekening
   ========================================================================== */

const KspMasterData = {
  currentFilterStatus: 'Semua',
  searchQuery: '',

  init: function() {
    this.renderAnggotaTable();
    this.bindEvents();
  },

  renderAnggotaTable: function() {
    const tableBody = document.getElementById('tableAnggotaBody');
    if (!tableBody) return;

    const data = KspDataStore.get();
    let list = data.anggota;

    // Filter by status
    if (this.currentFilterStatus !== 'Semua') {
      list = list.filter(a => a.status_keanggotaan === this.currentFilterStatus);
    }

    // Filter by query
    if (this.searchQuery) {
      const q = this.searchQuery.toLowerCase();
      list = list.filter(a => 
        a.nama_lengkap.toLowerCase().includes(q) ||
        a.nik.includes(q) ||
        a.id_anggota.toLowerCase().includes(q) ||
        a.unit_usaha.toLowerCase().includes(q)
      );
    }

    // Update Counter
    const countEl = document.getElementById('totalAnggotaCount');
    if (countEl) countEl.innerText = `${list.length} Anggota`;

    if (list.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-users-slash" style="font-size: 28px; margin-bottom: 8px; display: block;"></i>
            Data anggota tidak ditemukan dengan kriteria pencarian ini.
          </td>
        </tr>
      `;
      return;
    }

    tableBody.innerHTML = list.map(a => {
      // Get total savings
      const accs = data.simpanan.filter(s => s.id_anggota === a.id_anggota);
      const totalSimp = accs.reduce((acc, curr) => acc + curr.saldo_aktif, 0);

      // Check loan
      const loan = data.pinjaman.find(p => p.id_anggota === a.id_anggota && p.status_approval === 'Disetujui');
      const bakiDebet = loan ? loan.baki_debet : 0;

      let badgeClass = 'aktif';
      if (a.status_keanggotaan === 'Luar Biasa') badgeClass = 'warning';
      if (a.status_keanggotaan.includes('Verifikasi')) badgeClass = 'info';

      return `
        <tr>
          <td>
            <div style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--emerald-500); font-size: 13px;">
              ${a.id_anggota}
            </div>
            <div style="font-size: 11px; color: var(--text-muted);">${a.tanggal_gabung}</div>
          </td>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-surface-elevated); border: 1.5px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--emerald-500); font-size: 12px;">
                ${a.nama_lengkap.charAt(0)}
              </div>
              <div>
                <div style="font-weight: 600; color: var(--text-heading);">${a.nama_lengkap}</div>
                <div style="font-size: 11px; color: var(--text-muted);">NIK: <span class="font-mono">${a.nik}</span></div>
              </div>
            </div>
          </td>
          <td>
            <div style="color: var(--text-heading); font-size: 13px;">${a.unit_usaha}</div>
            <div style="font-size: 11px; color: var(--text-muted);"><i class="fa-solid fa-phone" style="font-size: 10px;"></i> ${a.no_telepon}</div>
          </td>
          <td>
            <span class="badge ${a.grade_keanggotaan.toLowerCase()}">${a.grade_keanggotaan}</span>
          </td>
          <td class="font-mono" style="font-weight: 700; color: var(--emerald-500);">
            ${KspDataStore.formatRupiah(totalSimp)}
          </td>
          <td class="font-mono" style="font-weight: 600; color: ${bakiDebet > 0 ? 'var(--amber-500)' : 'var(--text-muted)'};">
            ${bakiDebet > 0 ? KspDataStore.formatRupiah(bakiDebet) : '-'}
          </td>
          <td>
            <span class="badge ${badgeClass}">${a.status_keanggotaan}</span>
          </td>
          <td style="text-align: right;">
            <div style="display: inline-flex; gap: 6px;">
              <button onclick="KspMasterData.showDetailModal('${a.id_anggota}')" class="btn btn-secondary btn-sm" title="Lihat 4 Pos Rekening & Profil">
                <i class="fa-solid fa-eye"></i> Detail
              </button>
              <button onclick="KspMasterData.transactFromMaster('${a.id_anggota}')" class="btn btn-primary btn-sm" title="Buka di Kasir POS">
                <i class="fa-solid fa-cash-register"></i> Kasir
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  },

  showDetailModal: function(id_anggota) {
    const data = KspDataStore.get();
    const a = data.anggota.find(m => m.id_anggota === id_anggota);
    if (!a) return;

    const accounts = data.simpanan.filter(s => s.id_anggota === id_anggota);
    const loan = data.pinjaman.find(p => p.id_anggota === id_anggota);

    const modalBody = document.getElementById('modalDetailAnggotaBody');
    if (!modalBody) return;

    modalBody.innerHTML = `
      <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-subtle);">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, var(--emerald-600), #0A1128); border: 2px solid var(--emerald-500); display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 800; color: white;">
          ${a.nama_lengkap.charAt(0)}
        </div>
        <div style="flex: 1;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-heading);">${a.nama_lengkap}</h3>
            <span class="badge ${a.grade_keanggotaan.toLowerCase()}">${a.grade_keanggotaan}</span>
          </div>
          <div style="color: var(--text-muted); font-size: 12.5px; margin-top: 4px;">
            ID: <b class="font-mono text-emerald">${a.id_anggota}</b> • NIK: <span class="font-mono">${a.nik}</span> • Bergabung: ${a.tanggal_gabung}
          </div>
          <div style="color: var(--text-secondary); font-size: 12.5px; margin-top: 2px;">
            <i class="fa-solid fa-shop" style="color: var(--emerald-500); margin-right: 4px;"></i> ${a.unit_usaha} | <i class="fa-solid fa-location-dot" style="margin-left: 6px; margin-right: 4px;"></i> ${a.alamat}
          </div>
        </div>
      </div>

      <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--text-heading);">
        <i class="fa-solid fa-wallet text-emerald" style="margin-right: 6px;"></i> Rekapitulasi 4 Pos Rekening Simpanan
      </h4>
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 24px;">
        ${accounts.map(acc => `
          <div style="padding: 14px; background: var(--bg-canvas); border: 1px solid var(--border-card); border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">
              <span>Simpanan ${acc.jenis_simpanan}</span>
              <span class="font-mono">${acc.id_rekening}</span>
            </div>
            <div class="font-mono" style="font-size: 18px; font-weight: 800; color: var(--emerald-500); margin-top: 6px;">
              ${KspDataStore.formatRupiah(acc.saldo_aktif)}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
              Bagi Hasil / Bunga: ${acc.bunga_tahunan}% p.a. ${acc.jatuh_tempo ? `• JT: ${acc.jatuh_tempo}` : ''}
            </div>
          </div>
        `).join('')}
      </div>

      <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--text-heading);">
        <i class="fa-solid fa-hand-holding-dollar text-amber" style="margin-right: 6px;"></i> Status Pembiayaan Pinjaman Aktif
      </h4>
      ${loan ? `
        <div style="padding: 16px; background: var(--bg-canvas); border: 1px solid var(--border-card); border-radius: 10px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <div>
              <span class="font-mono" style="font-weight: 700; color: var(--text-heading); font-size: 14px;">${loan.id_pinjaman}</span>
              <span class="badge ${loan.status_approval === 'Disetujui' ? 'success' : 'warning'}" style="margin-left: 8px;">${loan.status_approval}</span>
            </div>
            <span class="badge ${loan.kolektibilitas.includes('1') ? 'lancar' : 'dpk'}">${loan.kolektibilitas}</span>
          </div>
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; font-size: 12px;">
            <div>
              <div style="color: var(--text-muted);">Plafon Pinjaman:</div>
              <div class="font-mono" style="font-weight: 700; color: var(--text-heading); font-size: 14px;">${KspDataStore.formatRupiah(loan.plafon_disetujui || loan.plafon_diajukan)}</div>
            </div>
            <div>
              <div style="color: var(--text-muted);">Sisa Baki Debet:</div>
              <div class="font-mono" style="font-weight: 700; color: var(--rose-500); font-size: 14px;">${KspDataStore.formatRupiah(loan.baki_debet)}</div>
            </div>
            <div>
              <div style="color: var(--text-muted);">Credit Score (5C):</div>
              <div class="font-mono" style="font-weight: 700; color: var(--emerald-500); font-size: 14px;">${loan.credit_score} / 1000</div>
            </div>
          </div>
        </div>
      ` : `
        <div style="padding: 14px; text-align: center; color: var(--text-muted); background: var(--bg-canvas); border-radius: 8px;">
          Anggota ini tidak memiliki catatan pinjaman berjalan.
        </div>
      `}
    `;

    document.getElementById('modalDetailAnggota').classList.add('active');
  },

  transactFromMaster: function(id_anggota) {
    KspApp.navigateTo('kasir');
    setTimeout(() => {
      if (typeof KspKasir !== 'undefined') {
        const sel = document.getElementById('kasirMemberSelect');
        if (sel) {
          sel.value = id_anggota;
          KspKasir.onMemberSelect(id_anggota);
        }
      }
    }, 200);
  },

  openRegisterModal: function() {
    const form = document.getElementById('formRegisterAnggota');
    if (form) form.reset();
    document.getElementById('modalRegisterAnggota').classList.add('active');
  },

  handleRegisterSubmit: function(e) {
    e.preventDefault();
    const nik = document.getElementById('regNik').value.trim();
    const nama = document.getElementById('regNama').value.trim();
    const alamat = document.getElementById('regAlamat').value.trim();
    const noHp = document.getElementById('regNoHp').value.trim();
    const unitUsaha = document.getElementById('regUnitUsaha').value.trim();
    const grade = document.getElementById('regGrade').value;
    const setoran = parseFloat(document.getElementById('regSetoranPokok').value.replace(/[^0-9]/g, '')) || 1000000;

    if (nik.length !== 16) {
      KspToast.danger("NIK harus terdiri dari 16 digit angka valid!");
      return;
    }

    if (!nama || !alamat || !noHp) {
      KspToast.danger("Semua bidang bertanda bintang wajib diisi!");
      return;
    }

    const data = KspDataStore.get();
    const newId = `KSP-00${2914 + data.anggota.length}`;

    const newAnggota = {
      id_anggota: newId,
      nik: nik,
      nama_lengkap: nama,
      alamat: alamat,
      no_telepon: noHp,
      unit_usaha: unitUsaha || "Wirausaha Mandiri",
      grade_keanggotaan: grade,
      status_keanggotaan: "Aktif",
      tanggal_gabung: new Date().toISOString().split('T')[0],
      email: `${nama.toLowerCase().replace(/\s+/g, '')}@gmail.com`,
      setoran_pokok: setoran,
      foto: "assets/img/avatar-teller.svg"
    };

    KspDataStore.addAnggota(newAnggota);
    KspToast.success(`Anggota baru ${nama} (${newId}) berhasil didaftarkan!`);
    KspApp.closeAllModals();
    this.renderAnggotaTable();
  },

  exportToCSV: function() {
    const data = KspDataStore.get();
    let csv = "ID Anggota,NIK,Nama Lengkap,Unit Usaha,Grade,Status,No Telepon\n";
    data.anggota.forEach(a => {
      csv += `"${a.id_anggota}","'${a.nik}","${a.nama_lengkap}","${a.unit_usaha}","${a.grade_keanggotaan}","${a.status_keanggotaan}","${a.no_telepon}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.setAttribute("download", `Master_Anggota_KSP_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    KspToast.info("File CSV Master Anggota berhasil diunduh.");
  },

  bindEvents: function() {
    const searchInput = document.getElementById('searchAnggotaInput');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.searchQuery = e.target.value.trim();
        this.renderAnggotaTable();
      });
    }

    document.querySelectorAll('.filter-status-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.filter-status-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        this.currentFilterStatus = e.target.dataset.status;
        this.renderAnggotaTable();
      });
    });

    const form = document.getElementById('formRegisterAnggota');
    if (form) {
      form.addEventListener('submit', (e) => this.handleRegisterSubmit(e));
    }
  }
};
