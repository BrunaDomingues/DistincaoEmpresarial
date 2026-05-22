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
        Schema::table('formulario_respostas', function (Blueprint $table) {
            $table->foreignId('formulario_envio_id')->constrained('formulario_envios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulario_respostas', function (Blueprint $table) {
            $table->dropForeign(['formulario_envio_id']);
            $table->dropColumn('formulario_envio_id');
        });
    }
};
