<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jogos', function (Blueprint $table) {
            $table->string('jgo_res_status')->default('nao_informado')->nullable()->after('jgo_status')->comment('Status do resultado: nao_informado, pendente, aprovado');
            $table->unsignedBigInteger('jgo_res_usuario_id')->nullable()->after('jgo_res_status')->comment('Usuário que enviou o resultado');
            $table->dateTime('jgo_res_data_envio')->nullable()->after('jgo_res_usuario_id');
            $table->boolean('jgo_vencedor_mandante')->nullable()->after('jgo_res_data_envio')->comment('True se mandante venceu, False se visitante');
        });
    }

    public function down()
    {
        Schema::table('jogos', function (Blueprint $table) {
             $table->dropColumn(['jgo_res_status', 'jgo_res_usuario_id', 'jgo_res_data_envio', 'jgo_vencedor_mandante']);
        });
    }
};
