<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierClosing extends Model
{
    protected $fillable = [
        'kasir_id',
        'finance_id',
        'tanggal_closing',
        'total_kas_masuk',
        'total_kas_keluar',
        'total_fisik_diserahkan',
        'status',
        'verified_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal_closing' => 'date',
        'total_kas_masuk' => 'decimal:2',
        'total_kas_keluar' => 'decimal:2',
        'total_fisik_diserahkan' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function finance()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }
}
