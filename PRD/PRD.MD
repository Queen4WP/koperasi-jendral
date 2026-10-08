# PRD — WEBSITE KOPERASI SIMPAN PINJAM KOPIN

## 1. Informasi Produk

| Item            | Detail                                                                              |
| --------------- | ----------------------------------------------------------------------------------- |
| Nama Produk     | KOPIN                                                                               |
| Jenis           | Website Koperasi Simpan Pinjam                                                      |
| Platform        | Responsive Web                                                                      |
| Target Pengguna | Karyawan dan Masyarakat Umum                                                        |
| Status          | Proyek Akademik                                                                     |
| Model Akses     | Admin dan Anggota                                                                   |
| Fokus Utama     | Profil koperasi, keanggotaan, simpanan, pinjaman, angsuran, pembayaran, dan laporan |

---

# 2. Latar Belakang

Koperasi Simpan Pinjam KOPIN merupakan sistem berbasis web yang dirancang untuk membantu proses pelayanan koperasi kepada anggota, khususnya dalam pengelolaan keanggotaan, simpanan, pengajuan pinjaman, pembayaran angsuran, dan pencatatan transaksi.

Sistem dibuat agar anggota tidak harus selalu datang ke kantor untuk melihat informasi atau melakukan proses administrasi tertentu. Anggota dapat melakukan registrasi, melihat status simpanan, mengajukan pinjaman, melihat jadwal angsuran, dan melakukan pembayaran melalui website.

Walaupun KOPIN dibuat sebagai proyek akademik, sistem dirancang dengan alur yang menyerupai sistem koperasi nyata sehingga dapat dikembangkan lebih lanjut apabila diperlukan.

---

# 3. Tujuan Sistem

Sistem KOPIN memiliki tujuan:

1. Menyediakan informasi mengenai KOPIN kepada masyarakat.
2. Memudahkan masyarakat melakukan pendaftaran sebagai anggota.
3. Memudahkan Admin melakukan verifikasi dan pengelolaan anggota.
4. Mengelola Simpanan Pokok dan Simpanan Wajib.
5. Memudahkan anggota mengajukan pinjaman secara online.
6. Membantu Admin melakukan verifikasi pengajuan pinjaman.
7. Mengelola jadwal dan pembayaran angsuran.
8. Menghitung denda keterlambatan secara otomatis.
9. Menyediakan riwayat transaksi anggota.
10. Menyediakan laporan dan aktivitas administrasi bagi Admin.

---

# 4. Role dan Hak Akses

Sistem menggunakan dua role utama.

## 4.1 Admin

Admin memiliki akses operasional penuh terhadap sistem.

Admin dapat:

* Mengelola data anggota.
* Memverifikasi registrasi anggota.
* Mengaktifkan atau menonaktifkan anggota.
* Mengelola simpanan.
* Memverifikasi pembayaran simpanan.
* Melihat pengajuan pinjaman.
* Memverifikasi dokumen pinjaman.
* Menyetujui atau menolak pinjaman.
* Mencatat pencairan pinjaman.
* Mengelola angsuran.
* Mencatat pembayaran di kantor.
* Melihat dan mengelola denda.
* Melihat laporan.
* Melihat riwayat transaksi.
* Melihat log aktivitas.

## 4.2 Anggota

Anggota dapat:

* Mengelola profil sendiri.
* Melihat status keanggotaan.
* Melihat Simpanan Pokok.
* Membayar Simpanan Wajib.
* Melihat riwayat pembayaran.
* Mengajukan pinjaman.
* Mengunggah dokumen pinjaman.
* Melihat status pengajuan.
* Melihat pinjaman aktif.
* Melihat jadwal angsuran.
* Membayar angsuran.
* Melihat denda.
* Melihat riwayat transaksi sendiri.
* Melihat notifikasi.

---

# 5. Website Publik

Website dapat diakses masyarakat sebelum melakukan login.

