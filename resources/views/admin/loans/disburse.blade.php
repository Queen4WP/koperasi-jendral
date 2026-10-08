@extends('layouts.app')
@section('header_title', 'Pencairan Dana (Disbursement)')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h4 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Proses Pencairan Pinjaman #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</h4>
    
    <table style="width: 100%; line-height: 2; margin-bottom: 20px;">
        <tr>
            <td style="color: #6b7280; width: 180px;">Pemohon</td>
            <td style="font-weight: bold;">{{ $loan->member->user->name }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Tujuan Rekening</td>
            <td><strong>{{ $loan->bank_name }}</strong><br>{{ $loan->bank_account_number }} a.n {{ $loan->bank_account_owner }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Nominal Disetujui</td>
            <td style="font-size: 1.2em; font-weight: bold; color: #059669;">Rp {{ number_format($loan->jumlah_pinjaman, 2, ',', '.') }}</td>
        </tr>
    </table>

    <x-validation-errors />

    @if($loan->status === 'approved')
        <form method="POST" action="{{ route('admin.loans.disburse', $loan->id) }}">
            @csrf
            
            <div class="form-group">
                <label for="nominal">Nominal Pencairan Aktual (Rp)</label>
                <input type="number" name="nominal" id="nominal" value="{{ old('nominal', $loan->jumlah_pinjaman) }}" class="form-control" required readonly style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; background: #f8f9fa;">
                <small style="color: #6c757d;">Nominal harus sesuai dengan yang disetujui (V1).</small>
            </div>

            <div class="form-group">
                <label for="tanggal_pencairan">Tanggal Pencairan</label>
                <input type="date" name="tanggal_pencairan" id="tanggal_pencairan" value="{{ old('tanggal_pencairan', date('Y-m-d')) }}" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div class="form-group">
                <label for="bukti_transfer">Nomor Referensi Bukti Transfer (Opsional)</label>
                <input type="text" name="bukti_transfer" id="bukti_transfer" value="{{ old('bukti_transfer') }}" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; font-size: 0.9em; margin-top: 15px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Menekan tombol ini akan merubah status pinjaman menjadi <strong>Aktif</strong> dan akan langsung mencetak/<strong>menggenerate jadwal angsuran</strong>. Pastikan transfer sudah benar-benar berhasil!
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;" onclick="return confirm('Konfirmasi pencairan dana dan aktivasi pinjaman?')">Konfirmasi Pencairan</button>
        </form>
    @else
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; text-align: center;">
            Pinjaman tidak dalam status <strong>approved</strong>. Pencairan dibatalkan.
        </div>
    @endif
</div>
@endsection
