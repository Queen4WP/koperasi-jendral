# PRD — SISTEM ENTERPRISE KOPERASI SIMPAN PINJAM KOPIN

## 1. Informasi Produk

| Item            | Detail                                                                                                     |
| --------------- | ---------------------------------------------------------------------------------------------------------- |
| Nama Produk     | KOPIN (Koperasi Simpan Pinjam Core Enterprise System)                                                      |
| Jenis           | Platform Web Koperasi Simpan Pinjam Multi-Role Terintegrasi                                                |
| Platform        | Responsive Web Application                                                                                 |
| Target Pengguna | Anggota Masyarakat Umum, Karyawan Internal, Kasir Loket, Staf Keuangan (Finance), dan Administrator        |
| Status          | Enterprise Standard (Siap Operasional Nyata)                                                               |
| Model Akses     | 5 Role Terisolasi: **Anggota**, **Karyawan**, **Kasir**, **Finance**, dan **Admin**                       |
| Prinsip Utama   | *Segregation of Duties* (Pemisahan Wewenang), *Prudential Cash Reserve* (10%), dan *Dual Ledger Integrity* |

---

# 2. Latar Belakang

Koperasi Simpan Pinjam KOPIN merupakan sistem enterprise berbasis web yang dirancang untuk mengelola seluruh ekosistem operasional koperasi secara nyata, transparan, dan akuntabel.

Sistem memfasilitasi kebutuhan nasabah eksternal (Anggota) yang membutuhkan kemudahan menabung dan meminjam modal usaha, karyawan internal (Karyawan) dengan kemudahan potong gaji otomatis (*payroll deduction*), petugas loket kantor (Kasir) yang melayani transaksi tunai harian secara cepat tanpa hambatan teknis, pengelola likuiditas dan brankas utama (Finance) yang menjaga cadangan kas aman, serta pengawas sistem (Admin) yang mengelola tata kelola data dan audit trail.

Sistem dirancang dengan arsitektur multi-guard session yang memungkinkan masing-masing aktor beroperasi secara independen tanpa bentrok sesi.

---

# 3. Tujuan Sistem

1. Menyediakan portal publik informatif dan pintu pendaftaran anggota baru yang transparan.
2. Memfasilitasi pendaftaran anggota masyarakat umum dan verifikasi KYC oleh Admin.
3. Mengelola Simpanan Pokok dan Simpanan Wajib bagi Anggota, serta Simpanan Sukarela bagi Karyawan.
4. Mendukung skema **Cicilan Parsial Simpanan Wajib (Skenario C)** yang fleksibel bagi anggota berpenghasilan harian/mingguan.
5. Mengotomatiskan pengingat menabung bulanan via email terjadwal (Tanggal 15 dan 20) sebelum akhir bulan (*End of Month* / EOM).
6. Mempercepat pelayanan tunai di loket kasir kantor tanpa kewajiban unggah file bukti (*cash-friendly*).
7. Menerapkan antarmuka kasir modern **POS Teller Card Grid** untuk mempermudah pemilihan dan penerimaan pembayaran angsuran pinjaman.
8. Menerapkan kebijakan **Single Active Loan (Opsi A - Strict Hard-Block)** guna mencegah anggota mengajukan pinjaman ganda sebelum melunasi kewajiban berjalan.
9. Mengatur pencairan pinjaman modal berbasis cadangan kas aman minimum (**10% Pocket Reserve**) oleh Finance.
10. Menyediakan *Live Status Tracking* pengajuan pinjaman modal yang ringkas dan transparan bagi anggota (`UNDER REVIEW` $\rightarrow$ `APPROVED` $\rightarrow$ `DISBURSED`).
11. Menjamin keamanan kas kantor melalui mekanisme Tutup Kas Sore harian (**Daily Zero-Cash Handover Jam 16:00**) dari Kasir ke Finance.
12. Memisahkan pencatatan keuangan anggota antara **Buku Tabungan** dan **Kartu Pinjaman** (*Dual-Ledger System*) guna mencegah saldo negatif.
13. Menyediakan pencatatan jejak audit (*Audit Log*) dan laporan analitik untuk integritas operasional.

---

# 4. Role dan Hak Akses (5 Aktor Terintegrasi)

