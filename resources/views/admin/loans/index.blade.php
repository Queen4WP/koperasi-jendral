@extends('layouts.app')
@section('header_title', 'Manajemen Pinjaman & Review')

@section('content')
<div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <div>
            <h4 style="margin: 0 0 5px 0;">Daftar Pengajuan Pinjaman</h4>
            <p style="margin: 0; color: #6b7280; font-size: 0.9em;">Kelola pengajuan pinjaman anggota, verifikasi dokumen, lakukan review kepatuhan, serta persetujuan atau penolakan pinjaman.</p>
        </div>
        
        <form method="GET" action="{{ route('admin.loans.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pinjaman, nama, NIK, email..." class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; min-width: 260px;">
            
            <select name="status" class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">Semua Status</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Diajukan (Submitted)</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Dalam Review (Under Review)</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                <option value="disbursed" {{ request('status') === 'disbursed' ? 'selected' : '' }}>Dicairkan (Disbursed)</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Active)</option>
                <option value="paid_off" {{ request('status') === 'paid_off' ? 'selected' : '' }}>Lunas (Paid Off)</option>
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                <i class="fa-solid fa-search"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary" style="padding: 8px 12px;">Reset</a>
            @endif
        </form>
    </div>

    <x-validation-errors />

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; width: 40px;">No</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">No. Pinjaman</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Anggota</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Jumlah Pinjaman</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center;">Tenor</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Tujuan</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Pengajuan</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Reviewer</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $index => $loan)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; text-align: center;">
                            {{ $loans->firstItem() ? $loans->firstItem() + $index : $index + 1 }}
                        </td>
                        <td style="padding: 10px; font-family: monospace; font-weight: 600; color: #1e3a8a;">
                            {{ $loan->nomor_pinjaman ?? ('#' . str_pad($loan->id, 5, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td style="padding: 10px;">
                            <div style="font-weight: 600; color: #1f2937;">{{ $loan->member->user->name ?? '-' }}</div>
                            <div style="font-size: 0.85em; color: #6b7280;">NIK: {{ $loan->member->nik ?? '-' }}</div>
                        </td>
                        <td style="padding: 10px; font-weight: 600;">
                            Rp {{ number_format($loan->jumlah_pinjaman, 2, ',', '.') }}
                        </td>
                        <td style="padding: 10px; text-align: center; color: #4b5563;">
                            {{ $loan->tenor }} bln
                        </td>
                        <td style="padding: 10px; color: #374151;">
                            {{ $loan->tujuan_pinjaman }}
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <x-status-badge :status="$loan->status" />
                        </td>
                        <td style="padding: 10px; color: #4b5563; font-size: 0.85em;">
                            {{ $loan->submitted_at ? $loan->submitted_at->format('d/m/Y H:i') : ($loan->created_at ? $loan->created_at->format('d/m/Y H:i') : '-') }}
                        </td>
                        <td style="padding: 10px; font-size: 0.85em; color: #4b5563;">
                            @if($loan->reviewedBy)
                                <div><i class="fa-solid fa-user-check" style="color: #059669; margin-right: 4px;"></i> {{ $loan->reviewedBy->name }}</div>
                                <div style="color: #9ca3af; font-size: 0.8em;">{{ $loan->reviewed_at ? $loan->reviewed_at->format('d/m/Y H:i') : '' }}</div>
                            @elseif($loan->rejection_reason)
                                <div style="color: #dc2626;" title="{{ $loan->rejection_reason }}">
                                    <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                </div>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                <a href="{{ route('admin.loans.show', $loan->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.85em; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-magnifying-glass"></i> Detail
                                </a>
                                @if($loan->status === 'approved')
                                    <a href="{{ route('admin.loans.disburse', $loan->id) }}" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 0.85em; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-money-bill-transfer"></i> Cairkan
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding: 24px; text-align: center; color: #6b7280;">
                            Tidak ada data pinjaman yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $loans->links() }}
    </div>
</div>
@endsection
