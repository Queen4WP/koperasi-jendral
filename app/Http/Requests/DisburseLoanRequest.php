<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisburseLoanRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'nominal' => ['nullable', 'numeric'],
            'tanggal_pencairan' => ['required', 'date'],
            'nomor_referensi' => ['required', 'string', 'max:255'],
            'bukti_transfer' => ['nullable', 'string', 'max:255'],
        ];
    }
}
