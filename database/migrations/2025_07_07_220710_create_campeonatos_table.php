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
        Schema::create('campeonatos', function (Blueprint $table) {
            $table->id('cpo_id');
            $table->string('cpo_nome', 50)->nullable();
            $table->year('cpo_ano')->nullable(); // Usando year() para o ano
            $table->date('cpo_dt_inicio')->nullable();
            $table->date('cpo_dt_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campeonatos');
    }
};
