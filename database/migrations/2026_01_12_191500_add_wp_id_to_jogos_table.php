<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jogos', function (Blueprint $table) {
            // Adiciona a coluna jgo_wp_id se ela não existir
            if (!Schema::hasColumn('jogos', 'jgo_wp_id')) {
                $table->unsignedBigInteger('jgo_wp_id')->nullable()->after('jgo_id')->comment('ID do post no WordPress');
                $table->index('jgo_wp_id');
            }
        });
    }

    public function down()
    {
        Schema::table('jogos', function (Blueprint $table) {
            if (Schema::hasColumn('jogos', 'jgo_wp_id')) {
                $table->dropColumn('jgo_wp_id');
            }
        });
    }
};
