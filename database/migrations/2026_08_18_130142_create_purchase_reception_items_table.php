<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_reception_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_reception_id')->constrained('purchase_receptions')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained('purchase_order_items')->cascadeOnDelete();
            $table->unsignedInteger('quantity_received');
            $table->enum('quality_status', ['conforme', 'non_conforme'])->default('conforme');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_reception_items');
    }
};
