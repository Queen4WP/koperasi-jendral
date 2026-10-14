<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayrollSetting extends Model
{
    protected $fillable = [
        'user_id',
        'nominal_sukarela_bulanan',
        'is_active',
    ];

    protected $casts = [
        'nominal_sukarela_bulanan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
