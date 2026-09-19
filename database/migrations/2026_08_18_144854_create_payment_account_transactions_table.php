<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_account_id')->constrained('payment_accounts')->cascadeOnDelete();
            $table->enum('type', ['entree', 'sortie']);
            $table->decimal('amount', 14, 2);
            $table->string('category')->nullable();
            $table->string('description');
            $table->string('reference')->nullable();
            $table->date('transaction_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_account_transactions');
    }
};
