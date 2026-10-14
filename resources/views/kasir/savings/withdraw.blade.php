@extends('layouts.app')

@section('title', 'Penarikan Simpanan Tunai — Loket Kasir KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="{{ route('kasir.dashboard') }}" style="color: #64748b; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Kasir
            </a>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0;">
                Penarikan Tunai Simpanan di Loket (BKK)
            </h1>
        </div>
        <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
            <i class="fa-solid fa-shield-halved"></i> Arrears Lock Protected
        </span>
    </div>
</div>

@if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div style="max-width: 650px; background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px; margin-bottom: 22px; font-size: 13px; color: #92400e; line-height: 1.5;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i>
        <strong>Ketentuan Penarikan Simpanan:</strong> Penarikan tunai simpanan wajib/sukarela hanya dapat diproses jika anggota <strong>tidak memiliki tunggakan angsuran pinjaman aktif</strong>. Sistem akan otomatis menolak transaksi jika ada tunggakan.
    </div>

    <form action="{{ route('kasir.savings.withdraw.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Pilih Anggota Penarik Dana
            </label>
            <select name="member_id" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 14px; box-sizing: border-box;">
                <option value="">-- Pilih Anggota KOPIN --</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->user->name }} (NIK: {{ $m->nik }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Nominal Uang Tunai Ditarik (Rp)
            </label>
            <input type="number" name="nominal" value="{{ old('nominal', 100000) }}" required min="1000" step="1000" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-size: 16px; font-weight: 700; color: #0f172a; box-sizing: border-box;" placeholder="100000">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Keperluan / Catatan Penarikan
            </label>
            <input type="text" name="catatan" value="{{ old('catatan') }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; box-sizing: border-box;" placeholder="Contoh: Penarikan simpanan untuk keperluan keluarga">
        </div>

        <button type="submit" style="width: 100%; background: #d97706; color: #FFFFFF; border: none; padding: 14px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fa-solid fa-hand-holding-dollar"></i> Serahkan Uang Tunai & Terbitkan BKK
        </button>
    </form>
</div>
@endsection
