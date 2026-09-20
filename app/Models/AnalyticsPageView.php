<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsPageView extends Model
{
    public $timestamps = false;

    protected $fillable = ['session_id', 'url', 'viewed_at'];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AnalyticsSession::class, 'session_id');
    }
}
