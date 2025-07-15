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
        Schema::create('equipes', function (Blueprint $table) {
            $table->id('eqp_id');
            $table->foreignId('eqp_time_id')->nullable()->constrained('times', 'tim_id')->onDelete('no action')->onUpdate('no action'); // Chaves estrangeiras
            $table->foreignId('eqp_categoria_id')->nullable()->constrained('categorias', 'cto_id')->onDelete('no action')->onUpdate('no action');
            $table->string('eqp_nome_detalhado', 50)->nullable();
            $table->string('eqp_nome_treinador', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipes');
    }
};
