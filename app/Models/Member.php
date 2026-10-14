<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'pekerjaan',
        'perusahaan',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipVerifications()
    {
        return $this->hasMany(MembershipVerification::class);
    }

    public function latestVerification()
    {
        return $this->hasOne(MembershipVerification::class)->latestOfMany();
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function savingsWithdrawals()
    {
        return $this->hasMany(SavingsWithdrawal::class);
    }
}
