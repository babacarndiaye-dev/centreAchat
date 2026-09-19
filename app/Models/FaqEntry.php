<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FaqEntry extends Model
{
    protected $fillable = [
        'question', 'answer', 'keywords', 'category', 'intent', 'is_active', 'position', 'hit_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected const STOP_WORDS = [
        'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'est', 'avez', 'vous',
        'votre', 'vos', 'pour', 'que', 'qui', 'quoi', 'comment', 'sur', 'dans', 'avec', 'ce',
        'cette', 'ces', 'mon', 'ma', 'mes', 'je', 'ai', 'au', 'aux', 'suis', 'etre', 'avoir',
        'faire', 'peut', 'peux', 'y', 'a', 'en', 'se', 'sa', 'son', 'ses', 'il', 'elle',
    ];

    /**
     * Lowercase, strip accents, and split into significant words (no external NLP —
     * accent-folding and trailing-"s" stripping are the only normalization applied).
     */
    public static function tokenize(string $text): array
    {
        $text = Str::lower(Str::ascii($text));
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text) ?? '';
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $words = array_map(fn ($w) => Str::endsWith($w, 's') && mb_strlen($w) > 4 ? Str::substr($w, 0, -1) : $w, $words);
        $words = array_filter($words, fn ($w) => mb_strlen($w) > 2 && ! in_array($w, self::STOP_WORDS, true));

        return array_values(array_unique($words));
    }

    /**
     * Simple keyword-overlap search across active entries (no external AI/NLP service).
     * When $intent is given, entries tagged with that intent are searched first;
     * if none score, the search falls back to the full active set.
     *
     * @return Collection<int, FaqEntry>
     */
    public static function search(string $query, int $limit = 1, ?string $intent = null): Collection
    {
        $queryWords = self::tokenize($query);

        if (empty($queryWords)) {
            return new Collection();
        }

        // A single incidental shared word (e.g. "fois") must never count as a confident
        // match — require at least 2 overlapping significant words, unless the query
        // itself only has 1 (short queries like "livraison" must still be matchable).
        $minScore = min(2, count($queryWords));

        $score = function (FaqEntry $entry) use ($queryWords) {
            $haystack = self::tokenize($entry->question.' '.$entry->keywords);
            $entry->setAttribute('search_score', count(array_intersect($queryWords, $haystack)));

            return $entry;
        };

        if ($intent) {
            $scoped = self::where('is_active', true)->where('intent', $intent)->get()
                ->map($score)
                ->filter(fn (FaqEntry $entry) => $entry->search_score >= $minScore)
                ->sortByDesc('search_score');

            if ($scoped->isNotEmpty()) {
                return $scoped->take($limit)->values();
            }
        }

        return self::where('is_active', true)->get()
            ->map($score)
            ->filter(fn (FaqEntry $entry) => $entry->search_score >= $minScore)
            ->sortByDesc('search_score')
            ->take($limit)
            ->values();
    }

    public static function mostPopular(int $limit = 4): Collection
    {
        return self::where('is_active', true)
            ->orderByDesc('hit_count')
            ->orderBy('position')
            ->limit($limit)
            ->get();
    }

    public function registerHit(): void
    {
        $this->increment('hit_count');
    }
}
