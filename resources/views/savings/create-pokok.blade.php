@extends('layouts.app')
@section('header_title', 'Buka Simpanan Pokok')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h4 style="margin-top: 0;">Form Simpanan Pokok</h4>
    <x-validation-errors />
    
    <form method="POST" action="{{ route('member.savings.pokok.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="nominal">Nominal Simpanan Pokok (Rp)</label>
            <input type="number" name="nominal" id="nominal" value="55000" class="form-control" readonly style="width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ced4da; border-radius: 4px; background-color: #f8f9fa;">
            <small style="color: #6c757d;">Simpanan Pokok ditetapkan sebesar Rp 55.000 (dibayar satu kali).</small>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 15px; width: 100%;">Ajukan Simpanan</button>
    </form>
</div>
@endsection
