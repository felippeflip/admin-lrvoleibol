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
            $table->string('jgo_fase_tipo', 50)->default('classificatoria')->nullable()->after('jgo_fase');
            $table->string('jgo_status_publicacao', 20)->default('pendente')->nullable()->after('jgo_fase_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jogos', function (Blueprint $table) {
            $table->dropColumn(['jgo_fase_tipo', 'jgo_status_publicacao']);
        });
    }
};
