# PRD UI/UX — WEBSITE KOPERASI SIMPAN PINJAM KOPIN

## 1. Informasi Dokumen

| Item     | Detail                                                           |
| -------- | ---------------------------------------------------------------- |
| Produk   | KOPIN — Koperasi Simpan Pinjam                                   |
| Dokumen  | UI/UX Product Requirements Document                              |
| Platform | Responsive Web                                                   |
| Target   | Karyawan dan Masyarakat Umum                                     |
| Role     | Admin dan Anggota                                                |
| Status   | Proyek Akademik                                                  |
| Tujuan   | Menentukan standar tampilan dan pengalaman pengguna sistem KOPIN |

---

# 2. Tujuan UI/UX

UI/UX KOPIN dirancang agar:

1. Mudah digunakan oleh anggota yang tidak terbiasa menggunakan sistem digital.
2. Informasi simpanan, pinjaman, dan angsuran mudah dipahami.
3. Admin dapat mengelola data secara cepat dan terstruktur.
4. Status transaksi mudah dibedakan secara visual.
5. Form pengajuan pinjaman tidak membingungkan.
6. Dokumen dapat diperiksa Admin dengan mudah.
7. Website dapat digunakan pada desktop, tablet, maupun smartphone.
8. Tampilan terlihat profesional dan terpercaya tanpa terlihat terlalu kompleks.

---

# 3. Konsep Visual

KOPIN menggunakan konsep:

> **Modern, Professional, Clean, Trustworthy, dan Friendly.**

Desain tidak menggunakan tampilan yang terlalu ramai. Fokus utama adalah keterbacaan informasi dan kemudahan melakukan tindakan.

## Karakter Visual

* Clean
* Modern
* Profesional
* Banyak whitespace
* Rounded corner secukupnya
* Card-based interface
* Ikon sederhana
* Typography mudah dibaca
* Hierarki informasi jelas

---

# 4. Design System

## 4.1 Warna

Warna utama menggunakan identitas koperasi yang memberikan kesan profesional dan terpercaya.

### Primary

**Hijau tua / Green**

Digunakan untuk:

* tombol utama
* logo/brand
* link penting
* status positif
* elemen navigasi aktif

### Secondary

**Hijau muda**

Digunakan untuk:

* background card tertentu
* highlight
* informasi tambahan

### Neutral

* Putih
* Abu-abu sangat muda
* Abu-abu
* Abu-abu gelap
* Hitam

Digunakan untuk background, teks, border, dan elemen pendukung.

### Status Color

Status harus memiliki perbedaan visual yang jelas:

| Status       | Tampilan      |
| ------------ | ------------- |
| PAID         | Hijau         |
| ACTIVE       | Hijau         |
| APPROVED     | Hijau         |
| UNPAID       | Abu/kuning    |
| UNDER REVIEW | Kuning/oranye |
| OVERDUE      | Merah         |
| REJECTED     | Merah         |
| INACTIVE     | Abu-abu       |
| PENDING      | Kuning/oranye |

Warna tidak menjadi satu-satunya indikator status. Status juga harus ditampilkan menggunakan teks/badge.

---

# 5. Typography

Font harus:

* mudah dibaca
* modern
* konsisten
* memiliki hierarchy yang jelas

Struktur:

```text id="e1u7k2"
Heading 1
↓
Heading 2
↓
Heading 3
↓
Body Text
↓
Caption / Helper Text
```

Ukuran typography harus responsif terhadap ukuran layar.

---

# 6. Layout Umum

## 6.1 Website Publik

Website publik menggunakan:

```text id="l8g4sa"
┌───────────────────────────────────────────────┐
│ LOGO KOPIN    Beranda  Tentang  Layanan      │
│                         Login   Daftar        │
├───────────────────────────────────────────────┤
│                                               │
│              CONTENT / HERO                   │
│                                               │
├───────────────────────────────────────────────┤
│                  SECTION                      │
├───────────────────────────────────────────────┤
│                  SECTION                      │
├───────────────────────────────────────────────┤
│                   FOOTER                      │
└───────────────────────────────────────────────┘
```

## 6.2 Dashboard

Dashboard menggunakan:

```text id="7f2s9k"
┌─────────────┬─────────────────────────────────┐
│             │ Header                          │
│   Sidebar   ├─────────────────────────────────┤
│             │                                 │
│             │ Main Content                    │
│             │                                 │
│             │                                 │
│             │                                 │
└─────────────┴─────────────────────────────────┘
```

Desktop menggunakan sidebar.

