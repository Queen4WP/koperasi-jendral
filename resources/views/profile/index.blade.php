@extends('layouts.app')
@section('header_title', 'Profil Anggota')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <x-validation-errors />

    @if($member->status === 'rejected')
        <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fee2e2; border-left: 4px solid #ef4444; border-radius: 6px; padding: 16px; margin-bottom: 20px;">
            <h4 style="margin: 0 0 8px 0; color: #991b1b;"><i class="fa-solid fa-triangle-exclamation"></i> Pendaftaran Keanggotaan Ditolak</h4>
            <p style="margin: 0 0 10px 0; color: #b91c1c; font-size: 0.95em;">
                Pendaftaran keanggotaan Anda telah ditinjau oleh Admin dan ditolak dengan catatan berikut:
            </p>
            <div style="background: white; border: 1px solid #fca5a5; padding: 12px; border-radius: 4px; color: #7f1d1d; font-weight: 500; margin-bottom: 12px;">
                "{{ $latestRejection ? $latestRejection->catatan : 'Data identitas belum memenuhi persyaratan.' }}"
            </div>
            <p style="margin: 0; color: #4b5563; font-size: 0.85em;">
                Silakan lakukan perbaikan pada data identitas di bawah ini (termasuk NIK jika diperlukan), lalu ajukan ulang verifikasi keanggotaan Anda.
            </p>
        </div>
    @endif

    <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 20px;">
            <h4 style="margin: 0;">Informasi Keanggotaan</h4>
            <div>
                Status: <x-status-badge :status="$member->status" />
            </div>
        </div>

        @if($member->status === 'rejected')
            <form method="POST" action="{{ route('member.profile.resubmit') }}">
                @csrf
        @else
            <form method="POST" action="{{ route('member.profile.update') }}">
                @csrf
                @method('PUT')
        @endif

            <h5 style="margin: 0 0 15px 0; color: #374151; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px;">Data Pribadi</h5>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="nik">Nomor Induk Kependudukan (NIK)</label>
                    @if($member->status === 'rejected')
                        <input type="text" name="nik" id="nik" value="{{ old('nik', $member->nik) }}" class="form-control" required maxlength="16" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #3b82f6; border-radius: 4px;">
                        <small style="color: #2563eb;">Perbaiki NIK jika terdapat kesalahan pengisian sebelumnya (16 digit angka).</small>
                    @else
                        <input type="text" id="nik" value="{{ $member->nik }}" class="form-control" readonly disabled style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; color: #6b7280;">
                        <small style="color: #6b7280;">Untuk mengubah data identitas tertentu, silakan hubungi Admin KOPIN.</small>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $member->tanggal_lahir ? $member->tanggal_lahir->format('Y-m-d') : '') }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" value="{{ $user->email }}" class="form-control" readonly disabled style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; color: #6b7280;">
                    <small style="color: #6b7280;">Email akun terdaftar bersifat permanen.</small>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="alamat">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat" rows="3" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">{{ old('alamat', $member->alamat) }}</textarea>
            </div>

            <h5 style="margin: 20px 0 15px 0; color: #374151; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px;">Data Pekerjaan & Kontak</h5>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="pekerjaan">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan', $member->pekerjaan) }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="perusahaan">Nama Perusahaan / Usaha</label>
                    <input type="text" name="perusahaan" id="perusahaan" value="{{ old('perusahaan', $member->perusahaan) }}" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="telepon">Nomor Telepon / WhatsApp</label>
                <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $member->telepon) }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
            </div>

            <div style="text-align: right; margin-top: 25px;">
                @if($member->status === 'rejected')
                    <button type="submit" class="btn btn-primary" style="background-color: #2563eb;">
                        <i class="fa-solid fa-paper-plane"></i> Perbaiki Data & Ajukan Ulang Verifikasi
                    </button>
                @else
                    <button type="submit" class="btn btn-primary">Simpan Perubahan Profil</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
