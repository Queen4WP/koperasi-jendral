<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $fillable = [
        'loan_id',
        'angsuran_ke',
        'jatuh_tempo',
        'pokok_angsuran',
        'jasa_angsuran',
        'total_angsuran',
        'denda',
        'total_tagihan',
        'status',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'pokok_angsuran' => 'decimal:2',
        'jasa_angsuran' => 'decimal:2',
        'total_angsuran' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class, 'installment_id');
    }
}
