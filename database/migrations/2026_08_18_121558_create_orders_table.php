<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->text('delivery_address');
            $table->string('city');
            $table->enum('status', [
                'brouillon',
                'nouvelle',
                'confirmee',
                'payee',
                'en_preparation',
                'prete',
                'en_livraison',
                'livree',
                'terminee',
                'annulee',
                'remboursee',
                'en_attente_paiement',
            ])->default('nouvelle');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['en_attente', 'paye', 'echoue', 'rembourse'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
