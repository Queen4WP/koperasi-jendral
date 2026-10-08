<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitLoanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jumlah_pinjaman' => ['required', 'numeric', 'min:1'],
            'tenor' => ['required', 'integer', 'min:1'],
            'persen_jasa' => ['required', 'numeric', 'min:0'],
            'tujuan_pinjaman' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_owner' => ['required', 'string', 'max:100'],
        ];
    }
}
