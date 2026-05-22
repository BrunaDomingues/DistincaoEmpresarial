<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('formulario_respostas', function (Blueprint $table) {
            $table->text('resposta')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('formulario_respostas', function (Blueprint $table) {
            $table->text('resposta')->nullable(false)->change();
        });
    }
};
