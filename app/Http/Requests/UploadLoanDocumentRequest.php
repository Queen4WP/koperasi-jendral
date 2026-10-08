<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadLoanDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization will be handled in the controller using Gate and Policy
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'jenis_dokumen' => ['required', 'string', 'in:ktp,bukti_penghasilan,rekening_bank,bukti_usaha,jaminan'],
        ];
    }
}
