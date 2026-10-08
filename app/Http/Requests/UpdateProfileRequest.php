<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'member';
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nama') && !$this->has('name')) {
            $this->merge(['name' => $this->input('nama')]);
        }
        if ($this->has('name') && !$this->has('nama')) {
            $this->merge(['nama' => $this->input('name')]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required_without:nama', 'string', 'max:255'],
            'nama' => ['required_without:name', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:20'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'perusahaan' => ['nullable', 'string', 'max:100'],
        ];
    }
}
