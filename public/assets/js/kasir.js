/* ==========================================================================
   KSP CORE ENTERPRISE SYSTEM - TERMINAL KASIR CEPAT (POS) ENGINE
   Hotkeys [F2], Kalkulasi Kembalian Otomatis, & Cetak Struk Thermal 80mm
   ========================================================================== */

const KspKasir = {
  currentMember: null,
  currentTab: 'setor', // 'setor', 'tarik', 'angsuran'
  selectedJenisSimpanan: 'Sukarela',
  nominalTransaksi: 0,
  cashTendered: 0,
  rincianAngsuran: null,

  init: function() {
    this.populateMemberDropdown();
    this.bindEvents();
    this.selectDefaultMember();
    this.updateSlipPreview();
  },

  populateMemberDropdown: function() {
    const select = document.getElementById('kasirMemberSelect');
    if (!select) return;

    const data = KspDataStore.get();
    select.innerHTML = '<option value="">-- Pilih / Scan Barcode Anggota --</option>' +
      data.anggota.map(a => `
        <option value="${a.id_anggota}">${a.id_anggota} - ${a.nama_lengkap} (${a.unit_usaha})</option>
      `).join('');
  },

  selectDefaultMember: function() {
    const data = KspDataStore.get();
    if (data.anggota.length > 0) {
      const defaultId = data.anggota[0].id_anggota;
      const select = document.getElementById('kasirMemberSelect');
      if (select) select.value = defaultId;
      this.onMemberSelect(defaultId);
    }
  },

  onMemberSelect: function(id_anggota) {
    if (!id_anggota) {
      this.currentMember = null;
      this.renderMemberBadge(null);
      return;
    }

    const data = KspDataStore.get();
    const member = data.anggota.find(a => a.id_anggota === id_anggota);
    this.currentMember = member;
    this.renderMemberBadge(member);

    // If on angsuran tab, lookup active loans
    if (this.currentTab === 'angsuran') {
      this.lookupMemberLoan(id_anggota);
    }

    this.updateSlipPreview();
  },

  renderMemberBadge: function(member) {
    const badgeContainer = document.getElementById('kasirMemberPreview');
    if (!badgeContainer) return;

    if (!member) {
      badgeContainer.innerHTML = `
        <div style="padding: 16px; text-align: center; color: var(--text-muted); border: 1px dashed var(--border-subtle); border-radius: 10px;">
          <i class="fa-solid fa-id-card" style="font-size: 20px; margin-bottom: 6px;"></i>
          <p>Silakan scan kartu anggota atau pilih dari daftar di atas</p>
        </div>
      `;
      return;
    }

    const data = KspDataStore.get();
    const accounts = data.simpanan.filter(s => s.id_anggota === member.id_anggota);
    const saldoSukarela = accounts.find(s => s.jenis_simpanan === 'Sukarela')?.saldo_aktif || 0;
    const saldoWajib = accounts.find(s => s.jenis_simpanan === 'Wajib')?.saldo_aktif || 0;

    badgeContainer.innerHTML = `
      <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: var(--bg-surface-elevated); border: 1px solid var(--border-card); border-radius: 12px;">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--emerald-600), #0A1128); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: #FFFFFF; border: 2px solid var(--emerald-500);">
            ${member.nama_lengkap.charAt(0)}
          </div>
          <div>
            <div style="font-size: 15px; font-weight: 700; color: var(--text-heading);">${member.nama_lengkap}</div>
            <div style="font-size: 12px; color: var(--text-muted); display: flex; gap: 8px;">
              <span>NIK: <b class="font-mono text-secondary">${member.nik}</b></span>
              <span>•</span>
              <span class="text-emerald">${member.unit_usaha}</span>
            </div>
          </div>
        </div>
        <div style="text-align: right;">
          <span class="badge ${member.grade_keanggotaan.toLowerCase()}">${member.grade_keanggotaan}</span>
          <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
            Saldo SiSuka: <b class="font-mono" style="color: var(--emerald-500);">${KspDataStore.formatRupiah(saldoSukarela)}</b>
          </div>
        </div>
      </div>
    `;
  },

  switchTab: function(tabName) {
    this.currentTab = tabName;
    document.querySelectorAll('.pos-tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.tab === tabName);
    });

    // Toggle panels
    document.getElementById('panelSetor').style.display = tabName === 'setor' ? 'block' : 'none';
    document.getElementById('panelTarik').style.display = tabName === 'tarik' ? 'block' : 'none';
    document.getElementById('panelAngsuran').style.display = tabName === 'angsuran' ? 'block' : 'none';

    if (tabName === 'angsuran' && this.currentMember) {
      this.lookupMemberLoan(this.currentMember.id_anggota);
    } else if (tabName === 'setor') {
      const nominalInput = document.getElementById('inputSetorNominal');
      if (nominalInput && nominalInput.value) {
        this.nominalTransaksi = parseFloat(nominalInput.value.replace(/[^0-9]/g, '')) || 0;
      }
    }

    this.calculateChange();
    this.updateSlipPreview();
  },

  lookupMemberLoan: function(id_anggota) {
    const data = KspDataStore.get();
    const loan = data.pinjaman.find(p => p.id_anggota === id_anggota && p.status_approval === 'Disetujui');
    const container = document.getElementById('kasirLoanInfo');
    if (!container) return;

    if (!loan) {
      container.innerHTML = `
        <div style="padding: 16px; text-align: center; color: var(--amber-500); background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 10px;">
          <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i> Anggota ini tidak memiliki pinjaman aktif yang perlu diangsur.
        </div>
      `;
      this.nominalTransaksi = 0;
      this.rincianAngsuran = null;
      this.calculateChange();
      this.updateSlipPreview();
      return;
    }

    const angsuran = loan.jadwal;
    const totalWajibBayar = angsuran.pokok + angsuran.bunga + angsuran.denda;
    this.nominalTransaksi = totalWajibBayar;
    this.rincianAngsuran = {
      id_pinjaman: loan.id_pinjaman,
      angsuran_ke: angsuran.angsuran_ke,
      pokok: angsuran.pokok,
      bunga: angsuran.bunga,
      denda: angsuran.denda,
      jatuh_tempo: angsuran.jatuh_tempo
    };

    container.innerHTML = `
      <div style="padding: 16px; background: var(--bg-surface-elevated); border: 1px solid var(--border-card); border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
          <span style="font-weight: 700; color: var(--text-heading); font-size: 13.5px;">
            <i class="fa-solid fa-file-invoice-dollar text-emerald" style="margin-right: 6px;"></i> ${loan.id_pinjaman}
          </span>
          <span class="badge warning">Angsuran Ke-${angsuran.angsuran_ke}</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 12px; font-size: 12.5px;">
          <div>
            <div style="color: var(--text-muted);">Pokok:</div>
            <div class="font-mono" style="font-weight: 700; color: var(--text-heading);">${KspDataStore.formatRupiah(angsuran.pokok)}</div>
          </div>
          <div>
            <div style="color: var(--text-muted);">Jasa/Bunga:</div>
            <div class="font-mono" style="font-weight: 700; color: var(--amber-500);">${KspDataStore.formatRupiah(angsuran.bunga)}</div>
          </div>
          <div>
            <div style="color: var(--text-muted);">Denda Telat:</div>
            <div class="font-mono" style="font-weight: 700; color: ${angsuran.denda > 0 ? 'var(--rose-500)' : 'var(--emerald-500)'};">${KspDataStore.formatRupiah(angsuran.denda)}</div>
          </div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 8px; border-top: 1px dashed var(--border-subtle);">
          <span style="font-size: 13px; font-weight: 600; color: var(--text-secondary);">Total Tagihan Bulan Ini:</span>
          <span class="font-mono" style="font-size: 17px; font-weight: 800; color: var(--emerald-500);">${KspDataStore.formatRupiah(totalWajibBayar)}</span>
        </div>
      </div>
    `;

    this.calculateChange();
    this.updateSlipPreview();
  },

  setQuickCash: function(amount) {
    const inputCash = document.getElementById('inputCashTendered');
    if (!inputCash) return;

    if (amount === 'pas') {
      this.cashTendered = this.nominalTransaksi;
    } else {
      this.cashTendered = amount;
    }

    inputCash.value = this.cashTendered.toLocaleString('id-ID');
    this.calculateChange();
    this.updateSlipPreview();
  },

  calculateChange: function() {
    const changeAmountEl = document.getElementById('kasirChangeAmount');
    const change = this.cashTendered - this.nominalTransaksi;

    if (changeAmountEl) {
      if (change < 0) {
        changeAmountEl.innerText = `Kurang ${KspDataStore.formatRupiah(Math.abs(change))}`;
        changeAmountEl.style.color = 'var(--rose-500)';
      } else {
        changeAmountEl.innerText = KspDataStore.formatRupiah(change);
        changeAmountEl.style.color = 'var(--emerald-500)';
      }
    }
  },

  updateSlipPreview: function() {
    const slipEl = document.getElementById('thermalSlipContainer');
    if (!slipEl) return;

    const memberName = this.currentMember ? this.currentMember.nama_lengkap : '-';
    const memberId = this.currentMember ? this.currentMember.id_anggota : '-';
    const trxId = `TRX-${new Date().toISOString().slice(0,10).replace(/-/g,'')}-${Math.floor(1000 + Math.random() * 9000)}`;

    let trxTitle = "SETORAN SIMPANAN";
    let detailRow = `<tr><td>Pos Simpanan</td><td style="text-align:right;">${this.selectedJenisSimpanan}</td></tr>`;

    if (this.currentTab === 'tarik') {
      trxTitle = "PENARIKAN TUNAI";
      detailRow = `<tr><td>Jenis</td><td style="text-align:right;">Simpanan Sukarela</td></tr>`;
    } else if (this.currentTab === 'angsuran') {
      trxTitle = "ANGSURAN PINJAMAN";
      const p = this.rincianAngsuran ? this.rincianAngsuran.pokok : 0;
      const b = this.rincianAngsuran ? this.rincianAngsuran.bunga : 0;
      const d = this.rincianAngsuran ? this.rincianAngsuran.denda : 0;
      detailRow = `
        <tr><td>Pokok Pinjaman</td><td style="text-align:right;">${p.toLocaleString('id-ID')}</td></tr>
        <tr><td>Bunga/Jasa Pinjaman</td><td style="text-align:right;">${b.toLocaleString('id-ID')}</td></tr>
        ${d > 0 ? `<tr><td>Denda Keterlambatan</td><td style="text-align:right;color:#DC2626;">${d.toLocaleString('id-ID')}</td></tr>` : ''}
      `;
    }

    const change = Math.max(0, this.cashTendered - this.nominalTransaksi);

    slipEl.innerHTML = `
      <div class="slip-header">
        <div class="slip-title">KOSPIN ENTERPRISE</div>
        <div class="slip-subtitle">Koperasi Simpan Pinjam Modern</div>
        <div class="slip-subtitle">Izin OJK / Kemenkop UKM No: 0882/KSP/2021</div>
        <div class="slip-subtitle">Jl. Pondok Pinang Raya No. 10 Jakarta</div>
      </div>

      <div class="slip-meta">
        <div class="slip-row"><span>No. Bukti:</span><span>${trxId}</span></div>
        <div class="slip-row"><span>Waktu:</span><span>${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')}</span></div>
        <div class="slip-row"><span>Kasir:</span><span>Siti Rahmawati</span></div>
        <div class="slip-row"><span>No. Anggota:</span><span>${memberId}</span></div>
        <div class="slip-row bold"><span>Nama:</span><span>${memberName}</span></div>
      </div>

      <div class="slip-items">
        <div style="font-weight: 700; text-transform: uppercase; margin-bottom: 6px; text-align: center; border-bottom: 1px dotted #9CA3AF; padding-bottom: 4px;">
          *** ${trxTitle} ***
        </div>
        <table style="width: 100%; font-size: 11px; line-height: 1.4;">
          ${detailRow}
        </table>
      </div>

      <div class="slip-total-block">
        <div class="slip-row bold" style="font-size: 13px;">
          <span>TOTAL:</span>
          <span>Rp ${this.nominalTransaksi.toLocaleString('id-ID')}</span>
        </div>
        <div class="slip-row" style="font-size: 11px; margin-top: 4px;">
          <span>Uang Diterima:</span>
          <span>Rp ${this.cashTendered.toLocaleString('id-ID')}</span>
        </div>
        <div class="slip-row bold" style="font-size: 12px; margin-top: 2px;">
          <span>KEMBALIAN:</span>
          <span>Rp ${change.toLocaleString('id-ID')}</span>
        </div>
      </div>

      <div class="slip-barcode">
        <div class="barcode-lines"></div>
      </div>
      <div class="slip-footer">
        <div>Terima kasih atas partisipasi Anda</div>
        <div>Simpanan Anda dijamin amanah & produktif</div>
        <div style="margin-top: 4px; font-weight: 600;">-- STRUK SAH TRANSAKSI KAS --</div>
      </div>
    `;
  },

  executeTransactionAndPrint: function() {
    if (!this.currentMember) {
      KspToast.danger("Pilih atau scan anggota terlebih dahulu!");
      return;
    }

    if (this.nominalTransaksi <= 0) {
      KspToast.warning("Nominal transaksi harus lebih dari Rp 0!");
      return;
    }

    if (this.cashTendered < this.nominalTransaksi && this.currentTab !== 'tarik') {
      KspToast.danger("Uang yang diterima kasir kurang!");
      return;
    }

    // Process Transaction in DataStore
    const trxType = this.currentTab === 'setor' ? `Setor Simpanan ${this.selectedJenisSimpanan}` :
                    this.currentTab === 'tarik' ? 'Tarik Tunai Simpanan' : 'Bayar Angsuran Pinjaman';

    const newTrx = {
      id_transaksi: `TRX-${Date.now().toString().slice(-8)}`,
      id_anggota: this.currentMember.id_anggota,
      nama_anggota: this.currentMember.nama_lengkap,
      jenis_transaksi: trxType,
      nominal: this.nominalTransaksi,
      uang_diterima: this.cashTendered,
      uang_kembali: Math.max(0, this.cashTendered - this.nominalTransaksi),
      kasir: "Siti Rahmawati",
      waktu: new Date().toISOString().replace('T', ' ').slice(0, 19),
      rincian: `${trxType} via Terminal Kasir POS`
    };

    KspDataStore.recordKasirTransaction(newTrx);
    KspApp.updateLiquidityBadge();

    KspToast.success(`Transaksi ${trxType} senilai ${KspDataStore.formatRupiah(this.nominalTransaksi)} BERHASIL!`);

    // Trigger Print Dialog
    setTimeout(() => {
      window.print();
    }, 400);
  },

  bindEvents: function() {
    // Select Member Change
    const memberSelect = document.getElementById('kasirMemberSelect');
    if (memberSelect) {
      memberSelect.addEventListener('change', (e) => this.onMemberSelect(e.target.value));
    }

    // Setor Nominal Input
    const inputSetor = document.getElementById('inputSetorNominal');
    if (inputSetor) {
      inputSetor.addEventListener('input', (e) => {
        const val = parseFloat(e.target.value.replace(/[^0-9]/g, '')) || 0;
        this.nominalTransaksi = val;
        this.calculateChange();
        this.updateSlipPreview();
      });
    }

    // Tarik Nominal Input
    const inputTarik = document.getElementById('inputTarikNominal');
    if (inputTarik) {
      inputTarik.addEventListener('input', (e) => {
        const val = parseFloat(e.target.value.replace(/[^0-9]/g, '')) || 0;
        this.nominalTransaksi = val;
        this.cashTendered = val; // Auto pass for cash withdrawal
        this.calculateChange();
        this.updateSlipPreview();
      });
    }

    // Cash Tendered Input
    const inputCash = document.getElementById('inputCashTendered');
    if (inputCash) {
      inputCash.addEventListener('input', (e) => {
        const val = parseFloat(e.target.value.replace(/[^0-9]/g, '')) || 0;
        this.cashTendered = val;
        this.calculateChange();
        this.updateSlipPreview();
      });
    }

    // Pos Simpanan Radios
    document.querySelectorAll("input[name='jenisSimpananPos']").forEach(radio => {
      radio.addEventListener('change', (e) => {
        this.selectedJenisSimpanan = e.target.value;
        this.updateSlipPreview();
      });
    });

    // Enter Hotkey to trigger Print / Submit when in cash input
    if (inputCash) {
      inputCash.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          this.executeTransactionAndPrint();
        }
      });
    }
  }
};
