<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\EmpresaNomeNormalizer;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insight_empresa_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('termo', 255);
            $table->string('termo_normalizado', 255)->unique();
            $table->string('nome_canonico', 255);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $aliases = (array) config('insight_empresa_aliases', []);
        $now = now();

        foreach ($aliases as $termo => $nomeCanonico) {
            $termoNormalizado = EmpresaNomeNormalizer::normalize((string) $termo);
            if ($termoNormalizado === '') {
                continue;
            }

            DB::table('insight_empresa_aliases')->insertOrIgnore([
                'termo' => (string) $termo,
                'termo_normalizado' => $termoNormalizado,
                'nome_canonico' => (string) $nomeCanonico,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_empresa_aliases');
    }
};
