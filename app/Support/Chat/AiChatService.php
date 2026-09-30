<?php

namespace App\Support\Chat;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\FaqEntry;
use App\Models\Product;
use App\Support\Ai\OpenAiClient;

class AiChatService
{
    protected const HANDOFF_SENTINEL = 'TRANSFERT_AGENT';

    public function __construct(protected OpenAiClient $client)
    {
    }

    /**
     * Ask the AI to answer using only the FAQ as grounding. Returns null when the AI
     * is disabled/unavailable/errors, or when it explicitly signals it cannot answer
     * (never invents — falls back to human handoff instead).
     */
    public function answer(Conversation $conversation, string $message): ?string
    {
        $text = $this->client->complete(
            $this->systemPrompt($message),
            $this->recentHistory($conversation),
            $message,
            maxTokens: 500,
            webSearch: false,
        );

        if ($text === null || str_contains($text, self::HANDOFF_SENTINEL)) {
            return null;
        }

        return $text;
    }

    protected function systemPrompt(string $message): string
    {
        $faq = FaqEntry::where('is_active', true)->orderBy('position')->get()
            ->map(fn (FaqEntry $entry) => "Q: {$entry->question}\nR: {$entry->answer}")
            ->implode("\n\n");

        $products = $this->relevantProducts($message);

        return <<<PROMPT
            Tu es l'assistant du chat en ligne de DIABA HOTEL, une centrale d'achat de produits locaux sénégalaise basée à Mbour.

            Règles strictes :
            - Réponds uniquement en français, en 1 à 4 phrases courtes, sur un ton chaleureux et naturel (jamais robotique).
            - Pour toute question portant sur un fait général propre à DIABA HOTEL (délai de livraison, zone desservie, moyen de paiement, politique de retour, promotion, statut d'une commande, etc.) : base-toi UNIQUEMENT sur la base de connaissances ci-dessous. N'invente JAMAIS un tel fait. Si absent, réponds EXACTEMENT et UNIQUEMENT par : {$this->handoffSentinel()}
            - Pour une question sur un produit précis (prix, stock, origine, unité) : base-toi UNIQUEMENT sur la liste "Produits en lien avec la question" ci-dessous si le produit y figure. N'invente jamais un prix ou un stock. S'il n'y figure pas, réponds par : {$this->handoffSentinel()}
            - Pour toute autre question qui n'exige pas un fait propre à DIABA HOTEL (conseils, cuisine, conservation, usage, culture générale, actualité, discussion, etc.) : réfléchis et réponds avec tes connaissances générales et les résultats de recherche web mis à ta disposition, de façon utile et concise, même si ce n'est pas écrit dans les bases ci-dessous. Ne te réfugie pas derrière le transfert vers un agent pour ce type de question. Si tu t'appuies sur une recherche web, reste factuel et ne mentionne pas explicitement "recherche web", réponds juste naturellement.
            - Tu ne peux ni consulter de commande réelle, ni effectuer d'action : ne prétends jamais connaître le statut d'une commande précise.

            Base de connaissances DIABA HOTEL :

            {$faq}

            Produits en lien avec la question (catalogue réel, données à jour) :

            {$products}
            PROMPT;
    }

    /**
     * Live catalog grounding: surfaces real product data (price, stock, origin) matching
     * the customer's message, so the AI never has to guess or invent product facts.
     */
    protected function relevantProducts(string $message): string
    {
        $queryWords = FaqEntry::tokenize($message);

        if (empty($queryWords)) {
            return '(aucun produit identifié dans la question)';
        }

        $matches = Product::where('is_active', true)->with('category')->get()
            ->map(function (Product $product) use ($queryWords) {
                $haystack = FaqEntry::tokenize($product->name.' '.$product->short_description.' '.($product->category->name ?? ''));
                $product->setAttribute('match_score', count(array_intersect($queryWords, $haystack)));

                return $product;
            })
            ->filter(fn (Product $product) => $product->match_score > 0)
            ->sortByDesc('match_score')
            ->take(4);

        if ($matches->isEmpty()) {
            return '(aucun produit identifié dans la question)';
        }

        return $matches->map(function (Product $product) {
            $price = $product->promo_price ? "{$product->promo_price} FCFA (promo, prix normal {$product->price} FCFA)" : "{$product->price} FCFA";
            $stock = $product->stock_quantity > 0 ? "en stock ({$product->stock_quantity} {$product->unit})" : 'rupture de stock';

            return "- {$product->name} | {$price} / {$product->unit} | origine : {$product->origin} | {$stock} | catégorie : {$product->category->name}";
        })->implode("\n");
    }

    protected function handoffSentinel(): string
    {
        return self::HANDOFF_SENTINEL;
    }

    /**
     * Last few messages of the conversation as OpenAI-style role turns, for light context.
     */
    protected function recentHistory(Conversation $conversation): array
    {
        return $conversation->messages()
            ->where('sender_type', '!=', 'staff')
            ->latest()
            ->limit(6)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $message) => [
                'role' => $message->sender_type === 'client' ? 'user' : 'assistant',
                'content' => $message->body,
            ])
            ->values()
            ->all();
    }
}
