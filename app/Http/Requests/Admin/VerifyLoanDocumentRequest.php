<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VerifyLoanDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy in Controller
    }

    public function rules(): array
    {
        // This request is specifically used for rejecting a document (verifyAsUnsuitable)
        return [
            'catatan' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
            'catatan.min' => 'Catatan penolakan minimal 3 karakter.',
            'catatan.max' => 'Catatan penolakan maksimal 1000 karakter.',
        ];
    }
}
