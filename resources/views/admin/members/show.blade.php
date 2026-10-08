@extends('layouts.app')
@section('header_title', 'Detail Data Anggota')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.members.index') }}" class="btn btn-secondary" style="font-size: 0.9em;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Anggota
        </a>
    </div>

    <x-validation-errors />

    <!-- CARD 1: INFORMASI IDENTITAS ANGGOTA -->
    <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 20px;">
            <h4 style="margin: 0; color: #1f2937;">Data Anggota</h4>
            <div>
                Status: <x-status-badge :status="$member->status" />
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; line-height: 2;">
            <tr>
                <td style="color: #6b7280; width: 200px;">Nama Lengkap</td>
                <td style="font-weight: 600; color: #111827;">{{ $member->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Nomor Induk Kependudukan (NIK)</td>
                <td style="font-family: monospace; font-size: 1.05em; font-weight: 600;">{{ $member->nik }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Email Akun</td>
                <td>{{ $member->user->email ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tanggal Lahir</td>
                <td>{{ $member->tanggal_lahir ? $member->tanggal_lahir->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Alamat Tempat Tinggal</td>
                <td>{{ $member->alamat }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Nomor Telepon / WhatsApp</td>
                <td>{{ $member->telepon }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Pekerjaan</td>
                <td>{{ $member->pekerjaan }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Nama Perusahaan / Usaha</td>
                <td>{{ $member->perusahaan ?? '-' }}</td>
            </tr>
            <tr>
                <td style="color: #6b7280;">Tanggal Registrasi</td>
                <td>{{ $member->created_at ? $member->created_at->format('d/m/Y H:i:s') : '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- CARD 2: KEPUTUSAN TERAKHIR (IF REJECTED OR REVIEWED) -->
    @if($latestVerification)
        <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-left: 4px solid {{ $latestVerification->status_keputusan === 'rejected' ? '#ef4444' : '#10b981' }};">
            <h4 style="margin: 0 0 12px 0; color: #1f2937;">Keputusan Verifikasi Terakhir</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Keputusan:</span>
                    <div style="font-weight: 600;">
                        <span class="badge {{ $latestVerification->status_keputusan === 'rejected' ? 'badge-danger' : 'badge-success' }}">
                            {{ ucfirst($latestVerification->status_keputusan) }}
                        </span>
                    </div>
                </div>
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Tanggal Review:</span>
                    <div style="font-weight: 600;">{{ $latestVerification->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <span style="color: #6b7280; font-size: 0.9em;">Ditinjau Oleh:</span>
                    <div>{{ $latestVerification->admin->name ?? 'Admin' }}</div>
                </div>
            </div>

            @if($latestVerification->status_keputusan === 'rejected' || !empty($latestVerification->catatan))
                <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 12px; border-radius: 6px; margin-top: 10px;">
                    <strong style="color: #991b1b; display: block; margin-bottom: 4px;">Alasan Penolakan / Catatan:</strong>
                    <div style="color: #7f1d1d;">{{ $latestVerification->catatan ?: '-' }}</div>
                </div>
            @endif
        </div>
    @endif

    <!-- CARD 3: ACTION FORM JIKA STATUS PENDING -->
    @if($member->status === 'pending')
        <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border-top: 4px solid #3b82f6;">
            <h4 style="margin: 0 0 10px 0; color: #1f2937;">Keputusan Verifikasi Keanggotaan</h4>
            <p style="color: #6b7280; margin-bottom: 15px; font-size: 0.9em;">Pilih tindakan verifikasi untuk anggota ini. Jika menolak, alasan penolakan wajib dicantumkan.</p>

            <form method="POST" action="{{ route('admin.members.review.store', $member->id) }}">
                @csrf
                
                <div class="form-group">
                    <label for="catatan">Catatan / Alasan Penolakan (Wajib jika menolak):</label>
                    <textarea name="catatan" id="catatan" class="form-control" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" placeholder="Contoh: NIK tidak valid atau data KTP tidak sesuai..."></textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 15px;">
                    <button type="submit" name="action" value="approve" class="btn btn-primary" style="flex: 1; padding: 10px;" onclick="return confirm('Apakah Anda yakin ingin menyetujui keanggotaan ini?')">
                        <i class="fa-solid fa-check"></i> Setujui (Approve)
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger" style="flex: 1; padding: 10px;" onclick="return confirm('Apakah Anda yakin ingin menolak keanggotaan ini?')">
                        <i class="fa-solid fa-times"></i> Tolak (Reject)
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- CARD 4: RIWAYAT VERIFIKASI (AUDIT TRAIL) -->
    @if(isset($verifications) && count($verifications) > 0)
        <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
            <h4 style="margin: 0 0 15px 0; color: #1f2937;">Riwayat Verifikasi (Audit Trail)</h4>
            
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Waktu</th>
                        <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Admin Reviewer</th>
                        <th style="padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center;">Keputusan</th>
                        <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($verifications as $v)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-size: 0.9em; color: #4b5563;">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                            <td style="padding: 10px;">{{ $v->admin->name ?? '-' }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span class="badge {{ $v->status_keputusan === 'rejected' ? 'badge-danger' : 'badge-success' }}">
                                    {{ ucfirst($v->status_keputusan) }}
                                </span>
                            </td>
                            <td style="padding: 10px; color: #374151;">{{ $v->catatan ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
