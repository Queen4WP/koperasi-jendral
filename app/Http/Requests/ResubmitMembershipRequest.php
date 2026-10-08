<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResubmitMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        if ($user->role !== 'member') {
            return false;
        }

        // If route has {member} parameter, enforce strict ownership
        $targetMember = $this->route('member');
        if ($targetMember) {
            if ($targetMember instanceof Member) {
                if ($targetMember->user_id !== $user->id) {
                    return false;
                }
            } elseif (is_numeric($targetMember)) {
                $target = Member::find($targetMember);
                if (!$target || $target->user_id !== $user->id) {
                    return false;
                }
            }
        }

        $member = $user->member;
        return $member !== null && $member->status === 'rejected';
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
        $member = auth()->user()?->member;
        $memberId = $member ? $member->id : null;

        return [
            'name' => ['required_without:nama', 'string', 'max:255'],
            'nama' => ['required_without:name', 'string', 'max:255'],
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                Rule::unique('members', 'nik')->ignore($memberId),
            ],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:20'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'perusahaan' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.size' => 'NIK harus berjumlah 16 digit.',
            'nik.regex' => 'NIK harus berupa 16 angka numerik.',
            'nik.unique' => 'NIK sudah digunakan oleh anggota lain.',
        ];
    }
}
