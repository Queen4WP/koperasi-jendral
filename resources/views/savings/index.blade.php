@extends('layouts.app')
@section('header_title', 'Daftar Simpanan Saya')

@section('content')
<div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <h4 style="margin: 0;">Data Simpanan</h4>
        <div>
            <a href="{{ route('member.savings.pokok.create') }}" class="btn btn-primary btn-sm">Buka Simpanan Pokok</a>
            <a href="{{ route('member.savings.wajib.create') }}" class="btn btn-primary btn-sm">Buka Simpanan Wajib</a>
        </div>
    </div>
    
    <x-validation-errors />

    @if(!isset($savings) || count($savings) == 0)
        <div style="text-align: center; padding: 40px; color: #6b7280;">
            <i class="fa-solid fa-folder-open" style="font-size: 48px; margin-bottom: 16px; color: #9ca3af;"></i>
            <p>Belum ada simpanan.</p>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Periode / Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($savings as $saving)
                <tr>
                    <td>{{ $saving->id }}</td>
                    <td>{{ ucfirst($saving->jenis_simpanan) }}</td>
                    <td>Rp {{ number_format($saving->nominal, 2, ',', '.') }}</td>
                    <td>{{ $saving->periode ?? '-' }} / {{ $saving->jatuh_tempo ?? '-' }}</td>
                    <td><x-status-badge :status="$saving->status" /></td>
                    <td>
                        @if($saving->status === 'unpaid')
                            <form method="POST" action="{{ route('member.savings.pay', $saving->id) }}" style="display: inline-block;">
                                @csrf
                                <input type="hidden" name="nominal_bayar" value="{{ $saving->nominal }}">
                                <input type="hidden" name="metode_pembayaran" value="tunai">
                                <input type="hidden" name="tanggal_bayar" value="{{ date('Y-m-d') }}">
                                <button type="submit" class="btn btn-primary" style="padding: 4px 8px; font-size: 0.85em;" onclick="return confirm('Proses pembayaran simpanan ini?')">
                                    Bayar
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
