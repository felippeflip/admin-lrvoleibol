<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adiciona a coluna is_resp_time à tabela users
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_resp_time')->default(false)->after('is_arbitro'); // Adiciona a coluna após a coluna is_arbitro
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //  Remove a coluna is_resp_time da tabela users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_resp_time');
        });
    }
};
