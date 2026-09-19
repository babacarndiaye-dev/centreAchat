<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_accounts', function (Blueprint $table) {
            $table->foreignId('chart_account_id')->nullable()->after('type')->constrained('chart_of_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chart_account_id');
        });
    }
};
