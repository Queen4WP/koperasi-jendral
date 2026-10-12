@extends('layouts.app')
@section('header_title', 'Review & Detail Pinjaman')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary" style="font-size: 0.9em; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pinjaman
        </a>
    </div>

    <x-validation-errors />

    @if($loan->status === 'rejected' || !empty($loan->rejection_reason))
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #dc2626;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: bold; font-size: 1.05em; margin-bottom: 6px;">
                <i class="fa-solid fa-circle-xmark"></i> Pengajuan Pinjaman Ditolak
            </div>
            <div><strong>Alasan Penolakan:</strong> {{ $loan->rejection_reason }}</div>
            @if($loan->rejected_at)
                <div style="font-size: 0.85em; color: #b91c1c; margin-top: 4px;">
                    Ditolak pada: {{ $loan->rejected_at->format('d/m/Y H:i') }}
                    @if($loan->reviewedBy) oleh {{ $loan->reviewedBy->name }} @endif
                </div>
            @endif
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <!-- CARD 1: INFORMASI PEMOHON (APPLICANT) -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
                <h5 style="margin: 0; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user" style="color: #4b5563;"></i> Data Pemohon
                </h5>
            </div>
            <table style="width: 100%; line-height: 1.9; font-size: 0.9em;">
                <tr>
                    <td style="color: #6b7280; width: 140px;">Nama Lengkap</td>
                    <td style="font-weight: 600; color: #111827;">{{ $loan->member->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">NIK</td>
                    <td style="font-family: monospace; font-weight: 600;">{{ $loan->member->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Email</td>
                    <td>{{ $loan->member->user->email ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Kontak / HP</td>
                    <td>{{ $loan->member->telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Pekerjaan</td>
                    <td>{{ $loan->member->pekerjaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Perusahaan / Usaha</td>
                    <td>{{ $loan->member->perusahaan ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Status Anggota</td>
                    <td><x-status-badge :status="$loan->member->status" /></td>
                </tr>
            </table>
        </div>

        <!-- CARD 2: INFORMASI PINJAMAN (LOAN DETAILS) -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
                <h5 style="margin: 0; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-invoice-dollar" style="color: #4b5563;"></i> Detail Pinjaman
                </h5>
                <x-status-badge :status="$loan->status" />
            </div>
            <table style="width: 100%; line-height: 1.9; font-size: 0.9em;">
                <tr>
                    <td style="color: #6b7280; width: 140px;">No. Pinjaman</td>
                    <td style="font-family: monospace; font-weight: 600; color: #1e3a8a;">
                        {{ $loan->nomor_pinjaman ?? ('#' . str_pad($loan->id, 5, '0', STR_PAD_LEFT)) }}
                    </td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Jumlah Pinjaman</td>
                    <td style="font-weight: 600; color: #111827;">Rp {{ number_format($loan->jumlah_pinjaman, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Tenor</td>
                    <td>{{ $loan->tenor }} bulan</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Persen Jasa</td>
                    <td>{{ $loan->persen_jasa }}%</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Total Jasa</td>
                    <td>Rp {{ number_format($loan->total_jasa, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Total Kewajiban</td>
                    <td style="font-weight: bold; color: #dc2626;">Rp {{ number_format($loan->total_kewajiban, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Angsuran / Bln</td>
                    <td style="font-weight: 600;">Rp {{ number_format($loan->angsuran_per_bulan, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Tujuan</td>
                    <td><strong>{{ $loan->tujuan_pinjaman }}</strong></td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Keterangan</td>
                    <td>{{ $loan->keterangan ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Bank Pencairan</td>
                    <td>{{ $loan->bank_name }} - {{ $loan->bank_account_number }} a.n {{ $loan->bank_account_owner }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280;">Waktu Pengajuan</td>
                    <td>{{ $loan->submitted_at ? $loan->submitted_at->format('d/m/Y H:i') : ($loan->created_at ? $loan->created_at->format('d/m/Y H:i') : '-') }}</td>
                </tr>
                @if($loan->reviewed_at)
                    <tr>
                        <td style="color: #6b7280;">Waktu Review</td>
                        <td>{{ $loan->reviewed_at->format('d/m/Y H:i') }} ({{ $loan->reviewedBy->name ?? 'Admin' }})</td>
                    </tr>
                @endif
                @if($loan->approved_at)
                    <tr>
                        <td style="color: #6b7280;">Waktu Disetujui</td>
                        <td style="color: #059669; font-weight: 600;">{{ $loan->approved_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- DOKUMEN PENDUKUNG -->
    <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <h5 style="margin: 0; color: #1f2937; font-size: 1.05em; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-folder-open" style="color: #4b5563;"></i> Dokumen Pendukung
            </h5>
            <span style="font-size: 0.85em; color: #6b7280;">Total: {{ $loan->documents ? $loan->documents->count() : 0 }} dokumen</span>
        </div>

        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 10px 14px; border-radius: 4px; margin-bottom: 15px; font-size: 0.85em; color: #1e40af;">
            <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i>
            Dokumen wajib (KTP, Bukti Penghasilan, Rekening Bank, dan Bukti Usaha jika Modal Usaha) harus diverifikasi berstatus <strong>Sesuai</strong> sebelum pengajuan dapat disetujui.
        </div>
        
        @if($loan->documents && $loan->documents->count() > 0)
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; font-size: 0.9em; text-align: left; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="padding: 10px; border-bottom: 1px solid #dee2e6; width: 22%;">Jenis Dokumen</th>
                            <th style="padding: 10px; border-bottom: 1px solid #dee2e6; width: 18%;">Status Verifikasi</th>
                            <th style="padding: 10px; border-bottom: 1px solid #dee2e6; width: 30%;">Catatan</th>
                            <th style="padding: 10px; border-bottom: 1px solid #dee2e6; width: 30%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->documents as $doc)
                            @php
                                $docLabel = match($doc->jenis_dokumen) {
                                    'ktp' => 'KTP',
                                    'bukti_penghasilan' => 'Bukti Penghasilan',
                                    'rekening_bank' => 'Rekening Bank',
                                    'bukti_usaha' => 'Bukti Usaha',
                                    'jaminan' => 'Jaminan',
                                    default => ucfirst($doc->jenis_dokumen)
                                };
                            @endphp
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #eee; vertical-align: middle;">
                                    <strong>{{ $docLabel }}</strong>
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee; vertical-align: middle;">
                                    <x-status-badge :status="$doc->status_verifikasi" />
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee; vertical-align: middle;">
                                    @if($doc->status_verifikasi === 'tidak_sesuai' && $doc->catatan)
                                        <div style="color: #dc2626; font-size: 0.9em; line-height: 1.4;">
                                            <strong>Catatan Admin:</strong> {{ $doc->catatan }}
                                        </div>
                                    @elseif($doc->catatan)
                                        <span style="color: #4b5563; font-size: 0.9em;">{{ $doc->catatan }}</span>
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee; vertical-align: middle;">
                                    <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                                        {{-- Authenticated Document View / Download Link --}}
                                        <a href="{{ route('member.loans.documents.show', ['loan' => $loan->id, 'document' => $doc->id]) }}" 
                                           target="_blank" 
                                           class="btn btn-secondary btn-sm" 
                                           style="padding: 4px 8px; font-size: 0.85em; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-eye"></i> Lihat
                                        </a>

                                        {{-- Verification actions when status is pending --}}
                                        @if($doc->status_verifikasi === 'pending')
                                            {{-- Sesuai Form --}}
                                            <form method="POST" action="{{ route('admin.loans.documents.verify', ['loan' => $loan->id, 'document' => $doc->id]) }}" style="display: inline-block; margin: 0;">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm" 
                                                        style="background-color: #059669; color: white; padding: 4px 8px; font-size: 0.85em; border-radius: 4px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" 
                                                        onclick="return confirm('Tandai dokumen {{ $docLabel }} sebagai Sesuai?')">
                                                    <i class="fa-solid fa-check"></i> Sesuai
                                                </button>
                                            </form>

                                            {{-- Tidak Sesuai Button (Trigger Modal) --}}
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm" 
                                                    style="padding: 4px 8px; font-size: 0.85em; display: inline-flex; align-items: center; gap: 4px; border-radius: 4px; border: none; cursor: pointer;" 
                                                    onclick="openRejectModal('{{ $doc->id }}')">
                                                <i class="fa-solid fa-xmark"></i> Tidak Sesuai
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Rejection Modals for Pending Documents --}}
            @foreach($loan->documents as $doc)
                @if($doc->status_verifikasi === 'pending')
                    @php
                        $modalDocLabel = match($doc->jenis_dokumen) {
                            'ktp' => 'KTP',
                            'bukti_penghasilan' => 'Bukti Penghasilan',
                            'rekening_bank' => 'Rekening Bank',
                            'bukti_usaha' => 'Bukti Usaha',
                            'jaminan' => 'Jaminan',
                            default => ucfirst($doc->jenis_dokumen)
                        };
                    @endphp
                    <div id="rejectModal-{{ $doc->id }}" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
                        <div style="background: white; border-radius: 8px; width: 90%; max-width: 480px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 15px;">
                                <h5 style="margin: 0; color: #dc2626; font-size: 1.1em; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Tolak Dokumen: {{ $modalDocLabel }}
                                </h5>
                                <button type="button" onclick="closeRejectModal('{{ $doc->id }}')" style="background: none; border: none; font-size: 1.4em; cursor: pointer; color: #9ca3af; line-height: 1;">&times;</button>
                            </div>
                            
                            <form method="POST" action="{{ route('admin.loans.documents.reject', ['loan' => $loan->id, 'document' => $doc->id]) }}">
                                @csrf
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label for="catatan-{{ $doc->id }}" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9em; color: #374151;">
                                        Catatan / Alasan Penolakan <span style="color: #dc2626;">*</span>
                                    </label>
                                    <textarea name="catatan" id="catatan-{{ $doc->id }}" class="form-control" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: inherit; font-size: 0.9em;" required placeholder="Contoh: Dokumen KTP buram dan nomor NIK tidak terbaca dengan jelas.">{{ old('catatan') }}</textarea>
                                    <small style="display: block; color: #6b7280; font-size: 0.8em; margin-top: 4px;">Minimal 3 karakter, maksimal 1000 karakter.</small>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; border-top: 1px solid #f3f4f6; padding-top: 12px;">
                                    <button type="button" onclick="closeRejectModal('{{ $doc->id }}')" class="btn btn-secondary" style="padding: 6px 14px; font-size: 0.875em; border-radius: 4px;">Batal</button>
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 14px; font-size: 0.875em; background-color: #dc2626; color: white; border-radius: 4px;">Tolak Dokumen</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div style="text-align: center; padding: 30px; color: #6b7280; background: #fafafa; border-radius: 6px; border: 1px dashed #d1d5db;">
                <i class="fa-solid fa-folder-open" style="font-size: 36px; margin-bottom: 10px; color: #9ca3af; display: block;"></i>
                <p style="margin: 0; font-size: 0.95em;">Belum ada dokumen pendukung yang diunggah anggota.</p>
            </div>
        @endif
    </div>

    <script>
        function openRejectModal(docId) {
            var modal = document.getElementById('rejectModal-' + docId);
            if (modal) {
                modal.style.display = 'flex';
                var textarea = document.getElementById('catatan-' + docId);
                if (textarea) textarea.focus();
            }
        }

        function closeRejectModal(docId) {
            var modal = document.getElementById('rejectModal-' + docId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openOfficePayModal(instId) {
            var modal = document.getElementById('officePayModal-' + instId);
            if (modal) {
                modal.style.display = 'flex';
                var input = document.getElementById('catatan-office-' + instId);
                if (input) input.focus();
            }
        }

        function closeOfficePayModal(instId) {
            var modal = document.getElementById('officePayModal-' + instId);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        });

        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-overlay').forEach(function(modal) {
                    modal.style.display = 'none';
                });
            }
        });
    </script>

    <!-- ACTION CARD SESUAI STATUS -->
    @if($loan->status === 'submitted')
        <!-- Action to Start Review -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
            <h5 style="margin-top: 0; margin-bottom: 8px; color: #1f2937;">Tindakan Review Pinjaman</h5>
            <p style="font-size: 0.9em; color: #4b5563; margin-bottom: 15px;">
                Pengajuan pinjaman ini berstatus <strong>Submitted</strong>. Klik tombol di bawah untuk mengambil alih proses verifikasi (berubah status menjadi <em>under_review</em>).
            </p>
            <form method="POST" action="{{ route('admin.loans.start_review', $loan->id) }}">
                @csrf
                <button type="submit" class="btn btn-warning" style="width: 100%; background: #ffc107; color: #212529; font-weight: bold; padding: 10px;">
                    <i class="fa-solid fa-play"></i> Mulai Review (Take Ownership)
                </button>
            </form>
        </div>
    @elseif($loan->status === 'under_review')
        <!-- Actions to Approve or Reject -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-top: 4px solid #3b82f6;">
            <h5 style="margin-top: 0; margin-bottom: 8px; color: #1f2937;">Keputusan Review Pinjaman</h5>
            <p style="font-size: 0.9em; color: #4b5563; margin-bottom: 15px;">
                Pastikan seluruh dokumen pendukung telah diverifikasi berstatus <strong>Sesuai</strong> sebelum menyetujui pinjaman. Jika menolak, alasan penolakan wajib diisi.
            </p>
            
            <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" style="margin-bottom: 20px;">
                @csrf
                <button type="submit" class="btn btn-success" style="width: 100%; background: #059669; color: white; font-weight: 600; padding: 10px;" onclick="return confirm('Apakah Anda yakin menyetujui pinjaman ini?')">
                    <i class="fa-solid fa-check"></i> Setujui Pinjaman (Approve)
                </button>
            </form>

            <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}">
                @csrf
                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="rejection_reason" style="font-weight: 600; font-size: 0.9em; color: #374151;">
                        Alasan Penolakan <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-family: inherit;" required placeholder="Tuliskan alasan penolakan pinjaman secara jelas..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger" style="width: 100%; font-weight: 600; padding: 10px;" onclick="return confirm('Tolak pinjaman ini?')">
                    <i class="fa-solid fa-xmark"></i> Tolak Pinjaman (Reject)
                </button>
            </form>
        </div>
    @elseif($loan->status === 'approved')
        <!-- Action to Disburse -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-top: 4px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h5 style="margin-top: 0; margin-bottom: 4px; color: #065f46; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Pinjaman Disetujui — Siap Dicairkan
                    </h5>
                    <p style="font-size: 0.9em; color: #4b5563; margin: 0;">
                        Pengajuan pinjaman ini telah disetujui. Lakukan proses pencairan dana (disbursement) untuk mentransfer dana ke rekening anggota dan mengaktifkan jadwal angsuran.
                    </p>
                </div>
                <a href="{{ route('admin.loans.disburse', $loan->id) }}" class="btn btn-primary" style="padding: 10px 18px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-money-bill-transfer"></i> Proses Pencairan Dana (Disburse)
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-info" style="background: #e2f3f5; color: #0c5460; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 24px;">
            Status pinjaman saat ini: <strong>{{ strtoupper($loan->status) }}</strong>.
        </div>
    @endif

    @if($loan->disbursement)
        <!-- INFORMASI PENCAIRAN DANA (DISBURSEMENT) -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-left: 4px solid #059669;">
            <h5 style="margin-top: 0; margin-bottom: 12px; color: #065f46; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-receipt" style="color: #059669;"></i> Informasi Pencairan Dana (Disbursement)
            </h5>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 0.9em;">
                <div>
                    <span style="color: #6b7280; display: block;">Tanggal Pencairan:</span>
                    <strong>{{ $loan->disbursement->tanggal_pencairan ? $loan->disbursement->tanggal_pencairan->format('d/m/Y') : '-' }}</strong>
                </div>
                <div>
                    <span style="color: #6b7280; display: block;">Nominal Dicairkan:</span>
                    <strong style="color: #059669;">Rp {{ number_format($loan->disbursement->nominal, 2, ',', '.') }}</strong>
                </div>
                <div>
                    <span style="color: #6b7280; display: block;">Nomor Referensi:</span>
                    <span style="font-family: monospace; font-weight: 600;">{{ $loan->disbursement->nomor_referensi }}</span>
                </div>
                <div>
                    <span style="color: #6b7280; display: block;">Bukti / Catatan:</span>
                    <span>{{ $loan->disbursement->bukti_transfer ?: '-' }}</span>
                </div>
                <div>
                    <span style="color: #6b7280; display: block;">Petugas Admin:</span>
                    <span>{{ $loan->disbursement->admin->name ?? 'Admin' }}</span>
                </div>
            </div>
        </div>
    @endif

    @if($loan->installments && $loan->installments->count() > 0)
        <!-- JADWAL ANGSURAN PINJAMAN (GENERATED INSTALLMENTS) -->
        <div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h5 style="margin: 0; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calendar-days" style="color: #4b5563;"></i> Jadwal Angsuran Pinjaman
                </h5>
                <span style="font-size: 0.85em; color: #6b7280;">Total Tenor: {{ $loan->installments->count() }} Bulan</span>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; font-size: 0.9em; text-align: left;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; width: 50px;">Bulan</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Jatuh Tempo</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Pokok</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Jasa</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Total Tagihan</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Denda</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center;">Status</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->installments as $inst)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; text-align: center; font-weight: 600;">{{ $inst->angsuran_ke }}</td>
                                <td style="padding: 10px; color: #4b5563;">{{ date('d/m/Y', strtotime($inst->jatuh_tempo)) }}</td>
                                <td style="padding: 10px;">Rp {{ number_format($inst->pokok_angsuran, 2, ',', '.') }}</td>
                                <td style="padding: 10px;">Rp {{ number_format($inst->jasa_angsuran, 2, ',', '.') }}</td>
                                <td style="padding: 10px; font-weight: 600; color: #1f2937;">Rp {{ number_format($inst->total_tagihan, 2, ',', '.') }}</td>
                                <td style="padding: 10px; color: #6b7280;">Rp {{ number_format($inst->denda, 2, ',', '.') }}</td>
                                <td style="padding: 10px; text-align: center;">
                                    <x-status-badge :status="$inst->status" />
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    @if($inst->status === 'paid')
                                        <span style="color: #059669; font-weight: 600; font-size: 0.85em; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-check"></i> Lunas
                                        </span>
                                    @else
                                        <button type="button" class="btn btn-sm" onclick="openOfficePayModal('{{ $inst->id }}')" style="padding: 4px 10px; font-size: 0.82em; display: inline-flex; align-items: center; gap: 4px; background: #059669; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                                            <i class="fa-solid fa-cash-register"></i> Bayar Kantor
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($loan->installments as $inst)
                @if($inst->status !== 'paid')
                    <div id="officePayModal-{{ $inst->id }}" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
                        <div style="background: white; border-radius: 8px; width: 90%; max-width: 500px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 15px;">
                                <h5 style="margin: 0; color: #065f46; font-size: 1.1em; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-cash-register" style="color: #059669;"></i> Pembayaran Angsuran di Kantor
                                </h5>
                                <button type="button" onclick="closeOfficePayModal('{{ $inst->id }}')" style="background: none; border: none; font-size: 1.4em; cursor: pointer; color: #9ca3af; line-height: 1;">&times;</button>
                            </div>

                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; font-size: 0.9em;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="color: #4b5563;">Angsuran Ke:</span>
                                    <strong style="color: #1f2937;">{{ $inst->angsuran_ke }} dari {{ $loan->tenor }} Bulan</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="color: #4b5563;">Jatuh Tempo:</span>
                                    <span style="font-weight: 600;">{{ date('d/m/Y', strtotime($inst->jatuh_tempo)) }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="color: #4b5563;">Pokok & Jasa:</span>
                                    <span>Rp {{ number_format($inst->pokok_angsuran, 2, ',', '.') }} + Rp {{ number_format($inst->jasa_angsuran, 2, ',', '.') }}</span>
                                </div>
                                @if($inst->denda > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; color: #dc2626;">
                                    <span>Denda Keterlambatan:</span>
                                    <span>Rp {{ number_format($inst->denda, 2, ',', '.') }}</span>
                                </div>
                                @endif
                                <div style="display: flex; justify-content: space-between; border-top: 1px dashed #86efac; padding-top: 6px; font-size: 1.05em;">
                                    <strong style="color: #065f46;">Total Tagihan:</strong>
                                    <strong style="color: #065f46;">Rp {{ number_format($inst->total_tagihan, 2, ',', '.') }}</strong>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.installments.office_pay', $inst->id) }}">
                                @csrf
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label for="catatan-office-{{ $inst->id }}" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9em; color: #374151;">
                                        Catatan Penerimaan (Opsional)
                                    </label>
                                    <input type="text" name="catatan" id="catatan-office-{{ $inst->id }}" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: inherit; font-size: 0.9em;" placeholder="Contoh: Diterima langsung oleh teller kas kantor koperasi">
                                    <small style="display: block; color: #6b7280; font-size: 0.8em; margin-top: 4px;">Maksimal 500 karakter.</small>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; border-top: 1px solid #f3f4f6; padding-top: 12px;">
                                    <button type="button" onclick="closeOfficePayModal('{{ $inst->id }}')" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.875em; border-radius: 4px;">Batal</button>
                                    <button type="submit" class="btn btn-success" style="padding: 8px 16px; font-size: 0.875em; background-color: #059669; color: white; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;" onclick="return confirm('Konfirmasi pencatatan pembayaran angsuran ke-{{ $inst->angsuran_ke }} sebesar Rp {{ number_format($inst->total_tagihan, 2, ',', '.') }} secara tunai di kantor?')">
                                        <i class="fa-solid fa-receipt"></i> Catat Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
