<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSavingWajibRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'member_id' => ['nullable', 'exists:members,id'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'periode' => ['required', 'date_format:Y-m-d'], // Format expected by DB is date
            'jatuh_tempo' => ['nullable', 'date'],
        ];
    }
}
