@extends('layouts.app')
@section('header_title', 'Dashboard Anggota')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
    <!-- SIMPANAN POKOK -->
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--emerald-500, #10b981); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 0.9em; margin-bottom: 5px;">Total Simpanan Pokok</div>
        <div style="font-size: 1.5em; font-weight: bold;">Rp {{ number_format($total_simpanan_pokok ?? 0, 2, ',', '.') }}</div>
    </div>
    
    <!-- SIMPANAN WAJIB -->
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 0.9em; margin-bottom: 5px;">Total Simpanan Wajib</div>
        <div style="font-size: 1.5em; font-weight: bold;">Rp {{ number_format($total_simpanan_wajib ?? 0, 2, ',', '.') }}</div>
    </div>
    
    <!-- SISA PINJAMAN -->
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #ef4444; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="color: #6b7280; font-size: 0.9em; margin-bottom: 5px;">Sisa Kewajiban Pinjaman</div>
        <div style="font-size: 1.5em; font-weight: bold;">Rp {{ number_format($sisa_pinjaman ?? 0, 2, ',', '.') }}</div>
    </div>
</div>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin-top: 0;">Status Keanggotaan</h4>
        <div style="margin-bottom: 10px;">
            Status: <x-status-badge :status="$member->status ?? 'pending'" />
        </div>
        @if(!isset($member) || $member->status !== 'active')
            <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px;">
                Akun Anda belum aktif atau masih dalam peninjauan admin. Fitur transaksi mungkin dibatasi.
            </div>
        @endif
    </div>

    <div class="card" style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin-top: 0;">Jadwal Angsuran Terdekat</h4>
        @if(!isset($next_installment))
            <p style="color: #6b7280;">Tidak ada angsuran terdekat.</p>
        @else
            <div style="border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px;">
                <div style="font-size: 0.9em; color: #6b7280;">Bulan ke-{{ $next_installment->angsuran_ke }}</div>
                <div style="font-size: 1.2em; font-weight: bold; margin: 5px 0;">Rp {{ number_format($next_installment->total_angsuran, 2, ',', '.') }}</div>
                <div style="font-size: 0.9em; color: #dc2626;"><i class="fa-regular fa-calendar"></i> Jatuh Tempo: {{ date('d M Y', strtotime($next_installment->jatuh_tempo)) }}</div>
                
                <form method="POST" action="{{ route('member.loan-payments.store', $next_installment->id) }}" style="margin-top: 10px;">
                    @csrf
                    <input type="hidden" name="nominal_bayar" value="{{ $next_installment->total_angsuran }}">
                    <input type="hidden" name="metode_pembayaran" value="tunai">
                    <input type="hidden" name="tanggal_bayar" value="{{ date('Y-m-d') }}">
                    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Bayar angsuran ini sekarang?')">Bayar Sekarang</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
