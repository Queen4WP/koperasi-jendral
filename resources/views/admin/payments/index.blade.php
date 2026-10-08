@extends('layouts.app')
@section('header_title', 'Verifikasi Pembayaran')

@section('content')
<div style="margin-bottom: 30px;">
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
        <h4 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-vault text-emerald-600"></i>
            Pembayaran Simpanan Menunggu Verifikasi
        </h4>
        <p style="color: #6b7280; font-size: 0.9em;">Daftar setoran simpanan pokok dan wajib dari anggota yang menunggu validasi admin.</p>
        
        <table class="table" style="font-size: 0.9em;">
            <thead>
                <tr>
                    <th>Ref Pembayaran</th>
                    <th>Nama Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Nominal</th>
                    <th>Tanggal Bayar</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($pendingSavings) && count($pendingSavings) > 0)
                    @foreach($pendingSavings as $savingTrx)
                        <tr>
                            <td style="font-family: monospace; font-weight: bold;">{{ $savingTrx->payment_reference }}</td>
                            <td>{{ $savingTrx->saving->member->user->name ?? '-' }} (NIK: {{ $savingTrx->saving->member->nik ?? '-' }})</td>
                            <td>{{ ucfirst($savingTrx->saving->jenis_simpanan) }}</td>
                            <td style="font-weight: bold;">Rp {{ number_format($savingTrx->nominal_bayar, 2, ',', '.') }}</td>
                            <td>{{ $savingTrx->tanggal_bayar ? $savingTrx->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($savingTrx->bukti_pembayaran)
                                    <a href="{{ route('payments.savings.proof', $savingTrx->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.8em;">
                                        <i class="fa-solid fa-image"></i> Lihat
                                    </a>
                                @else
                                    <span style="color: #9ca3af;">Tidak ada</span>
                                @endif
                            </td>
                            <td><x-status-badge :status="$savingTrx->status" /></td>
                            <td>
                                <a href="{{ route('admin.payments.savings.show', $savingTrx->id) }}" class="btn btn-primary btn-sm">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" style="text-align: center; color: #6b7280; padding: 20px;">
                            Tidak ada pembayaran simpanan yang menunggu verifikasi saat ini.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i>
            Pembayaran Angsuran Pinjaman Menunggu Verifikasi
        </h4>
        <p style="color: #6b7280; font-size: 0.9em;">Daftar pembayaran angsuran kredit anggota yang menunggu konfirmasi bukti transfer.</p>

        <table class="table" style="font-size: 0.9em;">
            <thead>
                <tr>
                    <th>Ref Pembayaran</th>
                    <th>Nama Anggota</th>
                    <th>No. Pinjaman</th>
                    <th>Angsuran Ke</th>
                    <th>Total Tagihan</th>
                    <th>Tanggal Bayar</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($pendingLoans) && count($pendingLoans) > 0)
                    @foreach($pendingLoans as $loanPay)
                        <tr>
                            <td style="font-family: monospace; font-weight: bold;">{{ $loanPay->payment_reference }}</td>
                            <td>{{ $loanPay->installment->loan->member->user->name ?? '-' }} (NIK: {{ $loanPay->installment->loan->member->nik ?? '-' }})</td>
                            <td>#{{ str_pad($loanPay->installment->loan_id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>Ke-{{ $loanPay->installment->angsuran_ke }}</td>
                            <td style="font-weight: bold;">Rp {{ number_format($loanPay->nominal_bayar, 2, ',', '.') }}</td>
                            <td>{{ $loanPay->tanggal_bayar ? $loanPay->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($loanPay->bukti_pembayaran)
                                    <a href="{{ route('payments.loans.proof', $loanPay->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.8em;">
                                        <i class="fa-solid fa-image"></i> Lihat
                                    </a>
                                @else
                                    <span style="color: #9ca3af;">Tidak ada</span>
                                @endif
                            </td>
                            <td><x-status-badge :status="$loanPay->status" /></td>
                            <td>
                                <a href="{{ route('admin.payments.loans.show', $loanPay->id) }}" class="btn btn-primary btn-sm">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center; color: #6b7280; padding: 20px;">
                            Tidak ada pembayaran angsuran yang menunggu verifikasi saat ini.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
