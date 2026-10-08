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
            'tujuan_pinjaman' => ['required', 'string', Rule::in([
                'Modal Usaha',
                'Pendidikan',
                'Kebutuhan Rumah Tangga',
                'Renovasi Rumah',
                'Keperluan Lainnya',
            ])],
            'keterangan' => ['nullable', 'string'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_owner' => ['required', 'string', 'max:100'],
            'ktp' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bukti_penghasilan' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'rekening_bank' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bukti_usaha' => ['required_if:tujuan_pinjaman,Modal Usaha', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
