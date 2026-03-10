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
        Schema::table('jogos', function (Blueprint $table) {
            $table->string('jgo_status_agendamento', 50)->nullable(); // 'pendente_preenchimento', 'pendente_aprovacao', 'aprovado'
            $table->unsignedBigInteger('jgo_sugerido_por_equipe_id')->nullable();
            $table->string('jgo_fase', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jogos', function (Blueprint $table) {
            $table->dropColumn(['jgo_status_agendamento', 'jgo_sugerido_por_equipe_id', 'jgo_fase']);
        });
    }
};
