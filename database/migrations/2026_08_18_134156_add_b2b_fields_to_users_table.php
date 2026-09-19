<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('b2b_status', ['non_applicable', 'en_attente', 'valide', 'refuse'])->default('non_applicable')->after('user_type');
            $table->decimal('credit_limit', 12, 2)->nullable()->after('b2b_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['b2b_status', 'credit_limit']);
        });
    }
};
