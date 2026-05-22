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
            $table->foreignId('fator_id')->nullable()->constrained('formularios_fator_satisfacao')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulario_respostas', function (Blueprint $table) {
            $table->dropForeign(['fator_id']);
            $table->dropColumn('fator_id');
        });
    }
};
