@extends('layouts.app')
@section('header_title', 'Manajemen Simpanan & Rekonsiliasi')

@section('content')
<div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
        <div>
            <h4 style="margin: 0 0 5px 0;">Data Simpanan Anggota</h4>
            <p style="margin: 0; color: #6b7280; font-size: 0.9em;">Kelola kewajiban simpanan pokok dan wajib, pantau status pembayaran, dan lakukan rekonsiliasi setoran.</p>
        </div>
        
        <form method="GET" action="{{ route('admin.savings.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, ref bayar..." class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; min-width: 200px;">
            
            <select name="jenis" class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">Semua Jenis</option>
                <option value="pokok" {{ request('jenis') === 'pokok' ? 'selected' : '' }}>Simpanan Pokok</option>
                <option value="wajib" {{ request('jenis') === 'wajib' ? 'selected' : '' }}>Simpanan Wajib</option>
            </select>

            <select name="status" class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Menunggu Verifikasi)</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success (Pembayaran Sukses)</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed (Ditolak)</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Tagihan: Unpaid</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Tagihan: Paid (Lunas)</option>
            </select>

            <select name="metode" class="form-control" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">Semua Metode</option>
                <option value="online" {{ request('metode') === 'online' ? 'selected' : '' }}>Online (Transfer)</option>
                <option value="kantor" {{ request('metode') === 'kantor' ? 'selected' : '' }}>Kantor (Langsung)</option>
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                <i class="fa-solid fa-search"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'jenis', 'status', 'metode']))
                <a href="{{ route('admin.savings.index') }}" class="btn btn-secondary" style="padding: 8px 12px;">Reset</a>
            @endif
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; width: 40px;">No</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Anggota</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Jenis</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Periode</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Kewajiban</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center;">Status Tagihan</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Pembayaran Terakhir</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($savings as $index => $item)
                    @php
                        $lastTrx = $item->latestTransaction;
                    @endphp
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; text-align: center;">
                            {{ $savings->firstItem() ? $savings->firstItem() + $index : $index + 1 }}
                        </td>
                        <td style="padding: 10px;">
                            <div style="font-weight: 600; color: #1f2937;">{{ $item->member->user->name ?? '-' }}</div>
                            <div style="font-size: 0.85em; color: #6b7280;">NIK: {{ $item->member->nik ?? '-' }}</div>
                        </td>
                        <td style="padding: 10px;">
                            <span class="badge {{ $item->jenis_simpanan === 'pokok' ? 'badge-primary' : 'badge-info' }}" style="background: {{ $item->jenis_simpanan === 'pokok' ? '#e0e7ff; color: #3730a3;' : '#dbeafe; color: #1e40af;' }}">
                                {{ ucfirst($item->jenis_simpanan) }}
                            </span>
                        </td>
                        <td style="padding: 10px; color: #4b5563;">
                            {{ $item->periode ? date('F Y', strtotime($item->periode)) : 'Satu Kali' }}
                        </td>
                        <td style="padding: 10px; font-weight: 600;">
                            Rp {{ number_format($item->nominal, 2, ',', '.') }}
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <x-status-badge :status="$item->status" />
                        </td>
                        <td style="padding: 10px;">
                            @if($lastTrx)
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <x-status-badge :status="$lastTrx->status" />
                                    <span style="font-size: 0.85em; color: #6b7280;">({{ ucfirst($lastTrx->metode_pembayaran) }})</span>
                                </div>
                                <div style="font-size: 0.8em; color: #9ca3af; font-family: monospace;">{{ $lastTrx->payment_reference }}</div>
                            @else
                                <span style="color: #9ca3af; font-size: 0.85em;">Belum ada pembayaran</span>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('admin.savings.show', $item->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.85em;">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                                @if($lastTrx && $lastTrx->status === 'pending')
                                    <a href="{{ route('admin.payments.savings.show', $lastTrx->id) }}" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 0.85em;">
                                        Review
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 24px; text-align: center; color: #6b7280;">
                            Tidak ada data simpanan yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $savings->links() }}
    </div>
</div>
@endsection