Sistem menerapkan prinsip pemisahan wewenang (*Segregation of Duties*):

```
                            ARSITEKTUR LENGKAP ROLE KOPIN
  ┌──────────────────────────────────────────────────────────────────────────────────┐
  │                                                                                  │
  │   [ 1. ANGGOTA (Eksternal) ]                   [ 2. KARYAWAN (Internal) ]        │
  │   - Simpanan Wajib bulanan (Bisa cicil)        - Simpanan Sukarela potong gaji   │
  │   - Transaksi TUNAI di Kasir                   - Cicilan otomatis potong gaji    │
  │   - Pinjaman Modal (Cair Tunai di Kasir)       - Pencairan simpanan saat resign  │
  │                                                                                  │
  │                                      ▲                                           │
  │                                      │ (Melayani Tunai di Loket)                 │
  │                                      ▼                                           │
  │                            [ 3. KASIR (Loket Kantor) ]                           │
  │                            - Terima setoran tunai anggota (Tanpa upload bukti)   │
  │                            - POS Teller Card Grid untuk terima cicilan angsuran  │
  │                            - Eksekusi penarikan tabungan anggota (BKK tunai)     │
  │                            - Serahkan uang pinjaman cair ke anggota (BKK)        │
  │                            - Closing sore (Setor 100% uang tunai ke Finance)     │
  │                                                                                  │
  │                                      ▲                                           │
  │                                      │ (Serah Terima Kas & Notifikasi Pinjaman)  │
  │                                      ▼                                           │
  │                            [ 4. FINANCE (Keuangan) ]                             │
  │                            - Kontrol Ambang Batas 10% Pocket Reserve             │
  │                            - Review & Approve/Reject Pinjaman Modal (Beri note)  │
  │                            - Kirim alert ke Kasir saat pinjaman di-approve       │
  │                            - Verifikasi & Terima kas fisik sore dari Kasir       │
  │                                                                                  │
  │                                      ▲                                           │
  │                                      │ (Pengawasan & Konfigurasi)                │
  │                                      ▼                                           │
  │                            [ 5. ADMIN (Master & Audit) ]                         │
  │                            - Verifikasi pendaftaran anggota baru (KYC)           │
  │                            - Manajemen data master Anggota & Karyawan            │
  │                            - Pengawasan Audit Log & Laporan Analitik Sistem      │
  │                                                                                  │
  └──────────────────────────────────────────────────────────────────────────────────┘
```

## 4.1 Anggota (Nasabah Eksternal)
* Mengakses portal nasabah (`/login`).
* Menabung Simpanan Wajib tunai di loket kantor kasir (bisa dicicil bertahap dengan status `partial` sampai lunas `paid`).
* Menerima email pengingat menabung tanggal 15 dan 20 jika belum menabung penuh di bulan berjalan.
* Mengajukan penarikan tunai saldo Simpanan Wajib di loket kasir (dengan syarat tidak ada tunggakan pinjaman).
* Mengajukan pinjaman modal usaha (plafon dibatasi saldo simpanan, dan tunduk pada kebijakan *Strict Hard-Block* jika ada pinjaman berjalan).
* Melacak status pinjaman modal secara langsung (`UNDER REVIEW` $\rightarrow$ `APPROVED` $\rightarrow$ `DISBURSED`).
* Mengambil uang tunai pinjaman modal di kantor kasir dengan tanda tangan tanda terima fisik.
* Membayar angsuran bulanan secara tunai di loket kasir.
* Memantau Buku Tabungan (Debit/Kredit) dan Kartu Pinjaman di dashboard secara terpisah.

## 4.2 Karyawan (Nasabah Internal Perusahaan)
* Mengakses portal karyawan (`/karyawan/login`).
* Menabung rutin melalui mekanisme **Simpanan Sukarela otomatis potong gaji** (*payroll deduction*).
* Tidak dibebani Simpanan Pokok & Simpanan Wajib.
* Mengatur nominal potong gaji bulanan secara mandiri.
* Membayar cicilan pinjaman modal secara **otomatis potong gaji** bulanan (tidak perlu ke kasir).
* Mengajukan pinjaman modal dan tracking status ke Finance.
* Penarikan tabungan tunai di kasir sewaktu-waktu.
* Mengambil seluruh saldo Simpanan Sukarela saat *resign* / berhenti bekerja (setelah dipotong sisa kewajiban pinjaman).

