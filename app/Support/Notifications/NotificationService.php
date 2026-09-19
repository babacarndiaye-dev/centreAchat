<?php

namespace App\Support\Notifications;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\PushSubscription;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    public function __construct(protected PushNotificationService $push)
    {
    }

    public static function recipientFromUser(User $user): array
    {
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];
    }

    public static function recipientFromGuest(string $name, ?string $email, ?string $phone): array
    {
        return [
            'user_id' => null,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ];
    }

    /**
     * Staff users (super-admins or role holders) allowed to see a given permission.
     *
     * @return array<int, array>
     */
    public static function staffRecipients(string $permission): array
    {
        $roleIds = Role::whereHas('permissions', fn ($q) => $q->where('permission', $permission))->pluck('id');

        return User::where('is_admin', true)
            ->orWhereIn('role_id', $roleIds)
            ->get()
            ->map(fn (User $user) => self::recipientFromUser($user))
            ->all();
    }

    /**
     * @param  iterable<array>  $recipients  each entry: ['user_id' => ?int, 'name' => string, 'email' => ?string, 'phone' => ?string]
     */
    public function send(string $eventKey, iterable $recipients, array $variables, ?array $channels = null): void
    {
        $templates = NotificationTemplate::where('event_key', $eventKey)
            ->where('is_active', true)
            ->when($channels, fn ($query) => $query->whereIn('channel', $channels))
            ->get();

        if ($templates->isEmpty()) {
            return;
        }

        foreach ($recipients as $recipient) {
            foreach ($templates as $template) {
                $this->dispatch($template, $recipient, $variables);
            }
        }
    }

    protected function dispatch(NotificationTemplate $template, array $recipient, array $variables): void
    {
        $rendered = $template->render([...$variables, 'destinataire_nom' => $recipient['name'] ?? '']);
        $title = $rendered['subject'] ?: NotificationEvents::label($template->event_key);
        $body = $rendered['body'];

        $base = [
            'user_id' => $recipient['user_id'] ?? null,
            'event_key' => $template->event_key,
            'channel' => $template->channel,
            'title' => $title,
            'body' => $body,
            'data' => $variables,
        ];

        match ($template->channel) {
            'interne' => $this->sendInternal($base, $recipient),
            'email' => $this->sendEmail($base, $recipient),
            'sms' => $this->sendSms($base, $recipient),
            'whatsapp' => $this->sendWhatsapp($base, $recipient),
            'push' => $this->sendPush($base, $recipient),
            default => null,
        };
    }

    protected function sendPush(array $base, array $recipient): void
    {
        if (! $recipient['user_id']) {
            return;
        }

        $subscriptions = PushSubscription::where('user_id', $recipient['user_id'])->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $sentAny = false;

        foreach ($subscriptions as $subscription) {
            if ($this->push->send($subscription, $base['title'], $base['body'])) {
                $sentAny = true;
            }
        }

        Notification::create([
            ...$base,
            'recipient' => 'push',
            'status' => $this->push->enabled() ? ($sentAny ? 'envoyee' : 'echouee') : 'en_attente_integration',
            'error' => $this->push->enabled() ? null : 'Clés VAPID non configurées.',
        ]);
    }

    protected function sendInternal(array $base, array $recipient): void
    {
        if (! $recipient['user_id']) {
            return;
        }

        Notification::create([...$base, 'recipient' => null, 'status' => 'envoyee']);
    }

    protected function sendEmail(array $base, array $recipient): void
    {
        if (empty($recipient['email'])) {
            return;
        }

        $status = 'envoyee';
        $error = null;

        $ctaUrl = collect($base['data'] ?? [])->first(fn ($value, $key) => str_ends_with((string) $key, '_lien'));

        try {
            Mail::send('emails.layout', [
                'title' => $base['title'],
                'body' => $base['body'],
                'ctaUrl' => $ctaUrl,
            ], function ($message) use ($recipient, $base) {
                $message->to($recipient['email'])->subject($base['title']);
            });
        } catch (Throwable $e) {
            $status = 'echouee';
            $error = $e->getMessage();
        }

        Notification::create([...$base, 'recipient' => $recipient['email'], 'status' => $status, 'error' => $error]);
    }

    protected function sendSms(array $base, array $recipient): void
    {
        if (empty($recipient['phone'])) {
            return;
        }

        Log::info('[SMS - intégration à configurer] '.$recipient['phone'].' : '.$base['body']);

        Notification::create([
            ...$base,
            'recipient' => $recipient['phone'],
            'status' => 'en_attente_integration',
            'error' => 'Aucune passerelle SMS configurée — message journalisé uniquement.',
        ]);
    }

    protected function sendWhatsapp(array $base, array $recipient): void
    {
        if (empty($recipient['phone'])) {
            return;
        }

        Log::info('[WhatsApp - intégration à configurer] '.$recipient['phone'].' : '.$base['body']);

        Notification::create([
            ...$base,
            'recipient' => $recipient['phone'],
            'status' => 'en_attente_integration',
            'error' => 'Aucune passerelle WhatsApp configurée — message journalisé uniquement.',
        ]);
    }
}
