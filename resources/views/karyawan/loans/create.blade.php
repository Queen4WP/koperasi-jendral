@extends('layouts.app')

@section('title', 'Ajukan Pinjaman Modal Karyawan — KOPIN Enterprise')

@section('content')
<div class="page-header" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="margin-bottom: 6px;">
                <a href="{{ route('karyawan.loans.index') }}" style="color: #64748b; font-size: 13px; text-decoration: none; font-weight: 600;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pinjaman
                </a>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
                <i class="fa-solid fa-file-signature" style="color: #4f46e5; margin-right: 8px;"></i>
                Formulir Pengajuan Pinjaman Modal Karyawan
            </h1>
            <p style="color: #64748b; margin: 0; font-size: 14px;">
                Plafon s/d Rp 20.000.000 dengan persetujuan Finance dan pencairan tunai langsung di loket Kasir.
            </p>
        </div>
        <div>
            <span style="background: #eef2ff; color: #4338ca; padding: 8px 14px; border-radius: 999px; font-weight: 700; font-size: 13px; border: 1px solid #c7d2fe; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-percent"></i> Jasa Spesial Staf: 5.0% / Tahun Flat
            </span>
        </div>
    </div>
</div>

@if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 18px; color: #dc2626;"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
        <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-circle-exclamation"></i> Terdapat kendala pengisian formulir:</div>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px; align-items: start;">
    <!-- KOLOM KIRI: FORMULIR PENGAJUAN -->
    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid #e2e8f0; padding: 26px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <form id="loanForm" action="{{ route('karyawan.loans.store') }}" method="POST">
            @csrf

            <!-- IDENTITAS PEMOHON -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px;">
                <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Pemohon Pinjaman (Internal):</div>
                <div style="font-size: 15px; font-weight: 800; color: #0f172a;">{{ $user->name }}</div>
                <div style="font-size: 12px; color: #64748b;">NIK: {{ $member->nik }} | Email: {{ $user->email }}</div>
            </div>

            <!-- NOMINAL PINJAMAN -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Nominal Pinjaman Modal (Rp) <span style="color: #dc2626;">*</span>
                </label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 12px; font-weight: 800; color: #64748b; font-size: 14px;">Rp</span>
                    <input type="text" id="inputNominal" name="jumlah_pinjaman" value="{{ old('jumlah_pinjaman', '5.000.000') }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 14px 12px 42px; font-size: 18px; font-weight: 800; color: #0f172a; box-sizing: border-box; background: #f8fafc;">
                </div>

                <!-- Quick Selection Chips -->
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;">
                    @php
                        $suggested = [2000000, 5000000, 10000000, 15000000, 20000000, 25000000, 30000000, 50000000];
                        $chips = array_filter($suggested, fn($amt) => $amt >= $minLimit && $amt < $maxLimit);
                    @endphp
                    @foreach($chips as $chip)
                        <button type="button" onclick="setNominal({{ $chip }})" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 700; color: #334155; cursor: pointer;">
                            Rp {{ number_format($chip, 0, ',', '.') }}
                        </button>
                    @endforeach
                    <button type="button" onclick="setNominal({{ $maxLimit }})" style="background: #eef2ff; border: 1px solid #a5b4fc; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 700; color: #4338ca; cursor: pointer;">
                        Rp {{ number_format($maxLimit, 0, ',', '.') }} (Maks)
                    </button>
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 6px;">
                    Rentang plafon: <strong>Rp {{ number_format($minLimit, 0, ',', '.') }}</strong> s/d <strong>Rp {{ number_format($maxLimit, 0, ',', '.') }}</strong> (Jasa Koperasi: <strong>{{ $ratePercent }}%</strong> flat/tahun)
                </div>
            </div>

            <!-- TENOR PINJAMAN -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Jangka Waktu / Tenor (Bulan) <span style="color: #dc2626;">*</span>
                </label>
                <select id="selectTenor" name="tenor" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 14px; font-size: 14px; font-weight: 700; color: #0f172a; box-sizing: border-box; background: #FFFFFF;">
                    <option value="3" {{ old('tenor') == 3 ? 'selected' : '' }}>3 Bulan</option>
                    <option value="6" {{ old('tenor') == 6 ? 'selected' : '' }}>6 Bulan</option>
                    <option value="10" {{ old('tenor', 10) == 10 ? 'selected' : '' }}>10 Bulan (Rekomendasi)</option>
                    <option value="12" {{ old('tenor') == 12 ? 'selected' : '' }}>12 Bulan (1 Tahun)</option>
                    <option value="18" {{ old('tenor') == 18 ? 'selected' : '' }}>18 Bulan</option>
                    <option value="24" {{ old('tenor') == 24 ? 'selected' : '' }}>24 Bulan (2 Tahun)</option>
                </select>
            </div>

            <!-- TUJUAN PINJAMAN -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Tujuan / Keperluan Pinjaman Modal <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="tujuan_pinjaman" value="{{ old('tujuan_pinjaman', 'Tambahan Modal Usaha Sampingan Karyawan') }}" required placeholder="Contoh: Modal Usaha Kuliner / Biaya Pendidikan Anak" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px 14px; font-size: 14px; color: #0f172a; box-sizing: border-box;">
            </div>

            <!-- KETERANGAN TAMBAHAN -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea name="keterangan" rows="2" placeholder="Catatan atau keterangan pendukung untuk Finance..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #0f172a; box-sizing: border-box;">{{ old('keterangan') }}</textarea>
            </div>

            <!-- PERNYATAAN & KONFIRMASI -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 24px;">
                <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 12px; color: #334155; line-height: 1.5; cursor: pointer;">
                    <input type="checkbox" required style="width: 18px; height: 18px; accent-color: #4f46e5; margin-top: 2px;">
                    <span>
                        Saya memahami bahwa pinjaman ini harus melalui persetujuan tim <strong>Finance</strong> terlebih dahulu. Setelah disetujui, dana akan saya terima secara tunai di <strong>Loket Kasir KOPIN</strong> dengan menandatangani Bukti Kas Keluar (BKK), dan angsuran bulanan akan otomatis dipotong dari slip gaji (Payroll).
                    </span>
                </label>
            </div>

            <div style="display: flex; gap: 12px;">
                <a href="{{ route('karyawan.loans.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 12px; font-weight: 700; font-size: 13px; text-decoration: none;">
                    Batal
                </a>
                <button type="submit" id="btnSubmitLoan" class="btn btn-primary" style="flex: 2; background: #4f46e5; border-color: #4338ca; padding: 12px; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan ke Finance
                </button>
            </div>
        </form>
    </div>

    <!-- KOLOM KANAN: LIVE SIMULATION BOX -->
    <div>
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); border-radius: 16px; padding: 24px; color: #FFFFFF; box-shadow: 0 10px 25px rgba(49, 46, 129, 0.25); position: sticky; top: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px; color: #93c5fd;">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; margin: 0;">Simulasi Angsuran Payroll</h3>
                    <div style="font-size: 11px; opacity: 0.8;">Perhitungan Otomatis Real-time</div>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.08); border-radius: 12px; padding: 16px; margin-bottom: 18px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="opacity: 0.8;">Plafon Pinjaman:</span>
                    <strong id="simPlafon" style="color: #93c5fd;">Rp 5.000.000</strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="opacity: 0.8;">Jangka Waktu (Tenor):</span>
                    <strong id="simTenor">10 Bulan</strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="opacity: 0.8;">Jasa Koperasi (Flat):</span>
                    <strong style="color: #fde047;">5.0% / Tahun</strong>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="opacity: 0.8;">Total Jasa Selama Tenor:</span>
                    <strong id="simTotalJasa" style="color: #fde047;">Rp 208.333</strong>
                </div>

                <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px dashed rgba(255,255,255,0.2); font-size: 14px;">
                    <span style="font-weight: 700;">Total Pengembalian:</span>
                    <strong id="simTotalKewajiban" style="color: #FFFFFF;">Rp 5.208.333</strong>
                </div>
            </div>

            <!-- HIGHLIGHT ANGSURAN BULANAN -->
            <div style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 12px; padding: 16px; text-align: center; margin-bottom: 18px;">
                <div style="font-size: 11px; opacity: 0.85; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                    Potongan Gaji / Bulan
                </div>
                <div id="simAngsuranBulan" style="font-size: 26px; font-weight: 800; color: #86efac; margin: 4px 0;">
                    Rp 520.834
                </div>
                <div style="font-size: 11px; opacity: 0.75;">
                    Otomatis didebet dari slip gaji setiap akhir bulan
                </div>
            </div>

            <div style="font-size: 12px; opacity: 0.85; line-height: 1.5;">
                <i class="fa-solid fa-circle-info" style="margin-right: 4px; color: #93c5fd;"></i>
                Uang tunai dicairkan <strong>penuh 100%</strong> tanpa potongan biaya administrasi tersembunyi.
            </div>
        </div>
    </div>
