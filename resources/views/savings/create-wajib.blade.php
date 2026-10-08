@extends('layouts.app')
@section('header_title', 'Buka Simpanan Wajib')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h4 style="margin-top: 0;">Form Simpanan Wajib</h4>
    <x-validation-errors />
    
    <form method="POST" action="{{ route('member.savings.wajib.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="periode">Periode (Bulan)</label>
            <input type="date" name="periode" id="periode" value="{{ old('periode', date('Y-m-d')) }}" class="form-control" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div class="form-group">
            <label for="jatuh_tempo">Jatuh Tempo (Opsional)</label>
            <input type="date" name="jatuh_tempo" id="jatuh_tempo" value="{{ old('jatuh_tempo') }}" class="form-control" style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <div class="form-group">
            <label for="nominal">Nominal Simpanan (Rp)</label>
            <input type="number" name="nominal" id="nominal" value="{{ old('nominal', 10000) }}" class="form-control" min="0" required style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px;">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;">Ajukan Simpanan Wajib</button>
    </form>
</div>
@endsection
