<?php

namespace App\Support\Chat;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\FaqEntry;
use App\Models\Order;
use App\Models\Product;
use App\Support\Notifications\NotificationService;
use Illuminate\Support\Str;

class ChatbotService
{
    protected const GREETINGS = [
        'Bonjour 👋 Comment puis-je vous aider aujourd\'hui ?',
        'Bonjour et bienvenue chez Central d\'Achat 🌿 Que puis-je faire pour vous ?',
    ];

    protected const IDENTITY_REPLY = 'Je suis l\'assistant virtuel de Central d\'Achat 🌿 Je peux répondre à vos questions sur les produits, les commandes, la livraison, le paiement et vous mettre en relation avec notre équipe si besoin.';

    protected const THANKS_REPLIES = [
        'Avec plaisir 😊 Je reste disponible si vous avez d\'autres questions.',
        'Je vous en prie ! N\'hésitez pas si vous avez besoin d\'autre chose.',
    ];

    protected const GOODBYE_REPLY = 'Merci d\'avoir discuté avec Central d\'Achat 🌿 À bientôt !';

    protected const CLARIFY_REPLY = 'Pas de souci, reformulez votre question ou donnez-moi un peu plus de détails et je vous aide.';

    public function __construct(protected NotificationService $notifications, protected AiChatService $ai)
    {
    }

    public function respond(Conversation $conversation, string $message): ?ChatMessage
    {
        if (! $conversation->bot_enabled) {
            return null;
        }

        $intent = Intent::classify($message);

        if ($intent === Intent::BONJOUR) {
            return $this->botMessage($conversation, self::GREETINGS[array_rand(self::GREETINGS)], intent: $intent);
        }

        if ($intent === Intent::IDENTITE_ASSISTANT) {
            return $this->botMessage($conversation, self::IDENTITY_REPLY, intent: $intent);
        }

        if ($intent === Intent::REMERCIEMENT) {
            return $this->botMessage($conversation, self::THANKS_REPLIES[array_rand(self::THANKS_REPLIES)], intent: $intent);
        }

        if ($intent === Intent::AU_REVOIR) {
            return $this->botMessage($conversation, self::GOODBYE_REPLY, intent: $intent);
        }

        if ($intent === Intent::INCOMPREHENSION) {
            return $this->botMessage($conversation, self::CLARIFY_REPLY, intent: $intent);
        }

        if ($intent === Intent::CONTACT_AGENT) {
            return $this->handoff($conversation, $intent);
        }

        $hasOrderNumber = (bool) preg_match('/CA-\d{8}-[A-Z0-9]{6}/i', $message);

        if ($hasOrderNumber || $intent === Intent::SUIVI_COMMANDE) {
            if ($reply = $this->tryOrderLookup($conversation, $message, $intent, $hasOrderNumber)) {
                return $reply;
            }
        }

        if ($reply = $this->tryFaq($conversation, $message, $intent)) {
            return $reply;
        }

        return $this->fallback($conversation, $message, $intent);
    }

    public function transfer(Conversation $conversation): ChatMessage
    {
        return $this->handoff($conversation, Intent::CONTACT_AGENT);
    }

    protected function handoff(Conversation $conversation, ?string $intent = null): ChatMessage
    {
        $conversation->update(['bot_enabled' => false]);

        $reply = $this->botMessage(
            $conversation,
            'Bien sûr 😊 Je vous mets en relation avec un membre de notre équipe, merci de patienter quelques instants.',
            intent: $intent ?? Intent::CONTACT_AGENT
        );

        $this->notifyStaffOnce($conversation, 'a demandé à parler à un agent.');

        return $reply;
    }

