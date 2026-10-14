@extends('layouts.app')

@section('title', 'Pencairan Pinjaman Tunai #' . $loan->nomor_pinjaman . ' — Loket Kasir')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="{{ route('kasir.dashboard') }}" style="color: #64748b; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Kasir
            </a>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0;">
                Pencairan Uang Tunai di Loket (DISBURSED)
            </h1>
        </div>
        <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
            <i class="fa-solid fa-certificate"></i> Status: APPROVED OLEH FINANCE
        </span>
    </div>
</div>

@if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div style="max-width: 720px; background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
        <div style="font-size: 14px; color: #1e40af; font-weight: 700; margin-bottom: 6px;">
            <i class="fa-solid fa-bullhorn"></i> Notifikasi Approval dari Finance:
        </div>
        <div style="font-size: 13px; color: #1e3a8a; background: #FFFFFF; padding: 10px 14px; border-radius: 8px; border: 1px dashed #93c5fd;">
            "{{ $loan->approval_note ?? 'Disetujui oleh Finance. Harap siapkan uang tunai di kasir.' }}"
        </div>
    </div>

    <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13px;">
            <div>
                <span style="color: #64748b;">Nomor Pinjaman:</span>
                <div style="font-size: 16px; font-weight: 800; color: #0f172a;">{{ $loan->nomor_pinjaman }}</div>
            </div>
            <div>
                <span style="color: #64748b;">Nama Anggota Penerima:</span>
                <div style="font-size: 16px; font-weight: 800; color: #0f172a;">{{ $loan->member->user->name }}</div>
                <div style="font-size: 11px; color: #64748b;">NIK: {{ $loan->member->nik }}</div>
            </div>
            <div>
                <span style="color: #64748b;">Nominal Uang Tunai Diserahkan:</span>
                <div style="font-size: 22px; font-weight: 800; color: #059669;">Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</div>
            </div>
            <div>
                <span style="color: #64748b;">Tenor & Angsuran:</span>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a;">{{ $loan->tenor }} Bulan &bull; Rp {{ number_format($loan->angsuran_per_bulan, 0, ',', '.') }}/bln</div>
            </div>
        </div>

        <div style="margin-top: 14px; padding-top: 14px; border-top: 1px dashed #cbd5e1; font-size: 12px; color: #64748b;">
            <i class="fa-solid fa-scale-balanced" style="color: #2563eb;"></i> <strong>Dual-Ledger Rule:</strong> Saldo simpanan anggota TIDAK terdebet secara negatif. Uang dicairkan dari kas operasional dan dicatat ke Kartu Pinjaman anggota.
        </div>
    </div>

    <form action="{{ route('kasir.loans.disburse.store', $loan->id) }}" method="POST">
        @csrf

        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px; margin-bottom: 20px;">
            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 13px; color: #92400e; font-weight: 700;">
                <input type="checkbox" name="konfirmasi_identitas" value="1" required style="margin-top: 3px; transform: scale(1.2);">
                <span>
                    Saya mengonfirmasi bahwa pemohon telah hadir di loket kasir, identitas telah diverifikasi, fisik uang tunai Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }} telah dihitung, dan anggota siap menandatangani tanda terima fisik penyerahan kas.
                </span>
            </label>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Catatan Kasir (Opsional)
            </label>
            <input type="text" name="catatan" value="{{ old('catatan') }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; box-sizing: border-box;" placeholder="Contoh: Diserahkan tunai pecahan 100.000">
        </div>

        <button type="submit" style="width: 100%; background: #059669; color: #FFFFFF; border: none; padding: 14px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fa-solid fa-money-bill-wave"></i> Serahkan Uang Tunai & Cetak Bukti Tanda Terima Fisik (BKK)
        </button>
    </form>
</div>
@endsection
