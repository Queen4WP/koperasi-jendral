@extends('layouts.app')
@section('header_title', 'Profil Anggota')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <x-validation-errors />

    <div class="card" style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 20px;">
            <h4 style="margin: 0;">Informasi Keanggotaan</h4>
            <div>
                Status: <x-status-badge :status="$member->status" />
            </div>
        </div>

        <form method="POST" action="{{ route('member.profile.update') }}">
            @csrf
            @method('PUT')

            <h5 style="margin: 0 0 15px 0; color: #374151; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px;">Data Pribadi</h5>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="nik">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" id="nik" value="{{ $member->nik }}" class="form-control" readonly disabled style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa; color: #6b7280;">
                    <small style="color: #6b7280;">Untuk mengubah data identitas tertentu, silakan hubungi Admin KOPIN.</small>
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
                <button type="submit" class="btn btn-primary">Simpan Perubahan Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