Mobile menggunakan:

* hamburger menu
* drawer/sidebar
* bottom navigation jika diperlukan
* card yang ditumpuk secara vertikal

---

# 7. Website Publik

## 7.1 Beranda

Halaman Beranda menjadi halaman pertama yang dilihat masyarakat.

Komponen:

### Navbar

* Logo KOPIN
* Beranda
* Tentang
* Layanan
* Pinjaman
* Simpanan
* Kontak
* Login
* Daftar

### Hero Section

Menampilkan:

* headline KOPIN
* penjelasan singkat
* CTA "Daftar Menjadi Anggota"
* CTA "Ajukan Pinjaman"

CTA pengajuan pinjaman dapat mengarahkan pengguna untuk login/register apabila belum menjadi anggota.

### Layanan

Card:

* Simpanan
* Pinjaman
* Pembayaran Angsuran

### Informasi Pinjaman

Menampilkan:

* Minimum Rp5.000.000
* Maksimum Rp50.000.000
* Tenor 6–24 bulan
* Informasi jasa berdasarkan tenor

### Cara Bergabung

```text id="u1k2pt"
1. Daftar
   ↓
2. Verifikasi Admin
   ↓
3. Bayar Simpanan
   ↓
4. Gunakan Layanan KOPIN
```

### Footer

* Logo
* Informasi KOPIN
* Kontak
* Alamat
* Navigasi
* Copyright

---

# 8. Halaman Tentang KOPIN

Menampilkan:

* Profil KOPIN
* Visi
* Misi
* Nilai koperasi
* Informasi layanan

Desain menggunakan kombinasi:

* text section
* image/illustration
* information card

---

# 9. Halaman Layanan

Menampilkan layanan dalam bentuk card:

```text id="5k1qaa"
┌─────────────┐
│   SIMPANAN  │
│             │
│ Informasi   │
│ simpanan    │
└─────────────┘

┌─────────────┐
│   PINJAMAN  │
│             │
│ Informasi   │
│ pinjaman    │
└─────────────┘

┌─────────────┐
│  ANGSURAN   │
│             │
│ Pembayaran  │
└─────────────┘
```

---

# 10. Halaman Informasi Pinjaman

Menampilkan:

* jenis pinjaman
* nominal minimum/maksimum
* pilihan tenor
* persentase jasa
* contoh simulasi
* syarat pengajuan
* dokumen yang diperlukan
* proses pengajuan

Contoh tabel:

|    Tenor | Total Jasa |
| -------: | ---------: |
|  6 bulan |         5% |
| 12 bulan |         7% |
| 18 bulan |       8,5% |
| 24 bulan |        10% |

---

# 11. Halaman Registrasi

Form registrasi dibuat bertahap atau dikelompokkan agar tidak terlihat terlalu panjang.

### Data Pribadi

* Nama lengkap
* NIK
* Tanggal lahir
* Alamat

### Kontak

* Nomor telepon
* Email

### Pekerjaan

* Pekerjaan
* Perusahaan

### Akun

* Password
* Konfirmasi password

Button:

**Daftar**

Setelah berhasil:

```text id="z9q4nc"
Registrasi Berhasil
        ↓
Menunggu Verifikasi Admin
        ↓
Email / Notifikasi
```

---

# 12. Halaman Login

Login sederhana.

```text id="k5f0wa"
        KOPIN
  Koperasi Simpan Pinjam

Email
[________________]

Password
[________________]

[       LOGIN       ]

Lupa Password?

Belum menjadi anggota?
[ Daftar Sekarang ]
```

---

# 13. Dashboard Anggota

Dashboard harus memberikan informasi penting dalam sekali lihat.

### Header

Menampilkan:

* nama anggota
* notifikasi
* profile menu

### Summary Cards

```text id="h0v6ts"
┌──────────────┐ ┌──────────────┐
│ Keanggotaan  │ │ Simpanan     │
│ ACTIVE       │ │ Rp155.000    │
└──────────────┘ └──────────────┘

┌──────────────┐ ┌──────────────┐
│ Pinjaman     │ │ Sisa         │
│ Rp10.000.000 │ │ Rp8.625.000  │
└──────────────┘ └──────────────┘
```

### Angsuran Berikutnya

Card khusus:

* nomor angsuran
* nominal
* jatuh tempo
* denda
* status
* tombol "Bayar Sekarang"

### Quick Action

* Bayar Simpanan
* Ajukan Pinjaman
* Bayar Angsuran

### Notifikasi Terbaru

