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
        Schema::create('atletas', function (Blueprint $table) {
            $table->id('atl_id');
            $table->string('atl_nome', 100)->nullable();
            $table->string('atl_cpf', 11)->unique(); // CPF como string e unique
            $table->string('atl_rg', 20)->nullable();
            $table->date('atl_dt_nasc')->nullable();
            $table->string('atl_resg', 20)->nullable();
            $table->string('atl_endereco', 50)->nullable();
            $table->string('atl_numero', 10)->nullable();
            $table->string('atl_bairro', 50)->nullable();
            $table->string('atl_cidade', 50)->nullable();
            $table->string('atl_estado', 50)->nullable();
            $table->string('atl_cep', 15)->nullable(); // CEP como string
            $table->string('atl_categoria', 50)->nullable(); // Se "categoria" será uma FK para a tabela `categorias`, isso deve ser alterado depois.
            $table->string('atl_ano_insc', 10)->nullable();
            $table->string('atl_foto', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atletas');
    }
};
