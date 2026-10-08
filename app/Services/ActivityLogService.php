<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     * Keys that must never be recorded in audit metadata.
     */
    protected const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
        'remember_token',
        'secret',
        'api_key',
        'private_key',
    ];

    /**
     * Record an immutable activity log entry.
     *
     * @param string $action Machine-readable action name (e.g. 'member.verified')
     * @param Model|null $subject The entity affected by this action
     * @param string|null $description Human-readable audit description
     * @param array|null $metadata Contextual safe metadata
     * @param int|null $actorId Optional explicit actor ID (defaults to auth()->id())
     * @return ActivityLog
     */
    public function record(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        ?array $metadata = null,
        ?int $actorId = null
    ): ActivityLog {
        $actorId = $actorId ?? (auth()->check() ? auth()->id() : null);

        $ipAddress = null;
        $userAgent = null;
        if (app()->bound('request')) {
            $request = request();
            if ($request) {
                $ipAddress = $request->ip();
                $userAgent = substr((string)$request->userAgent(), 0, 500);
            }
        }

        $sanitizedMetadata = $this->sanitizeMetadata($metadata);

        return ActivityLog::create([
            'user_id' => $actorId,
            'activity' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'keterangan' => $description,
            'data' => $sanitizedMetadata,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Remove sensitive information from metadata recursively.
     */
    protected function sanitizeMetadata(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $sanitized = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string)$key);
            if (in_array($lowerKey, self::SENSITIVE_KEYS, true)) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeMetadata($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