Menampilkan beberapa notifikasi terakhir.

---

# 14. Halaman Profil Anggota

Layout menggunakan card/form.

Bagian:

### Data Pribadi

* Nama
* NIK
* Tanggal lahir
* Alamat

### Data Pekerjaan

* Pekerjaan
* Perusahaan

### Kontak

* Nomor telepon
* Email

Button:

**Edit Profil**

Field yang tidak boleh diubah langsung diberi informasi:

> Untuk mengubah data identitas tertentu, silakan hubungi Admin KOPIN.

---

# 15. Halaman Simpanan

Dashboard simpanan menggunakan card.

```text id="9t3f6x"
Simpanan Pokok
Rp55.000
Status: PAID

Simpanan Wajib
Rp100.000 / bulan
Status Bulan Ini: PAID
```

Di bawahnya terdapat:

**Riwayat Pembayaran**

Tabel:

| Tanggal | Jenis | Periode | Nominal | Status |
| ------- | ----- | ------- | ------: | ------ |

Mobile menggunakan card/list agar tabel tidak terlalu lebar.

---

# 16. Halaman Pembayaran Simpanan

Menampilkan:

* jenis simpanan
* periode
* nominal
* metode pembayaran

Pilihan:

```text id="4xq8zr"
○ Pembayaran Online
○ Pembayaran di Kantor
```

Jika online:

**Bayar Sekarang**

Jika kantor:

> Silakan melakukan pembayaran langsung kepada Admin KOPIN.

---

# 17. Halaman Ajukan Pinjaman

Form dibuat bertahap.

## Step 1 — Informasi Pinjaman

* Jumlah pinjaman
* Tenor
* Tujuan pinjaman
* Keterangan

## Step 2 — Informasi Rekening

* Nama bank
* Nomor rekening
* Nama pemilik rekening

## Step 3 — Dokumen

* KTP
* Bukti penghasilan
* Rekening bank
* Foto bukti usaha jika Modal Usaha
* Dokumen jaminan jika diminta

## Step 4 — Simulasi

Menampilkan:

```text id="2c5m7a"
Pokok Pinjaman       Rp10.000.000
Tenor                24 bulan
Total Jasa           10%
Nilai Jasa           Rp1.000.000
Total Kewajiban      Rp11.000.000
Angsuran / Bulan     Rp458.333
```

## Step 5 — Konfirmasi

Anggota melihat seluruh data sebelum menekan:

**Ajukan Pinjaman**

---

# 18. Validasi Form Pinjaman

Frontend memberikan validasi langsung.

Contoh:

```text id="r8n1ys"
Jumlah Pinjaman
[ Rp3.000.000 ]

Minimal pinjaman adalah Rp5.000.000.
```

Namun validasi juga dilakukan oleh backend.

Sistem harus mencegah pengajuan apabila:

* anggota belum aktif
* Simpanan Pokok belum dibayar
* Simpanan Wajib bulan berjalan belum dibayar
* terdapat tunggakan Simpanan Wajib
* nominal di luar batas
* tenor tidak tersedia
* dokumen wajib belum lengkap

---

# 19. Halaman Status Pinjaman

Status ditampilkan menggunakan visual progress.

```text id="s6m2qa"
SUBMITTED
    ●
    │
UNDER REVIEW
    ●
    │
APPROVED
    ●
    │
DISBURSED
    ●
    │
ACTIVE
```

Jika ditolak:

```text id="d7p4ne"
SUBMITTED
    ↓
UNDER REVIEW
    ↓
REJECTED

Alasan:
Dokumen bukti usaha tidak memenuhi
hasil verifikasi.
```

---

# 20. Halaman Pinjaman Saya

Menampilkan card pinjaman aktif:

* Nomor pinjaman
* Pokok
* Jasa
* Total kewajiban
* Total dibayar
* Sisa kewajiban
* Tenor
* Angsuran
* Status

Progress pembayaran dapat ditampilkan menggunakan progress bar.

---

# 21. Halaman Jadwal Angsuran

Tampilan tabel:

| No | Jatuh Tempo | Angsuran | Denda | Total | Status | Aksi |
| -: | ----------- | -------: | ----: | ----: | ------ | ---- |

Status menggunakan badge.

Untuk angsuran yang dapat dibayar:

**Bayar**

Untuk PAID:

**Lihat Bukti**

Untuk OVERDUE:

**Bayar + Denda**

---

# 22. Halaman Pembayaran Angsuran

Sebelum membayar, sistem menampilkan ringkasan:

