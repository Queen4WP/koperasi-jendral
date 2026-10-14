<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'member_id',
        'nomor_pinjaman',
        'jumlah_pinjaman',
        'tenor',
        'tujuan_pinjaman',
        'keterangan',
        'persen_jasa',
        'total_jasa',
        'total_kewajiban',
        'angsuran_per_bulan',
        'bank_name',
        'bank_account_number',
        'bank_account_owner',
        'status',
        'rejection_reason',
        'approval_note',
        'reviewed_by',
        'finance_id',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'disbursed_at',
        'paid_off_at',
    ];

    protected $casts = [
        'jumlah_pinjaman' => 'decimal:2',
        'persen_jasa' => 'decimal:2',
        'total_jasa' => 'decimal:2',
        'total_kewajiban' => 'decimal:2',
        'angsuran_per_bulan' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'paid_off_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function finance()
    {
        return $this->belongsTo(User::class, 'finance_id');
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class);
    }

    public function disbursement()
    {
        return $this->hasOne(Disbursement::class);
    }
}
