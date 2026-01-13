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
            $table->boolean('jgo_notificacao_arbitro_p')->default(false)->after('jgo_apontador');
            $table->boolean('jgo_notificacao_arbitro_s')->default(false)->after('jgo_notificacao_arbitro_p');
            $table->boolean('jgo_notificacao_apontador')->default(false)->after('jgo_notificacao_arbitro_s');
            $table->boolean('jgo_notificacao_resultado')->default(false)->after('jgo_notificacao_apontador');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jogos', function (Blueprint $table) {
            //
        });
    }
};