```text id="n8j5bc"
Angsuran #3

Pokok Angsuran     Rp458.333
Denda              Rp10.000
----------------------------
Total Pembayaran   Rp468.333

[ BAYAR SEKARANG ]
```

Sistem harus menegaskan bahwa pembayaran dilakukan **secara penuh**.

---

# 23. Halaman Riwayat Transaksi Anggota

Menampilkan:

* tanggal
* nomor transaksi
* jenis transaksi
* keterangan
* nominal
* status

Filter:

* Semua
* Simpanan
* Pinjaman
* Angsuran
* Denda

Anggota hanya dapat melihat transaksi miliknya sendiri.

---

# 24. Halaman Notifikasi

Notifikasi menggunakan list.

Contoh:

```text id="q7v3xe"
● Angsuran Anda akan jatuh tempo
  2 hari lagi

● Pembayaran Simpanan berhasil
  10 Oktober 2026

● Pinjaman Anda telah disetujui
  8 Oktober 2026
```

Notifikasi yang belum dibaca memiliki indikator visual.

---

# 25. Dashboard Admin

Dashboard Admin memiliki fokus pada monitoring operasional.

### Summary Cards

```text id="b1k8su"
Total Anggota
1.250

Pending Verifikasi
12

Pengajuan Pinjaman
24

Pinjaman Aktif
184

Total Simpanan
RpXXX

Overdue
17
```

### Pengajuan Terbaru

Tabel:

| Anggota | Pinjaman | Tujuan | Tanggal | Status | Aksi |
| ------- | -------: | ------ | ------- | ------ | ---- |

### Overdue

Menampilkan daftar angsuran yang melewati jatuh tempo.

### Aktivitas Terbaru

Menampilkan aktivitas Admin.

---

# 26. Halaman Daftar Anggota Admin

Fitur:

* search
* filter status
* pagination
* detail

Kolom:

| Nama | NIK | Pekerjaan | Status | Registrasi | Aksi |
| ---- | --- | --------- | ------ | ---------- | ---- |

Action:

**Detail**

---

# 27. Halaman Verifikasi Registrasi

Menampilkan anggota dengan status:

**PENDING VERIFICATION**

Admin dapat:

* melihat data
* memeriksa informasi
* menyetujui
* menolak jika diperlukan

Tombol:

**Verify / Activate**

---

# 28. Halaman Detail Anggota Admin

Menggunakan tab:

```text id="u3m6rp"
Profil
Simpanan
Pinjaman
Angsuran
Denda
Riwayat Transaksi
```

Tujuannya agar Admin tidak harus membuka banyak halaman untuk memahami kondisi seorang anggota.

---

# 29. Halaman Pengajuan Pinjaman Admin

Filter:

* Semua
* Submitted
* Under Review
* Approved
* Rejected
* Disbursed
* Active

Tabel:

| Nomor | Anggota | Nominal | Tenor | Tujuan | Status | Aksi |
| ----- | ------- | ------: | ----: | ------ | ------ | ---- |

---

# 30. Halaman Detail Pengajuan Pinjaman

Halaman ini menjadi halaman utama untuk proses verifikasi.

Struktur:

```text id="y2h7km"
Informasi Anggota
        ↓
Informasi Pinjaman
        ↓
Simulasi Pinjaman
        ↓
Dokumen
        ↓
Bukti Usaha
        ↓
Jaminan jika diminta
        ↓
Catatan Verifikasi
        ↓
APPROVE / REJECT
```

Dokumen memiliki tombol:

**Lihat Dokumen**

Foto bukti usaha dapat ditampilkan menggunakan image preview.

Jika ada jaminan:

**Lihat Dokumen Jaminan**

---

# 31. UI Approval / Rejection

Pada status UNDER REVIEW:

```text id="x8q4la"
┌───────────────────────────────────────┐
│ Hasil Verifikasi                      │
│                                       │
│ Catatan:                              │
│ [...................................] │
│                                       │
│ [ REJECT ]             [ APPROVE ]    │
└───────────────────────────────────────┘
```

### Approve

Membuka confirmation modal:

> Apakah Anda yakin ingin menyetujui pengajuan pinjaman ini?

Button:

**Batal | Ya, Approve**

### Reject

Modal meminta:

> Masukkan alasan penolakan.

Field alasan wajib diisi.

Button:

**Batal | Reject Pengajuan**

---

# 32. UI Verifikasi Modal Usaha

Jika tujuan pinjaman adalah Modal Usaha, Admin dapat melihat:

