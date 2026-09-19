<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use Illuminate\Console\Command;

class PurgeEphemeralConversations extends Command
{
    protected $signature = 'chat:purge-ephemeral';

    protected $description = "Supprime les conversations de visiteurs anonymes (sans compte) inactives depuis plus de 24h — les conversations de clients connectés sont conservées";

    public function handle(): int
    {
        $deleted = Conversation::whereNull('user_id')
            ->where('last_message_at', '<', now()->subHours(24))
            ->delete();

        $this->info("{$deleted} conversation(s) anonyme(s) supprimée(s).");

        return self::SUCCESS;
    }
}
