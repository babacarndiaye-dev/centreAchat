<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('analytics_sessions')->cascadeOnDelete();
            $table->string('url', 255);
            $table->dateTime('viewed_at');

            $table->index(['session_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_page_views');
    }
};
