<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'user_id', 'guest_name', 'guest_email', 'assigned_to', 'status', 'bot_enabled', 'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'bot_enabled' => 'boolean',
    ];

    public const STATUSES = [
        'ouverte' => 'Ouverte',
        'en_cours' => 'En cours',
        'fermee' => 'Fermée',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->oldest();
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function customerName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Visiteur';
    }

    public function unreadByStaffCount(): int
    {
        return $this->messages()->where('sender_type', 'client')->whereNull('read_at')->count();
    }

    public function unreadByCustomerCount(): int
    {
        return $this->messages()->where('sender_type', 'staff')->whereNull('read_at')->count();
    }

    public static function unreadForStaffCount(): int
    {
        return self::where('status', '!=', 'fermee')
            ->whereHas('messages', fn ($q) => $q->where('sender_type', 'client')->whereNull('read_at'))
            ->count();
    }
}
