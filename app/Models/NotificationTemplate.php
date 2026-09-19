<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'event_key', 'channel', 'name', 'subject', 'body', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function render(array $variables): array
    {
        $replace = function (?string $text) use ($variables) {
            if ($text === null) {
                return null;
            }

            foreach ($variables as $key => $value) {
                $text = str_replace('{{'.$key.'}}', (string) $value, $text);
            }

            return $text;
        };

        return [
            'subject' => $replace($this->subject),
            'body' => $replace($this->body),
        ];
    }
}