## 4.3 Kasir (Petugas Loket Kantor)
* Mengakses portal kasir (`/kasir/login`).
* Melayani setoran tunai anggota tanpa upload bukti fisik (langsung lunas dan mencetak kuitansi BKM).
* Menerima setoran cicilan bertahap untuk Simpanan Wajib (Skenario C).
* Menggunakan antarmuka **POS Teller Card Grid** untuk memproses pembayaran angsuran nasabah secara intuitif.
* Melayani penarikan tunai simpanan wajib anggota (mencetak slip penarikan BKK fisik untuk ditandatangani anggota).
* Menerima notifikasi lonceng & email saat Finance menyetujui pinjaman modal anggota untuk menyiapkan uang tunai.
* Menyerahkan uang tunai pinjaman modal kepada anggota yang datang dengan tanda tangan fisik, dan mengaktifkan status pinjaman ke `DISBURSED`.
* Melakukan **Tutup Kas Harian (Daily Zero-Cash Handover Jam 16:00)** dengan menyerahkan 100% uang fisik di laci kasir ke Finance.

## 4.4 Finance (Bagian Keuangan & Pengelola Brankas)
* Mengakses portal keuangan (`/finance/login`).
* Memantau likuiditas kas koperasi dan total dana cadangan (*Pocket Reserve*).
* Menerapkan aturan **Cadangan Kas Minimum 10% Pocket Reserve**: Minimal 10% dari total simpanan wajib seluruh anggota wajib mengendap di kas koperasi dan dilarang dipinjamkan.
* Memeriksa antrean pengajuan pinjaman modal berstatus `UNDER REVIEW`.
* Memberikan keputusan:
  * **APPROVE:** Menyertakan Catatan Persetujuan (*Approval Note*) dan memicu alert otomatis ke Kasir untuk menyiapkan uang tunai di loket.
  * **REJECT:** Menyertakan Catatan Penolakan (*Rejection Note*) transparan jika pencairan melanggar ambang batas 10% pocket reserve.
* Menerima, menghitung uang fisik, dan mengonfirmasi serah terima kas harian jam 16:00 dari Kasir di sistem hingga laci kasir kembali Rp 0 dan kas masuk ke brankas Finance.

## 4.5 Admin (Pengawas Sistem & Master Data)
* Mengakses portal admin backoffice (`/admin/login`).
* Tidak memegang uang tunai fisik sama sekali (*segregation of duties*).
* Memeriksa dan memverifikasi pendaftaran anggota baru (KYC, validasi NIK).
* Mengelola data master Anggota dan Karyawan (aktivasi, penonaktifan).
* Mengawasi seluruh rekaman aktivitas sistem (*Audit Log*).
* Memantau dashboard analitik dan laporan kinerja koperasi secara menyeluruh.

---

# 5. Website Publik

Website publik dapat diakses masyarakat umum tanpa login:
* Beranda & Profil KOPIN
* Layanan Simpanan & Pinjaman Modal
* Simulasi Pinjaman Interaktif
* Syarat Menjadi Anggota & FAQ
* Kontak & Lokasi Kantor Koperasi
* Akses Portal: Login Anggota, Login Karyawan, dan Registrasi Anggota Baru

---

# 6. Registrasi & Aktivasi Anggota

1. Masyarakat mendaftar secara online atau datang langsung ke kantor koperasi.
2. Data registrasi: Nama lengkap, NIK, tanggal lahir, alamat, nomor telepon, email, password, pekerjaan, dan perusahaan.
3. Akun berstatus `PENDING VERIFICATION`.
4. Admin memeriksa keabsahan identitas dan menyetujui akun menjadi `ACTIVE`.
5. Anggota aktif melakukan pembayaran Simpanan Pokok awal di loket kasir.

---

# 7. Pengelolaan Simpanan

## 7.1 Simpanan Pokok
* Kewajiban awal sebesar **Rp55.000**.
* Dibayar sekali selama menjadi anggota, menjadi ekuitas dasar, dan tidak dapat ditarik selama berstatus anggota aktif.

