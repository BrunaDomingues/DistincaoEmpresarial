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
        Schema::table('formulario_envios', function (Blueprint $table) {
            $table->timestamp('inicio_resposta')->nullable()->after('estado');
            $table->timestamp('fim_resposta')->nullable()->after('inicio_resposta');
            $table->integer('duracao_em_segundos')->nullable()->after('fim_resposta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulario_envios', function (Blueprint $table) {
            $table->dropColumn(['inicio_resposta', 'fim_resposta', 'duracao_em_segundos']);
        });
    }
};
