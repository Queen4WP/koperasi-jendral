@extends('layouts.app')

@section('title', 'Pengaturan Plafon Pinjaman — Finance KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="margin-bottom: 6px;">
                <a href="{{ route('finance.dashboard') }}" style="color: #64748b; font-size: 13px; text-decoration: none; font-weight: 600;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Finance
                </a>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-sliders" style="color: #2563eb; margin-right: 8px;"></i>
                Pengaturan Plafon & Batas Limit Pinjaman
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Kelola batas minimum, plafon maksimum, dan kebijakan jasa pinjaman untuk Anggota Karyawan dan Anggota Umum secara real-time.
            </p>
        </div>
        <div>
            <span style="background: #eff6ff; color: #1d4ed8; padding: 8px 16px; border-radius: 999px; font-weight: 700; font-size: 13px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-shield-halved"></i> Kebijakan Resmi Komite Keuangan
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
        <i class="fa-solid fa-circle-check" style="font-size: 20px; color: #16a34a;"></i>
        <div>
            <strong>Berhasil Diperbarui!</strong> {{ session('success') }}
        </div>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;">
        <div style="font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-exclamation"></i> Terjadi Kesalahan Validasi:
        </div>
        <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- INFORMASI STATUS AKTIF SAAT INI -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 28px;">
    <!-- KARYAWAN CARD -->
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border-radius: 14px; padding: 20px; color: #FFFFFF; box-shadow: 0 4px 12px rgba(49, 46, 129, 0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="font-size: 11px; font-weight: 800; background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 999px; text-transform: uppercase;">
                Internal Karyawan
            </span>
            <i class="fa-solid fa-user-tie" style="font-size: 18px; color: #a5b4fc;"></i>
        </div>
        <div style="font-size: 12px; color: #c7d2fe; margin-bottom: 4px;">Plafon Maksimum Aktif</div>
        <div style="font-size: 26px; font-weight: 800; color: #FFFFFF; margin-bottom: 6px;">
            Rp {{ number_format($karyawanMax, 0, ',', '.') }}
        </div>
        <div style="font-size: 12px; color: #e0e7ff; display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 8px; margin-top: 8px;">
            <span>Min: Rp {{ number_format($karyawanMin, 0, ',', '.') }}</span>
            <span style="color: #fde047; font-weight: 700;">Jasa: {{ $karyawanRate }}% / thn</span>
        </div>
    </div>

    <!-- ANGGOTA CARD -->
    <div style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); border-radius: 14px; padding: 20px; color: #FFFFFF; box-shadow: 0 4px 12px rgba(4, 120, 87, 0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="font-size: 11px; font-weight: 800; background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 999px; text-transform: uppercase;">
                Anggota Umum
            </span>
            <i class="fa-solid fa-users" style="font-size: 18px; color: #a7f3d0;"></i>
        </div>
        <div style="font-size: 12px; color: #a7f3d0; margin-bottom: 4px;">Plafon Maksimum Aktif</div>
        <div style="font-size: 26px; font-weight: 800; color: #FFFFFF; margin-bottom: 6px;">
            Rp {{ number_format($anggotaMax, 0, ',', '.') }}
        </div>
        <div style="font-size: 12px; color: #d1fae5; display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 8px; margin-top: 8px;">
            <span>Min: Rp {{ number_format($anggotaMin, 0, ',', '.') }}</span>
            <span style="color: #fef08a; font-weight: 700;">Matriks Tenor PRD</span>
        </div>
    </div>
</div>

