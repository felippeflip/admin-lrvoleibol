<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabela para sequência de atletas
        Schema::create('atleta_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('next_number');
            $table->timestamps();
        });

        // Tabela para sequência de técnicos
        Schema::create('tecnico_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('next_number');
            $table->timestamps();
        });

        // Tabela para sequência de árbitros
        Schema::create('arbitro_sequences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('next_number');
            $table->timestamps();
        });

        // Inserir os valores iniciais passados pelo usuário
        DB::table('atleta_sequences')->insert(['next_number' => 13025, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('tecnico_sequences')->insert(['next_number' => 1920, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('arbitro_sequences')->insert(['next_number' => 72, 'created_at' => now(), 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atleta_sequences');
        Schema::dropIfExists('tecnico_sequences');
        Schema::dropIfExists('arbitro_sequences');
    }
};
