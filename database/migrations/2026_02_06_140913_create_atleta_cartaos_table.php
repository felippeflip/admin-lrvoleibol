<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('atleta_cartoes', function (Blueprint $table) {
            $table->id('atc_id');
            $table->unsignedBigInteger('atc_atl_id');
            $table->year('atc_ano');
            $table->boolean('atc_impresso')->default(false);
            $table->timestamps();

            $table->foreign('atc_atl_id')->references('atl_id')->on('atletas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atleta_cartoes');
    }
};
