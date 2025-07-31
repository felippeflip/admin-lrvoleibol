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
        Schema::table('atletas', function (Blueprint $table) {
            $table->string('atl_celular', 15)->nullable()->after('atl_rg');
            $table->string('atl_telefone', 15)->nullable()->after('atl_celular');
            $table->string('atl_email')->nullable()->after('atl_telefone');
            $table->string('atl_sexo', 1)->nullable()->after('atl_email');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atletas', function (Blueprint $table) {
            $table->dropColumn(['atl_celular', 'atl_telefone', 'atl_email', 'atl_sexo']);
        });
    }
};
