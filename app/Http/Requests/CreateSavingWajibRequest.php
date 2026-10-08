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
            'nominal' => ['nullable', 'numeric', 'in:100000'],
            'periode' => ['required', 'date_format:Y-m-d'],
            'jatuh_tempo' => ['nullable', 'date'],
        ];
    }
}