## 7.2 Simpanan Wajib (Anggota Eksternal) & Skenario C (Cicilan Parsial)
* Kewajiban bulanan sebesar **Rp100.000 per bulan**.
* **Skema Cicilan Parsial (Skenario C):** Anggota yang belum sanggup membayar Rp 100.000 sekaligus (misalnya tanggal 14 hanya sanggup Rp 10.000) dapat menyetor cicilan secara bertahap di loket kasir.
  * Sistem mencatat status simpanan sebagai **`partial` (Sebagian / Cicilan)** dengan sisa kewajiban tercatat jelas (misal: Sisa Rp 90.000).
  * Uang yang disetor langsung masuk dan menambah saldo tabungan anggota (*Dual-Ledger Buku Tabungan*).
  * Status berubah menjadi **`paid` (Lunas)** secara otomatis begitu akumulasi setoran mencapai minimal Rp 100.000.
* **Periode Jatuh Tempo:** Berlaku dari tanggal 1 hingga hari terakhir bulan berjalan (*End of Month* / EOM).
* **Pengingat Terjadwal Otomatis (Email Blast Opsi Hangat & Gotong Royong):**
  * **Tanggal 15 (Reminder 1):** Sistem mengirimkan email pengingat ramah. Jika anggota sudah mencicil sebagian, email secara spesifik menyebutkan jumlah yang sudah terbayar dan sisa kekurangan (contoh: *"Anda telah menyetor Rp 10.000, sisa kewajiban Anda Rp 90.000"*).
  * **Tanggal 20 (Reminder 2):** Sistem mengirimkan email pengingat penting kedua yang mengimbau penyelesaian sisa sebelum akhir bulan. Anggota yang sudah lunas penuh Rp 100.000 sebelum tanggal 20 dikecualikan secara otomatis (*idempotent*).
* **Penyetoran di Loket Kasir:** Anggota menyetor uang tunai ke loket kantor. Kasir klik terima tunai $\rightarrow$ tagihan terupdate (parsial/lunas) $\rightarrow$ printer mencetak Kuitansi Bukti Kas Masuk (BKM) yang mencantumkan status setoran & sisa kewajiban. Tanpa kewajiban unggah file bukti transfer.
* **Penarikan Saldo Simpanan Wajib:** Anggota dapat menarik uang simpanan wajibnya secara tunai di loket kasir.
  * **Filter Anti-Macet & Pre-Screening Pinjaman:** Pengajuan pinjaman modal dan penarikan simpanan terkunci otomatis jika Simpanan Wajib bulan berjalan belum dibayar penuh Rp 100.000 atau memiliki tunggakan periode lampau.

## 7.3 Simpanan Sukarela (Karyawan Internal)
* Dikhususkan bagi Karyawan internal perusahaan.
* Penyetoran dilakukan secara **otomatis potong gaji bulanan** (*payroll deduction*) saat tanggal gajian.
* Nominal potongan ditentukan oleh karyawan dan dapat diubah sebelum periode *cut-off*.
* Dapat ditarik sewaktu-waktu secara tunai di loket kasir saat membutuhkan dana darurat.
* Dicairkan penuh saat karyawan mengundurkan diri (*resign*) dari perusahaan setelah dipotong sisa utang pinjaman modal (jika ada).

---

# 8. Pengajuan & Pencairan Pinjaman Modal

## 8.1 Syarat Kelayakan & Validasi Opsi A (Strict Hard-Block)
Anggota memenuhi syarat pengajuan jika:
1. Akun berstatus aktif dan terverifikasi.
2. Simpanan Pokok lunas.
3. Simpanan Wajib bulan berjalan lunas dan tidak ada tunggakan masa lalu (didukung skema cicilan parsial).
4. **Single Active Loan Policy (Opsi A - Strict Hard-Block):** Anggota yang masih memiliki kewajiban pinjaman modal aktif (misalnya pada Kartu Pinjaman Modal sebesar Rp11.800.000) secara otomatis **terkunci keras (hard-blocked)** saat mencoba mengajukan pinjaman baru (misal Rp12.000.000).
   * Pada tampilan anggota, form menampilkan banner merah peringatan detail nomor pinjaman & sisa utang.
   * Tombol submit otomatis dinonaktifkan (*disabled*).
   * Request API/form ditolak tegas oleh controller dengan pesan edukatif untuk melunasi pinjaman berjalan terlebih dahulu di loket kasir.