Halaman publik meliputi:

* Beranda
* Tentang KOPIN
* Layanan
* Informasi Simpanan
* Informasi Pinjaman
* Syarat dan Ketentuan
* Cara Bergabung
* Kontak
* Login
* Registrasi

Website publik berfungsi sebagai media informasi sekaligus pintu masuk bagi masyarakat yang ingin menjadi anggota.

---

# 6. Registrasi Anggota

Karyawan dan masyarakat umum dapat menjadi anggota KOPIN.

Registrasi dapat dilakukan melalui dua cara:

### A. Registrasi Online

Anggota mengisi formulir melalui website.

### B. Registrasi di Kantor

Masyarakat datang langsung ke kantor KOPIN dan dibantu oleh Admin untuk memasukkan data ke dalam sistem.

Kedua metode menggunakan database anggota yang sama.

Sistem dapat menyimpan sumber pendaftaran:

* ONLINE
* OFFICE

## Data Registrasi

* Nama lengkap
* NIK
* Tanggal lahir
* Alamat
* Nomor telepon
* Email
* Password
* Pekerjaan
* Perusahaan

## Status Registrasi

```text
PENDING VERIFICATION
        ↓
      ACTIVE
        ↓
     INACTIVE
```

Anggota yang melakukan registrasi belum dapat menggunakan seluruh layanan sebelum diverifikasi Admin.

---

# 7. Simpanan

KOPIN menggunakan dua jenis simpanan:

## 7.1 Simpanan Pokok

* Nominal: **Rp55.000**
* Dibayarkan satu kali.
* Menjadi salah satu syarat untuk mengajukan pinjaman.

## 7.2 Simpanan Wajib

* Nominal: **Rp100.000 per bulan**
* Dibayarkan setiap bulan.
* Menjadi salah satu syarat kelayakan pengajuan pinjaman.

## 7.3 Simpanan Sukarela

Tidak digunakan pada versi pertama sistem.

---

# 8. Pembayaran Simpanan

Pembayaran simpanan dapat dilakukan melalui:

### Online

Alur:

```text
Anggota
   ↓
Pilih Tagihan
   ↓
Pembayaran Online
   ↓
Payment Gateway
   ↓
Pembayaran Berhasil
   ↓
Status PAID
   ↓
Bukti Pembayaran
```

### Kantor KOPIN

Alur:

```text
Anggota datang ke kantor
        ↓
Membayar kepada Admin
        ↓
Admin mencatat pembayaran
        ↓
Status PAID
        ↓
Bukti Pembayaran
```

Anggota tidak dapat mengubah status pembayaran secara manual.

---

# 9. Syarat Pengajuan Pinjaman

Anggota harus memenuhi persyaratan simpanan sebelum dapat mengajukan pinjaman.

Syarat:

1. Simpanan Pokok sudah dibayar.
2. Simpanan Wajib bulan berjalan sudah dibayar.
3. Tidak memiliki tunggakan Simpanan Wajib.

Validasi dilakukan pada backend sehingga tidak hanya bergantung pada tampilan frontend.

Contoh pesan:

> Anda belum dapat mengajukan pinjaman karena Simpanan Pokok belum dibayar.

atau:

> Anda belum dapat mengajukan pinjaman karena masih memiliki tunggakan Simpanan Wajib.

---

# 10. Produk Pinjaman

KOPIN memiliki satu produk pinjaman:

## Pinjaman Reguler

| Ketentuan          |               Nilai |
| ------------------ | ------------------: |
| Minimum Pinjaman   |         Rp5.000.000 |
| Maksimum Pinjaman  |        Rp50.000.000 |
| Tenor              | 6, 12, 18, 24 bulan |
| Metode Perhitungan |                Flat |
| Jasa               |   Berdasarkan tenor |

Batas Rp5.000.000–Rp50.000.000 merupakan **rentang pengajuan**, bukan jaminan bahwa seluruh pengajuan akan disetujui.

