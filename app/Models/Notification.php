<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'action_text',
        'is_read',
        'read_at',
        'expires_at',
        'priority',
        'category'
    ];

    protected $casts = [
        'data' => 'string',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Notification types
    const TYPE_ALERT = 'alert';
    const TYPE_INFO = 'info';
    const TYPE_REMINDER = 'reminder';
    const TYPE_SYSTEM = 'system';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Categories
    const CATEGORY_LOAN = 'loan';
    const CATEGORY_ACCOUNT = 'account';
    const CATEGORY_TRANSACTION = 'transaction';
    const CATEGORY_SAVINGS = 'savings';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_MEMBER = 'member';
    const CATEGORY_BRANCH = 'branch';

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUrgent(): bool
    {
        return $this->priority === self::PRIORITY_URGENT;
    }

    public function isHighPriority(): bool
    {
        return in_array($this->priority, [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    // Static methods for creating notifications
    public static function createAlert($userId, $title, $message, $data = [], $actionUrl = null, $actionText = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_ALERT,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'priority' => self::PRIORITY_HIGH,
        ]);
    }

    public static function createInfo($userId, $title, $message, $data = [], $actionUrl = null, $actionText = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_INFO,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'priority' => self::PRIORITY_NORMAL,
        ]);
    }

    public static function createReminder($userId, $title, $message, $data = [], $actionUrl = null, $actionText = null, $expiresAt = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_REMINDER,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'priority' => self::PRIORITY_NORMAL,
            'expires_at' => $expiresAt,
        ]);
    }

    public static function createSystem($userId, $title, $message, $data = [], $actionUrl = null, $actionText = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_SYSTEM,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'priority' => self::PRIORITY_NORMAL,
            'category' => self::CATEGORY_SYSTEM,
        ]);
    }
}
