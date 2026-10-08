<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    protected $fillable = [
        'installment_id',
        'admin_id',
        'nominal_bayar',
        'denda_dibayar',
        'metode_pembayaran',
        'tanggal_bayar',
        'payment_reference',
        'external_reference',
        'status',
    ];

    protected $casts = [
        'nominal_bayar' => 'decimal:2',
        'denda_dibayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    public function installment()
    {
        return $this->belongsTo(LoanInstallment::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