<!-- FORM PENGATURAN LIMIT PINJAMAN -->
<form id="formLoanLimits" action="{{ route('finance.settings.loan_limits.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <!-- PANEL KARYAWAN -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #eef2ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-id-badge"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0;">Plafon Pinjaman Karyawan</h2>
                        <span style="font-size: 12px; color: #64748b;">Skema Kredit Karyawan Internal via Pemotongan Payroll</span>
                    </div>
                </div>

                <!-- BATAS MINIMUM KARYAWAN -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Batas Minimum Pinjaman (Rp) <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 12px; font-weight: 800; color: #64748b; font-size: 14px;">Rp</span>
                        <input type="text" name="loan_karyawan_min_limit" id="inputKaryawanMin" value="{{ old('loan_karyawan_min_limit', number_format($karyawanMin, 0, ',', '.')) }}" required class="currency-input" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px 10px 42px; font-size: 15px; font-weight: 700; color: #0f172a; box-sizing: border-box;">
                    </div>
                    <small style="font-size: 11px; color: #64748b;">Nominal terendah yang diperkenankan untuk diajukan staf.</small>
                </div>

                <!-- BATAS MAKSIMUM KARYAWAN -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Batas Maksimum Plafon Karyawan (Rp) <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 12px; font-weight: 800; color: #4338ca; font-size: 14px;">Rp</span>
                        <input type="text" name="loan_karyawan_max_limit" id="inputKaryawanMax" value="{{ old('loan_karyawan_max_limit', number_format($karyawanMax, 0, ',', '.')) }}" required class="currency-input" style="width: 100%; border: 2px solid #a5b4fc; border-radius: 8px; padding: 10px 14px 10px 42px; font-size: 18px; font-weight: 800; color: #312e81; box-sizing: border-box; background: #faf5ff;">
                    </div>

                    <!-- Preset Chips Karyawan -->
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;">
                        <span style="font-size: 11px; color: #64748b; align-self: center; margin-right: 4px;">Pilihan Cepat:</span>
                        <button type="button" onclick="setKaryawanMax(20000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">20 Juta</button>
                        <button type="button" onclick="setKaryawanMax(30000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">30 Juta</button>
                        <button type="button" onclick="setKaryawanMax(50000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">50 Juta</button>
                        <button type="button" onclick="setKaryawanMax(100000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">100 Juta</button>
                    </div>
                </div>

                <!-- BUNGA / JASA FLAT KARYAWAN -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Tarif Jasa Koperasi Karyawan (% Flat / Tahun) <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="0.1" min="0" max="100" name="loan_karyawan_rate_percent" value="{{ old('loan_karyawan_rate_percent', $karyawanRate) }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 40px 10px 14px; font-size: 15px; font-weight: 700; color: #0f172a; box-sizing: border-box;">
                        <span style="position: absolute; right: 14px; top: 12px; font-weight: 800; color: #64748b; font-size: 14px;">%</span>
                    </div>
                    <small style="font-size: 11px; color: #64748b;">Standar benefit staf KOPIN adalah 5.0% flat per tahun.</small>
                </div>
            </div>

            <div style="background: #eff6ff; border: 1px solid #dbeafe; border-radius: 10px; padding: 12px 14px; font-size: 12px; color: #1e40af; line-height: 1.5;">
                <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i>
                Plafon ini akan otomatis diterapkan pada formulir pengajuan pinjaman karyawan (`/karyawan/loans/create`) beserta simulasi cicilan payrollnya.
            </div>
        </div>

        <!-- PANEL ANGGOTA UMUM -->
        <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #ecfdf5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <h2 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0;">Plafon Pinjaman Anggota Umum</h2>
                        <span style="font-size: 12px; color: #64748b;">Skema Pinjaman Modal Anggota Reguler (Kartu Pinjaman Modal)</span>
                    </div>
                </div>

                <!-- BATAS MINIMUM ANGGOTA -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Batas Minimum Pinjaman (Rp) <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 12px; font-weight: 800; color: #64748b; font-size: 14px;">Rp</span>
                        <input type="text" name="loan_anggota_min_limit" id="inputAnggotaMin" value="{{ old('loan_anggota_min_limit', number_format($anggotaMin, 0, ',', '.')) }}" required class="currency-input" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px 10px 42px; font-size: 15px; font-weight: 700; color: #0f172a; box-sizing: border-box;">
                    </div>
                    <small style="font-size: 11px; color: #64748b;">Standar minimum pengajuan anggota umum adalah Rp 5.000.000.</small>
                </div>

                <!-- BATAS MAKSIMUM ANGGOTA -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Batas Maksimum Plafon Anggota (Rp) <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 12px; font-weight: 800; color: #047857; font-size: 14px;">Rp</span>
                        <input type="text" name="loan_anggota_max_limit" id="inputAnggotaMax" value="{{ old('loan_anggota_max_limit', number_format($anggotaMax, 0, ',', '.')) }}" required class="currency-input" style="width: 100%; border: 2px solid #a7f3d0; border-radius: 8px; padding: 10px 14px 10px 42px; font-size: 18px; font-weight: 800; color: #065f46; box-sizing: border-box; background: #f0fdf4;">
                    </div>

                    <!-- Preset Chips Anggota -->
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;">
                        <span style="font-size: 11px; color: #64748b; align-self: center; margin-right: 4px;">Pilihan Cepat:</span>
                        <button type="button" onclick="setAnggotaMax(50000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">50 Juta</button>
                        <button type="button" onclick="setAnggotaMax(75000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">75 Juta</button>
                        <button type="button" onclick="setAnggotaMax(100000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">100 Juta</button>
                        <button type="button" onclick="setAnggotaMax(200000000)" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">200 Juta</button>
                    </div>
                </div>

                <!-- ATURAN STRICT HARD-BLOCK -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Kebijakan Validasi Risiko Pinjaman
                    </label>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; font-size: 12px; color: #475569;">
                        <i class="fa-solid fa-lock" style="color: #059669; margin-right: 4px;"></i>
                        <strong>Strict Hard-Block Aktif:</strong> Anggota tidak dapat mengajukan pinjaman baru jika masih memiliki kewajiban pinjaman yang aktif/berjalan.
                    </div>
                </div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; font-size: 12px; color: #166534; line-height: 1.5;">
                <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i>
                Perubahan plafon anggota akan otomatis memperbarui validasi batas formulir pengajuan anggota (`/loans/create`) dan validasi server-side.
            </div>
        </div>
    </div>

    <!-- ACTION BUTTON & AUDIT LOG -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div style="font-size: 12px; color: #64748b;">
            @if($lastUpdated)
                <i class="fa-solid fa-clock-rotate-left"></i> Terakhir diperbarui:
                <strong>{{ $lastUpdated->updated_at ? $lastUpdated->updated_at->format('d/m/Y H:i') : '-' }}</strong>
                @if($lastUpdated->updatedByUser)
                    oleh <strong>{{ $lastUpdated->updatedByUser->name }}</strong>
                @endif
            @else
                <i class="fa-solid fa-info-circle"></i> Nilai konfigurasi awal bawaan sistem koperasi.
            @endif
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('finance.dashboard') }}" class="btn btn-secondary" style="padding: 12px 20px; font-size: 13px; font-weight: 700; text-decoration: none;">
                Batal
            </a>
            <button type="submit" id="btnSaveLoanLimits" class="btn btn-primary" style="background: #2563eb; border-color: #1d4ed8; padding: 12px 28px; font-size: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan & Terapkan Plafon Baru
            </button>
        </div>
    </div>
