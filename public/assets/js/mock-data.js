/* ==========================================================================
   KSP CORE ENTERPRISE SYSTEM - MOCK DATA ENGINE
   Database Lokal (localStorage) Koperasi Modern
   ========================================================================== */

const KSP_STORAGE_KEY = 'KSP_CORE_DATABASE_V1';

const INITIAL_MOCK_DATA = {
  // Financial Overview & Ratios
  financials: {
    kas_teller: 142500000,
    kas_brankas: 850000000,
    bank_mandiri: 2450000000,
    bank_bni: 1820000000,
    deposito_penempatan: 3500000000,
    total_pinjaman_aktif: 14850000000,
    total_simpanan_terhimpun: 18250000000,
    car_ratio: 24.85, // Capital Adequacy Ratio (%)
    npl_gross: 1.82,   // Non-Performing Loan (%)
    npl_net: 0.94,
    cadangan_ppa: 420000000,
    shu_berjalan: 890500000
  },

  // 10 Master Anggota
  anggota: [
    {
      id_anggota: "KSP-002914",
      nik: "3273051904880003",
      nama_lengkap: "Hendra Wijaya",
      alamat: "Jl. Pondok Pinang Raya No. 42, Kebayoran Lama, Jakarta Selatan",
      no_telepon: "0812-8942-8819",
      unit_usaha: "Toko Sembako Berkah Mandiri",
      grade_keanggotaan: "Platinum",
      status_keanggotaan: "Aktif",
      tanggal_gabung: "2021-03-14",
      email: "hendra.sembako@gmail.com",
      foto: "assets/img/avatar-teller.svg"
    },
    {
      id_anggota: "KSP-002915",
      nik: "3273052108920001",
      nama_lengkap: "Siti Rahmawati",
      alamat: "Komp. Deplu No. 12, Cipadu, Tangerang",
      no_telepon: "0813-1122-3344",
      unit_usaha: "Distributor Hijab & Busana Muslim",
      grade_keanggotaan: "Gold",
      status_keanggotaan: "Aktif",
      tanggal_gabung: "2022-01-10",
      email: "siti.rahmawati@gmail.com",
      foto: "assets/img/avatar-teller.svg"
    },
    {
      id_anggota: "KSP-002916",
      nik: "3171040506850007",
      nama_lengkap: "Bambang Soediro",
      alamat: "Jl. Fatmawati Kav. 18, Cilandak, Jakarta Selatan",
      no_telepon: "0811-9876-5432",
      unit_usaha: "Bengkel Motor & Spareparts Maju",
      grade_keanggotaan: "Platinum",
      status_keanggotaan: "Aktif",
      tanggal_gabung: "2020-07-22",
      email: "bambang.soediro@bengkelmaju.com",
      foto: "assets/img/avatar-teller.svg"
    },
    {
      id_anggota: "KSP-002917",
      nik: "3201081203950009",
      nama_lengkap: "Nurul Hidayah",
      alamat: "Jl. Margonda Raya No. 88, Beji, Depok",
      no_telepon: "0857-4455-6677",
      unit_usaha: "Catering Nasi Box Bu Nurul",
      grade_keanggotaan: "Silver",
      status_keanggotaan: "Aktif",
      tanggal_gabung: "2023-05-18",
      email: "nurul.catering@yahoo.com",
      foto: "assets/img/avatar-teller.svg"
    },
    {
      id_anggota: "KSP-002918",
      nik: "3674011409900004",
      nama_lengkap: "Agus Pratama",
      alamat: "Jl. Bintaro Utama Sektor 3A, Tangsel",
      no_telepon: "0819-2233-4455",
      unit_usaha: "Jasa Percetakan & Sablon Digital",
      grade_keanggotaan: "Gold",
      status_keanggotaan: "Luar Biasa",
      tanggal_gabung: "2023-11-01",
      email: "agus.pratama@printexpress.com",
      foto: "assets/img/avatar-teller.svg"
    },
    {
      id_anggota: "KSP-002919",
      nik: "3275022802870002",
      nama_lengkap: "Dewi Lestari",
      alamat: "Perum Galuh Mas Cluster Aster No. 5, Karawang",
      no_telepon: "0821-3344-5566",
      unit_usaha: "Apotek Sehat Sejahtera",
      grade_keanggotaan: "Platinum",
      status_keanggotaan: "Aktif",
      tanggal_gabung: "2019-11-15",
      email: "dewi.lestari@apoteksehat.id",
      foto: "assets/img/avatar-teller.svg"
    },
    {
      id_anggota: "KSP-002920",
      nik: "3174090101980005",
      nama_lengkap: "Rian Firmansyah",
      alamat: "Jl. Tebet Timur Dalam VI No. 9, Jakarta Selatan",
      no_telepon: "0878-9988-7766",
      unit_usaha: "Coffee Shop Kopi Seduh Santai",
      grade_keanggotaan: "Silver",
      status_keanggotaan: "Menunggu Verifikasi",
      tanggal_gabung: "2024-09-02",
      email: "rian.coffee@gmail.com",
      foto: "assets/img/avatar-teller.svg"
    }
  ],

  // 4 Pos Simpanan
  simpanan: [
    { id_rekening: "SIMP-PKK-001", id_anggota: "KSP-002914", jenis_simpanan: "Pokok", saldo_aktif: 1000000, bunga_tahunan: 0.0, status: "Aktif" },
    { id_rekening: "SIMP-WJB-001", id_anggota: "KSP-002914", jenis_simpanan: "Wajib", saldo_aktif: 14500000, bunga_tahunan: 4.5, status: "Aktif" },
    { id_rekening: "SIMP-SUK-001", id_anggota: "KSP-002914", jenis_simpanan: "Sukarela", saldo_aktif: 28750000, bunga_tahunan: 6.0, status: "Aktif" },
    { id_rekening: "SIMP-SJK-001", id_anggota: "KSP-002914", jenis_simpanan: "Berjangka", saldo_aktif: 50000000, bunga_tahunan: 8.5, jatuh_tempo: "2025-06-15", status: "Aktif" },

    { id_rekening: "SIMP-PKK-002", id_anggota: "KSP-002915", jenis_simpanan: "Pokok", saldo_aktif: 1000000, bunga_tahunan: 0.0, status: "Aktif" },
    { id_rekening: "SIMP-WJB-002", id_anggota: "KSP-002915", jenis_simpanan: "Wajib", saldo_aktif: 8200000, bunga_tahunan: 4.5, status: "Aktif" },
    { id_rekening: "SIMP-SUK-002", id_anggota: "KSP-002915", jenis_simpanan: "Sukarela", saldo_aktif: 12400000, bunga_tahunan: 6.0, status: "Aktif" },

    { id_rekening: "SIMP-PKK-003", id_anggota: "KSP-002916", jenis_simpanan: "Pokok", saldo_aktif: 1000000, bunga_tahunan: 0.0, status: "Aktif" },
    { id_rekening: "SIMP-WJB-003", id_anggota: "KSP-002916", jenis_simpanan: "Wajib", saldo_aktif: 24000000, bunga_tahunan: 4.5, status: "Aktif" },
    { id_rekening: "SIMP-SUK-003", id_anggota: "KSP-002916", jenis_simpanan: "Sukarela", saldo_aktif: 65000000, bunga_tahunan: 6.0, status: "Aktif" },
    { id_rekening: "SIMP-SJK-003", id_anggota: "KSP-002916", jenis_simpanan: "Berjangka", saldo_aktif: 100000000, bunga_tahunan: 8.5, jatuh_tempo: "2025-12-01", status: "Aktif" }
  ],

  // Portofolio Pinjaman & Kolektibilitas
  pinjaman: [
    {
      id_pinjaman: "PJ-2024-0891",
      id_anggota: "KSP-002914",
      nama_peminjam: "Hendra Wijaya",
      plafon_diajukan: 50000000,
      plafon_disetujui: 50000000,
      baki_debet: 32500000,
      tenor_bulan: 24,
      suku_bunga_flat: 0.90, // % per bulan
      angsuran_bulanan: 2533333,
      credit_score: 845,
      dsr_ratio: 22.40,
      kolektibilitas: "Kol 1 (Lancar)",
      status_approval: "Disetujui",
      tanggal_pengajuan: "2024-02-10",
      agunan: {
        jenis: "BPKB Mobil",
        detail: "Toyota Avanza 1.3 G M/T 2019 (B 1892 PNF)",
        nilai_pasar: 140000000,
        nilai_likuidasi: 110000000,
        ltv_persen: 35.71
      },
      jadwal: {
        angsuran_ke: 9,
        pokok: 2083333,
        bunga: 450000,
        denda: 0,
        jatuh_tempo: "2024-11-05"
      }
    },
    {
      id_pinjaman: "PJ-2024-0902",
      id_anggota: "KSP-002915",
      nama_peminjam: "Siti Rahmawati",
      plafon_diajukan: 30000000,
      plafon_disetujui: 30000000,
      baki_debet: 18000000,
      tenor_bulan: 18,
      suku_bunga_flat: 0.95,
      angsuran_bulanan: 1951666,
      credit_score: 780,
      dsr_ratio: 26.50,
      kolektibilitas: "Kol 1 (Lancar)",
      status_approval: "Disetujui",
      tanggal_pengajuan: "2024-04-12",
      agunan: {
        jenis: "BPKB Motor",
        detail: "Honda PCX 160 ABS 2022 (B 3910 SOT)",
        nilai_pasar: 32000000,
        nilai_likuidasi: 25000000,
        ltv_persen: 62.50
      },
      jadwal: {
        angsuran_ke: 6,
        pokok: 1666666,
        bunga: 285000,
        denda: 0,
        jatuh_tempo: "2024-10-28"
      }
    },
    {
      id_pinjaman: "PJ-2024-0744",
      id_anggota: "KSP-002918",
      nama_peminjam: "Agus Pratama",
      plafon_diajukan: 25000000,
      plafon_disetujui: 25000000,
      baki_debet: 21200000,
      tenor_bulan: 12,
      suku_bunga_flat: 1.0,
      angsuran_bulanan: 2333333,
      credit_score: 620,
      dsr_ratio: 38.20,
      kolektibilitas: "Kol 2 (Dalam Perhatian Khusus - DPK)",
      status_approval: "Disetujui",
      tanggal_pengajuan: "2024-06-01",
      agunan: {
        jenis: "Mesin Digital Printing",
        detail: "Roland VersaUV LEF2-200",
        nilai_pasar: 45000000,
        nilai_likuidasi: 30000000,
        ltv_persen: 55.55
      },
      jadwal: {
        angsuran_ke: 4,
        pokok: 2083333,
        bunga: 250000,
        denda: 75000,
        jatuh_tempo: "2024-10-05" // Terlambat lewat batas! EWS
      }
    },
    {
      id_pinjaman: "PJ-2024-1011",
      id_anggota: "KSP-002917",
      nama_peminjam: "Nurul Hidayah",
      plafon_diajukan: 40000000,
      plafon_disetujui: 0,
      baki_debet: 0,
      tenor_bulan: 24,
      suku_bunga_flat: 0.90,
      angsuran_bulanan: 2026666,
      credit_score: 715,
      dsr_ratio: 28.10,
      kolektibilitas: "Calon Debitur",
      status_approval: "Menunggu_Komite",
      tanggal_pengajuan: "2024-10-18",
      agunan: {
        jenis: "SHM Tanah & Bangunan",
        detail: "SHM No. 4912 Kel. Beji Luas 96m2",
        nilai_pasar: 250000000,
        nilai_likuidasi: 180000000,
        ltv_persen: 16.00
      }
    }
  ],

  // Riwayat Transaksi Kasir POS
  transaksi_kas: [
    {
      id_transaksi: "TRX-20241024-0082",
      id_anggota: "KSP-002914",
      nama_anggota: "Hendra Wijaya",
      jenis_transaksi: "Bayar Angsuran Pinjaman",
      nominal: 2533333,
      uang_diterima: 3000000,
      uang_kembali: 466667,
      kasir: "Siti Rahmawati",
      waktu: "2024-10-24 10:14:22",
      rincian: "Pokok Rp 2.083.333 + Jasa Rp 450.000 (Angs. Ke-8)"
    },
    {
      id_transaksi: "TRX-20241024-0081",
      id_anggota: "KSP-002916",
      nama_anggota: "Bambang Soediro",
      jenis_transaksi: "Setor Simpanan Sukarela",
      nominal: 5000000,
      uang_diterima: 5000000,
      uang_kembali: 0,
      kasir: "Siti Rahmawati",
      waktu: "2024-10-24 09:45:10",
      rincian: "Setoran Kas ke Rekening SiSuka"
    },
    {
      id_transaksi: "TRX-20241024-0080",
      id_anggota: "KSP-002915",
      nama_anggota: "Siti Rahmawati",
      jenis_transaksi: "Setor Simpanan Wajib",
      nominal: 100000,
      uang_diterima: 100000,
      uang_kembali: 0,
      kasir: "Siti Rahmawati",
      waktu: "2024-10-24 09:12:05",
      rincian: "Iuran Wajib Bulan Oktober 2024"
    }
  ],

  // Jurnal Umum Buku Besar (Double Entry Berimbang)
  jurnal_umum: [
    {
      id_jurnal: "JRN-202410-091",
      tanggal: "2024-10-24",
      no_bukti: "TRX-20241024-0082",
      keterangan: "Penerimaan Angsuran Pinjaman #PJ-2024-0891 Hendra Wijaya",
      items: [
        { kode_akun: "101.01", nama_akun: "Kas Teller", debet: 2533333, kredit: 0 },
        { kode_akun: "103.01", nama_akun: "Piutang Pinjaman Anggota", debet: 0, kredit: 2083333 },
        { kode_akun: "401.01", nama_akun: "Pendapatan Jasa Pinjaman", debet: 0, kredit: 450000 }
      ]
    },
    {
      id_jurnal: "JRN-202410-090",
      tanggal: "2024-10-24",
      no_bukti: "TRX-20241024-0081",
      keterangan: "Penerimaan Setoran Simpanan Sukarela Bambang Soediro",
      items: [
        { kode_akun: "101.01", nama_akun: "Kas Teller", debet: 5000000, kredit: 0 },
        { kode_akun: "201.03", nama_akun: "Simpanan Sukarela Anggota", debet: 0, kredit: 5000000 }
      ]
    },
    {
      id_jurnal: "JRN-202410-089",
      tanggal: "2024-10-23",
      no_bukti: "TRX-20241023-0044",
      keterangan: "Dropping Kas Teller dari Kas Brankas Pusat",
      items: [
        { kode_akun: "101.01", nama_akun: "Kas Teller", debet: 50000000, kredit: 0 },
        { kode_akun: "101.02", nama_akun: "Kas Brankas Pusat", debet: 0, kredit: 50000000 }
      ]
    }
  ]
};

