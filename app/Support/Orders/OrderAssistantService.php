<?php

namespace App\Support\Orders;

use App\Models\Conversation;
use App\Models\Order;
use App\Support\Ai\OpenAiClient;

class OrderAssistantService
{
    protected const PERISHABLE_CATEGORIES = ['Fruits & Légumes', 'Produits de la mer'];

    protected const LARGE_ORDER_THRESHOLD = 100000;

    public function __construct(protected OpenAiClient $client)
    {
    }

    /**
     * Rule-based, no AI call — cheap enough to run on every page view.
     *
     * @return array<int, array{type: string, severity: string, label: string}>
     */
    public function flags(Order $order): array
    {
        $order->loadMissing(['items.product.category', 'user']);

        return $this->detectFlags($order);
    }

    /**
     * AI-drafted recommendation for staff, plus — when relevant — a customer
     * message that is sent immediately through the chat conversation (this is
     * the one AI-triggered action this assistant is allowed to take on its own;
     * order status, payments, and refunds always stay a manual staff action).
     *
     * @return array{note: ?string, customer_message: ?string, sent: bool}
     */
    public function suggest(Order $order, array $flags): array
    {
        $order->loadMissing('items', 'user');

        $raw = $this->buildSuggestion($order, $flags);

        if ($raw === null) {
            return ['note' => null, 'customer_message' => null, 'sent' => false];
        }

        [$note, $customerMessage] = $this->extractSections($raw);

        $sent = false;
        if ($customerMessage) {
            $sent = $this->dispatchCustomerMessage($order, $customerMessage);
        }

        return ['note' => $note, 'customer_message' => $customerMessage, 'sent' => $sent];
    }

    protected function detectFlags(Order $order): array
    {
        $flags = [];

        if ($order->invoice_due_date && $order->invoice_due_date->isPast() && $order->payment_status !== 'paye') {
            $flags[] = [
                'type' => 'paiement_retard',
                'severity' => 'critical',
                'label' => 'Facture échue depuis le '.$order->invoice_due_date->format('d/m/Y').' — non réglée.',
            ];
        }

        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock_quantity <= 0) {
                $flags[] = [
                    'type' => 'rupture_stock',
                    'severity' => 'warning',
                    'label' => "« {$item->product_name} » est maintenant en rupture de stock (utile en cas d'échange).",
                ];
            }
        }

        $hasPerishable = $order->items->contains(
            fn ($item) => $item->product && in_array($item->product->category?->name, self::PERISHABLE_CATEGORIES, true)
        );

        if ($hasPerishable && in_array($order->status, ['nouvelle', 'confirmee'], true) && $order->created_at->diffInHours(now()) > 24) {
            $flags[] = [
                'type' => 'urgent',
                'severity' => 'critical',
                'label' => 'Contient des produits périssables et attend depuis plus de 24h sans préparation.',
            ];
        }

        if ((float) $order->total > self::LARGE_ORDER_THRESHOLD) {
            $flags[] = [
                'type' => 'gros_montant',
                'severity' => 'warning',
                'label' => 'Montant élevé ('.number_format((float) $order->total, 0, ',', ' ').' FCFA) — vérification recommandée.',
            ];
        }

        if (mb_strlen(trim($order->delivery_address)) < 15) {
            $flags[] = [
                'type' => 'adresse_incomplete',
                'severity' => 'warning',
                'label' => 'Adresse de livraison très courte — pourrait être incomplète.',
            ];
        }

        if ($order->user_id) {
            $cancelledCount = Order::where('user_id', $order->user_id)
                ->whereIn('status', ['annulee', 'remboursee'])
                ->count();

            if ($cancelledCount >= 2) {
                $flags[] = [
                    'type' => 'client_a_suivre',
                    'severity' => 'warning',
                    'label' => "Ce client a {$cancelledCount} commande(s) annulée(s)/remboursée(s) précédemment.",
                ];
            }
        }

        return $flags;
    }

    protected function buildSuggestion(Order $order, array $flags): ?string
    {
        $itemsList = $order->items->map(fn ($item) => "{$item->quantity}x {$item->product_name}")->implode(', ');
        $flagsList = collect($flags)->pluck('label')->implode(' | ') ?: 'Aucune alerte détectée.';
        $statusLabel = Order::STATUSES[$order->status] ?? $order->status;
        $paymentLabel = Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status;
        $clientNom = $order->user?->name ?? $order->customer_name;

        $userMessage = <<<MSG
            Commande {$order->order_number}
            Client : {$clientNom}
            Statut : {$statusLabel}
            Paiement : {$paymentLabel}
            Total : {$order->total} FCFA
            Articles : {$itemsList}
            Alertes détectées automatiquement : {$flagsList}
            MSG;

        return $this->client->complete($this->systemPrompt(), [], $userMessage, temperature: 0.2, maxTokens: 260);
    }

    protected function systemPrompt(): string
    {
        return <<<PROMPT
            Tu assistes le service commandes de DIABA HOTEL, une centrale d'achat de produits locaux sénégalaise basée à Mbour.

            Tu dois produire EXACTEMENT deux sections, dans cet ordre, avec ces étiquettes littérales :

            NOTE_INTERNE: <2 phrases courtes pour l'employé — l'action prioritaire à effectuer sur cette commande. Il reste seul décisionnaire pour tout changement de statut, paiement ou remboursement.>
            MESSAGE_CLIENT: <soit un message prêt à envoyer tel quel au client (poli, chaleureux, 1-2 phrases, signé "L'équipe DIABA HOTEL"), soit exactement le mot AUCUN si aucun message n'est utile.>

            Règles strictes :
            - Réponds uniquement en français.
            - Base-toi UNIQUEMENT sur les informations de la commande fournies. N'invente jamais un fait, un délai ou une information qui n'y figure pas.
            - MESSAGE_CLIENT sera envoyé automatiquement et tel quel au client — écris-le comme un message final, jamais comme une suggestion ("vous pourriez dire...").
            - N'utilise MESSAGE_CLIENT que si un contact avec le client est réellement utile là maintenant (ex: facture échue, retard anormal). Pour une simple commande en cours sans anomalie, réponds AUCUN.
            PROMPT;
    }

    /**
     * @return array{0: ?string, 1: ?string} [note, customerMessage]
     */
    protected function extractSections(string $raw): array
    {
        $note = null;
        $customerMessage = null;

        if (preg_match('/NOTE_INTERNE\s*:\s*(.+?)(?=MESSAGE_CLIENT\s*:|$)/su', $raw, $m)) {
            $note = trim($m[1]) ?: null;
        }

        if (preg_match('/MESSAGE_CLIENT\s*:\s*(.+)$/su', $raw, $m)) {
            $value = trim($m[1]);
            $customerMessage = (strcasecmp($value, 'AUCUN') === 0 || $value === '') ? null : $value;
        }

        return [$note, $customerMessage];
    }

    protected function dispatchCustomerMessage(Order $order, string $message): bool
    {
        if (! $order->user_id) {
            return false;
        }

        $conversation = Conversation::where('user_id', $order->user_id)
            ->where('status', '!=', 'fermee')
            ->latest('last_message_at')
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_id' => $order->user_id,
                'status' => 'ouverte',
                'bot_enabled' => true,
                'last_message_at' => now(),
            ]);
        }

        $conversation->messages()->create([
            'sender_type' => 'bot',
            'body' => $message,
            'meta' => ['source' => 'order_assistant', 'order_id' => $order->id],
        ]);

        $conversation->update(['last_message_at' => now(), 'status' => 'en_cours']);

        return true;
    }
}
