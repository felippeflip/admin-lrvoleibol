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
        Schema::create('resultado_sets', function (Blueprint $table) {
            $table->id('set_id');
            // Chave estrangeira
            $table->foreignId('set_jgo_id')->nullable()->constrained('jogos', 'jgo_id')->onDelete('no action')->onUpdate('no action');

            $table->integer('set_numero')->nullable();
            $table->integer('set_pontos_mandante')->nullable();
            $table->integer('set_pontos_visitante')->nullable();
            $table->integer('set_vencedor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultado_sets');
    }
};
