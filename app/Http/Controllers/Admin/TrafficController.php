<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsPageView;
use App\Models\AnalyticsSession;
use App\Models\AnalyticsVisitor;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TrafficController extends Controller
{
    public function index()
    {
        $since = now()->subDays(29)->startOfDay();

        $visitsByDay = collect(range(29, 0))->map(function ($i) {
            $day = now()->subDays($i);

            return [
                'label' => $day->translatedFormat('d M'),
                'value' => AnalyticsSession::whereDate('started_at', $day->toDateString())->count(),
            ];
        });

        $visitorsTotal = AnalyticsVisitor::where('first_seen_at', '>=', $since)->count();
        $sessionsTotal = AnalyticsSession::where('started_at', '>=', $since)->count();
        $ordersTotal = Order::where('created_at', '>=', $since)->count();
        $revenueTotal = (float) Order::where('created_at', '>=', $since)
            ->whereNotIn('status', ['annulee', 'remboursee', 'brouillon'])
            ->sum('total');

        $deviceBreakdown = AnalyticsSession::where('started_at', '>=', $since)
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type');

        $topPages = AnalyticsPageView::where('viewed_at', '>=', $since)
            ->select('url', DB::raw('COUNT(*) as total'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $topProducts = AnalyticsEvent::where('event_type', 'product_view')
            ->where('created_at', '>=', $since)
            ->whereNotNull('product_id')
            ->with('product:id,name,slug')
            ->select('product_id', DB::raw('COUNT(*) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(8)
            ->get()
            ->map(fn ($row) => ['name' => $row->product?->name ?? '—', 'views' => $row->total]);

        $funnel = [
            'sessions' => $sessionsTotal,
            'product_view' => AnalyticsEvent::where('event_type', 'product_view')->where('created_at', '>=', $since)->distinct('session_id')->count('session_id'),
            'add_to_cart' => AnalyticsEvent::where('event_type', 'add_to_cart')->where('created_at', '>=', $since)->distinct('session_id')->count('session_id'),
            'purchase' => AnalyticsEvent::where('event_type', 'purchase')->where('created_at', '>=', $since)->distinct('session_id')->count('session_id'),
        ];

        return Inertia::render('Admin/Traffic/Index', [
            'stats' => [
                'visitors' => $visitorsTotal,
                'sessions' => $sessionsTotal,
                'orders' => $ordersTotal,
                'revenue' => $revenueTotal,
            ],
            'visitsByDay' => $visitsByDay,
            'deviceBreakdown' => $deviceBreakdown,
            'topPages' => $topPages,
            'topProducts' => $topProducts,
            'funnel' => $funnel,
        ]);
    }

    public function live()
    {
        $sessions = AnalyticsSession::with(['visitor:id,visitor_uid,country'])
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->latest('last_activity_at')
            ->take(50)
            ->get()
            ->map(function (AnalyticsSession $session) {
                $lastPage = $session->pageViews()->latest('viewed_at')->value('url');

                return [
                    'visitor_uid' => $session->visitor?->visitor_uid,
                    'page' => $lastPage,
                    'device_type' => $session->device_type,
                    'browser' => $session->browser,
                    'country' => $session->visitor?->country,
                    'last_activity_at' => $session->last_activity_at->diffForHumans(),
                ];
            });

        return response()->json(['sessions' => $sessions]);
    }
}