5. **Batas Plafon:** Nominal pinjaman yang diajukan tidak melampaui batas saldo simpanan yang dimiliki anggota.

## 8.2 Parameter Pinjaman Modal Reguler
* **Plafon Pengajuan:** Rp5.000.000 s/d Rp50.000.000 (disesuaikan dengan saldo simpanan peminjam).
* **Pilihan Tenor:** 6, 12, 18, dan 24 bulan.
* **Metode Bunga/Jasa:** Flat Rate:
  * 6 bulan = 5,0%
  * 12 bulan = 7,0%
  * 18 bulan = 8,5%
  * 24 bulan = 10,0%
* **Rumus Perhitungan:**
  ```text
  Total Jasa = Pokok Pinjaman × Persentase Jasa
  Total Kewajiban = Pokok Pinjaman + Total Jasa
  Angsuran per Bulan = Total Kewajiban ÷ Tenor
  ```

## 8.3 Alur Persetujuan & Live Status Tracking
Alur disederhanakan menjadi 3 status utama transparan yang dapat dipantau anggota di dashboard:

```text
Anggota Submit Form Pinjaman
           ↓
     [ UNDER REVIEW ]
(Antrean Masuk ke Portal Finance)
           ↓
Finance Mengevaluasi Likuiditas 10% Pocket Reserve
     ├── Melanggar Batas 10% ──► [ REJECTED ] (Catatan: Likuiditas Cadangan Kas 10% Tercapai)
     └── Lolos Batas 10%     ──► [ APPROVED ] (Catatan: Catatan Persetujuan Finance)
                                       ↓
                        Sistem Kirim Notifikasi Lonceng & Email ke Kasir
                                       ↓
                        Kasir Siapkan Uang Tunai di Loket
                                       ↓
                                [ DISBURSED ]
                  (Sistem Kirim Notifikasi ke Anggota: "Uang Siap Diambil di Kantor")
                                       ↓
                  Anggota Datang ke Loket, Tanda Tangan Bukti Fisik, Terima Uang Tunai
                                       ↓
                  Pinjaman Menjadi Aktif & Jadwal Angsuran Resmi Terbit
```

### Penjelasan Status `APPROVED` vs `DISBURSED` pada Kartu Pinjaman Modal:
* Ketika pengajuan disetujui Finance, status pinjaman adalah **`APPROVED`**.
* Pada tahap ini, uang fisik **belum keluar** dari kas koperasi dan belum diserahkan ke nasabah.
* Oleh karena itu, di dashboard anggota, kartu pinjaman modal masih menampilkan **Rp 0** disertai banner biru informatif: *"Pinjaman telah disetujui Finance! Silakan datang ke loket kasir kantor untuk pengambilan uang tunai."*
* Begitu kasir menyerahkan uang tunai dan mengklik **"Serahkan Uang Tunai di Loket"**, status berubah menjadi **`DISBURSED`**, dana fisik resmi keluar dari kasir, dan **Kartu Pinjaman Modal nasabah otomatis aktif terisi nominal pinjaman**.

### Aturan Likuiditas Finance (10% Pocket Reserve):
* Finance memeriksa total saldo simpanan wajib seluruh anggota (*Total Pocket*).
* Cadangan kas minimal 10% dari total pocket wajib mengendap di kas koperasi sebagai jaminan keamanan operasional.
* Pengajuan yang menyebabkan kas sisa koperasi turun di bawah 10% akan ditolak secara transparan dengan alasan batasan likuiditas.

---

# 9. Pembayaran Angsuran & Manajemen Keterlambatan

## 9.1 Mekanisme Pembayaran Angsuran di Loket Kasir (POS Teller Card Grid)
* **Antarmuka POS Kasir:** Kasir disajikan tampilan visual dua kolom berisi kartu-kartu tagihan anggota.
* **Filter Next Due:** Sistem secara cerdas hanya menampilkan **1 kartu tagihan per pinjaman**, yaitu tagihan angsuran terdekat (*next due installment*), sehingga kasir tidak kebingungan dengan puluhan cicilan di masa depan.
* **Kalkulasi Denda Otomatis:**
  * Hari ke 1–6 keterlambatan: **Rp 0** (Masa Tenggang / *Grace Period*).
  * Hari ke 7 dan seterusnya: Dikenakan denda **Rp 10.000 per kelipatan 7 hari** keterlambatan.
  * Rumus: `Denda = floor(hari keterlambatan ÷ 7) × Rp10.000`.
