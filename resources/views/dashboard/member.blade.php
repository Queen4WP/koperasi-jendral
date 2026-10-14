@extends('layouts.app')
@section('header_title', 'Dashboard Anggota')

@section('content')
<!-- DUAL-LEDGER DISPLAY (BUKU TABUNGAN VS KARTU PINJAMAN) -->
<div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
        <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
            <i class="fa-solid fa-scale-balanced" style="color: #059669; margin-right: 8px;"></i>
            Dual-Ledger Keuangan Anggota (Buku Tabungan & Kartu Pinjaman)
        </h4>
        <span style="font-size: 11px; font-weight: 700; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 999px;">
            Saldo Simpanan Terproteksi (Tidak Terdebet Minus)
        </span>
    </div>
    <p style="margin: 0 0 16px 0; font-size: 13px; color: #64748b; line-height: 1.5;">
        Sistem memisahkan pembukuan antara <strong>Buku Tabungan</strong> (dana hak anggota) dan <strong>Kartu Pinjaman</strong> (kewajiban modal yang dicairkan). Pencairan pinjaman modal tidak mendebet simpanan menjadi minus.
    </p>

    @if(isset($approved_loan) && $approved_loan)
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #10b981; padding: 14px 18px; border-radius: 10px; margin-bottom: 18px; color: #166534; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-circle-check" style="font-size: 24px; color: #10b981;"></i>
                <div>
                    <strong style="font-size: 14px;">Pengajuan Pinjaman Disetujui Finance (Nomor: {{ $approved_loan->nomor_pinjaman }})</strong>
                    <div style="font-size: 12px; color: #15803d; margin-top: 2px;">
                        Plafon pinjaman sebesar <strong>Rp {{ number_format($approved_loan->jumlah_pinjaman, 0, ',', '.') }}</strong> telah disetujui. Silakan kunjungi <strong>Loket Kasir Kantor KOPIN</strong> untuk mengambil pencairan tunai & menandatangani tanda terima fisik (BKK).
                    </div>
                </div>
            </div>
            <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase;">
                Siap Dicairkan Kasir
            </span>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <!-- LEDGER 1: BUKU TABUNGAN ANGGOTA -->
        <div style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0; border-radius: 14px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 12px; font-weight: 800; color: #047857; text-transform: uppercase;">
                    <i class="fa-solid fa-book-bookmark"></i> Buku Tabungan Simpanan
                </span>
                <span style="font-size: 11px; background: #dcfce7; color: #166534; font-weight: 700; padding: 2px 8px; border-radius: 999px;">Aset Anggota</span>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #065f46; margin-bottom: 12px;">
                Rp {{ number_format($total_saldo_tabungan ?? 0, 2, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: #047857; border-top: 1px dashed #a7f3d0; padding-top: 10px; line-height: 1.6;">
                <div>&bull; Simpanan Wajib: <strong>Rp {{ number_format($total_simpanan_wajib ?? 0, 0, ',', '.') }}</strong></div>
                <div>&bull; Simpanan Pokok: <strong>Rp {{ number_format($total_simpanan_pokok ?? 0, 0, ',', '.') }}</strong></div>
                <div>&bull; Simpanan Sukarela: <strong>Rp {{ number_format($total_simpanan_sukarela ?? 0, 0, ',', '.') }}</strong></div>
            </div>
        </div>

        <!-- LEDGER 2: KARTU PINJAMAN MODAL -->
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%); border: 1px solid #bfdbfe; border-radius: 14px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 12px; font-weight: 800; color: #1d4ed8; text-transform: uppercase;">
                    <i class="fa-solid fa-credit-card"></i> Kartu Pinjaman Modal
                </span>
                <span style="font-size: 11px; background: #dbeafe; color: #1e40af; font-weight: 700; padding: 2px 8px; border-radius: 999px;">Kewajiban Angsuran</span>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #1e3a8a; margin-bottom: 12px;">
                Rp {{ number_format($sisa_pinjaman ?? 0, 2, ',', '.') }}
            </div>
            <div style="font-size: 12px; color: #1e40af; border-top: 1px dashed #bfdbfe; padding-top: 10px; line-height: 1.6;">
                <div>&bull; Status: <strong>{{ ($sisa_pinjaman ?? 0) > 0 ? 'Pinjaman Aktif Berjalan' : (isset($approved_loan) && $approved_loan ? 'Disetujui (Menunggu Pencairan Kasir)' : 'Tidak Ada Pinjaman Aktif') }}</strong></div>
                @if(isset($approved_loan) && $approved_loan && ($sisa_pinjaman ?? 0) == 0)
                    <div style="color: #b45309; font-weight: 700; margin-top: 2px;">&bull; Plafon Disetujui: Rp {{ number_format($approved_loan->jumlah_pinjaman, 0, ',', '.') }} (Siap Ambil di Loket Kasir)</div>
                @endif
                <div>&bull; Metode Angsuran: <strong>Tunai di Loket Kasir / Transfer</strong></div>
                <div>&bull; Tanggal Pelunasan: Sesuai jadwal angsuran bulanan</div>
            </div>
        </div>
    </div>
</div>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="margin-top: 0; font-size: 15px; font-weight: 800; color: #0f172a;">Status Keanggotaan</h4>
        <div style="margin-bottom: 12px;">
            Status: <x-status-badge :status="$member->status ?? 'pending'" />
        </div>
        @if(!isset($member) || $member->status !== 'active')
            <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; font-size: 13px;">
                Akun Anda belum aktif atau masih dalam peninjauan admin. Fitur transaksi mungkin dibatasi.
            </div>
        @else
            <div style="font-size: 13px; color: #475569; line-height: 1.6;">
                <div><strong>NIK:</strong> {{ $member->nik }}</div>
                <div><strong>Pekerjaan:</strong> {{ $member->pekerjaan }}</div>
                <div><strong>Telepon:</strong> {{ $member->telepon }}</div>
            </div>
        @endif
    </div>

    <div class="card" style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="margin-top: 0; font-size: 15px; font-weight: 800; color: #0f172a;">Jadwal Angsuran Terdekat</h4>
        @if(!isset($next_installment))
            <p style="color: #6b7280; font-size: 13px; margin: 0;">Tidak ada angsuran terdekat yang belum lunas.</p>
        @else
            <div style="border: 1px solid #e5e7eb; padding: 16px; border-radius: 10px; background: #fafafa;">
                <div style="font-size: 12px; color: #6b7280;">Bulan ke-{{ $next_installment->angsuran_ke }}</div>
                <div style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 4px 0;">Rp {{ number_format($next_installment->total_angsuran, 2, ',', '.') }}</div>
                <div style="font-size: 13px; color: #dc2626; font-weight: 600;"><i class="fa-regular fa-calendar"></i> Jatuh Tempo: {{ date('d M Y', strtotime($next_installment->jatuh_tempo)) }}</div>
                
                @php
                    $pendingPay = $next_installment->payments ? $next_installment->payments->firstWhere('status', 'pending') : null;
                @endphp
                <div style="margin-top: 14px;">
                    @if($pendingPay)
                        <span class="badge badge-warning" style="padding: 6px 12px; font-size: 12px;">Menunggu Verifikasi</span>
                    @else
                        <a href="{{ route('member.loans.show', $next_installment->loan_id) }}" class="btn btn-primary btn-sm" style="padding: 8px 16px; font-size: 13px;">
                            <i class="fa-solid fa-receipt"></i> Detail & Pembayaran Loket
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
