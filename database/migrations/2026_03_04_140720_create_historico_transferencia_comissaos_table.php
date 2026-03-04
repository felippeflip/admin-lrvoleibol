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
        Schema::create('historico_transferencia_comissaos', function (Blueprint $table) {
            $table->id('htrc_id');
            $table->unsignedBigInteger('htrc_comissao_id');
            $table->unsignedBigInteger('htrc_tim_origem_id')->nullable();
            $table->unsignedBigInteger('htrc_tim_destino_id');
            $table->unsignedBigInteger('htrc_user_id')->nullable();
            $table->timestamps();

            $table->foreign('htrc_comissao_id')->references('id')->on('comissao_tecnicas')->onDelete('cascade');
            $table->foreign('htrc_tim_origem_id')->references('tim_id')->on('times')->onDelete('set null');
            $table->foreign('htrc_tim_destino_id')->references('tim_id')->on('times')->onDelete('cascade');
            $table->foreign('htrc_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_transferencia_comissaos');
    }
};
