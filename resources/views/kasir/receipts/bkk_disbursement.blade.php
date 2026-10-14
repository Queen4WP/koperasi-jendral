<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BKK & Tanda Terima Pencairan — {{ $disbursement->nomor_referensi }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; padding: 30px; margin: 0; color: #0f172a; }
        .receipt-card { max-width: 650px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; padding: 36px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .header { text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 18px; margin-bottom: 20px; }
        .title { font-size: 17px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; margin: 6px 0; }
        .table-meta { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px; }
        .table-meta td { padding: 6px 0; }
        .nominal-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; text-align: center; margin: 18px 0; }
        .signatures { display: flex; justify-content: space-between; margin-top: 36px; text-align: center; font-size: 13px; }
        .sig-col { width: 45%; }
        .sig-line { margin-top: 60px; border-top: 1px solid #0f172a; font-weight: 700; padding-top: 4px; }
        .legal-notice { font-size: 11px; color: #64748b; line-height: 1.5; margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; text-align: justify; }
        .actions { text-align: center; margin-top: 24px; }
        .btn { background: #059669; color: #FFFFFF; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-secondary { background: #64748b; margin-left: 8px; }
        @media print {
            body { background: #FFFFFF; padding: 0; }
            .receipt-card { box-shadow: none; border: none; padding: 0; width: 100%; max-width: 100%; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="header">
            <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #047857;">KSP KOPIN ENTERPRISE</h2>
            <div style="font-size: 12px; color: #64748b;">Koperasi Simpan Pinjam — Kantor Operasional Pusat</div>
            <div class="title">BUKTI KAS KELUAR (BKK) & TANDA TERIMA PINJAMAN TUNAI</div>
            <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 700; color: #0f172a;">
                NO: {{ $disbursement->nomor_referensi }}
            </div>
        </div>

        <table class="table-meta">
            <tr>
                <td style="color: #64748b; width: 35%;">Tanggal Pencairan</td>
                <td style="font-weight: 600;">: {{ \Carbon\Carbon::parse($disbursement->tanggal_pencairan)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Nomor Pinjaman</td>
                <td style="font-weight: 800; color: #0f172a;">: {{ $disbursement->loan->nomor_pinjaman }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Penerima Uang Tunai</td>
                <td style="font-weight: 700;">: {{ $disbursement->loan->member->user->name }} (NIK: {{ $disbursement->loan->member->nik }})</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Tenor Pinjaman</td>
                <td style="font-weight: 600;">: {{ $disbursement->loan->tenor }} Bulan</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Kewajiban Angsuran</td>
                <td style="font-weight: 700;">: Rp {{ number_format($disbursement->loan->angsuran_per_bulan, 0, ',', '.') }} / bulan</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Persetujuan Finance</td>
                <td style="font-weight: 600; color: #1e40af;">: Disetujui (10% Pocket Reserve Compliant)</td>
            </tr>
        </table>

        <div class="nominal-box">
            <div style="font-size: 12px; color: #166534; font-weight: 600;">JUMLAH UANG PINJAMAN DISERAHKAN TUNAI</div>
            <div style="font-size: 28px; font-weight: 800; color: #047857;">
                Rp {{ number_format($disbursement->nominal, 0, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: #166534; margin-top: 4px;">Status Pinjaman: DISBURSED &bull; AKTIF</div>
        </div>

        <div class="legal-notice">
            <strong>PERNYATAAN TANDA TERIMA FISIK:</strong>
            Saya yang bertanda tangan di bawah ini menyatakan telah menerima uang tunai pinjaman modal kerja sejumlah nominal tersebut di atas secara penuh dan tanpa potongan dari Loket Kasir KSP KOPIN. Saya berjanji akan memenuhi kewajiban angsuran bulanan sesuai jadwal yang telah ditentukan.
        </div>

        <div class="signatures">
            <div class="sig-col">
                <div>Penerima Uang Tunai (Anggota),</div>
                <div class="sig-line">{{ $disbursement->loan->member->user->name }}</div>
            </div>
            <div class="sig-col">
                <div>Kasir Penyerah Kas,</div>
                <div class="sig-line">{{ $disbursement->admin->name ?? 'Kasir KOPIN' }}</div>
            </div>
        </div>

        <div class="actions">
            <button class="btn" onclick="window.print()">
                Cetak Tanda Terima Fisik (BKK)
            </button>
            <a href="{{ route('kasir.dashboard') }}" class="btn btn-secondary">
                Kembali ke Dashboard Kasir
            </a>
        </div>
    </div>
</body>
</html>
