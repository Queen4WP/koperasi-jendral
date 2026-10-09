<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        // Enforce immutability of business content (Section 16)
        static::updating(function ($notification) {
            $dirty = array_keys($notification->getDirty());
            $allowed = ['is_read', 'read_at', 'updated_at'];
            $disallowed = array_diff($dirty, $allowed);
            if (!empty($disallowed)) {
                throw new RuntimeException('Notification content is immutable. Only read state may be modified.');
            }
        });

        // Enforce immutability against row deletion (Option A: prohibit deletion)
        // Note: Model lifecycle events protect Eloquent operations; direct SQL / query-builder operations bypass model hooks.
        static::deleting(function ($notification) {
            throw new RuntimeException('Notification records are immutable and cannot be deleted.');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function markAsRead(): self
    {
        if (!$this->is_read) {
            $this->forceFill([
                'is_read' => true,
                'read_at' => now(),
            ])->save();
        }

        return $this;
    }

    public function markAsUnread(): self
    {
        if ($this->is_read) {
            $this->forceFill([
                'is_read' => false,
                'read_at' => null,
            ])->save();
        }

        return $this;
    }

    public function getPayloadAttribute(): ?array
    {
        return $this->data;
    }
}