// Data Store Helper Functions
const KspDataStore = {
  get: function() {
    const raw = localStorage.getItem(KSP_STORAGE_KEY);
    if (!raw) {
      this.init();
      return INITIAL_MOCK_DATA;
    }
    try {
      return JSON.parse(raw);
    } catch (e) {
      console.warn("Failed to parse localStorage data, resetting:", e);
      this.init();
      return INITIAL_MOCK_DATA;
    }
  },

  set: function(data) {
    localStorage.setItem(KSP_STORAGE_KEY, JSON.stringify(data));
  },

  init: function() {
    localStorage.setItem(KSP_STORAGE_KEY, JSON.stringify(INITIAL_MOCK_DATA));
  },

  formatRupiah: function(number) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(number);
  },

  // Add Member
  addAnggota: function(newAnggota) {
    const data = this.get();
    data.anggota.unshift(newAnggota);
    // Automatically create Simpanan Pokok & Wajib
    data.simpanan.push({
      id_rekening: `SIMP-PKK-${data.anggota.length}`,
      id_anggota: newAnggota.id_anggota,
      jenis_simpanan: "Pokok",
      saldo_aktif: newAnggota.setoran_pokok || 1000000,
      bunga_tahunan: 0.0,
      status: "Aktif"
    });
    data.simpanan.push({
      id_rekening: `SIMP-WJB-${data.anggota.length}`,
      id_anggota: newAnggota.id_anggota,
      jenis_simpanan: "Wajib",
      saldo_aktif: 0,
      bunga_tahunan: 4.5,
      status: "Aktif"
    });
    data.simpanan.push({
      id_rekening: `SIMP-SUK-${data.anggota.length}`,
      id_anggota: newAnggota.id_anggota,
      jenis_simpanan: "Sukarela",
      saldo_aktif: 0,
      bunga_tahunan: 6.0,
      status: "Aktif"
    });
    this.set(data);
    return newAnggota;
  },

  // Record Kasir Transaction
  recordKasirTransaction: function(trx) {
    const data = this.get();
    data.transaksi_kas.unshift(trx);
    
    // Update Kas Teller
    if (trx.jenis_transaksi === "Tarik Tunai Simpanan") {
      data.financials.kas_teller -= trx.nominal;
    } else {
      data.financials.kas_teller += trx.nominal;
    }

    // Auto Create General Journal
    const jrnId = `JRN-${Date.now().toString().slice(-6)}`;
    let journalItems = [];
    if (trx.jenis_transaksi === "Bayar Angsuran Pinjaman") {
      journalItems = [
        { kode_akun: "101.01", nama_akun: "Kas Teller", debet: trx.nominal, kredit: 0 },
        { kode_akun: "103.01", nama_akun: "Piutang Pinjaman Anggota", debet: 0, kredit: Math.round(trx.nominal * 0.82) },
        { kode_akun: "401.01", nama_akun: "Pendapatan Jasa Pinjaman", debet: 0, kredit: Math.round(trx.nominal * 0.18) }
      ];
    } else if (trx.jenis_transaksi.includes("Setor")) {
      journalItems = [
        { kode_akun: "101.01", nama_akun: "Kas Teller", debet: trx.nominal, kredit: 0 },
        { kode_akun: "201.01", nama_akun: "Simpanan Anggota", debet: 0, kredit: trx.nominal }
      ];
    } else {
      journalItems = [
        { kode_akun: "201.01", nama_akun: "Simpanan Anggota", debet: trx.nominal, kredit: 0 },
        { kode_akun: "101.01", nama_akun: "Kas Teller", debet: 0, kredit: trx.nominal }
      ];
    }

    data.jurnal_umum.unshift({
      id_jurnal: jrnId,
      tanggal: new Date().toISOString().split('T')[0],
      no_bukti: trx.id_transaksi,
      keterangan: `${trx.jenis_transaksi} - ${trx.nama_anggota}`,
      items: journalItems
    });

    this.set(data);
    return trx;
  },

  // Submit New Loan Application
  addPinjaman: function(loan) {
    const data = this.get();
    data.pinjaman.unshift(loan);
    this.set(data);
    return loan;
  },

  // Update Loan Status
  updateLoanStatus: function(id_pinjaman, status, plafonDisetujui) {
    const data = this.get();
    const loan = data.pinjaman.find(p => p.id_pinjaman === id_pinjaman);
    if (loan) {
      loan.status_approval = status;
      if (plafonDisetujui) loan.plafon_disetujui = plafonDisetujui;
      this.set(data);
    }
    return loan;
  }
};

// Auto initialize if needed
if (!localStorage.getItem(KSP_STORAGE_KEY)) {
  KspDataStore.init();
}
