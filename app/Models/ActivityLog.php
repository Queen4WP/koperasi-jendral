<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
        'subject_type',
        'subject_id',
        'keterangan',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            throw new \RuntimeException('Activity logs are immutable audit records and cannot be updated.');
        });

        static::deleting(function ($model) {
            throw new \RuntimeException('Activity logs are immutable audit records and cannot be deleted.');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function getActionAttribute(): string
    {
        return (string) $this->activity;
    }

    public function setActionAttribute(string $value): void
    {
        $this->attributes['activity'] = $value;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->keterangan;
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['keterangan'] = $value;
    }

    public function getMetadataAttribute(): ?array
    {
        return $this->data;
    }

    public function setMetadataAttribute(?array $value): void
    {
        $this->attributes['data'] = $value ? json_encode($value) : null;
    }

    public function getActorIdAttribute(): ?int
    {
        return $this->user_id;
    }
}
