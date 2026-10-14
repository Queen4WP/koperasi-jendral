<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BKK Penarikan — {{ $withdrawal->nomor_referensi }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; padding: 30px; margin: 0; color: #0f172a; }
        .receipt-card { max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; padding: 36px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .header { text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 18px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; margin: 6px 0; }
        .table-meta { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px; }
        .table-meta td { padding: 6px 0; }
        .nominal-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px; text-align: center; margin: 18px 0; }
        .signatures { display: flex; justify-content: space-between; margin-top: 36px; text-align: center; font-size: 13px; }
        .sig-col { width: 45%; }
        .sig-line { margin-top: 55px; border-top: 1px solid #0f172a; font-weight: 700; padding-top: 4px; }
        .actions { text-align: center; margin-top: 24px; }
        .btn { background: #d97706; color: #FFFFFF; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
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
            <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #b45309;">KSP KOPIN ENTERPRISE</h2>
            <div style="font-size: 12px; color: #64748b;">Koperasi Simpan Pinjam — Kantor Operasional Pusat</div>
            <div class="title">BUKTI KAS KELUAR (BKK) PENARIKAN</div>
            <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 700; color: #0f172a;">
                NO: {{ $withdrawal->nomor_referensi }}
            </div>
        </div>

        <table class="table-meta">
            <tr>
                <td style="color: #64748b; width: 35%;">Tanggal Penarikan</td>
                <td style="font-weight: 600;">: {{ $withdrawal->tanggal_penarikan->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Diserahkan Kepada</td>
                <td style="font-weight: 700;">: {{ $withdrawal->member->user->name }} (NIK: {{ $withdrawal->member->nik }})</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Jenis Transaksi</td>
                <td style="font-weight: 600;">: Penarikan Simpanan Tunai di Loket</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Keterangan</td>
                <td style="font-weight: 600;">: {{ $withdrawal->catatan ?? 'Penarikan tunai' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Verifikasi Tunggakan</td>
                <td style="font-weight: 700; color: #166534;">: Bebas Tunggakan Pinjaman (Lolos Validasi)</td>
            </tr>
        </table>

        <div class="nominal-box">
            <div style="font-size: 12px; color: #92400e; font-weight: 600;">JUMLAH UANG DISERAHKAN TUNAI</div>
            <div style="font-size: 26px; font-weight: 800; color: #b45309;">
                Rp {{ number_format($withdrawal->nominal, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #92400e; margin-top: 4px;">Uang Tunai Telah Diterima Lengkap di Loket Kasir</div>
        </div>

        <div class="signatures">
            <div class="sig-col">
                <div>Penerima / Anggota,</div>
                <div class="sig-line">{{ $withdrawal->member->user->name }}</div>
            </div>
            <div class="sig-col">
                <div>Kasir Penyerah,</div>
                <div class="sig-line">{{ $withdrawal->kasir->name ?? 'Kasir KOPIN' }}</div>
            </div>
        </div>

        <div class="actions">
            <button class="btn" onclick="window.print()">
                Cetak Bukti Penarikan (BKK)
            </button>
            <a href="{{ route('kasir.dashboard') }}" class="btn btn-secondary">
                Kembali ke Dashboard Kasir
            </a>
        </div>
    </div>
</body>
</html>
