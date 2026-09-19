<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY user_type ENUM(
            'particulier', 'professionnel', 'hotel', 'restaurant', 'entreprise',
            'institution', 'touriste', 'revendeur', 'fournisseur', 'staff'
        ) DEFAULT 'particulier'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY user_type ENUM(
            'particulier', 'professionnel', 'hotel', 'restaurant', 'entreprise',
            'institution', 'touriste', 'revendeur', 'fournisseur'
        ) DEFAULT 'particulier'");
    }
};
