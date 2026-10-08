<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoanPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nominal_bayar' => ['required', 'numeric', 'min:0'],
            'metode_pembayaran' => ['required', 'string', 'max:50'],
            'tanggal_bayar' => ['required', 'date'],
            'external_reference' => ['nullable', 'string', 'max:100'],
        ];
    }
}