---

# 11. Jasa Pinjaman

Jasa dihitung sebagai persentase **total untuk seluruh periode pinjaman**, bukan persentase per bulan.

|    Tenor | Total Jasa |
| -------: | ---------: |
|  6 bulan |         5% |
| 12 bulan |         7% |
| 18 bulan |       8,5% |
| 24 bulan |        10% |

### Rumus

```text
Total Jasa = Pokok Pinjaman × Persentase Jasa

Total Kewajiban = Pokok Pinjaman + Total Jasa

Angsuran = Total Kewajiban ÷ Tenor
```

Contoh:

Pinjaman Rp10.000.000 selama 24 bulan.

```text
Jasa = Rp10.000.000 × 10%
     = Rp1.000.000

Total kewajiban
     = Rp11.000.000

Angsuran
     = Rp11.000.000 ÷ 24
     = Rp458.333,33
```

Sistem harus menangani pembulatan sehingga total pembayaran akhir tetap sama dengan total kewajiban yang ditetapkan.

---

# 12. Pengajuan Pinjaman

Form pengajuan pinjaman terdiri dari:

* Jumlah pinjaman
* Tenor
* Kategori tujuan pinjaman
* Keterangan tujuan
* Rekening bank pencairan
* Dokumen pendukung

## Kategori Tujuan

* Modal Usaha
* Pendidikan
* Kebutuhan Rumah Tangga
* Renovasi Rumah
* Keperluan Lainnya

Kategori tujuan **wajib dipilih**.

Keterangan tujuan bersifat **opsional**.

Contoh:

```text
Kategori:
Modal Usaha

Keterangan:
Tambahan modal untuk membeli bahan baku usaha.
```

---

# 13. Dokumen Pengajuan Pinjaman

## Dokumen Wajib

Anggota wajib mengunggah:

1. KTP
2. Bukti penghasilan
3. Rekening bank

Bukti penghasilan dapat berupa dokumen yang relevan dengan kondisi anggota, seperti slip gaji atau bukti pendapatan.

## Dokumen Tambahan untuk Modal Usaha

Jika kategori pinjaman adalah **Modal Usaha**, anggota wajib mengunggah:

**Foto Bukti Usaha / Foto Lokasi Usaha**

Contohnya:

* Foto toko
* Foto tempat usaha
* Foto gerobak
* Foto perlengkapan usaha
* Foto produk atau aktivitas usaha

Tujuannya adalah memberikan bukti pendukung bahwa pengajuan memang berkaitan dengan kegiatan usaha.

## Jaminan / Agunan

KOPIN dapat meminta dokumen jaminan apabila diperlukan dalam proses verifikasi.

Contoh:

* BPKB kendaraan
* Sertifikat tanah/bangunan
* Dokumen aset lainnya

Dokumen jaminan bersifat **kondisional**, bukan wajib untuk setiap pengajuan.

Untuk proyek akademik, sistem tidak mencakup proses appraisal, penilaian harga agunan, maupun proses hukum eksekusi jaminan.

---

# 14. Proses Verifikasi Pinjaman

Status pengajuan:

```text
DRAFT
  ↓
SUBMITTED
  ↓
UNDER REVIEW
  ↓
APPROVED / REJECTED
  ↓
DISBURSED
  ↓
ACTIVE
  ↓
PAID OFF
```

Jika ditolak, Admin wajib memberikan alasan penolakan.

## Estimasi Verifikasi

Secara aturan sistem:

> Pengajuan pinjaman membutuhkan waktu maksimal 3 hari kerja untuk proses verifikasi dan keputusan Admin.

Namun karena sistem merupakan **proyek akademik**, tidak perlu membuat sistem benar-benar menunggu tiga hari.

Admin dapat langsung melakukan simulasi keputusan menggunakan tombol:

```text
[ APPROVE ]    [ REJECT ]
```

Dengan demikian:

**3 hari = aturan/prosedur bisnis**

