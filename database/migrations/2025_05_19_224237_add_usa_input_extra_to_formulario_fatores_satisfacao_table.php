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
        Schema::table('formularios_fator_satisfacao', function (Blueprint $table) {
            $table->boolean('usa_input_extra')->default(false)->after('resposta_obrigatoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formularios_fator_satisfacao', function (Blueprint $table) {
            $table->dropColumn('usa_input_extra');
        });
    }
};
