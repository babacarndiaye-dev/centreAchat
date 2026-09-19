<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('producer_id')->nullable()->constrained('producers')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('reference')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('origin')->nullable();
            $table->string('unit')->default('unité');
            $table->decimal('price', 10, 2);
            $table->decimal('professional_price', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('promo_price', 10, 2)->nullable();
            $table->timestamp('promo_starts_at')->nullable();
            $table->timestamp('promo_ends_at')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('stock_alert_threshold')->default(5);
            $table->decimal('weight', 8, 3)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
