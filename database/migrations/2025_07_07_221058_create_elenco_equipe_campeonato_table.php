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
        Schema::create('elenco_equipe_campeonato', function (Blueprint $table) {
            $table->id('ele_id');
            // Chaves estrangeiras
            $table->foreignId('ele_fk_eqp_cpo_id')->nullable()->constrained('equipe_campeonato', 'eqp_cpo_id')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('ele_fk_atl_id')->nullable()->constrained('atletas', 'atl_id')->onDelete('no action')->onUpdate('no action');

            $table->integer('ele_num_camisa')->nullable();
            $table->string('ele_posicao_atuando', 15)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elenco_equipe_campeonato');
    }
};
