<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    protected $fillable = [
        'loan_id',
        'admin_id',
        'nominal',
        'tanggal_pencairan',
        'bukti_transfer',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pencairan' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
