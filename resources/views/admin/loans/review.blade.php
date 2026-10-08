@extends('layouts.app')
@section('header_title', 'Review Pengajuan Pinjaman')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
        <h4 style="margin: 0;">Pengajuan Pinjaman #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</h4>
        <x-status-badge :status="$loan->status" />
    </div>

    <table style="width: 100%; line-height: 1.8; margin-bottom: 20px;">
        <tr>
            <td style="color: #6b7280; width: 180px;">Pemohon</td>
            <td style="font-weight: bold;">{{ $loan->member->user->name }} (NIK: {{ $loan->member->nik }})</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Tujuan</td>
            <td>{{ $loan->tujuan_pinjaman }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Keterangan</td>
            <td>{{ $loan->keterangan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Jumlah / Tenor</td>
            <td>Rp {{ number_format($loan->jumlah_pinjaman, 2, ',', '.') }} / {{ $loan->tenor }} bulan</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Jasa / Bulan</td>
            <td>{{ $loan->persen_jasa }}%</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Bank Pencairan</td>
            <td>{{ $loan->bank_name }} - {{ $loan->bank_account_number }} a.n {{ $loan->bank_account_owner }}</td>
        </tr>
    </table>

    <x-validation-errors />

    <!-- DOKUMEN PENDUKUNG -->
    <div style="background: #fdfdfd; padding: 20px; border-radius: 6px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <h5 style="margin: 0; color: #374151; font-size: 1.05em; font-weight: 600;">Dokumen Pendukung</h5>
            <span style="font-size: 0.85em; color: #6b7280;">Total: {{ $loan->documents ? $loan->documents->count() : 0 }} dokumen</span>
        </div>

        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 10px 14px; border-radius: 4px; margin-bottom: 15px; font-size: 0.85em; color: #1e40af;">
            <i class="fa-solid fa-circle-info" style="margin-right: 4px;"></i>
            Dokumen yang belum sesuai harus diverifikasi sebelum pengajuan dapat disetujui.
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

    @if($loan->status === 'submitted')
        <!-- Action to Start Review -->
        <form method="POST" action="{{ route('admin.loans.start_review', $loan->id) }}">
            @csrf
            <button type="submit" class="btn btn-warning" style="width: 100%; background: #ffc107; color: #212529; font-weight: bold;">Mulai Review (Take Ownership)</button>
        </form>
    @elseif($loan->status === 'under_review')
        <!-- Actions to Approve or Reject -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #dee2e6;">
            <h5 style="margin-top: 0;">Keputusan Review</h5>
            
            <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" style="margin-bottom: 15px;">
                @csrf
                <button type="submit" class="btn btn-success" style="width: 100%; background: #28a745; color: white;" onclick="return confirm('Apakah Anda yakin menyetujui pinjaman ini?')">Setujui Pinjaman</button>
            </form>

            <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}">
                @csrf
                <div class="form-group">
                    <label for="rejection_reason">Alasan Penolakan</label>
                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" required placeholder="Wajib diisi jika menolak..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger" style="width: 100%;" onclick="return confirm('Tolak pinjaman ini secara permanen?')">Tolak Pinjaman</button>
            </form>
        </div>
    @else
        <div class="alert alert-info" style="background: #e2f3f5; color: #0c5460; padding: 15px; border-radius: 4px; text-align: center;">
            Status pinjaman saat ini: <strong>{{ strtoupper($loan->status) }}</strong>. <br>Tidak ada tindakan review lebih lanjut.
        </div>
    @endif
</div>
@endsection
