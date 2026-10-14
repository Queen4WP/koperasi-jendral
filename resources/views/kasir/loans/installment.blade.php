@extends('layouts.app')

@section('title', 'Loket Pembayaran Angsuran — Kasir KOPIN')

@section('content')
<div class="page-header" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <a href="{{ route('kasir.dashboard') }}" style="color: #64748b; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Kasir
            </a>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-cash-register" style="color: #059669;"></i> Loket Angsuran Pinjaman (POS Teller)
            </h1>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="background: #ecfdf5; color: #047857; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; border: 1px solid #a7f3d0;">
                <i class="fa-solid fa-bolt"></i> Auto BKM &bull; Tanpa Upload Foto
            </span>
            <span style="background: #f1f5f9; color: #334155; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                <strong id="cardCountDisplay">{{ count($dueCards) }}</strong> Anggota Tertagih
            </span>
        </div>
    </div>
</div>

@if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<!-- SEARCH FILTER BAR -->
<div style="background: #FFFFFF; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <i class="fa-solid fa-magnifying-glass" style="color: #94a3b8; font-size: 16px;"></i>
    <input type="text" id="posFilterInput" placeholder="Ketik Nama Anggota, NIK, atau Nomor Pinjaman untuk memfilter kartu..." style="border: none; outline: none; width: 100%; font-size: 14px; color: #0f172a;">
    <button type="button" id="btnClearSearch" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 14px; display: none;">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<!-- MAIN POS SPLIT LAYOUT -->
