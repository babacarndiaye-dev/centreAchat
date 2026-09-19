<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = ['code', 'name', 'type'];

    public const TYPES = [
        'ventes' => 'Journal des ventes',
        'achats' => 'Journal des achats',
        'banque' => 'Journal de banque',
        'caisse' => 'Journal de caisse',
        'od' => 'Opérations diverses',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}
