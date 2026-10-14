<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsWithdrawal extends Model
{
    protected $fillable = [
        'member_id',
        'kasir_id',
        'nominal',
        'nomor_referensi',
        'tanggal_penarikan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_penarikan' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}