    protected function tryOrderLookup(Conversation $conversation, string $message, ?string $intent, bool $hasOrderNumber): ?ChatMessage
    {
        $order = null;

        if ($hasOrderNumber) {
            preg_match('/CA-\d{8}-[A-Z0-9]{6}/i', $message, $matches);
            $order = Order::where('order_number', strtoupper($matches[0]))->first();
        } elseif ($conversation->user_id) {
            $order = Order::where('user_id', $conversation->user_id)->latest()->first();
        }

        if ($order) {
            $statusLabel = Order::STATUSES[$order->status] ?? $order->status;

            return $this->botMessage(
                $conversation,
                "Votre commande {$order->order_number} est actuellement : {$statusLabel}.",
                [['label' => 'Voir la commande '.$order->order_number, 'url' => route('commande.confirmation', $order->order_number)]],
                intent: Intent::SUIVI_COMMANDE
            );
        }

        // Tracking intent detected but nothing resolvable yet — ask rather than guess.
        return $this->botMessage(
            $conversation,
            $conversation->user_id
                ? "Je ne trouve pas de commande associée à votre compte pour le moment. Pouvez-vous me communiquer votre numéro de commande (ex. CA-20260101-ABC123) ?"
                : "Pouvez-vous me communiquer votre numéro de commande (ex. CA-20260101-ABC123) pour que je vérifie son statut ?",
            intent: Intent::SUIVI_COMMANDE
        );
    }

    protected function tryFaq(Conversation $conversation, string $message, ?string $intent): ?ChatMessage
    {
        $match = FaqEntry::search($message, 1, $intent)->first();

        if (! $match) {
            return null;
        }

        $match->registerHit();

        $links = $this->productLinks($message);

        return $this->botMessage($conversation, $match->answer, $links, intent: $intent ?? $match->intent, source: 'faq');
    }

    protected function fallback(Conversation $conversation, string $message, ?string $intent): ChatMessage
    {
        $links = $this->productLinks($message);

        if ($aiAnswer = $this->ai->answer($conversation, $message)) {
            return $this->botMessage($conversation, $aiAnswer, $links, intent: $intent, source: 'ai');
        }

        if ($links) {
            $body = 'Voici ce que j\'ai trouvé dans notre catalogue en lien avec votre question. Si ce n\'est pas ce que vous cherchez, un membre de notre équipe va vous répondre.';
        } else {
            $body = 'Je ne dispose pas encore de cette information 🙏 Un membre de notre équipe va vous répondre rapidement. En attendant, vous pouvez parcourir notre catalogue.';
            $links = [['label' => 'Voir tous les produits', 'url' => route('produits.index')]];
        }

        $reply = $this->botMessage($conversation, $body, $links, intent: $intent);

        $this->notifyStaffOnce($conversation, 'attend une réponse (aucune réponse automatique trouvée).');

        return $reply;
    }

    protected function productLinks(string $message): array
    {
        $words = FaqEntry::tokenize($message);

        if (empty($words)) {
            return [];
        }

        $query = Product::where('is_active', true);
        $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                $q->orWhere('name', 'like', "%{$word}%");
            }
        });

        return $query->limit(3)->get()->map(fn (Product $product) => [
            'label' => $product->name,
            'url' => route('produits.show', $product->slug),
        ])->all();
    }

    protected function botMessage(Conversation $conversation, string $body, array $links = [], ?string $intent = null, ?string $source = null): ChatMessage
    {
        $meta = array_filter([
            'links' => $links ?: null,
            'source' => $source,
            'intent' => $intent,
        ]);

        return $conversation->messages()->create([
            'sender_type' => 'bot',
            'body' => $body,
            'meta' => $meta ?: null,
        ]);
    }

    protected function notifyStaffOnce(Conversation $conversation, string $context): void
    {
        $alreadyNotified = \App\Models\Notification::where('event_key', 'nouvelle_conversation')
            ->where('data->conversation_id', $conversation->id)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $this->notifications->send('nouvelle_conversation', NotificationService::staffRecipients('messagerie.voir'), [
            'conversation_id' => $conversation->id,
            'client_nom' => $conversation->customerName(),
            'message_extrait' => Str::limit($context, 120),
            'conversation_lien' => route('admin.messagerie.show', $conversation),
        ]);
    }
}
