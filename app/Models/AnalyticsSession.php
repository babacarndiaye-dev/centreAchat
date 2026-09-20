<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticsSession extends Model
{
    protected $fillable = [
        'visitor_id', 'session_uid', 'started_at', 'last_activity_at', 'ended_at',
        'device_type', 'browser', 'os', 'entry_page', 'referrer',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(AnalyticsVisitor::class, 'visitor_id');
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(AnalyticsPageView::class, 'session_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class, 'session_id');
    }
}
