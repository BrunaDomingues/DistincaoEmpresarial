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
        Schema::table('formulario_perguntas', function (Blueprint $table) {
            $table->boolean('usa_fatores_satisfacao')->default(false)->after('obrigatorio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formularios_perguntas', function (Blueprint $table) {
            $table->dropColumn('usa_fatores_satisfacao');
        });
    }
};
