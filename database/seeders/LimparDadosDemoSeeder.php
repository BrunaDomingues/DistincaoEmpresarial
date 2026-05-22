<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LimparDadosDemoSeeder extends Seeder
{
    /**
     * E-mails dos aplicadores criados pelo DistincaoRespostasDemoSeeder.
     *
     * @var list<string>
     */
    public static array $emailsDemo = [
        'rita.aplicadora@demo.local',
        'carlos.campo@demo.local',
        'maria.pesquisa@demo.local',
        'pedro.aciba@demo.local',
        'ana.bage@demo.local',
        'lucas.entrevista@demo.local',
        'juliana.fronteira@demo.local',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $this->zerarRespostas();

            $removidos = $this->removerUsuariosDemo();

            $restantes = User::query()->orderBy('name')->pluck('email', 'name');

            $this->command?->warn('Todas as respostas e envios foram removidos.');
            $this->command?->info("Usuários demo removidos: {$removidos}.");

            if ($restantes->isEmpty()) {
                $this->command?->error('Nenhum usuário restante. Rode: php artisan db:seed --class=UserSeeder --force');
            } else {
                $this->command?->info('Usuários mantidos:');
                foreach ($restantes as $nome => $email) {
                    $this->command?->line("  • {$nome} <{$email}>");
                }
            }
        });
    }

    private function zerarRespostas(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('formulario_respostas_tratadas')->delete();
        DB::table('formulario_respostas')->delete();
        DB::table('formulario_envios')->delete();

        Schema::enableForeignKeyConstraints();
    }

    private function removerUsuariosDemo(): int
    {
        return User::query()
            ->where(function ($q) {
                $q->whereIn('email', self::$emailsDemo)
                    ->orWhere('email', 'like', '%@demo.local');
            })
            ->delete();
    }
}
