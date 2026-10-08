<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\KoperasiService;

class SubmitLoanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jumlah_pinjaman' => ['required', 'numeric', 'min:' . KoperasiService::MIN_LOAN_AMOUNT, 'max:' . KoperasiService::MAX_LOAN_AMOUNT],
            'tenor' => ['required', 'integer', Rule::in(KoperasiService::ALLOWED_TENORS)],
            'persen_jasa' => ['nullable', 'numeric'],
            'tujuan_pinjaman' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_owner' => ['required', 'string', 'max:100'],
        ];
    }
}
