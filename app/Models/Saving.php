<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    protected $fillable = [
        'member_id',
        'jenis_simpanan',
        'periode',
        'nominal',
        'status',
        'jatuh_tempo',
    ];

    protected $casts = [
        'periode' => 'date',
        'jatuh_tempo' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->hasMany(SavingsTransaction::class, 'saving_id');
    }

    public function latestTransaction()
    {
        return $this->hasOne(SavingsTransaction::class, 'saving_id')->latestOfMany();
    }

    public function pendingTransaction()
    {
        return $this->hasOne(SavingsTransaction::class, 'saving_id')->where('status', 'pending');
    }
}
