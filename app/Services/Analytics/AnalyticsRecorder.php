<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\Log;

class AnalyticsRecorder
{
    /**
     * Records an analytics event tied to the current request's tracked
     * session (set by TrackVisit). No-ops silently if the request wasn't
     * tracked (admin traffic, bot, excluded route) — callers never need to
     * check first. Never lets a tracking failure break the caller.
     */
    public static function record(string $eventType, array $attributes = []): void
    {
        $sessionId = request()?->attributes->get('analytics_session_id');

        if (! $sessionId) {
            return;
        }

        try {
            AnalyticsEvent::create(array_merge([
                'session_id' => $sessionId,
                'event_type' => $eventType,
                'created_at' => now(),
            ], $attributes));
        } catch (\Throwable $e) {
            Log::warning('AnalyticsRecorder::record failed', ['event_type' => $eventType, 'message' => $e->getMessage()]);
        }
    }
}