<div style="display: grid; grid-template-columns: 1fr 400px; gap: 24px; align-items: start;">
    
    <!-- LEFT: POS CARD GRID (Daftar Tagihan Angsuran Anggota) -->
    <div>
        <div style="font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
            Pilih Kartu Angsuran Anggota:
        </div>

        @if(count($dueCards) === 0)
            <div style="background: #FFFFFF; border: 1px dashed #cbd5e1; border-radius: 16px; padding: 48px 24px; text-align: center; color: #64748b;">
                <i class="fa-solid fa-circle-check" style="font-size: 42px; color: #10b981; margin-bottom: 12px;"></i>
                <h4 style="margin: 0 0 6px 0; color: #0f172a; font-size: 16px;">Seluruh Angsuran Lunas!</h4>
                <p style="margin: 0; font-size: 13px;">Tidak ada tagihan angsuran pinjaman aktif yang tertagih saat ini.</p>
            </div>
        @else
            <div id="posCardsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
                @foreach($dueCards as $index => $c)
                    <div class="pos-card {{ $index === 0 ? 'active' : '' }}" 
                         data-id="{{ $c['installment_id'] }}"
                         data-name="{{ $c['member_name'] }}"
                         data-nik="{{ $c['member_nik'] }}"
                         data-loan="{{ $c['nomor_pinjaman'] }}"
                         data-purpose="{{ $c['tujuan_pinjaman'] }}"
                         data-step="{{ $c['angsuran_ke'] }}"
                         data-tenor="{{ $c['total_tenor'] }}"
                         data-due="{{ $c['jatuh_tempo_formatted'] }}"
                         data-overdue="{{ $c['is_overdue'] ? '1' : '0' }}"
                         data-days="{{ $c['hari_terlambat'] }}"
                         data-pokok="{{ $c['pokok_angsuran'] }}"
                         data-jasa="{{ $c['jasa_angsuran'] }}"
                         data-total="{{ $c['total_tagihan'] }}"
                         data-denda="{{ $c['denda'] }}"
                         style="background: #FFFFFF; border: 2px solid {{ $index === 0 ? '#059669' : '#e2e8f0' }}; border-radius: 14px; padding: 18px; cursor: pointer; transition: all 0.2s ease; position: relative; box-shadow: {{ $index === 0 ? '0 8px 16px -2px rgba(5,150,105,0.15)' : '0 1px 3px rgba(0,0,0,0.04)' }};">
                        
                        <!-- Check Indicator -->
                        <div class="pos-check-badge" style="display: {{ $index === 0 ? 'flex' : 'none' }}; position: absolute; top: 12px; right: 12px; width: 22px; height: 22px; background: #059669; border-radius: 50%; color: #fff; align-items: center; justify-content: center; font-size: 11px;">
                            <i class="fa-solid fa-check"></i>
                        </div>

                        <!-- Member Info -->
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; padding-right: 24px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: #f0fdf4; color: #059669; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px; border: 1px solid #bbf7d0;">
                                {{ strtoupper(substr($c['member_name'], 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 14px; color: #0f172a; line-height: 1.2;">
                                    {{ $c['member_name'] }}
                                </div>
                                <div style="font-size: 11px; color: #64748b; font-family: monospace;">
                                    NIK: {{ $c['member_nik'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Loan & Installment Meta -->
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px;">
                            <span style="background: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; font-family: monospace;">
                                {{ $c['nomor_pinjaman'] }}
                            </span>
                            <span style="background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                                Angs Ke-{{ $c['angsuran_ke'] }} / {{ $c['total_tenor'] }}
                            </span>
                        </div>

                        <!-- Amount & Due Date -->
                        <div style="border-top: 1px dashed #e2e8f0; padding-top: 12px; display: flex; justify-content: space-between; align-items: flex-end;">
                            <div>
                                <div style="font-size: 11px; color: #64748b; margin-bottom: 2px;">Tagihan Angsuran:</div>
                                <div style="font-size: 18px; font-weight: 900; color: #047857;">
                                    Rp {{ number_format($c['total_tagihan'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                @if($c['is_overdue'])
                                    <span style="background: #fef2f2; color: #dc2626; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; border: 1px solid #fecaca;">
                                        <i class="fa-solid fa-clock"></i> Lewat ({{ $c['jatuh_tempo_formatted'] }})
                                    </span>
                                @else
                                    <span style="font-size: 11px; color: #64748b;">
                                        Tempo: {{ $c['jatuh_tempo_formatted'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- RIGHT: KASIR CHECKOUT REGISTER PANEL (Form Eksekusi Pembayaran) -->
    <div style="position: sticky; top: 20px;">
        <div style="background: #FFFFFF; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
            
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-receipt" style="color: #2563eb;"></i> Rincian Pembayaran Loket
                </div>
                <span id="regStatusBadge" style="background: #eff6ff; color: #2563eb; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                    Siap Diterima
                </span>
            </div>

            <form action="{{ route('kasir.loans.installment.store') }}" method="POST" id="posForm">
                @csrf
                <input type="hidden" name="loan_installment_id" id="posSelectedInstallmentId" value="{{ count($dueCards) > 0 ? $dueCards[0]['installment_id'] : '' }}">

                <!-- Ringkasan Anggota & Pinjaman Terpilih -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 6px;">
                        Anggota Penyetor:
                    </div>
                    <div id="regMemberName" style="font-size: 16px; font-weight: 800; color: #0f172a;">
                        {{ count($dueCards) > 0 ? $dueCards[0]['member_name'] : '-' }}
                    </div>
                    <div id="regMemberNik" style="font-size: 12px; color: #64748b; font-family: monospace; margin-bottom: 8px;">
                        NIK: {{ count($dueCards) > 0 ? $dueCards[0]['member_nik'] : '-' }}
                    </div>
                    <div style="display: flex; gap: 6px; font-size: 12px; color: #334155; font-weight: 600;">
                        <span id="regLoanNo" style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ count($dueCards) > 0 ? $dueCards[0]['nomor_pinjaman'] : '-' }}</span>
                        <span id="regStep" style="background: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 4px;">Angs Ke-{{ count($dueCards) > 0 ? $dueCards[0]['angsuran_ke'] : '-' }}</span>
                    </div>
                </div>

                <!-- Breakdown Tagihan -->
                <div style="font-size: 13px; color: #475569; margin-bottom: 14px;">
                    <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                        <span>Pokok Angsuran</span>
                        <strong id="regPokok">Rp {{ count($dueCards) > 0 ? number_format($dueCards[0]['pokok_angsuran'], 0, ',', '.') : '0' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 4px 0;">
                        <span>Jasa Koperasi</span>
                        <strong id="regJasa">Rp {{ count($dueCards) > 0 ? number_format($dueCards[0]['jasa_angsuran'], 0, ',', '.') : '0' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; color: #dc2626;" id="regDendaRow">
                        <span>Denda Keterlambatan</span>
                        <strong id="regDenda">Rp {{ count($dueCards) > 0 ? number_format($dueCards[0]['denda'], 0, ',', '.') : '0' }}</strong>
                    </div>
                </div>

                <!-- Big Total Display -->
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px; text-align: center; margin-bottom: 18px;">
                    <div style="font-size: 12px; font-weight: 700; color: #166534; text-transform: uppercase;">
                        Total Kas Masuk (Tagihan)
                    </div>
                    <div id="regTotalDisplay" style="font-size: 26px; font-weight: 900; color: #047857; margin: 4px 0;">
                        Rp {{ count($dueCards) > 0 ? number_format($dueCards[0]['total_tagihan'], 0, ',', '.') : '0' }}
                    </div>
                    <div id="regDueDateDisplay" style="font-size: 11px; color: #15803d;">
                        Jatuh Tempo: {{ count($dueCards) > 0 ? $dueCards[0]['jatuh_tempo_formatted'] : '-' }}
                    </div>
                </div>

                <!-- Calculator Kasir: Uang Diterima & Kembalian -->
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                        Uang Tunai Diterima (Rp)
                    </label>
                    <input type="number" id="cashReceivedInput" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 16px; font-weight: 700; color: #0f172a; box-sizing: border-box;" 
                           value="{{ count($dueCards) > 0 ? round($dueCards[0]['total_tagihan']) : '' }}">
                    
                    <!-- Quick Presets -->
                    <div style="display: flex; gap: 6px; margin-top: 6px;">
                        <button type="button" class="btn-preset" id="btnPas" style="flex: 1; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            Uang Pas
                        </button>
                        <button type="button" class="btn-preset" id="btnPlus50k" style="flex: 1; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            +50.000
                        </button>
                        <button type="button" class="btn-preset" id="btnPlus100k" style="flex: 1; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            +100.000
                        </button>
                    </div>
                </div>

                <!-- Kembalian Display -->
                <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 8px; padding: 8px 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; font-weight: 700; color: #6b21a8;">Kembalian:</span>
                    <strong id="changeAmountDisplay" style="font-size: 15px; color: #7e22ce;">Rp 0</strong>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                        Catatan Loket (Opsional)
                    </label>
                    <input type="text" name="catatan" id="catatanInput" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; font-size: 12px; box-sizing: border-box;" placeholder="Contoh: Tunai teller loket">
                </div>

                <button type="submit" id="btnSubmitPayment" {{ count($dueCards) === 0 ? 'disabled' : '' }} 
                        style="width: 100%; background: #059669; color: #FFFFFF; border: none; padding: 14px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(5,150,105,0.3);">
                    <i class="fa-solid fa-receipt"></i> Terima Uang & Terbitkan BKM
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.pos-card');
    const filterInput = document.getElementById('posFilterInput');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const cardCountDisplay = document.getElementById('cardCountDisplay');

    // Register fields
    const selectedInput = document.getElementById('posSelectedInstallmentId');
    const regMemberName = document.getElementById('regMemberName');
    const regMemberNik = document.getElementById('regMemberNik');
    const regLoanNo = document.getElementById('regLoanNo');
    const regStep = document.getElementById('regStep');
    const regPokok = document.getElementById('regPokok');
    const regJasa = document.getElementById('regJasa');
    const regDenda = document.getElementById('regDenda');
    const regTotalDisplay = document.getElementById('regTotalDisplay');
    const regDueDateDisplay = document.getElementById('regDueDateDisplay');
    const cashReceivedInput = document.getElementById('cashReceivedInput');
    const changeAmountDisplay = document.getElementById('changeAmountDisplay');
    const btnPas = document.getElementById('btnPas');
    const btnPlus50k = document.getElementById('btnPlus50k');
    const btnPlus100k = document.getElementById('btnPlus100k');

    let currentTotal = {{ count($dueCards) > 0 ? $dueCards[0]['total_tagihan'] : 0 }};

    function formatRupiah(number) {
        return 'Rp ' + Math.round(number).toLocaleString('id-ID');
    }

    function calculateChange() {
        const received = parseFloat(cashReceivedInput.value) || 0;
        const change = Math.max(0, received - currentTotal);
        changeAmountDisplay.textContent = formatRupiah(change);
    }

    function selectCard(card) {
        cards.forEach(c => {
            c.style.borderColor = '#e2e8f0';
            c.style.boxShadow = '0 1px 3px rgba(0,0,0,0.04)';
            const check = c.querySelector('.pos-check-badge');
            if (check) check.style.display = 'none';
        });

        card.style.borderColor = '#059669';
        card.style.boxShadow = '0 8px 16px -2px rgba(5,150,105,0.15)';
        const check = card.querySelector('.pos-check-badge');
        if (check) check.style.display = 'flex';

        // Extract data
        const id = card.dataset.id;
        const name = card.dataset.name;
        const nik = card.dataset.nik;
        const loan = card.dataset.loan;
        const step = card.dataset.step;
        const tenor = card.dataset.tenor;
        const due = card.dataset.due;
        const pokok = parseFloat(card.dataset.pokok) || 0;
        const jasa = parseFloat(card.dataset.jasa) || 0;
        const denda = parseFloat(card.dataset.denda) || 0;
        const total = parseFloat(card.dataset.total) || 0;

        currentTotal = total;
        selectedInput.value = id;
        regMemberName.textContent = name;
        regMemberNik.textContent = 'NIK: ' + nik;
        regLoanNo.textContent = loan;
        regStep.textContent = 'Angs Ke-' + step + ' / ' + tenor;
        regPokok.textContent = formatRupiah(pokok);
        regJasa.textContent = formatRupiah(jasa);
        regDenda.textContent = formatRupiah(denda);
        regTotalDisplay.textContent = formatRupiah(total);
        regDueDateDisplay.textContent = 'Jatuh Tempo: ' + due;

        cashReceivedInput.value = Math.round(total);
        calculateChange();
    }

    cards.forEach(card => {
        card.addEventListener('click', function() {
            selectCard(this);
        });
    });

    // Real-time Search Filter
    if (filterInput) {
        filterInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            btnClearSearch.style.display = query.length > 0 ? 'inline' : 'none';
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const nik = card.dataset.nik.toLowerCase();
                const loan = card.dataset.loan.toLowerCase();

                if (name.includes(query) || nik.includes(query) || loan.includes(query)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            cardCountDisplay.textContent = visibleCount;
        });

        btnClearSearch.addEventListener('click', function() {
            filterInput.value = '';
            filterInput.dispatchEvent(new Event('input'));
            filterInput.focus();
        });
    }

    // Cash Calculator
    if (cashReceivedInput) {
        cashReceivedInput.addEventListener('input', calculateChange);
    }

    if (btnPas) {
        btnPas.addEventListener('click', function() {
            cashReceivedInput.value = Math.round(currentTotal);
            calculateChange();
        });
    }

    if (btnPlus50k) {
        btnPlus50k.addEventListener('click', function() {
            const val = parseFloat(cashReceivedInput.value) || currentTotal;
            cashReceivedInput.value = Math.round(val + 50000);
            calculateChange();
        });
    }

    if (btnPlus100k) {
        btnPlus100k.addEventListener('click', function() {
            const val = parseFloat(cashReceivedInput.value) || currentTotal;
            cashReceivedInput.value = Math.round(val + 100000);
            calculateChange();
        });
    }

    calculateChange();
});
</script>
@endsection
