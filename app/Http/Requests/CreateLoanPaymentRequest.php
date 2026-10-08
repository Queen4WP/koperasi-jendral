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
            'bukti_pembayaran' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'external_reference' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'nominal_bayar' => ['nullable', 'numeric'],
            'metode_pembayaran' => ['nullable', 'string', 'max:50'],
            'tanggal_bayar' => ['nullable', 'date'],
        ];
    }
}
