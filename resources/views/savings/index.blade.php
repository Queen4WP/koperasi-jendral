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
                            @php
                                $pendingTrx = $saving->transactions->firstWhere('status', 'pending');
                                $lastTrx = $saving->transactions->sortByDesc('created_at')->first();
                            @endphp

                            @if($pendingTrx)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <x-status-badge status="pending" />
                                    @if($pendingTrx->bukti_pembayaran)
                                        <a href="{{ route('payments.savings.proof', $pendingTrx->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.8em;">
                                            <i class="fa-solid fa-eye"></i> Bukti
                                        </a>
                                    @endif
                                </div>
                            @else
                                @if($lastTrx && $lastTrx->status === 'failed')
                                    <div style="margin-bottom: 6px;">
                                        <span class="badge badge-danger">Ditolak</span>
                                        <div style="font-size: 0.8em; color: #dc2626; margin-top: 2px;">
                                            <strong>Alasan:</strong> {{ $lastTrx->catatan ?? '-' }}
                                        </div>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modal-pay-{{ $saving->id }}').style.display = 'block'">
                                    <i class="fa-solid fa-upload"></i> Bayar Online
                                </button>

                                <!-- MODAL UPLOAD BUKTI -->
                                <div id="modal-pay-{{ $saving->id }}" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
                                    <div style="background: white; width: 90%; max-width: 480px; margin: 10% auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                            <h4 style="margin: 0;">Upload Bukti Pembayaran</h4>
                                            <button type="button" onclick="document.getElementById('modal-pay-{{ $saving->id }}').style.display = 'none'" style="border: none; background: transparent; font-size: 1.2em; cursor: pointer;">&times;</button>
                                        </div>
                                        <p style="font-size: 0.9em; color: #4b5563; margin-bottom: 15px;">
                                            Simpanan {{ ucfirst($saving->jenis_simpanan) }} - Tagihan: <strong>Rp {{ number_format($saving->nominal, 2, ',', '.') }}</strong>
                                        </p>
                                        <form method="POST" action="{{ route('member.savings.pay', $saving->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <label for="bukti_pembayaran_{{ $saving->id }}" style="font-size: 0.9em;">File Bukti Transfer (JPG, PNG, PDF max 2MB) <span style="color: red;">*</span></label>
                                                <input type="file" name="bukti_pembayaran" id="bukti_pembayaran_{{ $saving->id }}" class="form-control" style="width: 100%;" required accept=".jpg,.jpeg,.png,.pdf">
                                            </div>
                                            <div class="form-group">
                                                <label for="catatan_{{ $saving->id }}" style="font-size: 0.9em;">Catatan Tambahan (Opsional)</label>
                                                <input type="text" name="catatan" id="catatan_{{ $saving->id }}" class="form-control" style="width: 100%;" placeholder="Contoh: Transfer via Mandiri a.n...">
                                            </div>
                                            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-pay-{{ $saving->id }}').style.display = 'none'">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Kirim Bukti</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @else
                            <span style="color: #059669; font-weight: bold;"><i class="fa-solid fa-check"></i> Lunas</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
