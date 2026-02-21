<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ginasios', function (Blueprint $table) {
            $table->boolean('gin_status')->default(true)->after('gin_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ginasios', function (Blueprint $table) {
            $table->dropColumn('gin_status');
        });
    }
};
