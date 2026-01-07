<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpa a coluna para evitar erro de conversão
        \Illuminate\Support\Facades\DB::table('atletas')->update(['atl_categoria' => null]);
        
        Schema::table('atletas', function (Blueprint $table) {
            $table->unsignedBigInteger('atl_categoria')->nullable()->change();
            $table->foreign('atl_categoria')->references('cto_id')->on('categorias')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atletas', function (Blueprint $table) {
            $table->dropForeign(['atl_categoria']);
            $table->string('atl_categoria', 50)->nullable()->change();
        });
    }
};