sedangkan

**Approve/Reject langsung = mekanisme simulasi untuk demonstrasi akademik.**

---

# 15. Halaman Verifikasi Admin

Admin dapat melihat:

### Informasi Anggota

* Nama
* NIK
* Pekerjaan
* Perusahaan
* Nomor telepon

### Informasi Pinjaman

* Nominal
* Tenor
* Tujuan
* Keterangan
* Jasa
* Total kewajiban
* Angsuran

### Dokumen

* KTP
* Bukti penghasilan
* Rekening bank
* Foto bukti usaha jika Modal Usaha
* Dokumen jaminan jika diminta

Admin dapat membuka dan memeriksa dokumen sebelum mengambil keputusan.

Tersedia:

```text
Catatan Verifikasi:
[................................]

[ APPROVE ]    [ REJECT ]
```

Jika memilih Reject, alasan penolakan wajib diisi.

---

# 16. Pencairan Pinjaman

Setelah pengajuan disetujui:

```text
APPROVED
   ↓
Pencairan
   ↓
DISBURSED
   ↓
Sistem membuat jadwal angsuran
   ↓
ACTIVE
```

Sistem mencatat:

* Nomor pinjaman
* Jumlah pencairan
* Tanggal pencairan
* Rekening tujuan
* Status pencairan

Tanggal pencairan menjadi dasar penentuan tanggal jatuh tempo angsuran.

---

# 17. Jadwal Angsuran

Jatuh tempo mengikuti tanggal pencairan.

Contoh:

```text
Pencairan       : 10 Januari
Angsuran 1      : 10 Februari
Angsuran 2      : 10 Maret
Angsuran 3      : 10 April
dst.
```

Sistem menghasilkan jadwal angsuran secara otomatis berdasarkan tenor.

---

# 18. Pembayaran Angsuran

KOPIN menggunakan sistem **full payment**.

Anggota tidak dapat membayar sebagian dari satu tagihan angsuran.

Contoh:

```text
Angsuran        Rp900.000
Denda           Rp20.000
-------------------------
Total           Rp920.000
```

Anggota harus membayar **Rp920.000 secara penuh**.

---

# 19. Status Angsuran

### UNPAID

Angsuran belum dibayar dan belum melewati jatuh tempo.

### OVERDUE

Angsuran belum dibayar dan telah melewati jatuh tempo.

### PAID

Angsuran telah dibayar secara penuh, termasuk denda apabila ada.

---

# 20. Denda Keterlambatan

Denda dihitung berdasarkan kelipatan 7 hari keterlambatan.

| Keterlambatan |    Denda |
| ------------: | -------: |
|      1–6 hari |      Rp0 |
|     7–13 hari | Rp10.000 |
|    14–20 hari | Rp20.000 |
|    21–27 hari | Rp30.000 |
|    28–30 hari | Rp40.000 |
|    31–37 hari | Rp50.000 |

Konsep perhitungan:

```text
Denda = floor(hari keterlambatan ÷ 7) × Rp10.000
```

Denda akan terus bertambah setiap kelipatan tujuh hari sampai angsuran dibayar.

---

# 21. Keterlambatan Lebih dari Satu Bulan

Jika angsuran belum dibayar **lebih dari satu bulan setelah tanggal jatuh tempo**, sistem mengirimkan notifikasi setiap hari sampai pembayaran dilakukan.

Notifikasi dikirim melalui:

* Website
* Email

Versi pertama tidak menggunakan status khusus seperti "macet" atau "bad debt" dan tidak membuat sistem penagihan manual yang kompleks.

---

# 22. Bukti Pembayaran

Setiap pembayaran yang berhasil menghasilkan bukti pembayaran.

Informasi:

* Nomor transaksi
* Nama anggota
* Nomor pinjaman
* Nomor angsuran
* Periode
* Nominal angsuran
* Denda jika ada
* Total pembayaran
* Tanggal pembayaran
* Metode pembayaran
* Status pembayaran

