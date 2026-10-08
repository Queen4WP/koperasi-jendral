@extends('layouts.app')
@section('header_title', 'Audit Log Aktivitas')

@section('content')
<div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <div>
            <h4 style="margin: 0 0 5px 0;">Audit Log & Riwayat Aktivitas</h4>
            <p style="margin: 0; color: #6b7280; font-size: 0.9em;">
                Catatan riwayat audit administratif dan finansial sistem KOPIN bersifat permanen dan tidak dapat diubah (immutable).
            </p>
        </div>
        
        <form method="GET" action="{{ route('admin.activity_logs.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, deskripsi, user, IP..." class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; min-width: 240px;">
            <select name="action" class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">Semua Aksi</option>
                @foreach($distinctActions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'action']))
                <a href="{{ route('admin.activity_logs.index') }}" class="btn btn-secondary" style="padding: 8px 12px;">Reset</a>
            @endif
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; width: 50px;">No</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; width: 140px;">Waktu</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; width: 160px;">Aksi</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Pelaku (Actor)</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Deskripsi</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; width: 140px;">Entitas Terkait</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; width: 140px;">IP / Info</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; text-align: center; color: #6b7280; font-size: 0.9em;">
                            {{ $logs->firstItem() ? $logs->firstItem() + $index : $index + 1 }}
                        </td>
                        <td style="padding: 12px; font-size: 0.85em; color: #374151; white-space: nowrap;">
                            <div style="font-weight: 600;">{{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}</div>
                            <div style="color: #6b7280;">{{ $log->created_at ? $log->created_at->format('H:i:s') : '-' }} WIB</div>
                        </td>
                        <td style="padding: 12px;">
                            <span style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-family: monospace; font-weight: 600; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                {{ $log->activity }}
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            @if($log->actor)
                                <div style="font-weight: 600; color: #1f2937;">{{ $log->actor->name }}</div>
                                <div style="font-size: 0.8em; color: #6b7280;">{{ $log->actor->email }} ({{ ucfirst($log->actor->role) }})</div>
                            @else
                                <span style="color: #9ca3af; font-style: italic;">Sistem / Anonim</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <div style="font-size: 0.9em; color: #1f2937; line-height: 1.4;">
                                {{ $log->keterangan ?? '-' }}
                            </div>
                            @if(!empty($log->data))
                                <details style="margin-top: 6px; font-size: 0.8em;">
                                    <summary style="cursor: pointer; color: var(--emerald-600, #059669); font-weight: 600;">
                                        Lihat Metadata
                                    </summary>
                                    <pre style="margin-top: 6px; padding: 8px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 11px; max-width: 380px; overflow-x: auto; white-space: pre-wrap; word-break: break-all;">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </details>
                            @endif
                        </td>
                        <td style="padding: 12px; font-size: 0.85em; color: #4b5563;">
                            @if($log->subject_type)
                                <div style="font-weight: 600;">{{ class_basename($log->subject_type) }}</div>
                                <div style="color: #6b7280;">ID: #{{ $log->subject_id }}</div>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px; font-size: 0.8em; color: #6b7280;">
                            <div><i class="fa-solid fa-network-wired" style="font-size: 10px; margin-right: 4px;"></i>{{ $log->ip_address ?? '127.0.0.1' }}</div>
                            @if($log->user_agent)
                                <div style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                                    <i class="fa-solid fa-desktop" style="font-size: 10px; margin-right: 4px;"></i>{{ Str::limit($log->user_agent, 20) }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 32px; text-align: center; color: #6b7280;">
                            <i class="fa-solid fa-clock-rotate-left" style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                            Belum ada riwayat aktivitas yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
