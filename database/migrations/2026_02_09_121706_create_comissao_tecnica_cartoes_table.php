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
        Schema::create('comissao_tecnica_cartoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comissao_tecnica_id')->constrained('comissao_tecnicas')->onDelete('cascade');
            $table->year('ano');
            $table->boolean('impresso')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissao_tecnica_cartoes');
    }
};