---

# 23. Metode Pembayaran Angsuran

Tersedia dua metode:

### Online

```text
Anggota
 ↓
Pilih Angsuran
 ↓
Bayar
 ↓
Payment Gateway
 ↓
Berhasil
 ↓
PAID
 ↓
Bukti Pembayaran
```

### Kantor

```text
Anggota datang
 ↓
Membayar kepada Admin
 ↓
Admin mencatat pembayaran
 ↓
PAID
 ↓
Bukti Pembayaran
```

Anggota tidak dapat mengubah status pembayaran sendiri.

---

# 24. Dashboard Anggota

Dashboard menampilkan informasi utama:

* Status keanggotaan
* Status Simpanan Pokok
* Status Simpanan Wajib bulan berjalan
* Pinjaman aktif
* Sisa kewajiban pinjaman
* Angsuran berikutnya
* Tanggal jatuh tempo
* Denda
* Tunggakan

### Quick Action

* Bayar Simpanan
* Bayar Angsuran
* Ajukan Pinjaman

---

# 25. Profil Anggota

Anggota dapat melihat:

* Nama lengkap
* NIK
* Tanggal lahir
* Alamat
* Nomor telepon
* Email
* Pekerjaan
* Perusahaan

Data identitas penting seperti NIK tidak dapat diubah secara bebas setelah akun aktif. Perubahan dapat dilakukan melalui proses yang melibatkan Admin.

---

# 26. Menu Simpanan Anggota

```text
Simpanan
├── Simpanan Pokok
├── Simpanan Wajib
└── Riwayat Pembayaran
```

Anggota dapat melihat:

* Nominal
* Status
* Periode
* Tanggal pembayaran
* Metode pembayaran
* Bukti pembayaran
* Tunggakan

---

# 27. Menu Pinjaman Anggota

```text
Pinjaman
├── Ajukan Pinjaman
├── Pinjaman Saya
└── Riwayat Pinjaman
```

## Pinjaman Saya

Menampilkan:

* Nomor pinjaman
* Pokok pinjaman
* Tenor
* Persentase jasa
* Total kewajiban
* Angsuran
* Total yang sudah dibayar
* Sisa kewajiban
* Status

---

# 28. Menu Angsuran Anggota

```text
Angsuran
├── Jadwal Angsuran
├── Bayar Angsuran
└── Riwayat Pembayaran
```

Jadwal menampilkan:

| Angsuran | Jatuh Tempo |   Nominal |    Denda | Status  |
| -------: | ----------- | --------: | -------: | ------- |
|        1 | 10/02/2027  | Rp458.333 |      Rp0 | PAID    |
|        2 | 10/03/2027  | Rp458.333 |      Rp0 | UNPAID  |
|        3 | 10/04/2027  | Rp458.333 | Rp10.000 | OVERDUE |

---

# 29. Riwayat Transaksi Anggota

Anggota hanya dapat melihat transaksi miliknya sendiri.

Contoh:

| Tanggal | Jenis    | Keterangan     |     Masuk |       Keluar |
| ------- | -------- | -------------- | --------: | -----------: |
| 10/01   | Simpanan | Simpanan Pokok |  Rp55.000 |            - |
| 10/01   | Pinjaman | Pencairan      |         - | Rp10.000.000 |
| 10/02   | Simpanan | Simpanan Wajib | Rp100.000 |            - |
| 10/02   | Angsuran | Angsuran #1    | Rp458.333 |            - |

---

# 30. Notifikasi

Notifikasi tersedia melalui **website dan email**.

Jenis notifikasi:

* Registrasi berhasil
* Akun berhasil diverifikasi
* Simpanan jatuh tempo
* Pengajuan pinjaman diterima
* Pengajuan sedang diverifikasi
* Pinjaman disetujui
* Pinjaman ditolak
* Pinjaman dicairkan
* Pengingat angsuran
* Angsuran jatuh tempo
* Angsuran terlambat
* Denda ditambahkan
* Keterlambatan lebih dari satu bulan