```text id="z4m9pd"
Bukti Usaha

[ FOTO 1 ]
[ FOTO 2 ]

Status Verifikasi:
○ Sesuai
○ Tidak Sesuai

Catatan:
[.................................]
```

Admin menggunakan informasi tersebut sebagai bagian dari proses pemeriksaan sebelum menentukan keputusan pinjaman.

---

# 33. Halaman Simpanan Admin

Menampilkan:

* total simpanan
* Simpanan Pokok
* Simpanan Wajib
* pembayaran terbaru
* tunggakan

Tabel pembayaran:

| Anggota | Jenis | Periode | Nominal | Metode | Status | Aksi |
| ------- | ----- | ------- | ------: | ------ | ------ | ---- |

---

# 34. Halaman Angsuran Admin

Dashboard angsuran memiliki filter:

* Semua
* UNPAID
* OVERDUE
* PAID

Filter tambahan:

* tanggal
* anggota
* nomor pinjaman

Admin dapat membuka detail dan mencatat pembayaran kantor.

---

# 35. Pencatatan Pembayaran Kantor

Form Admin:

```text id="m6r2vx"
Anggota
[ Pilih Anggota ]

Jenis Pembayaran
[ Simpanan / Angsuran ]

Nomor Tagihan
[..............]

Nominal
[..............]

Tanggal Pembayaran
[..............]

[ SIMPAN PEMBAYARAN ]
```

Setelah berhasil, sistem mengubah status pembayaran sesuai aturan dan membuat bukti pembayaran.

---

# 36. Halaman Laporan

Dashboard laporan menggunakan card dan tabel.

Kategori:

```text id="p7w3ka"
Laporan
├── Anggota
├── Simpanan
├── Pinjaman
├── Angsuran
└── Transaksi
```

Fitur:

* filter tanggal
* filter status
* pencarian
* lihat detail
* export apabila diimplementasikan

---

# 37. Halaman Log Aktivitas

Tabel:

| Waktu | Admin | Aktivitas | Data | Keterangan |
| ----- | ----- | --------- | ---- | ---------- |

Contoh aktivitas:

* Verify member
* Approve loan
* Reject loan
* Record payment
* Record disbursement
* Edit member

---

# 38. Komponen UI Standar

Seluruh sistem menggunakan komponen yang konsisten:

### Button

* Primary
* Secondary
* Danger
* Outline
* Disabled

### Badge

* ACTIVE
* PENDING
* PAID
* UNPAID
* OVERDUE
* APPROVED
* REJECTED

### Form

* Label
* Input
* Select
* Date picker
* File upload
* Helper text
* Validation message

### Card

Digunakan untuk:

* statistik
* informasi pinjaman
* informasi simpanan
* angsuran
* notifikasi

### Table

Digunakan pada:

* anggota
* transaksi
* pinjaman
* angsuran
* laporan
* log

---

# 39. State UI

Setiap fitur penting harus memiliki beberapa kondisi.

## Loading

Menampilkan loading indicator atau skeleton.

## Empty

Contoh:

> Belum terdapat riwayat transaksi.

## Error

Contoh:

> Terjadi kesalahan saat mengambil data. Silakan coba lagi.

## Success

Contoh:

> Pembayaran berhasil dicatat.

## Confirmation

Untuk tindakan penting:

> Apakah Anda yakin ingin menyetujui pengajuan ini?

---

# 40. Responsive Design

Website harus responsive pada:

* Desktop
* Laptop
* Tablet
* Smartphone

### Desktop

Sidebar tetap terlihat pada dashboard.

### Tablet

Sidebar dapat dibuat collapsible.

### Mobile

Sidebar berubah menjadi drawer/hamburger menu.

Tabel yang terlalu lebar dapat:

* diubah menjadi card
* menggunakan horizontal scroll
* menyembunyikan kolom yang tidak penting

Form harus menggunakan layout satu kolom pada layar kecil.

Button penting harus mudah disentuh.

---

# 41. Accessibility

UI harus memperhatikan:

* kontras teks
* ukuran font
* label form yang jelas
* tombol memiliki nama yang jelas
* status tidak hanya dibedakan berdasarkan warna
* error message mudah dipahami
* navigasi keyboard untuk elemen penting
* responsive text

---

# 42. UX Prinsip Penggunaan

### Prinsip 1 — Informasi Penting Harus Terlihat

Anggota tidak perlu membuka banyak halaman untuk mengetahui:

* status simpanan
* pinjaman aktif
* angsuran berikutnya
* denda

