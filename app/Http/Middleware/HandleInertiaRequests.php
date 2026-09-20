<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shared = [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'cart' => [
                'count' => \App\Support\Cart::count(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'site' => [
                'name' => \App\Models\Setting::get('site_name') ?: "Centrale d'achat",
                'logoUrl' => ($logoPath = \App\Models\Setting::get('logo_path')) ? asset('fichiers/'.$logoPath) : null,
                'announcementActive' => \App\Models\Setting::getBool('announcement_active', false),
                'announcementText' => \App\Models\Setting::get('announcement_text'),
                'address' => \App\Models\Setting::get('address', 'Rond-Point Malicounda, Mbour – Sénégal'),
                'showNewsletter' => \App\Models\Setting::getBool('show_newsletter', true),
            ],
        ];

        $user = $request->user();
        if ($user && $request->routeIs('admin.*') && $user->isStaff()) {
            $unreadChat = $user->hasPermission('messagerie.voir') ? \App\Models\Conversation::unreadForStaffCount() : 0;

            $shared['admin'] = [
                'nav' => \App\Support\AdminNav::forUser($user),
                'unreadChat' => $unreadChat,
                'unreadNotifications' => $user->unreadNotificationsCount(),
                'vapidPublicKey' => config('services.vapid.public_key'),
            ];
        }

        return $shared;
    }
}