Untuk tunggakan lebih dari satu bulan, notifikasi dikirim setiap hari sampai pembayaran dilakukan.

---

# 31. Dashboard Admin

Dashboard Admin menjadi pusat pengelolaan operasional KOPIN.

## Informasi Ringkasan

* Total anggota
* Anggota aktif
* Registrasi menunggu verifikasi
* Total pengajuan pinjaman
* Pengajuan dalam proses
* Pinjaman aktif
* Total simpanan
* Angsuran belum dibayar
* Angsuran overdue
* Angsuran overdue lebih dari satu bulan

## Informasi Tambahan

* Pengajuan terbaru
* Aktivitas terbaru
* Ringkasan transaksi
* Pembayaran terbaru

---

# 32. Manajemen Anggota

Admin dapat:

* Melihat daftar anggota
* Mencari anggota
* Memfilter anggota
* Memverifikasi registrasi
* Mengaktifkan anggota
* Menonaktifkan anggota
* Mengedit data anggota
* Melihat detail anggota

---

# 33. Detail Anggota Admin

Halaman Detail Anggota terdiri dari:

```text
Detail Anggota
│
├── Data Profil
├── Simpanan
├── Pinjaman
├── Angsuran
├── Denda
└── Riwayat Transaksi
```

Admin dapat melihat keseluruhan aktivitas finansial anggota yang berkaitan dengan KOPIN.

---

# 34. Riwayat Transaksi Anggota di Admin

Istilah yang digunakan adalah:

**Riwayat Transaksi Anggota**

bukan "pemasukan dan pengeluaran anggota", karena data tersebut menggambarkan transaksi antara anggota dan KOPIN, bukan kondisi keuangan pribadi anggota.

### Dana masuk ke KOPIN dari anggota:

* Simpanan Pokok
* Simpanan Wajib
* Pembayaran angsuran
* Denda

### Dana keluar dari KOPIN kepada anggota:

* Pencairan pinjaman

---

# 35. Manajemen Simpanan Admin

Admin dapat:

* Melihat Simpanan Pokok
* Melihat Simpanan Wajib
* Melihat pembayaran
* Mencatat pembayaran kantor
* Memverifikasi pembayaran
* Melihat tunggakan
* Melihat bukti pembayaran
* Melihat riwayat transaksi simpanan

---

# 36. Manajemen Pinjaman Admin

Admin dapat:

* Melihat pengajuan pinjaman
* Melihat detail pengajuan
* Memeriksa dokumen
* Memeriksa bukti usaha
* Memeriksa dokumen jaminan jika ada
* Memberikan catatan verifikasi
* Approve pinjaman
* Reject pinjaman
* Memberikan alasan penolakan
* Mencatat pencairan
* Melihat pinjaman aktif
* Melihat riwayat pinjaman

---

# 37. Manajemen Angsuran Admin

Admin dapat:

* Melihat seluruh jadwal angsuran
* Melihat angsuran UNPAID
* Melihat angsuran OVERDUE
* Melihat angsuran PAID
* Melihat denda
* Mencatat pembayaran di kantor
* Melihat riwayat pembayaran

---

# 38. Laporan

Admin memiliki akses terhadap:

### Laporan Anggota

* Daftar anggota
* Anggota aktif
* Anggota belum diverifikasi
* Anggota nonaktif

### Laporan Simpanan

* Simpanan Pokok
* Simpanan Wajib
* Total simpanan
* Tunggakan

### Laporan Pinjaman

* Pengajuan pinjaman
* Pinjaman disetujui
* Pinjaman ditolak
* Pinjaman aktif
* Pinjaman selesai
* Total pencairan

### Laporan Angsuran

* Angsuran dibayar
* Angsuran belum dibayar
* Angsuran overdue
* Denda

