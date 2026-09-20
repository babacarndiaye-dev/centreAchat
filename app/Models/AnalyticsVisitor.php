<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticsVisitor extends Model
{
    protected $fillable = ['visitor_uid', 'first_seen_at', 'last_seen_at', 'country', 'city'];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AnalyticsSession::class, 'visitor_id');
    }
}
