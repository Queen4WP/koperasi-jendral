<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsTransaction extends Model
{
    protected $fillable = [
        'saving_id',
        'admin_id',
        'nominal_bayar',
        'metode_pembayaran',
        'tanggal_bayar',
        'payment_reference',
        'external_reference',
        'bukti_pembayaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'nominal_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    public function savings()
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function getSavingAttribute()
    {
        return $this->savings;
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
