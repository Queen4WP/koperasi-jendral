@extends('layouts.app')
@section('header_title', 'Daftar Pinjaman Saya')

@section('content')
<div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <h4 style="margin: 0;">Pengajuan & Pinjaman Aktif</h4>
        <a href="#" class="btn btn-primary btn-sm">Ajukan Pinjaman Baru</a>
    </div>
    
    <x-validation-errors />

    @if(!isset($loans) || count($loans) == 0)
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <i class="fa-solid fa-file-invoice-dollar" style="font-size: 48px; margin-bottom: 16px; color: #9ca3af;"></i>
            <p>Belum ada pengajuan pinjaman.</p>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tujuan</th>
                    <th>Jumlah (Rp)</th>
                    <th>Tenor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr>
                    <td>{{ $loan->id }}</td>
                    <td>{{ $loan->tujuan_pinjaman }}</td>
                    <td>Rp {{ number_format($loan->jumlah_pinjaman, 2, ',', '.') }}</td>
                    <td>{{ $loan->tenor }} bln</td>
                    <td><x-status-badge :status="$loan->status" /></td>
                    <td>
                        <a href="#" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.85em;">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
