<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'updated_by',
    ];

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get a setting value by key with type casting and default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();
            if (!$setting || $setting->value === null) {
                return $default;
            }

            return match ($setting->type) {
                'integer' => (int) $setting->value,
                'float', 'numeric' => (float) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set a setting value by key.
     */
    public static function set(
        string $key,
        mixed $value,
        string $group = 'loan',
        string $type = 'string',
        ?string $description = null,
        ?int $updatedBy = null
    ): static {
        $stringValue = match ($type) {
            'json' => is_string($value) ? $value : json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'group' => $group,
                'type' => $type,
                'description' => $description,
                'updated_by' => $updatedBy,
            ]
        );
    }
}
