@extends('layouts.app')

@section('title', 'Setoran Tunai Simpanan — Loket Kasir KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="{{ route('kasir.dashboard') }}" style="color: #64748b; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Kasir
            </a>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0;">
                Terima Setoran Tunai Simpanan di Loket
            </h1>
        </div>
        <span style="background: #ecfdf5; color: #047857; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
            <i class="fa-solid fa-bolt"></i> Auto BKM &bull; Tanpa Upload Foto
        </span>
    </div>
</div>

@if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div style="max-width: 650px; background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px; margin-bottom: 22px; font-size: 13px; color: #1e40af; line-height: 1.5;">
        <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i>
        <strong>SOP Kasir:</strong> Transaksi uang tunai langsung di loket diproses secara instan tanpa perlu unggah foto bukti transfer. Sistem otomatis menerbitkan <strong>Bukti Kas Masuk (BKM)</strong> resmi bernomor urut untuk dicetak.
    </div>

    <form action="{{ route('kasir.savings.deposit.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Pilih Anggota Penyetor
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
                Jenis Simpanan
            </label>
            <select name="jenis_simpanan" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 14px; box-sizing: border-box;">
                <option value="wajib" {{ old('jenis_simpanan', 'wajib') == 'wajib' ? 'selected' : '' }}>Simpanan Wajib (Bulanan)</option>
                <option value="pokok" {{ old('jenis_simpanan') == 'pokok' ? 'selected' : '' }}>Simpanan Pokok (Awal Masuk)</option>
                <option value="sukarela" {{ old('jenis_simpanan') == 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
            </select>
        </div>

        <div style="margin-bottom: 18px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Nominal Uang Tunai Diterima (Rp)
            </label>
            <input type="number" name="nominal" value="{{ old('nominal', 50000) }}" required min="1000" step="1000" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-size: 16px; font-weight: 700; color: #0f172a; box-sizing: border-box;" placeholder="50000">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Catatan Loket (Opsional)
            </label>
            <input type="text" name="catatan" value="{{ old('catatan') }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; box-sizing: border-box;" placeholder="Contoh: Setoran simpanan wajib tunai">
        </div>

        <button type="submit" style="width: 100%; background: #059669; color: #FFFFFF; border: none; padding: 14px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="fa-solid fa-receipt"></i> Terima Uang & Terbitkan BKM
        </button>
    </form>
</div>
@endsection
