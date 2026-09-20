<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\Request as RequestFacade;

class AnalyticsRecorder
{
    /**
     * Records an analytics event tied to the current request's tracked
     * session (set by TrackVisit). No-ops silently if the request wasn't
     * tracked (admin traffic, bot, excluded route) — callers never need to
     * check first.
     */
    public static function record(string $eventType, array $attributes = []): void
    {
        $sessionId = RequestFacade::attributes->get('analytics_session_id');

        if (! $sessionId) {
            return;
        }

        AnalyticsEvent::create(array_merge([
            'session_id' => $sessionId,
            'event_type' => $eventType,
            'created_at' => now(),
        ], $attributes));
    }
}
