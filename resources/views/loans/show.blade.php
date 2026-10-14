@extends('layouts.app')
@section('header_title', 'Detail Pinjaman')

@section('content')
@if($loan->status === 'rejected')
    <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 18px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #f87171;">
        <h5 style="margin: 0 0 6px 0; font-size: 16px; font-weight: 800;"><i class="fa-solid fa-circle-xmark"></i> Pengajuan Pinjaman Ditolak</h5>
        <p style="margin: 0; font-size: 14px;"><strong>Alasan Penolakan:</strong> {{ $loan->rejection_reason ?? 'Pengajuan belum memenuhi syarat likuiditas atau kelayakan koperasi.' }}</p>
    </div>
@else
    <!-- SIMPLIFIED LIVE STATUS TRACKING WIDGET -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                <i class="fa-solid fa-route" style="color: #2563eb; margin-right: 8px;"></i>
                Live Tracking Pengajuan Pinjaman Modal
            </h4>
            <span style="font-size: 12px; font-weight: 700; background: #eff6ff; color: #1e40af; padding: 4px 12px; border-radius: 999px;">
                Status Terkini: {{ strtoupper(str_replace('_', ' ', $loan->status)) }}
            </span>
        </div>

        @php
            $isUnderReview = in_array($loan->status, ['submitted', 'under_review']);
            $isApproved = ($loan->status === 'approved');
            $isDisbursed = in_array($loan->status, ['disbursed', 'active', 'paid_off']);
        @endphp

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; position: relative;">
            <!-- STEP 1: UNDER REVIEW -->
            <div style="background: {{ $isUnderReview ? '#eff6ff' : ($isApproved || $isDisbursed ? '#f0fdf4' : '#f8fafc') }}; border: 2px solid {{ $isUnderReview ? '#3b82f6' : ($isApproved || $isDisbursed ? '#22c55e' : '#cbd5e1') }}; border-radius: 12px; padding: 16px; position: relative;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: {{ $isApproved || $isDisbursed ? '#22c55e' : ($isUnderReview ? '#3b82f6' : '#94a3b8') }}; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                        @if($isApproved || $isDisbursed) <i class="fa-solid fa-check"></i> @else 1 @endif
                    </div>
                    <span style="font-size: 13px; font-weight: 800; color: {{ $isUnderReview ? '#1e40af' : ($isApproved || $isDisbursed ? '#166534' : '#64748b') }};">
                        UNDER REVIEW
                    </span>
                </div>
                <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                    Pengajuan masuk antrean review modal kerja ke POV Finance & validasi cadangan 10%.
                </div>
            </div>

            <!-- STEP 2: APPROVED -->
            <div style="background: {{ $isApproved ? '#eff6ff' : ($isDisbursed ? '#f0fdf4' : '#f8fafc') }}; border: 2px solid {{ $isApproved ? '#3b82f6' : ($isDisbursed ? '#22c55e' : '#cbd5e1') }}; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: {{ $isDisbursed ? '#22c55e' : ($isApproved ? '#3b82f6' : '#94a3b8') }}; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                        @if($isDisbursed) <i class="fa-solid fa-check"></i> @else 2 @endif
                    </div>
                    <span style="font-size: 13px; font-weight: 800; color: {{ $isApproved ? '#1e40af' : ($isDisbursed ? '#166534' : '#64748b') }};">
                        APPROVED
                    </span>
                </div>
                <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                    @if($isApproved || $isDisbursed)
                        Disetujui oleh Finance.
                        @if($loan->approval_note)
                            <div style="margin-top: 4px; font-size: 11px; background: rgba(37,99,235,0.08); padding: 4px 8px; border-radius: 6px; color: #1e40af;">
                                <strong>Note:</strong> "{{ $loan->approval_note }}"
                            </div>
                        @endif
                    @else
                        Menunggu persetujuan Finance dan konfirmasi kesiapan kas di loket kasir.
                    @endif
                </div>
            </div>

            <!-- STEP 3: DISBURSED -->
            <div style="background: {{ $isDisbursed ? '#ecfdf5' : '#f8fafc' }}; border: 2px solid {{ $isDisbursed ? '#059669' : '#cbd5e1' }}; border-radius: 12px; padding: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <div style="width: 26px; height: 26px; border-radius: 50%; background: {{ $isDisbursed ? '#059669' : '#94a3b8' }}; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                        @if($isDisbursed) <i class="fa-solid fa-hand-holding-dollar"></i> @else 3 @endif
                    </div>
                    <span style="font-size: 13px; font-weight: 800; color: {{ $isDisbursed ? '#065f46' : '#64748b') }};">
                        DISBURSED
                    </span>
                </div>
                <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                    @if($isDisbursed)
                        <strong style="color: #047857;">Uang tunai telah dicairkan di kantor!</strong> Tanda terima fisik telah ditandatangani di loket kasir.
                    @elseif($isApproved)
                        <strong style="color: #d97706;">Uang tunai siap diambil di kantor!</strong> Kunjungi loket kasir KOPIN untuk mengambil dana fisik.
                    @else
                        Uang tunai dapat diambil di loket kasir kantor setelah disetujui.
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <!-- INFORMASI PINJAMAN -->
    <div class="card" style="flex: 1; min-width: 300px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
            <h4 style="margin: 0;">Informasi Pengajuan</h4>
            <x-status-badge :status="$loan->status" />
        </div>
        
        <table style="width: 100%; line-height: 1.8;">
            <tr>
                <td style="color: #6b7280; width: 150px;">Nomor Pinjaman</td>
                <td style="font-weight: bold; font-family: monospace;">{{ $loan->nomor_pinjaman ?? ('#' . str_pad($loan->id, 5, '0', STR_PAD_LEFT)) }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tujuan</td>
                <td>{{ $loan->tujuan_pinjaman }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Jumlah Pinjaman</td>
                <td>Rp {{ number_format($loan->jumlah_pinjaman, 2, ',', '.') }}</td>
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
        </table>
        
        <h5 style="margin-top: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Pencairan Dana</h5>
        <table style="width: 100%; line-height: 1.8;">
            <tr>
                <td style="color: #6b7280; width: 150px;">Bank</td>
                <td>{{ $loan->bank_name }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">No. Rekening</td>
                <td>{{ $loan->bank_account_number }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Pemilik</td>
                <td>{{ $loan->bank_account_owner }}</td>
            </tr>
        </table>
    </div>

    <!-- JADWAL ANGSURAN -->
    <div class="card" style="flex: 2; min-width: 400px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h4 style="margin: 0 0 15px 0;">Jadwal Angsuran</h4>
        
        @if($loan->installments->count() == 0)
            <div style="text-align: center; padding: 30px; color: #6b7280;">
                <p>Belum ada jadwal angsuran (Pinjaman belum aktif).</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table" style="font-size: 0.9em;">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Jatuh Tempo</th>
                            <th>Pokok</th>
                            <th>Jasa</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->installments as $inst)
                        <tr>
                            <td>{{ $inst->angsuran_ke }}</td>
                            <td>{{ date('d/m/Y', strtotime($inst->jatuh_tempo)) }}</td>
                            <td>Rp {{ number_format($inst->pokok_angsuran, 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($inst->jasa_angsuran, 2, ',', '.') }}</td>
                            <td style="font-weight: bold;">Rp {{ number_format($inst->total_angsuran, 2, ',', '.') }}</td>
                            <td><x-status-badge :status="$inst->status" /></td>
                            <td>
                                @if(in_array($inst->status, ['unpaid', 'overdue']))
                                    @php
                                        $pendingPay = $inst->payments->firstWhere('status', 'pending');
                                        $lastPay = $inst->payments->sortByDesc('created_at')->first();
                                    @endphp

                                    @if($pendingPay)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <x-status-badge status="pending" />
                                            @if($pendingPay->bukti_pembayaran)
                                                <a href="{{ route('payments.loans.proof', $pendingPay->id) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 0.8em;">
                                                    <i class="fa-solid fa-eye"></i> Bukti
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        @if($lastPay && $lastPay->status === 'failed')
                                            <div style="margin-bottom: 6px;">
                                                <span class="badge badge-danger">Ditolak</span>
                                                <div style="font-size: 0.8em; color: #dc2626; margin-top: 2px;">
                                                    <strong>Alasan:</strong> {{ $lastPay->catatan ?? '-' }}
                                                </div>
                                            </div>
                                        @endif
                                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modal-pay-inst-{{ $inst->id }}').style.display = 'block'">
                                            <i class="fa-solid fa-upload"></i> Bayar Online
                                        </button>

                                        <!-- MODAL UPLOAD BUKTI ANGSURAN -->
                                        <div id="modal-pay-inst-{{ $inst->id }}" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
                                            <div style="background: white; width: 90%; max-width: 480px; margin: 10% auto; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                                    <h4 style="margin: 0;">Upload Bukti Bayar Angsuran</h4>
                                                    <button type="button" onclick="document.getElementById('modal-pay-inst-{{ $inst->id }}').style.display = 'none'" style="border: none; background: transparent; font-size: 1.2em; cursor: pointer;">&times;</button>
                                                </div>
                                                <p style="font-size: 0.9em; color: #4b5563; margin-bottom: 15px;">
                                                    Angsuran Bulan Ke-{{ $inst->angsuran_ke }} - Total Tagihan: <strong>Rp {{ number_format($inst->total_angsuran, 2, ',', '.') }}</strong>
                                                </p>
                                                <form method="POST" action="{{ route('member.loan-payments.store', $inst->id) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="bukti_pembayaran_inst_{{ $inst->id }}" style="font-size: 0.9em;">File Bukti Transfer (JPG, PNG, PDF max 2MB) <span style="color: red;">*</span></label>
                                                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran_inst_{{ $inst->id }}" class="form-control" style="width: 100%;" required accept=".jpg,.jpeg,.png,.pdf">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="catatan_inst_{{ $inst->id }}" style="font-size: 0.9em;">Catatan Tambahan (Opsional)</label>
                                                        <input type="text" name="catatan" id="catatan_inst_{{ $inst->id }}" class="form-control" style="width: 100%;" placeholder="Contoh: Transfer via BRI a.n...">
                                                    </div>
                                                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('modal-pay-inst-{{ $inst->id }}').style.display = 'none'">Batal</button>
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
            </div>
        @endif
    </div>
</div>

<div class="card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 20px;">
    <h4 style="margin: 0 0 15px 0;">Dokumen Pendukung</h4>
    
    <x-validation-errors />

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <!-- LIST DOKUMEN -->
        <div style="flex: 2; min-width: 300px;">
            @if($loan->documents && $loan->documents->count() > 0)
                <table class="table" style="width: 100%; font-size: 0.9em; text-align: left;">
                    <thead>
                        <tr>
                            <th style="padding: 10px; border-bottom: 1px solid #eee;">Jenis Dokumen</th>
                            <th style="padding: 10px; border-bottom: 1px solid #eee;">Status Verifikasi</th>
                            <th style="padding: 10px; border-bottom: 1px solid #eee;">Tanggal Upload</th>
                            <th style="padding: 10px; border-bottom: 1px solid #eee;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->documents as $doc)
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                    {{ match($doc->jenis_dokumen) {
                                        'ktp' => 'KTP',
                                        'bukti_penghasilan' => 'Bukti Penghasilan',
                                        'rekening_bank' => 'Rekening Bank',
                                        'bukti_usaha' => 'Bukti Usaha',
                                        'jaminan' => 'Jaminan',
                                        default => ucfirst($doc->jenis_dokumen)
                                    } }}
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                    <x-status-badge :status="$doc->status_verifikasi" />
                                </td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $doc->updated_at->format('d/m/Y H:i') }}</td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                    <a href="{{ route('member.loans.documents.show', ['loan' => $loan->id, 'document' => $doc->id]) }}" target="_blank" class="btn btn-secondary" style="padding: 5px 10px; text-decoration: none; font-size: 0.85em;">Lihat / Unduh</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: #6b7280;">Belum ada dokumen pendukung yang diunggah.</p>
            @endif
        </div>

        <!-- FORM UPLOAD -->
        <div style="flex: 1; min-width: 300px; background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
            <h5 style="margin-top: 0; color: #374151;">Unggah Dokumen Baru</h5>
            <p style="font-size: 0.85em; color: #6b7280; line-height: 1.4;">
                Maksimal 5 MB. Format: PDF, JPG, JPEG, PNG. <br>
                Jika dokumen dengan jenis yang sama sudah ada, file baru akan menggantikan dokumen sebelumnya dan status verifikasinya kembali menjadi Pending.
            </p>
            
            <form method="POST" action="{{ route('member.loans.documents.store', $loan->id) }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label for="jenis_dokumen" style="display: block; font-size: 0.9em; font-weight: bold; margin-bottom: 5px;">Jenis Dokumen</label>
                    <select name="jenis_dokumen" id="jenis_dokumen" class="form-control" style="width: 100%; padding: 8px;" required>
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        <option value="ktp">KTP</option>
                        <option value="bukti_penghasilan">Bukti Penghasilan</option>
                        <option value="rekening_bank">Rekening Bank</option>
                        <option value="bukti_usaha">Bukti Usaha</option>
                        <option value="jaminan">Jaminan</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="file" style="display: block; font-size: 0.9em; font-weight: bold; margin-bottom: 5px;">File Dokumen</label>
                    <input type="file" name="file" id="file" class="form-control" style="width: 100%; padding: 8px;" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Unggah Dokumen</button>
            </form>
        </div>
    </div>
</div>
@endsection
