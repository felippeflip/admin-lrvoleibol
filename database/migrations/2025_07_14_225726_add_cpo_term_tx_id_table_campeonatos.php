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
        // adcionar a coluna cpo_term_tx_id na tabela campeonatos
        Schema::table('campeonatos', function (Blueprint $table) {
            $table->unsignedBigInteger('cpo_term_tx_id')->nullable()->after('cpo_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remover a coluna cpo_term_tx_id da tabela campeonatos
        Schema::table('campeonatos', function (Blueprint $table) {
            $table->dropColumn('cpo_term_tx_id');
        });
    }
};
