<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('user_type', [
                'particulier',
                'professionnel',
                'hotel',
                'restaurant',
                'entreprise',
                'institution',
                'touriste',
                'revendeur',
            ])->default('particulier')->after('phone');
            $table->string('company_name')->nullable()->after('user_type');
            $table->boolean('is_admin')->default(false)->after('company_name');
            $table->boolean('is_active')->default(true)->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'user_type', 'company_name', 'is_admin', 'is_active']);
        });
    }
};
