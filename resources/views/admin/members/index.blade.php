@extends('layouts.app')
@section('header_title', 'Manajemen Anggota')

@section('content')
<div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <div>
            <h4 style="margin: 0 0 5px 0;">Daftar Anggota Koperasi</h4>
            <p style="margin: 0; color: #6b7280; font-size: 0.9em;">Kelola data anggota, verifikasi pendaftaran baru, dan tinjau status keanggotaan.</p>
        </div>
        
        <form method="GET" action="{{ route('admin.members.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, email..." class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; min-width: 220px;">
            <select name="status" class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Aktif)</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                <i class="fa-solid fa-search"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.members.index') }}" class="btn btn-secondary" style="padding: 8px 12px;">Reset</a>
            @endif
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; width: 50px;">No</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Nama & Identitas</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Kontak</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center;">Status</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Tgl Daftar</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center; width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $item)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; text-align: center;">
                            {{ $members->firstItem() ? $members->firstItem() + $index : $index + 1 }}
                        </td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 600; color: #1f2937;">{{ $item->user->name ?? '-' }}</div>
                            <div style="font-size: 0.85em; color: #6b7280;">NIK: {{ $item->nik }}</div>
                            <div style="font-size: 0.8em; color: #9ca3af;">{{ $item->user->email ?? '-' }}</div>
                        </td>
                        <td style="padding: 12px;">
                            <div>{{ $item->telepon }}</div>
                            <div style="font-size: 0.85em; color: #6b7280;">{{ $item->pekerjaan }}</div>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <x-status-badge :status="$item->status" />
                        </td>
                        <td style="padding: 12px; font-size: 0.9em; color: #4b5563;">
                            {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('admin.members.show', $item->id) }}" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.85em;">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                                @if($item->status === 'pending')
                                    <a href="{{ route('admin.members.review', $item->id) }}" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.85em;">
                                        <i class="fa-solid fa-user-check"></i> Review
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 24px; text-align: center; color: #6b7280;">
                            Tidak ada data anggota yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $members->links() }}
    </div>
</div>
@endsection
