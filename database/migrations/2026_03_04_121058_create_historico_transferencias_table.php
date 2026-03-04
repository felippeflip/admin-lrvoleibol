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
        Schema::create('historico_transferencias', function (Blueprint $table) {
            $table->id('htr_id');
            $table->unsignedBigInteger('htr_atl_id');
            $table->unsignedBigInteger('htr_tim_origem_id')->nullable();
            $table->unsignedBigInteger('htr_tim_destino_id');
            $table->unsignedBigInteger('htr_user_id')->nullable();
            $table->timestamps();

            $table->foreign('htr_atl_id')->references('atl_id')->on('atletas')->onDelete('cascade');
            $table->foreign('htr_tim_origem_id')->references('tim_id')->on('times')->onDelete('set null');
            $table->foreign('htr_tim_destino_id')->references('tim_id')->on('times')->onDelete('cascade');
            $table->foreign('htr_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_transferencias');
    }
};
