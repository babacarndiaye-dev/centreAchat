<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('analytics_visitors')->cascadeOnDelete();
            $table->string('session_uid', 20)->unique();
            $table->dateTime('started_at');
            $table->dateTime('last_activity_at')->index();
            $table->dateTime('ended_at')->nullable();
            $table->string('device_type', 10);
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('entry_page', 255)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
