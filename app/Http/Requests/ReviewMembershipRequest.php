<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewMembershipRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'catatan' => ['required_if:action,reject', 'nullable', 'string', 'max:255'],
        ];
    }
}
