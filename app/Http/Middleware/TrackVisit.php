<?php

namespace App\Http\Middleware;

use App\Models\AnalyticsPageView;
use App\Models\AnalyticsSession;
use App\Models\AnalyticsVisitor;
use App\Services\Analytics\GeoLocator;
use App\Services\Analytics\UaParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    private const EXCLUDED_ROUTES = ['chat.state', 'fichiers.fallback', 'pwa.manifest', 'pwa.offline'];

    private const VISITOR_COOKIE = 'av_uid';

    private const SESSION_COOKIE = 'as_uid';

    private const SESSION_TIMEOUT_MINUTES = 30;

    private const VISITOR_COOKIE_DAYS = 365;

    private bool $isNewVisitor = false;

    private ?string $visitorIp = null;

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $visitor = $this->resolveVisitor($request);
        $session = $this->resolveSession($request, $visitor);

        if ($request->isMethod('get')) {
            AnalyticsPageView::create([
                'session_id' => $session->id,
                'url' => $request->path(),
                'viewed_at' => now(),
            ]);
        }

        $request->attributes->set('analytics_session_id', $session->id);

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $this->isNewVisitor || ! $this->visitorIp) {
            return;
        }

        $location = GeoLocator::lookup($this->visitorIp);

        if (! $location) {
            return;
        }

        AnalyticsVisitor::where('visitor_uid', $request->cookie(self::VISITOR_COOKIE))
            ->update(['country' => $location['country'], 'city' => $location['city']]);
    }

    private function shouldSkip(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, self::EXCLUDED_ROUTES, true)) {
            return true;
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return true;
        }

        if ($request->user()?->isStaff()) {
            return true;
        }

        $userAgent = (string) $request->userAgent();

        if ($userAgent === '' || UaParser::isBot($userAgent)) {
            return true;
        }

        return false;
    }

    private function resolveVisitor(Request $request): AnalyticsVisitor
    {
        $uid = $request->cookie(self::VISITOR_COOKIE);
        $visitor = $uid ? AnalyticsVisitor::where('visitor_uid', $uid)->first() : null;

        if ($visitor) {
            $visitor->update(['last_seen_at' => now()]);

            return $visitor;
        }

        $newUid = 'VIS-'.strtoupper(bin2hex(random_bytes(4)));

        $visitor = AnalyticsVisitor::create([
            'visitor_uid' => $newUid,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        Cookie::queue(self::VISITOR_COOKIE, $newUid, self::VISITOR_COOKIE_DAYS * 24 * 60);

        $this->isNewVisitor = true;
        $this->visitorIp = $request->ip();

        return $visitor;
    }

    private function resolveSession(Request $request, AnalyticsVisitor $visitor): AnalyticsSession
    {
        $uid = $request->cookie(self::SESSION_COOKIE);
        $session = $uid ? AnalyticsSession::where('session_uid', $uid)->first() : null;

        if ($session && $session->last_activity_at?->gt(now()->subMinutes(self::SESSION_TIMEOUT_MINUTES))) {
            $session->update(['last_activity_at' => now()]);

            return $session;
        }

        if ($session) {
            $session->update(['ended_at' => $session->last_activity_at]);
        }

        $ua = UaParser::parse((string) $request->userAgent());
        $newUid = 'SES-'.strtoupper(bin2hex(random_bytes(4)));

        $newSession = AnalyticsSession::create([
            'visitor_id' => $visitor->id,
            'session_uid' => $newUid,
            'started_at' => now(),
            'last_activity_at' => now(),
            'device_type' => $ua['device_type'],
            'browser' => $ua['browser'],
            'os' => $ua['os'],
            'entry_page' => $request->path(),
            'referrer' => $request->headers->get('referer'),
        ]);

        Cookie::queue(self::SESSION_COOKIE, $newUid, self::SESSION_TIMEOUT_MINUTES);

        return $newSession;
    }
}
