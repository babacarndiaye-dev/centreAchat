<?php

namespace App\Support\Notifications;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class PushNotificationService
{
    protected ?WebPush $client = null;

    public function enabled(): bool
    {
        return filled(config('services.vapid.public_key')) && filled(config('services.vapid.private_key'));
    }

    protected function client(): ?WebPush
    {
        if (! $this->enabled()) {
            return null;
        }

        return $this->client ??= new WebPush([
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);
    }

    public function send(PushSubscription $subscription, string $title, string $body, ?string $url = null): bool
    {
        $client = $this->client();

        if (! $client) {
            return false;
        }

        $sub = Subscription::create([
            'endpoint' => $subscription->endpoint,
            'publicKey' => $subscription->public_key,
            'authToken' => $subscription->auth_token,
        ]);

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? '/',
        ]);

        try {
            $report = $client->sendOneNotification($sub, $payload);
        } catch (Throwable $e) {
            Log::warning('PushNotificationService: envoi échoué', ['error' => $e->getMessage()]);

            return false;
        }

        if (! $report->isSuccess() && $report->isSubscriptionExpired()) {
            $subscription->delete();
        }

        return $report->isSuccess();
    }
}