### Laporan Transaksi

Menampilkan seluruh transaksi yang terjadi dalam sistem KOPIN.

---

# 39. Log Aktivitas

Sistem mencatat aktivitas penting Admin.

Contoh:

* Verifikasi anggota
* Mengaktifkan anggota
* Menonaktifkan anggota
* Approve pinjaman
* Reject pinjaman
* Mencatat pembayaran
* Mencatat pencairan
* Perubahan data penting

Contoh:

```text
Tanggal       : 10/01/2027 10:15
Admin         : Admin KOPIN
Aktivitas     : Approve Pinjaman
Nomor Pinjaman: PLN-00001
Keterangan    : Pengajuan pinjaman disetujui
```

---

# 40. Struktur Menu Admin

```text
ADMIN
│
├── Dashboard
│
├── Anggota
│   ├── Daftar Anggota
│   ├── Verifikasi Registrasi
│   └── Detail Anggota
│       ├── Data Profil
│       ├── Simpanan
│       ├── Pinjaman
│       ├── Angsuran
│       ├── Denda
│       └── Riwayat Transaksi
│
├── Simpanan
│
├── Pinjaman
│
├── Angsuran
│
├── Laporan
│
└── Log Aktivitas
```

---

# 41. Struktur Menu Anggota

```text
ANGGOTA
│
├── Dashboard
├── Profil
│
├── Simpanan
│   ├── Simpanan Pokok
│   ├── Simpanan Wajib
│   └── Riwayat Pembayaran
│
├── Pinjaman
│   ├── Ajukan Pinjaman
│   ├── Pinjaman Saya
│   └── Riwayat Pinjaman
│
├── Angsuran
│   ├── Jadwal Angsuran
│   ├── Bayar Angsuran
│   └── Riwayat Pembayaran
│
├── Riwayat Transaksi
│
└── Notifikasi
```

---

# 42. Alur Utama Sistem

## 42.1 Alur Menjadi Anggota

```text
Masyarakat
    ↓
Registrasi Online / Datang ke Kantor
    ↓
Data Anggota Dicatat
    ↓
PENDING VERIFICATION
    ↓
Admin Verifikasi
    ↓
ACTIVE
    ↓
Anggota Membayar Simpanan
```

## 42.2 Alur Pengajuan Pinjaman

```text
Anggota Login
    ↓
Sistem Mengecek Kelayakan
    ↓
Syarat Terpenuhi
    ↓
Isi Pengajuan
    ↓
Upload Dokumen
    ↓
SUBMITTED
    ↓
UNDER REVIEW
    ↓
Admin Memeriksa
    ↓
APPROVE / REJECT
    ↓
Jika Approve
    ↓
DISBURSED
    ↓
Jadwal Angsuran Dibuat
    ↓
ACTIVE
```

## 42.3 Alur Pembayaran Angsuran

```text
Anggota
    ↓
Melihat Jadwal
    ↓
Pilih Angsuran
    ↓
Sistem Menghitung Denda
    ↓
Pembayaran Full
    ↓
Online / Kantor
    ↓
Pembayaran Berhasil
    ↓
PAID
    ↓
Bukti Pembayaran
```

---

# 43. Aturan Bisnis Utama

