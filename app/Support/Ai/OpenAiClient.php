<?php

namespace App\Support\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OpenAiClient
{
    public function enabled(): bool
    {
        if (! (bool) config('services.openai.chat_enabled')) {
            return false;
        }

        return filled(config('services.openrouter.api_key')) || filled(config('services.openai.api_key'));
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function complete(string $systemPrompt, array $history, string $userMessage, float $temperature = 0.3, int $maxTokens = 300, bool $webSearch = false): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        [$url, $token, $model, $extraHeaders, $isOpenRouter] = $this->provider();

        $payload = [
            'model' => $model,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ...$history,
                ['role' => 'user', 'content' => $userMessage],
            ],
        ];

        // OpenRouter-only: lets the model ground general-knowledge answers in live
        // web results instead of only static training data. Not supported when
        // falling back to the direct OpenAI endpoint.
        if ($webSearch && $isOpenRouter) {
            $payload['plugins'] = [['id' => 'web', 'max_results' => 3]];
        }

        try {
            $response = Http::withToken($token)
                ->withHeaders($extraHeaders)
                ->timeout(25)
                ->post($url, $payload);
        } catch (Throwable $e) {
            Log::warning('OpenAiClient: appel échoué', ['error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('OpenAiClient: réponse non réussie', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        $text = trim((string) $response->json('choices.0.message.content'));

        return $text === '' ? null : $text;
    }

    /**
     * @return array{0: string, 1: string, 2: string, 3: array<string, string>, 4: bool}
     */
    protected function provider(): array
    {
        if (filled(config('services.openrouter.api_key'))) {
            return [
                rtrim(config('services.openrouter.base_url'), '/').'/chat/completions',
                config('services.openrouter.api_key'),
                config('services.openrouter.model'),
                [
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ],
                true,
            ];
        }

        return [
            'https://api.openai.com/v1/chat/completions',
            config('services.openai.api_key'),
            config('services.openai.model'),
            [],
            false,
        ];
    }
}
