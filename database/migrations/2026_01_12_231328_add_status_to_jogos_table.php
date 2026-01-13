<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jogos', function (Blueprint $table) {
            if (!Schema::hasColumn('jogos', 'jgo_status')) {
                $table->string('jgo_status')->default('ativo')->after('jgo_wp_id')->comment('Status do Jogo: ativo/inativo');
            }
        });
    }

    public function down()
    {
        Schema::table('jogos', function (Blueprint $table) {
            if (Schema::hasColumn('jogos', 'jgo_status')) {
                $table->dropColumn('jgo_status');
            }
        });
    }
};
