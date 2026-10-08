@extends('layouts.app')
@section('header_title', 'Verifikasi Keanggotaan')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h4 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Review Data Anggota</h4>
    
    <table style="width: 100%; line-height: 2; margin-bottom: 20px;">
        <tr>
            <td style="color: #6b7280; width: 150px;">Nama Lengkap</td>
            <td style="font-weight: bold;">{{ $member->user->name }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">NIK</td>
            <td>{{ $member->nik }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Nomor HP</td>
            <td>{{ $member->telepon }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Alamat</td>
            <td>{{ $member->alamat }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Status Saat Ini</td>
            <td><x-status-badge :status="$member->status" /></td>
        </tr>
    </table>

    <x-validation-errors />

    @if($member->status === 'pending')
        <form method="POST" action="{{ route('admin.members.review', $member->id) }}">
            @csrf
            
            <div class="form-group">
                <label for="catatan">Catatan / Alasan Penolakan</label>
                <textarea name="catatan" id="catatan" class="form-control" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;" placeholder="Wajib diisi jika menolak..."></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" name="action" value="approve" class="btn btn-primary" style="flex: 1;" onclick="return confirm('Setujui keanggotaan ini?')">Setujui (Approve)</button>
                <button type="submit" name="action" value="reject" class="btn btn-danger" style="flex: 1;" onclick="return confirm('Tolak keanggotaan ini?')">Tolak (Reject)</button>
            </div>
        </form>
    @else
        <div class="alert alert-info" style="background: #e2f3f5; color: #0c5460; padding: 15px; border-radius: 4px; text-align: center;">
            Keanggotaan ini sudah diverifikasi atau tidak berstatus pending.
        </div>
    @endif
</div>
@endsection