### Prinsip 2 — Aksi Penting Harus Jelas

Contoh:

**Bayar Sekarang**

lebih jelas daripada hanya menggunakan ikon.

### Prinsip 3 — Tindakan Berisiko Menggunakan Konfirmasi

Contoh:

* Approve
* Reject
* Nonaktifkan anggota

harus memiliki confirmation.

### Prinsip 4 — Jangan Membebani Pengguna

Form hanya meminta data yang memang diperlukan.

Contohnya keterangan tujuan pinjaman dibuat opsional.

### Prinsip 5 — Status Harus Transparan

Anggota selalu dapat mengetahui proses pinjamannya:

```text
SUBMITTED
→ UNDER REVIEW
→ APPROVED
→ DISBURSED
→ ACTIVE
```

---

# 43. Mobile First untuk Fitur Anggota

Fitur anggota diprioritaskan agar nyaman digunakan melalui smartphone karena aktivitas seperti:

* melihat tagihan
* melihat notifikasi
* membayar
* melihat status pinjaman

dapat dilakukan dari perangkat mobile.

Dashboard Admin tetap dioptimalkan untuk desktop karena aktivitas Admin lebih banyak menggunakan tabel dan pengelolaan data.

---

# 44. Konsistensi Navigasi

Website publik dan dashboard harus memiliki identitas KOPIN yang konsisten.

Namun keduanya memiliki karakter berbeda:

### Public Website

**Marketing + Information**

### Member Dashboard

**Personal Financial Service**

### Admin Dashboard

**Operational Management**

Ketiga bagian tersebut tetap menggunakan design system yang sama.

---

# 45. Struktur UI Keseluruhan

```text id="x1q7nc"
KOPIN
│
├── PUBLIC WEBSITE
│   ├── Beranda
│   ├── Tentang
│   ├── Layanan
│   ├── Simpanan
│   ├── Pinjaman
│   ├── Syarat
│   ├── Kontak
│   ├── Login
│   └── Registrasi
│
├── ANGGOTA
│   ├── Dashboard
│   ├── Profil
│   ├── Simpanan
│   ├── Pinjaman
│   ├── Angsuran
│   ├── Riwayat Transaksi
│   └── Notifikasi
│
└── ADMIN
    ├── Dashboard
    ├── Anggota
    ├── Simpanan
    ├── Pinjaman
    ├── Angsuran
    ├── Laporan
    └── Log Aktivitas
```

---

# 46. Prioritas UI

## Prioritas Tinggi

* Login
* Registrasi
* Dashboard Anggota
* Dashboard Admin
* Pengajuan Pinjaman
* Verifikasi Pinjaman
* Pembayaran Simpanan
* Pembayaran Angsuran
* Jadwal Angsuran
* Detail Anggota

## Prioritas Menengah

* Notifikasi
* Riwayat Transaksi
* Laporan
* Log Aktivitas

## Prioritas Pendukung

* Animasi
* Micro-interaction
* Ilustrasi
* Visual enhancement

Fungsionalitas dan keterbacaan harus diprioritaskan dibandingkan animasi atau dekorasi.

---

# 47. Output yang Diharapkan

Implementasi UI/UX KOPIN harus menghasilkan:

1. Website publik yang profesional.
2. Dashboard Anggota yang sederhana dan mudah dipahami.
3. Dashboard Admin yang terstruktur.
4. Form pengajuan pinjaman yang bertahap.
5. Tampilan dokumen yang mudah diverifikasi.
6. Status transaksi yang mudah dibedakan.
7. Tampilan pembayaran yang jelas.
8. Tampilan responsive.
9. Komponen UI yang konsisten.
10. Pengalaman pengguna yang tidak membingungkan.

---

# 48. Hubungan PRD Sistem dan PRD UI/UX

Dokumen ini merupakan turunan dari PRD utama KOPIN.

**PRD Sistem** menentukan:

> Apa yang harus dilakukan sistem.

**PRD UI/UX** menentukan:

> Bagaimana fitur tersebut ditampilkan dan digunakan oleh pengguna.

Contoh:

```text id="r5n2qd"
PRD SISTEM
"Anggota dapat mengajukan pinjaman."

             ↓

PRD UI/UX
"Pengajuan pinjaman dibuat dalam
5 tahap: Informasi → Rekening →
Dokumen → Simulasi → Konfirmasi."
```

Dengan demikian, kedua dokumen harus digunakan bersama ketika sistem mulai diimplementasikan.
