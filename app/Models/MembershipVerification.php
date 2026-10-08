<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipVerification extends Model
{
    protected $fillable = [
        'member_id',
        'admin_id',
        'status_keputusan',
        'catatan',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
