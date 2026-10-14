<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BKM Angsuran — {{ $payment->nomor_referensi }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; padding: 30px; margin: 0; color: #0f172a; }
        .receipt-card { max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; padding: 36px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .header { text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 18px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; margin: 6px 0; }
        .table-meta { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px; }
        .table-meta td { padding: 6px 0; }
        .nominal-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px; text-align: center; margin: 18px 0; }
        .signatures { display: flex; justify-content: space-between; margin-top: 36px; text-align: center; font-size: 13px; }
        .sig-col { width: 45%; }
        .sig-line { margin-top: 55px; border-top: 1px solid #0f172a; font-weight: 700; padding-top: 4px; }
        .actions { text-align: center; margin-top: 24px; }
        .btn { background: #2563eb; color: #FFFFFF; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
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
            <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #1e40af;">KSP KOPIN ENTERPRISE</h2>
            <div style="font-size: 12px; color: #64748b;">Koperasi Simpan Pinjam — Kantor Operasional Pusat</div>
            <div class="title">BUKTI KAS MASUK (BKM) ANGSURAN</div>
            <div style="font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 700; color: #0f172a;">
                NO: {{ $payment->nomor_referensi }}
            </div>
        </div>

        <table class="table-meta">
            <tr>
                <td style="color: #64748b; width: 35%;">Tanggal & Waktu</td>
                <td style="font-weight: 600;">: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Diterima Dari</td>
                <td style="font-weight: 700;">: {{ $payment->installment->loan->member->user->name }} (NIK: {{ $payment->installment->loan->member->nik }})</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Nomor Pinjaman</td>
                <td style="font-weight: 700;">: {{ $payment->installment->loan->nomor_pinjaman }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Kewajiban Angsuran</td>
                <td style="font-weight: 600;">: Angsuran Ke-{{ $payment->installment->angsuran_ke }} dari {{ $payment->installment->loan->tenor }} Bulan</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Metode Penyetoran</td>
                <td style="font-weight: 600;">: Tunai di Loket Kasir (Cash Desk)</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Keterangan</td>
                <td style="font-weight: 600;">: {{ $payment->notes ?? 'Pembayaran angsuran tunai' }}</td>
            </tr>
        </table>

        <div class="nominal-box">
            <div style="font-size: 12px; color: #1e40af; font-weight: 600;">JUMLAH ANGSURAN DITERIMA</div>
            <div style="font-size: 26px; font-weight: 800; color: #1d4ed8;">
                Rp {{ number_format($payment->nominal, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: #1e40af; margin-top: 4px;">Status Angsuran: LUNAS &bull; SAH DITERIMA KASIR</div>
        </div>

        <div class="signatures">
            <div class="sig-col">
                <div>Penyetor / Peminjam,</div>
                <div class="sig-line">{{ $payment->installment->loan->member->user->name }}</div>
            </div>
            <div class="sig-col">
                <div>Kasir Penerima,</div>
                <div class="sig-line">{{ $payment->admin->name ?? 'Kasir KOPIN' }}</div>
            </div>
        </div>

        <div class="actions">
            <button class="btn" onclick="window.print()">
                Cetak Kwitansi Angsuran
            </button>
            <a href="{{ route('kasir.dashboard') }}" class="btn btn-secondary">
                Kembali ke Dashboard Kasir
            </a>
        </div>
    </div>
</body>
</html>
