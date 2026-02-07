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
        Schema::create('comissao_tecnicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('time_id');
            $table->string('nome');
            $table->string('cpf')->unique();
            $table->string('rg')->nullable();
            $table->string('funcao'); // Técnico, Assistente Técnico, Médico, Fisioterapeuta, Massagista
            $table->string('documento_registro')->nullable(); // CREF, CRM, CREFITO, Diploma
            $table->string('foto')->nullable();
            $table->string('comprovante_documento')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->timestamps();

            $table->foreign('time_id')->references('tim_id')->on('times')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissao_tecnicas');
    }
};
