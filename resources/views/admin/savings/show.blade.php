@extends('layouts.app')
@section('header_title', 'Detail Simpanan & Pembayaran')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.savings.index') }}" class="btn btn-secondary" style="font-size: 0.9em;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Simpanan
        </a>
    </div>

    <x-validation-errors />

    <!-- CARD 1: INFORMASI KEWAJIBAN SIMPANAN -->
    <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 20px;">
            <h4 style="margin: 0; color: #1f2937;">Informasi Kewajiban Simpanan</h4>
            <div>
                Status Tagihan: <x-status-badge :status="$saving->status" />
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; line-height: 2;">
            <tr>
                <td style="color: #6b7280; width: 220px;">Nama Anggota</td>
                <td style="font-weight: 600; color: #111827;">{{ $saving->member->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">NIK</td>
                <td style="font-family: monospace; font-weight: 600;">{{ $saving->member->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Email</td>
                <td>{{ $saving->member->user->email ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Jenis Simpanan</td>
                <td>
                    <span class="badge {{ $saving->jenis_simpanan === 'pokok' ? 'badge-primary' : 'badge-info' }}" style="background: {{ $saving->jenis_simpanan === 'pokok' ? '#e0e7ff; color: #3730a3;' : '#dbeafe; color: #1e40af;' }}">
                        {{ ucfirst($saving->jenis_simpanan) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Periode Tagihan</td>
                <td>{{ $saving->periode ? date('F Y', strtotime($saving->periode)) : 'Satu Kali (Pokok)' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Nominal Kewajiban</td>
                <td style="font-size: 1.1em; font-weight: bold; color: var(--emerald-600, #059669);">
                    Rp {{ number_format($saving->nominal, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tanggal Jatuh Tempo</td>
                <td>{{ $saving->jatuh_tempo ? date('d/m/Y', strtotime($saving->jatuh_tempo)) : '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tanggal Dibuat</td>
                <td>{{ $saving->created_at ? $saving->created_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- PENDING PAYMENT VERIFICATION ACTION -->
    @php
        $pendingTrx = $saving->transactions->firstWhere('status', 'pending');
    @endphp

    @if($pendingTrx)
        <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-top: 4px solid #f59e0b;">
            <h4 style="margin: 0 0 10px 0; color: #b45309;"><i class="fa-solid fa-clock"></i> Verifikasi Pembayaran Online Menunggu Validasi</h4>
            <p style="color: #6b7280; font-size: 0.9em; margin-bottom: 15px;">
                Anggota telah mengunggah bukti pembayaran via transfer online. Periksa kesesuaian nominal dan bukti pembayaran sebelum memberikan keputusan.
            </p>

            <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.95em;">
                    <div><strong>Ref Pembayaran:</strong> <span style="font-family: monospace;">{{ $pendingTrx->payment_reference }}</span></div>
                    <div><strong>Nominal Bayar:</strong> Rp {{ number_format($pendingTrx->nominal_bayar, 2, ',', '.') }}</div>
                    <div><strong>Tanggal Bayar:</strong> {{ $pendingTrx->tanggal_bayar ? $pendingTrx->tanggal_bayar->format('d/m/Y H:i') : '-' }}</div>
                    <div>
                        <strong>Bukti Transfer:</strong> 
                        @if($pendingTrx->bukti_pembayaran)
                            <a href="{{ route('payments.savings.proof', $pendingTrx->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.85em;">
                                <i class="fa-solid fa-file-image"></i> Buka Bukti
                            </a>
                        @else
                            <span style="color: #9ca3af;">Tidak terlampir</span>
                        @endif
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <form method="POST" action="{{ route('admin.savings.approve', $pendingTrx->id) }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;" onclick="return confirm('Apakah Anda yakin menyetujui setoran simpanan ini?')">
                        <i class="fa-solid fa-check"></i> Setujui Pembayaran (Approve)
                    </button>
                </form>

                <div style="flex: 1;">
                    <form method="POST" action="{{ route('admin.savings.reject', $pendingTrx->id) }}">
                        @csrf
                        <div class="form-group" style="margin-bottom: 8px;">
                            <textarea name="catatan" class="form-control" rows="2" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" placeholder="Alasan penolakan (Wajib)..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width: 100%; padding: 8px;" onclick="return confirm('Tolak pembayaran simpanan ini?')">
                            <i class="fa-solid fa-times"></i> Tolak Pembayaran (Reject)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($saving->status === 'unpaid')
        <!-- OFFICE PAYMENT OPTION -->
        <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-top: 4px solid var(--emerald-500, #10b981);">
            <h4 style="margin: 0 0 10px 0; color: #065f46;"><i class="fa-solid fa-cash-register"></i> Pembayaran Langsung di Kantor Koperasi</h4>
            <p style="color: #6b7280; font-size: 0.9em; margin-bottom: 15px;">
                Jika anggota melakukan pembayaran langsung secara fisik di kantor teller, catat transaksi di bawah ini. Nominal pembayaran ditentukan otomatis sebesar <strong>Rp {{ number_format($saving->nominal, 2, ',', '.') }}</strong>.
            </p>

            <form method="POST" action="{{ route('admin.savings.office_pay', $saving->id) }}">
                @csrf
                <div class="form-group">
                    <label for="catatan_office">Catatan Penerimaan (Opsional):</label>
                    <input type="text" name="catatan" id="catatan_office" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" placeholder="Contoh: Diterima langsung oleh teller di kantor kas">
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;" onclick="return confirm('Catat pembayaran simpanan langsung di kantor?')">
                    <i class="fa-solid fa-receipt"></i> Catat Pembayaran Kantor (Success)
                </button>
            </form>
        </div>
    @endif

    <!-- CARD 2: RIWAYAT TRANSAKSI PEMBAYARAN -->
    <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
        <h4 style="margin: 0 0 15px 0; color: #1f2937;">Riwayat Transaksi & Rekonsiliasi</h4>

        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Waktu Bayar</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Ref Transaksi</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Metode</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Nominal</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Bukti</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center;">Status</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Verifikator</th>
                    <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($saving->transactions as $trx)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; color: #4b5563;">
                            {{ $trx->tanggal_bayar ? $trx->tanggal_bayar->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td style="padding: 10px; font-family: monospace; font-weight: bold;">
                            {{ $trx->payment_reference }}
                            @if($trx->external_reference)
                                <div style="font-size: 0.8em; color: #6b7280;">Ext: {{ $trx->external_reference }}</div>
                            @endif
                        </td>
                        <td style="padding: 10px;">{{ ucfirst($trx->metode_pembayaran) }}</td>
                        <td style="padding: 10px; font-weight: 600;">
                            Rp {{ number_format($trx->nominal_bayar, 2, ',', '.') }}
                        </td>
                        <td style="padding: 10px;">
                            @if($trx->bukti_pembayaran)
                                <a href="{{ route('payments.savings.proof', $trx->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.8em;">
                                    <i class="fa-solid fa-image"></i> Lihat
                                </a>
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <x-status-badge :status="$trx->status" />
                        </td>
                        <td style="padding: 10px; color: #4b5563;">
                            {{ $trx->admin->name ?? '-' }}
                        </td>
                        <td style="padding: 10px; color: #6b7280; font-size: 0.85em;">
                            {{ $trx->catatan ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 20px; text-align: center; color: #6b7280;">
                            Belum ada riwayat transaksi pembayaran untuk kewajiban simpanan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
