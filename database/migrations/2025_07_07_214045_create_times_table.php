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
        Schema::create('times', function (Blueprint $table) {
            $table->id('tim_id'); // Corresponde a INT NOT NULL AUTO_INCREMENT, Primary Key
            $table->integer('tim_user_id')->nullable();
            $table->integer('tim_registro')->nullable();
            $table->string('tim_cnpj', 14)->nullable(); // Ajustado para string para lidar com zeros à esquerda
            $table->string('tim_nome', 100)->nullable();
            $table->string('tim_nome_abre', 20)->nullable();
            $table->string('tim_sigla', 10)->nullable();
            $table->string('tim_endereco', 50)->nullable();
            $table->string('tim_numero', 10)->nullable();
            $table->string('tim_bairro', 50)->nullable();
            $table->string('tim_cidade', 50)->nullable();
            $table->string('tim_cep', 50)->nullable(); // Mantido como string, CEPs podem ter hífens e zeros à esquerda
            $table->string('tim_telefone', 20)->nullable();
            $table->string('tim_celular', 50)->nullable();
            $table->string('tim_email', 50)->nullable();
            $table->string('tim_logo', 100)->nullable();
            $table->string('tim_responsavel', 50)->nullable();
            $table->timestamps(); // Adiciona created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('times');
    }
};
