<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_import_id')->constrained('bank_statement_imports')->cascadeOnDelete();
            $table->foreignId('payment_account_id')->constrained('payment_accounts')->cascadeOnDelete();
            $table->date('statement_date');
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->string('reference')->nullable();
            $table->foreignId('matched_transaction_id')->nullable()->constrained('payment_account_transactions')->nullOnDelete();
            $table->enum('status', ['non_rapproche', 'rapproche', 'ecart'])->default('non_rapproche');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
    }
};
