<?php

namespace App\Services;

use App\Constants\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class NotificationService
{
    /**
     * Keys that must never be recorded in notification payload.
     */
    protected const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
        'remember_token',
        'csrf_token',
        'secret',
        'api_key',
        'private_key',
        'authorization',
        'session',
        'session_id',
        'credential',
        'credentials',
        'nik',
        'ktp',
        'file_ktp',
        'foto_ktp',
        'slip_gaji',
        'bukti_transfer',
        'bukti_bayar',
        'bukti_pembayaran',
        'bank_account',
        'account_number',
        'nomor_rekening',
        'no_rekening',
        'no_rek',
        'cvv',
        'pin',
        'card_number',
        'cookie',
        'cookies',
        'bearer',
    ];

    /**
     * Suffixes that indicate sensitive credentials.
     */
    protected const SENSITIVE_SUFFIXES = [
        '_token',
        '_secret',
        '_password',
    ];

    /**
     * Create and deliver a notification to a specific recipient.
     *
     * @param User|int $recipient
     * @param string $type Centralized notification identifier (e.g. NotificationType::LOAN_APPROVED)
     * @param string $title Short title of the notification
     * @param string $message Detailed human-readable notification message
     * @param array|null $payload Safe minimal contextual data
     * @return Notification
     */
    public function send(
        User|int $recipient,
        string $type,
        string $title,
        string $message,
        ?array $payload = null
    ): Notification {
        // SEC-01: Validate notification type against centralized NotificationType registry
        if (empty($type) || !NotificationType::isValid($type)) {
            throw new InvalidArgumentException("Tipe notifikasi tidak valid: {$type}");
        }

        $userId = $recipient instanceof User ? $recipient->id : (int) $recipient;

        if (empty($userId) || $userId <= 0) {
            throw new InvalidArgumentException('Recipient ID must be a positive integer.');
        }

        // SEC-05: Verify recipient existence in database (do not trust in-memory state or foreign-key error alone)
        if (!User::where('id', $userId)->exists()) {
            throw new InvalidArgumentException("Recipient user with ID {$userId} not found.");
        }

        $sanitizedPayload = $this->sanitizePayload($payload);

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $sanitizedPayload,
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Deliver the same notification to multiple recipients atomically.
     *
     * @param iterable<User|int> $recipients
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array|null $payload
     * @return list<Notification>
     */
    public function sendMany(
        iterable $recipients,
        string $type,
        string $title,
        string $message,
        ?array $payload = null
    ): array {
        // SEC-03: Wrap batch dispatch in transaction to guarantee all-or-nothing delivery
        return DB::transaction(function () use ($recipients, $type, $title, $message, $payload) {
            $created = [];
            foreach ($recipients as $recipient) {
                $created[] = $this->send($recipient, $type, $title, $message, $payload);
            }

            return $created;
        });
    }

    /**
     * Mark a specific notification as read, ensuring recipient ownership.
     */
    public function markAsRead(Notification $notification, User $user): Notification
    {
        if ($notification->user_id !== $user->id) {
            throw new RuntimeException('Unauthorized notification access: user does not own this notification.');
        }

        return $notification->markAsRead();
    }

    /**
     * Mark all unread notifications for a user as read.
     */
    public function markAllAsRead(User $user): int
    {
        $unread = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->get();

        $count = 0;
        foreach ($unread as $notification) {
            $notification->markAsRead();
            $count++;
        }

        return $count;
    }

    /**
     * Count unread notifications for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get paginated or listed notifications for a user.
     *
     * @param User $user
     * @param int $limit
     * @param bool $unreadOnly
     * @return LengthAwarePaginator
     */
    public function getUserNotifications(User $user, int $limit = 20, bool $unreadOnly = false): LengthAwarePaginator
    {
        $query = Notification::where('user_id', $user->id)->latest('id');

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->paginate($limit);
    }

    /**
     * Recursively sanitize payload to remove sensitive data and credentials.
     */
    public function sanitizePayload(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $sanitized = [];
        foreach ($data as $key => $value) {
            if ($this->isSensitiveKey((string)$key)) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizePayload($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if a key name indicates sensitive information.
     */
    protected function isSensitiveKey(string $key): bool
    {
        $lowerKey = strtolower(trim($key));
        if (in_array($lowerKey, self::SENSITIVE_KEYS, true)) {
            return true;
        }

        foreach (self::SENSITIVE_SUFFIXES as $suffix) {
            if (str_ends_with($lowerKey, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
