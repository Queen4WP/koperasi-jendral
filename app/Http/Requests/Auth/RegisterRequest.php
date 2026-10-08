<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'nik' => ['required', 'string', 'size:16', 'unique:members,nik'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:20'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'perusahaan' => ['nullable', 'string', 'max:100'],
        ];
    }
}
