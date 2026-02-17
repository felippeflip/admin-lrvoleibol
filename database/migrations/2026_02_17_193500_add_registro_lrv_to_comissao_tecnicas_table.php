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
        Schema::table('comissao_tecnicas', function (Blueprint $table) {
            $table->string('registro_lrv')->nullable()->after('nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comissao_tecnicas', function (Blueprint $table) {
            $table->dropColumn('registro_lrv');
        });
    }
};
