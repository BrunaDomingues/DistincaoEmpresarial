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
            $table->boolean('invalido')->default(false)->after('estado');
            $table->text('motivo_invalido')->nullable()->after('invalido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulario_envios', function (Blueprint $table) {
            $table->dropColumn('invalido');
            $table->dropColumn('motivo_invalido');
        });
    }
};