</form>

<script>
    function parseRupiah(str) {
        return parseInt(str.toString().replace(/[^0-9]/g, '')) || 0;
    }

    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    function setKaryawanMax(nominal) {
        const input = document.getElementById('inputKaryawanMax');
        if (input) {
            input.value = formatRupiah(nominal);
        }
    }

    function setAnggotaMax(nominal) {
        const input = document.getElementById('inputAnggotaMax');
        if (input) {
            input.value = formatRupiah(nominal);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto format currency inputs on type
        document.querySelectorAll('.currency-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const val = parseRupiah(this.value);
                this.value = formatRupiah(val);
            });
        });

        // Form submit sweet alert confirmation
        const form = document.getElementById('formLoanLimits');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (typeof Swal !== 'undefined' && !form.dataset.confirmed) {
                    e.preventDefault();

                    const kryMax = document.getElementById('inputKaryawanMax').value;
                    const agtMax = document.getElementById('inputAnggotaMax').value;

                    Swal.fire({
                        title: 'Konfirmasi Pembaruan Plafon',
                        html: `Anda akan menerapkan batas plafon baru:<br>` +
                              `<b>Plafon Karyawan:</b> Rp ${kryMax}<br>` +
                              `<b>Plafon Anggota:</b> Rp ${agtMax}<br><br>` +
                              `<small style="color: #64748b;">Perubahan ini akan langsung berlaku pada seluruh form pengajuan pinjaman karyawan dan anggota.</small>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Terapkan Sekarang',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'kopin-swal-confirm',
                            cancelButton: 'kopin-swal-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            });
        }
    });
</script>
@endsection
