<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('atletas', function (Blueprint $table) {
            $table->boolean('atl_estrangeiro')->default(0)->after('atl_nome');
            $table->string('atl_passaporte', 50)->nullable()->unique()->after('atl_cpf');
        });
        
        DB::statement('ALTER TABLE atletas MODIFY atl_cpf VARCHAR(20) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atletas', function (Blueprint $table) {
            $table->dropColumn(['atl_estrangeiro', 'atl_passaporte']);
        });
        
        DB::statement('ALTER TABLE atletas MODIFY atl_cpf VARCHAR(20) NOT NULL');
    }
};
