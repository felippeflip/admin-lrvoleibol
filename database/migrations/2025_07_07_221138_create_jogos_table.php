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
        Schema::create('jogos', function (Blueprint $table) {
            $table->id('jgo_id');
            // Chaves estrangeiras
            $table->foreignId('jgo_eqp_cpo_mandante_id')->nullable()->constrained('equipe_campeonato', 'eqp_cpo_id')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('jgo_eqp_cpo_visitante_id')->nullable()->constrained('equipe_campeonato', 'eqp_cpo_id')->onDelete('no action')->onUpdate('no action');

            $table->dateTime('jgo_dt_jogo')->nullable();
            $table->time('jgo_hora_jogo')->nullable();
            $table->integer('jgo_local_jogo_id')->nullable(); // Assumindo que 'local_jogo' seria outra tabela não definida
            $table->integer('jgo_arbitro_principal')->nullable(); // Assumindo que 'arbitros' seria outra tabela não definida
            $table->integer('jgo_arbitro_secundario')->nullable(); // Assumindo que 'arbitros' seria outra tabela não definida
            $table->integer('jgo_apontador')->nullable(); // Assumindo que 'apontadores' seria outra tabela não definida
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jogos');
    }
};
