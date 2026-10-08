@extends('layouts.app')
@section('header_title', 'Review Pembayaran Simpanan')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Verifikasi
    </a>
</div>

<x-validation-errors />

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
    <!-- RINCIAN PEMBAYARAN -->
    <div class="card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin-top: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fa-solid fa-file-invoice text-emerald-600"></i> Informasi Pembayaran
        </h4>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px 0; color: #6b7280; width: 45%;">Ref Pembayaran</td>
                <td style="padding: 8px 0; font-family: monospace; font-weight: bold;">{{ $transaction->payment_reference }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Nama Anggota</td>
                <td style="padding: 8px 0; font-weight: 600;">{{ $transaction->saving->member->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">NIK Anggota</td>
                <td style="padding: 8px 0;">{{ $transaction->saving->member->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Jenis Simpanan</td>
                <td style="padding: 8px 0; font-weight: bold;">Simpanan {{ ucfirst($transaction->saving->jenis_simpanan) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Periode / Jatuh Tempo</td>
                <td style="padding: 8px 0;">{{ $transaction->saving->periode ? $transaction->saving->periode->format('m/Y') : '-' }} / {{ $transaction->saving->jatuh_tempo ? $transaction->saving->jatuh_tempo->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Nominal Kewajiban</td>
                <td style="padding: 8px 0; font-weight: bold; color: var(--emerald-600, #059669);">Rp {{ number_format($transaction->nominal_bayar, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Metode Pembayaran</td>
                <td style="padding: 8px 0;">{{ ucfirst($transaction->metode_pembayaran) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Tanggal Pengajuan</td>
                <td style="padding: 8px 0;">{{ $transaction->tanggal_bayar ? $transaction->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Status Saat Ini</td>
                <td style="padding: 8px 0;"><x-status-badge :status="$transaction->status" /></td>
            </tr>
        </table>

        <!-- BUKTI TRANSFER -->
        <h5 style="margin-bottom: 10px;">Bukti Transfer / Dokumen Pembayaran</h5>
        @if($transaction->bukti_pembayaran)
            <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; text-align: center; background: #f9fafb;">
                <div style="margin-bottom: 10px;">
                    <a href="{{ route('payments.savings.proof', $transaction->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka File di Tab Baru
                    </a>
                </div>
                <iframe src="{{ route('payments.savings.proof', $transaction->id) }}" style="width: 100%; height: 350px; border: 1px solid #ddd; border-radius: 4px;" title="Bukti Pembayaran"></iframe>
            </div>
        @else
            <p style="color: #dc2626;">Tidak ada file bukti pembayaran yang diunggah.</p>
        @endif
    </div>

    <!-- TINDAKAN VERIFIKASI -->
    <div class="card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); height: fit-content;">
        <h4 style="margin-top: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fa-solid fa-gavel text-emerald-600"></i> Keputusan Verifikasi
        </h4>

        @if($transaction->status === 'pending')
            <!-- SETUJUI -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                <h5 style="color: #166534; margin-top: 0; margin-bottom: 8px;">
                    <i class="fa-solid fa-circle-check"></i> Setujui Pembayaran
                </h5>
                <p style="font-size: 0.9em; color: #15803d; margin-bottom: 15px;">
                    Jika bukti transfer valid dan dana telah masuk ke rekening koperasi, setujui pembayaran ini. Kewajiban simpanan akan otomatis tercatat <strong>Lunas</strong>.
                </p>
                <form method="POST" action="{{ route('admin.payments.savings.approve', $transaction->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')" style="width: 100%;">
                        <i class="fa-solid fa-check"></i> Setujui Pembayaran
                    </button>
                </form>
            </div>

            <!-- TOLAK -->
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 20px;">
                <h5 style="color: #991b1b; margin-top: 0; margin-bottom: 8px;">
                    <i class="fa-solid fa-circle-xmark"></i> Tolak Pembayaran
                </h5>
                <p style="font-size: 0.9em; color: #b91c1c; margin-bottom: 15px;">
                    Jika bukti transfer palsu, tidak terbaca, atau dana belum masuk, tolak pembayaran ini. Berikan alasan penolakan yang jelas agar anggota dapat mengunggah ulang.
                </p>
                <form method="POST" action="{{ route('admin.payments.savings.reject', $transaction->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="catatan" style="color: #7f1d1d; font-size: 0.9em;">Alasan Penolakan <span style="color: red;">*</span></label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control" style="width: 100%; padding: 8px; border: 1px solid #f87171; border-radius: 4px;" required placeholder="Contoh: Bukti transfer buram / nominal tidak sesuai / dana belum masuk."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')" style="width: 100%;">
                        <i class="fa-solid fa-xmark"></i> Tolak Pembayaran
                    </button>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                Transaksi ini sudah diproses dengan status: <strong>{{ ucfirst($transaction->status) }}</strong>.
                @if($transaction->catatan)
                    <div style="margin-top: 8px;"><strong>Catatan:</strong> {{ $transaction->catatan }}</div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
