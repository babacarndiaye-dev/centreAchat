<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['vehicule', 'ordinateur', 'mobilier', 'equipement', 'materiel', 'autre']);
            $table->string('name');
            $table->date('acquisition_date');
            $table->decimal('acquisition_value', 14, 2);
            $table->unsignedTinyInteger('useful_life_years');
            $table->enum('depreciation_method', ['lineaire', 'degressif'])->default('lineaire');
            $table->foreignId('payment_account_id')->nullable()->constrained('payment_accounts')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->enum('status', ['en_service', 'cede', 'reforme'])->default('en_service');
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};
