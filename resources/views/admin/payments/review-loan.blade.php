@extends('layouts.app')
@section('header_title', 'Review Pembayaran Angsuran Pinjaman')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Verifikasi
    </a>
</div>

<x-validation-errors />

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
    <!-- RINCIAN PEMBAYARAN ANGSURAN -->
    <div class="card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin-top: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px;">
            <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Informasi Tagihan & Pembayaran
        </h4>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px 0; color: #6b7280; width: 45%;">Ref Pembayaran</td>
                <td style="padding: 8px 0; font-family: monospace; font-weight: bold;">{{ $payment->payment_reference }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Nama Anggota</td>
                <td style="padding: 8px 0; font-weight: 600;">{{ $payment->installment->loan->member->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">NIK Anggota</td>
                <td style="padding: 8px 0;">{{ $payment->installment->loan->member->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">No. Pinjaman</td>
                <td style="padding: 8px 0; font-weight: bold;">Pinjaman #{{ str_pad($payment->installment->loan_id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Angsuran Ke</td>
                <td style="padding: 8px 0; font-weight: bold;">Bulan Ke-{{ $payment->installment->angsuran_ke }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Rincian Angsuran</td>
                <td style="padding: 8px 0; font-size: 0.9em;">
                    Pokok: Rp {{ number_format($payment->installment->pokok_angsuran, 2, ',', '.') }}<br>
                    Jasa: Rp {{ number_format($payment->installment->jasa_angsuran, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Total Tagihan</td>
                <td style="padding: 8px 0; font-weight: bold; color: var(--emerald-600, #059669);">Rp {{ number_format($payment->nominal_bayar, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Metode Pembayaran</td>
                <td style="padding: 8px 0;">{{ ucfirst($payment->metode_pembayaran) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Tanggal Pengajuan</td>
                <td style="padding: 8px 0;">{{ $payment->tanggal_bayar ? $payment->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6b7280;">Status Saat Ini</td>
                <td style="padding: 8px 0;"><x-status-badge :status="$payment->status" /></td>
            </tr>
        </table>

        <!-- BUKTI TRANSFER -->
        <h5 style="margin-bottom: 10px;">Bukti Transfer / Dokumen Pembayaran</h5>
        @if($payment->bukti_pembayaran)
            <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 15px; text-align: center; background: #f9fafb;">
                <div style="margin-bottom: 10px;">
                    <a href="{{ route('payments.loans.proof', $payment->id) }}" target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka File di Tab Baru
                    </a>
                </div>
                <iframe src="{{ route('payments.loans.proof', $payment->id) }}" style="width: 100%; height: 350px; border: 1px solid #ddd; border-radius: 4px;" title="Bukti Pembayaran"></iframe>
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

        @if($payment->status === 'pending')
            <!-- SETUJUI -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 20px; margin-bottom: 25px;">
                <h5 style="color: #166534; margin-top: 0; margin-bottom: 8px;">
                    <i class="fa-solid fa-circle-check"></i> Setujui Pembayaran
                </h5>
                <p style="font-size: 0.9em; color: #15803d; margin-bottom: 15px;">
                    Jika transfer angsuran telah masuk dan sesuai, setujui pembayaran ini. Angsuran akan otomatis tercatat <strong>Lunas</strong> dan jika seluruh angsuran telah lunas, status pinjaman akan beralih ke <strong>Lunas</strong>.
                </p>
                <form method="POST" action="{{ route('admin.payments.loans.approve', $payment->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran angsuran ini?')" style="width: 100%;">
                        <i class="fa-solid fa-check"></i> Setujui Pembayaran Angsuran
                    </button>
                </form>
            </div>

            <!-- TOLAK -->
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 20px;">
                <h5 style="color: #991b1b; margin-top: 0; margin-bottom: 8px;">
                    <i class="fa-solid fa-circle-xmark"></i> Tolak Pembayaran
                </h5>
                <p style="font-size: 0.9em; color: #b91c1c; margin-bottom: 15px;">
                    Jika bukti transfer tidak valid atau dana belum masuk, tolak pembayaran ini dan sertakan alasan penolakan. Tagihan angsuran akan tetap berstatus belum bayar.
                </p>
                <form method="POST" action="{{ route('admin.payments.loans.reject', $payment->id) }}">
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
                Pembayaran ini sudah diproses dengan status: <strong>{{ ucfirst($payment->status) }}</strong>.
                @if($payment->catatan)
                    <div style="margin-top: 8px;"><strong>Catatan:</strong> {{ $payment->catatan }}</div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
