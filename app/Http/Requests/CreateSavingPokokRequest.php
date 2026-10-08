<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSavingPokokRequest extends FormRequest
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
        ];
    }
}