1. Hanya anggota aktif yang dapat menggunakan layanan pinjaman.
2. Simpanan Pokok harus sudah dibayar sebelum pengajuan pinjaman.
3. Simpanan Wajib bulan berjalan harus sudah dibayar.
4. Anggota tidak boleh memiliki tunggakan Simpanan Wajib.
5. Pinjaman minimum Rp5.000.000.
6. Pinjaman maksimum Rp50.000.000.
7. Tenor yang tersedia adalah 6, 12, 18, dan 24 bulan.
8. Perhitungan jasa menggunakan metode flat.
9. Jasa merupakan persentase total selama masa pinjaman.
10. Kategori tujuan pinjaman wajib dipilih.
11. Keterangan tujuan pinjaman bersifat opsional.
12. KTP, bukti penghasilan, dan rekening bank wajib diunggah.
13. Modal Usaha membutuhkan foto bukti usaha.
14. Dokumen jaminan dapat diminta apabila diperlukan dalam proses verifikasi.
15. Admin bertanggung jawab terhadap proses approval/rejection.
16. Reject pinjaman harus disertai alasan.
17. Estimasi proses verifikasi pinjaman adalah maksimal 3 hari kerja.
18. Untuk proyek akademik, Admin dapat langsung melakukan simulasi Approve/Reject.
19. Jatuh tempo angsuran mengikuti tanggal pencairan.
20. Pembayaran angsuran harus dilakukan secara penuh.
21. Denda bertambah setiap kelipatan 7 hari keterlambatan.
22. Anggota tidak dapat mengubah status pembayaran secara manual.
23. Pembayaran kantor dicatat oleh Admin.
24. Tunggakan lebih dari satu bulan menghasilkan notifikasi harian.
25. Anggota hanya dapat melihat data dan transaksi miliknya sendiri.
26. Admin dapat melihat seluruh data anggota dan transaksi.
27. Setiap aktivitas administrasi penting dicatat dalam Log Aktivitas.

---

# 44. Batasan Versi Akademik

Agar sistem tetap realistis tetapi tidak terlalu kompleks, versi akademik KOPIN tidak mencakup:

* Sistem appraisal jaminan secara otomatis.
* Penilaian harga aset/agunan.
* Proses hukum eksekusi jaminan.
* Sistem penagihan lapangan.
* Status kredit macet yang kompleks.
* Integrasi perbankan nyata.
* Integrasi payment gateway produksi apabila belum tersedia.
* Sistem akuntansi koperasi secara menyeluruh.
* Banyak level jabatan organisasi koperasi.
* Approval otomatis berbasis skor kredit.

Fitur pembayaran dan pencairan dapat menggunakan **simulasi payment gateway** untuk kebutuhan demonstrasi apabila integrasi layanan pembayaran nyata belum dilakukan.

---

# 45. Prinsip Keamanan

Sistem harus menerapkan:

* Authentication untuk anggota dan Admin.
* Password disimpan dalam bentuk hash.
* Authorization berdasarkan role.
* Anggota hanya dapat mengakses datanya sendiri.
* Admin memiliki akses operasional.
* Dokumen pribadi tidak boleh dapat diakses secara bebas melalui URL publik.
* Validasi file upload.
* Validasi ukuran dan tipe file.
* Validasi input pada frontend dan backend.
* Status pembayaran hanya dapat diubah melalui proses sistem/Admin yang berwenang.
* Aktivitas penting Admin dicatat dalam Log Aktivitas.

---

# 46. Kesimpulan Produk

KOPIN merupakan website koperasi simpan pinjam yang mengintegrasikan proses keanggotaan, simpanan, pengajuan pinjaman, verifikasi dokumen, pencairan, jadwal angsuran, pembayaran, denda, notifikasi, transaksi, dan laporan dalam satu sistem.

Sistem menggunakan dua role utama, yaitu **Admin dan Anggota**. Anggota dapat melakukan sebagian besar proses secara mandiri melalui website, sedangkan Admin bertanggung jawab terhadap verifikasi, pengelolaan transaksi, persetujuan pinjaman, pencatatan pembayaran kantor, dan administrasi sistem.

Meskipun KOPIN dikembangkan sebagai proyek akademik, alur sistem dibuat menyerupai proses operasional nyata. Contohnya adalah adanya verifikasi dokumen, bukti usaha untuk pengajuan Modal Usaha, kemungkinan permintaan jaminan, estimasi verifikasi maksimal tiga hari kerja, serta mekanisme approval/rejection yang pada versi akademik dapat dilakukan langsung oleh Admin untuk kebutuhan demonstrasi.
