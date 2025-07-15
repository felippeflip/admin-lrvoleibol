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
        //quero adicionar a coluna cto_term_tx_id e cto_descricao na tabela categorias
    Schema::table('categorias', function (Blueprint $table) {
        $table->unsignedBigInteger('cto_term_tx_id')->nullable()->after('cto_slug');
        $table->string('cto_descricao')->nullable()->after('cto_term_tx_id');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
