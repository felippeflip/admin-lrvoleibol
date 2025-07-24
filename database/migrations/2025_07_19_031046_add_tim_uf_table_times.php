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
        // Adicionar na tabela 'times' a coluna 'tim_uf'
        Schema::table('times', function (Blueprint $table) {
            $table->string('tim_uf', 2)->nullable()->after('tim_cidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover a coluna 'tim_uf' da tabela 'times'
        Schema::table('times', function (Blueprint $table) {
            $table->dropColumn('tim_uf');
        });
    }
};
