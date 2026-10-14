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

    @if(isset($activeLoan) && $activeLoan)
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #dc2626; color: #991b1b; padding: 16px 18px; border-radius: 8px; margin-bottom: 22px;">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <i class="fa-solid fa-ban" style="font-size: 22px; color: #dc2626; margin-top: 2px;"></i>
                <div>
                    <h5 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 800; color: #991b1b;">
                        Pengajuan Terkunci: Anda Memiliki Pinjaman Modal Berjalan
                    </h5>
                    <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #7f1d1d;">
                        Sistem mendeteksi kewajiban aktif pada <strong>Kartu Pinjaman Modal</strong> Anda sebesar <strong>Rp {{ number_format($activeLoan->total_kewajiban, 0, ',', '.') }}</strong> (Nomor: <code>{{ $activeLoan->nomor_pinjaman }}</code>, Status: <strong>{{ strtoupper($activeLoan->status) }}</strong>).
                    </p>
                    <p style="margin: 8px 0 0 0; font-size: 12px; color: #991b1b; font-weight: 600;">
                        <i class="fa-solid fa-circle-xmark"></i> Berdasarkan regulasi KOPIN (Opsi A - Strict Hard-Block), pengajuan baru tidak dapat diproses sebelum pinjaman berjalan diselesaikan / lunas di loket kasir.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('member.loans.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="jumlah_pinjaman">Jumlah Pinjaman (Rp)</label>
            <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" value="{{ old('jumlah_pinjaman') }}" class="form-control" min="{{ $minLoan ?? 5000000 }}" max="{{ $maxLoan ?? 50000000 }}" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
            <small style="color: #6c757d;">Batas pengajuan: Rp {{ number_format($minLoan ?? 5000000, 0, ',', '.') }} s/d Rp {{ number_format($maxLoan ?? 50000000, 0, ',', '.') }}.</small>
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
            <select name="tujuan_pinjaman" id="tujuan_pinjaman" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
                <option value="">-- Pilih Tujuan Pinjaman --</option>
                <option value="Modal Usaha" {{ old('tujuan_pinjaman') == 'Modal Usaha' ? 'selected' : '' }}>Modal Usaha</option>
                <option value="Pendidikan" {{ old('tujuan_pinjaman') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                <option value="Kebutuhan Rumah Tangga" {{ old('tujuan_pinjaman') == 'Kebutuhan Rumah Tangga' ? 'selected' : '' }}>Kebutuhan Rumah Tangga</option>
                <option value="Renovasi Rumah" {{ old('tujuan_pinjaman') == 'Renovasi Rumah' ? 'selected' : '' }}>Renovasi Rumah</option>
                <option value="Keperluan Lainnya" {{ old('tujuan_pinjaman') == 'Keperluan Lainnya' ? 'selected' : '' }}>Keperluan Lainnya</option>
            </select>
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

        <h5 style="margin-top: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Dokumen Persyaratan</h5>
        <p style="font-size: 0.85em; color: #6b7280; margin-bottom: 15px;">Maksimal 5 MB per dokumen. Format: PDF, JPG, JPEG, PNG.</p>

        <div class="form-group">
            <label for="ktp">Kartu Tanda Penduduk (KTP) <span style="color: #dc2626;">*</span></label>
            <input type="file" name="ktp" id="ktp" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div class="form-group">
            <label for="bukti_penghasilan">Bukti Penghasilan / Slip Gaji <span style="color: #dc2626;">*</span></label>
            <input type="file" name="bukti_penghasilan" id="bukti_penghasilan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div class="form-group">
            <label for="rekening_bank">Rekening Koran / Buku Tabungan Bank <span style="color: #dc2626;">*</span></label>
            <input type="file" name="rekening_bank" id="rekening_bank" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div class="form-group" id="group_bukti_usaha" style="{{ old('tujuan_pinjaman') == 'Modal Usaha' ? '' : 'display: none;' }}">
            <label for="bukti_usaha">Bukti Usaha (Wajib untuk Modal Usaha) <span style="color: #dc2626;">*</span></label>
            <input type="file" name="bukti_usaha" id="bukti_usaha" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
            <small style="color: #6c757d;">Foto tempat usaha, NIB, atau surat keterangan usaha.</small>
        </div>

        <div class="alert alert-info" style="margin-top: 15px; background: #e2f3f5; color: #0c5460; padding: 15px; border-radius: 4px;">
            <i class="fa-solid fa-circle-info"></i> Pastikan data bank dan dokumen yang Anda masukkan sesuai dengan identitas KTP Anda.
        </div>

        @if(isset($activeLoan) && $activeLoan)
            <button type="button" class="btn btn-secondary" disabled style="margin-top: 15px; width: 100%; opacity: 0.6; cursor: not-allowed; background: #94a3b8; border: none; font-weight: 700; padding: 12px;">
                <i class="fa-solid fa-lock"></i> Pengajuan Terkunci (Ada Pinjaman Berjalan Rp {{ number_format($activeLoan->total_kewajiban, 0, ',', '.') }})
            </button>
        @else
            <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;" {{ isset($eligibility) && !$eligibility['eligible'] ? 'disabled' : '' }}>
                Submit Pengajuan
            </button>
        @endif
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rateMap = { '6': '5%', '12': '7%', '18': '8.5%', '24': '10%' };
        const tenorSelect = document.getElementById('tenor');
        const rateDisplay = document.getElementById('persen_jasa_display');
        const tujuanSelect = document.getElementById('tujuan_pinjaman');
        const buktiUsahaGroup = document.getElementById('group_bukti_usaha');
        const buktiUsahaInput = document.getElementById('bukti_usaha');

        function updateRate() {
            if (tenorSelect && rateDisplay) {
                rateDisplay.value = rateMap[tenorSelect.value] || 'Pilih tenor';
            }
        }

        function updateBuktiUsaha() {
            if (tujuanSelect && buktiUsahaGroup && buktiUsahaInput) {
                if (tujuanSelect.value === 'Modal Usaha') {
                    buktiUsahaGroup.style.display = 'block';
                    buktiUsahaInput.setAttribute('required', 'required');
                } else {
                    buktiUsahaGroup.style.display = 'none';
                    buktiUsahaInput.removeAttribute('required');
                }
            }
        }

        if (tenorSelect) {
            tenorSelect.addEventListener('change', updateRate);
            updateRate();
        }

        if (tujuanSelect) {
            tujuanSelect.addEventListener('change', updateBuktiUsaha);
            updateBuktiUsaha();
        }
    });
</script>
@endsection
