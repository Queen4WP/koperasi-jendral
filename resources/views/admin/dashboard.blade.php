@extends('layouts.app')
@section('header_title', 'Dashboard Admin')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--emerald-500, #10b981); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 0.9em; margin-bottom: 5px;">Anggota Aktif</div>
        <div style="font-size: 1.5em; font-weight: bold;">{{ $total_anggota ?? 0 }}</div>
    </div>
    
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 0.9em; margin-bottom: 5px;">Pengajuan Pinjaman (Pending)</div>
        <div style="font-size: 1.5em; font-weight: bold;">{{ $pinjaman_pending ?? 0 }}</div>
    </div>

    <div class="card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 0.9em; margin-bottom: 5px;">Pembayaran Pending</div>
        <div style="font-size: 1.5em; font-weight: bold;">
            <a href="{{ route('admin.payments.index') }}" style="text-decoration: none; color: inherit;">{{ $pembayaran_pending ?? 0 }}</a>
        </div>
    </div>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h4 style="margin-top: 0;">Task List Operasional</h4>
    <p style="color: #6b7280;">Berikut ini adalah daftar aksi yang perlu diselesaikan administrator hari ini.</p>
    
    <table class="table">
        <thead>
            <tr>
                <th>Jenis Task</th>
                <th>Referensi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tasks) && count($tasks) > 0)
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task['type'] }}</td>
                        <td>{{ $task['ref'] }}</td>
                        <td><x-status-badge :status="$task['status']" /></td>
                        <td>
                            <a href="{{ $task['url'] }}" class="btn btn-secondary btn-sm">Proses</a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" style="text-align: center; color: #6b7280;">Tidak ada task pending saat ini.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
