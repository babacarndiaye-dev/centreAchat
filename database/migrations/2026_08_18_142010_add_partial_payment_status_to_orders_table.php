<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('en_attente', 'partiellement_paye', 'paye', 'echoue', 'rembourse') DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('en_attente', 'paye', 'echoue', 'rembourse') DEFAULT 'en_attente'");
    }
};
