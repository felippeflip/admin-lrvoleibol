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
        Schema::create('ginasios', function (Blueprint $table) {
            $table->id('gin_id');
            $table->string('gin_nome');
            $table->string('gin_cep');
            $table->string('gin_endereco');
            $table->string('gin_numero');
            $table->string('gin_bairro');
            $table->string('gin_cidade');
            $table->string('gin_estado');
            $table->string('gin_complemento')->nullable();
            $table->string('gin_telefone')->nullable();
            $table->string('gin_email')->nullable();
            $table->unsignedBigInteger('gin_tim_id')->nullable();
            $table->foreign('gin_tim_id')->references('tim_id')->on('times')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ginasios');
    }
};
