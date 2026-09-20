<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_uid', 20)->unique();
            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at');
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_visitors');
    }
};
