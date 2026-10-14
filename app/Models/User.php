<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function membershipVerifications()
    {
        return $this->hasMany(MembershipVerification::class, 'admin_id');
    }

    public function reviewedLoans()
    {
        return $this->hasMany(Loan::class, 'reviewed_by');
    }

    public function savingsTransactions()
    {
        return $this->hasMany(SavingsTransaction::class, 'admin_id');
    }

    public function loanPayments()
    {
        return $this->hasMany(LoanPayment::class, 'admin_id');
    }

    public function disbursements()
    {
        return $this->hasMany(Disbursement::class, 'admin_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function employeePayrollSetting()
    {
        return $this->hasOne(EmployeePayrollSetting::class);
    }

    public function cashierClosings()
    {
        return $this->hasMany(CashierClosing::class, 'kasir_id');
    }

    public function financeClosings()
    {
        return $this->hasMany(CashierClosing::class, 'finance_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
