<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDocument extends Model
{
    protected $fillable = [
        'loan_id',
        'jenis_dokumen',
        'file_path',
        'status_verifikasi',
        'catatan',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