* Kasir memilih kartu tagihan $\rightarrow$ memeriksa uang fisik $\rightarrow$ klik **"Terima Uang & Cetak Struk"** $\rightarrow$ kuitansi BKM pembayaran angsuran langsung terbit.

## 9.2 Karyawan Internal
* Pembayaran angsuran bulanan dipotong otomatis dari slip gaji (*payroll deduction*), tanpa perlu datang ke loket kasir.

---

# 10. Tutup Kas Harian Jam 16:00 (Daily Zero-Cash Handover)

Guna menjamin integritas uang tunai dan meniadakan risiko penyimpanan kas semalaman (*zero overnight cash risk*):
1. Jam 16:00 saat loket kasir tutup, Kasir menekan menu **`[ Serahkan Kas ke Finance Sekarang ]`** di dashboard loket.
2. Sistem merekap mutasi harian secara *authoritative*:
   $$\text{Kas Masuk} = \text{Setoran Simpanan Tunai} + \text{Pembayaran Angsuran Tunai}$$
   $$\text{Kas Keluar} = \text{Penarikan Simpanan Tunai} + \text{Pencairan Pinjaman Tunai}$$
   $$\text{Saldo Kas Fisik Bersih} = \max\Big(0,\ \text{Kas Masuk} - \text{Kas Keluar}\Big)$$
   * **Kondisi Khusus Kas Keluar > Kas Masuk:** Jika dalam hari yang sama kasir melakukan pencairan pinjaman bernilai besar (misal Rp 15.000.000) yang melampaui kas masuk hari itu, kas fisik laci terpakai habis untuk pencairan anggota sehingga saldo netto terkunci di **Rp 0**. Sistem menampilkan banner penjelasan visual transparan di dashboard kasir.
3. Kasir menyerahkan 100% uang fisik yang ada di laci kepada Finance.
4. Data closing kas harian terkirim ke portal Finance dengan status **`pending`**.
5. Staf Finance menghitung fisik uang dan mengklik tombol **`[ Verifikasi & Terima Uang Fisik ]`** di dashboard Finance.
6. Status closing diperbarui menjadi **`verified`**, notifikasi resmi `CLOSING_VERIFIED` terkirim ke Kasir, dan saldo laci kasir kembali **Rp 0** semalaman.

---

# 11. Dual Ledger & Dashboard Anggota

Guna mencegah risiko kesalahpahaman anggota dan menghindari saldo negatif di antarmuka pengguna:
* **Buku Tabungan Anggota:**
  * Menampilkan saldo simpanan wajib yang transparan.
  * Menampilkan riwayat transaksi Debet (tarik tabungan) dan Kredit (setor tabungan).
  * Saldo tabungan tidak pernah terpotong oleh nominal pinjaman modal.
* **Kartu Pinjaman Modal:**
  * Menampilkan plafon pinjaman yang dicairkan, persentase jasa, total kewajiban, dan sisa utang berjalan.
  * Menampilkan tabel jadwal cicilan bulanan yang berkurang setiap kali angsuran dibayar hingga lunas penuh (*PAID OFF*).

---

# 12. Log Aktivitas & Keamanan Data

* Sistem mencatat seluruh aktivitas sensitif ke dalam tabel `activity_logs`:
  * Verifikasi dan perubahan status anggota oleh Admin.
  * Persetujuan dan penolakan pinjaman modal beserta catatan oleh Finance.
  * Penerimaan setoran tunai, eksekusi penarikan, dan penyerahan pencairan oleh Kasir.
  * Konfirmasi serah terima tutup kas sore oleh Finance.
* Setiap log mengunci ID aktor, IP address, waktu transaksi, dan rincian parameter perubahan secara *tamper-proof*.
* Kata sandi dienkripsi dengan standar Bcrypt, data sensitif dilindungi dari akses lintas-pengguna, dan sesi otentikasi diisolasi penuh per guard.
