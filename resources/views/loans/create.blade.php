@extends('layouts.app')
@section('header_title', 'Pengajuan Pinjaman')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h4 style="margin-top: 0;">Form Pengajuan Pinjaman</h4>
    <x-validation-errors />
    
    @if(isset($eligibility) && !$eligibility['eligible'])
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f87171;">
            <i class="fa-solid fa-triangle-exclamation"></i> <strong>Perhatian:</strong> {{ $eligibility['message'] }}
        </div>
    @endif

    <form method="POST" action="{{ route('member.loans.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="jumlah_pinjaman">Jumlah Pinjaman (Rp)</label>
            <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" value="{{ old('jumlah_pinjaman') }}" class="form-control" min="5000000" max="50000000" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
            <small style="color: #6c757d;">Batas pengajuan: Rp 5.000.000 s/d Rp 50.000.000.</small>
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label for="tenor">Tenor (Bulan)</label>
                <select name="tenor" id="tenor" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                    <option value="">-- Pilih Tenor --</option>
                    <option value="6" {{ old('tenor') == 6 ? 'selected' : '' }}>6 Bulan (Jasa 5%)</option>
                    <option value="12" {{ old('tenor') == 12 ? 'selected' : '' }}>12 Bulan (Jasa 7%)</option>
                    <option value="18" {{ old('tenor') == 18 ? 'selected' : '' }}>18 Bulan (Jasa 8.5%)</option>
                    <option value="24" {{ old('tenor') == 24 ? 'selected' : '' }}>24 Bulan (Jasa 10%)</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="persen_jasa_display">Tarif Jasa (%)</label>
                <input type="text" id="persen_jasa_display" class="form-control" readonly value="Pilih tenor" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa;">
                <small style="color: #6c757d;">Ditetapkan otomatis oleh sistem.</small>
            </div>
        </div>

        <div class="form-group">
            <label for="tujuan_pinjaman">Tujuan Pinjaman</label>
            <input type="text" name="tujuan_pinjaman" id="tujuan_pinjaman" value="{{ old('tujuan_pinjaman') }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan Tambahan (Opsional)</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">{{ old('keterangan') }}</textarea>
        </div>

        <h5 style="margin-top: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Informasi Rekening Pencairan</h5>

        <div class="form-group">
            <label for="bank_name">Nama Bank</label>
            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label for="bank_account_number">Nomor Rekening</label>
                <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number') }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="bank_account_owner">Nama Pemilik Rekening</label>
                <input type="text" name="bank_account_owner" id="bank_account_owner" value="{{ old('bank_account_owner') }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
            </div>
        </div>

        <div class="alert alert-info" style="margin-top: 15px; background: #e2f3f5; color: #0c5460; padding: 15px; border-radius: 4px;">
            <i class="fa-solid fa-circle-info"></i> Pastikan data bank yang Anda masukkan sesuai dengan KTP Anda.
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;">Submit Pengajuan</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rateMap = { '6': '5%', '12': '7%', '18': '8.5%', '24': '10%' };
        const tenorSelect = document.getElementById('tenor');
        const rateDisplay = document.getElementById('persen_jasa_display');

        function updateRate() {
            if (tenorSelect && rateDisplay) {
                rateDisplay.value = rateMap[tenorSelect.value] || 'Pilih tenor';
            }
        }

        if (tenorSelect) {
            tenorSelect.addEventListener('change', updateRate);
            updateRate();
        }
    });
</script>
@endsection