</div>

<script>
    function parseRupiah(str) {
        return parseFloat(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    function updateSimulation() {
        const nominalInput = document.getElementById('inputNominal');
        const tenorSelect = document.getElementById('selectTenor');

        const plafon = parseRupiah(nominalInput.value);
        const tenor = parseInt(tenorSelect.value) || 10;
        const ratePercent = {{ (float) ($ratePercent ?? 5.0) }};

        const totalJasa = (plafon * (ratePercent / 100)) * (tenor / 12);
        const totalKewajiban = plafon + totalJasa;
        const angsuranPerBulan = tenor > 0 ? Math.ceil(totalKewajiban / tenor) : 0;

        document.getElementById('simPlafon').textContent = 'Rp ' + formatRupiah(plafon);
        document.getElementById('simTenor').textContent = tenor + ' Bulan';
        document.getElementById('simTotalJasa').textContent = 'Rp ' + formatRupiah(totalJasa);
        document.getElementById('simTotalKewajiban').textContent = 'Rp ' + formatRupiah(totalKewajiban);
        document.getElementById('simAngsuranBulan').textContent = 'Rp ' + formatRupiah(angsuranPerBulan);
    }

    function setNominal(amount) {
        const input = document.getElementById('inputNominal');
        if (input) {
            input.value = formatRupiah(amount);
            updateSimulation();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('inputNominal');
        const select = document.getElementById('selectTenor');

        if (input) {
            input.addEventListener('input', function() {
                const val = parseRupiah(this.value);
                this.value = formatRupiah(val);
                updateSimulation();
            });
        }

        if (select) {
            select.addEventListener('change', updateSimulation);
        }

        updateSimulation();

        // Form submit spinner
        document.getElementById('loanForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitLoan');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim ke Finance...';
                btn.style.opacity = '0.7';
            }
        });
    });
</script>
@endsection
