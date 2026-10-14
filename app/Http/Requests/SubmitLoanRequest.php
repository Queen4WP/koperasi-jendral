<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\KoperasiService;

class SubmitLoanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $min = KoperasiService::getMinLoanAmount();
        $max = KoperasiService::getMaxLoanAmount();

        return [
            'jumlah_pinjaman' => ['required', 'numeric', 'min:' . $min, 'max:' . $max],
            'tenor' => ['required', 'integer', Rule::in(KoperasiService::ALLOWED_TENORS)],
            'persen_jasa' => ['nullable', 'numeric'],
            'tujuan_pinjaman' => ['required', 'string', Rule::in([
                'Modal Usaha',
                'Pendidikan',
                'Kebutuhan Rumah Tangga',
                'Renovasi Rumah',
                'Keperluan Lainnya',
            ])],
            'keterangan' => ['nullable', 'string'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_owner' => ['required', 'string', 'max:100'],
            'ktp' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bukti_penghasilan' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'rekening_bank' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bukti_usaha' => ['required_if:tujuan_pinjaman,Modal Usaha', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages()
    {
        $minFmt = number_format(KoperasiService::getMinLoanAmount(), 0, ',', '.');
        $maxFmt = number_format(KoperasiService::getMaxLoanAmount(), 0, ',', '.');

        return [
            'jumlah_pinjaman.min' => "Batas minimum pinjaman anggota adalah Rp {$minFmt}.",
            'jumlah_pinjaman.max' => "Batas maksimum pinjaman anggota adalah Rp {$maxFmt}.",
        ];
    }
}
