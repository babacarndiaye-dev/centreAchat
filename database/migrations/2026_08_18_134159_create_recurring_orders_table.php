<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('frequency', ['hebdomadaire', 'bimensuelle', 'mensuelle'])->default('hebdomadaire');
            $table->text('delivery_address');
            $table->string('city');
            $table->string('payment_method')->default('especes');
            $table->enum('status', ['active', 'suspendu'])->default('active');
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_orders');
    }
};
