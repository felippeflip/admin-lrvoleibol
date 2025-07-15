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
        Schema::create('equipe_campeonato', function (Blueprint $table) {
            $table->id('eqp_cpo_id');
            // Chaves estrangeiras
            $table->foreignId('cpo_fk_id')->nullable()->constrained('campeonatos', 'cpo_id')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('eqp_fk_id')->nullable()->constrained('equipes', 'eqp_id')->onDelete('no action')->onUpdate('no action');

            $table->date('eqp_cpo_dt_inscricao')->nullable();
            $table->integer('eqp_cpo_classificacaofinal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipe_campeonato');
    }
};
