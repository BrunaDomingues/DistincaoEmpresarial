<?php

namespace App\Exports;

use App\Models\FormularioEnvio;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RelatorioEnviosUsuariosExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected ?int $formularioId = null,
        protected ?string $data = null,
    ) {}

    public function collection(): Collection
    {
        $timezone = 'America/Sao_Paulo';

        return FormularioEnvio::query()
            ->with(['usuario:id,name,email', 'formulario:id,titulo'])
            ->where('invalido', false)
            ->when($this->formularioId, function ($q) {
                $q->where('formulario_id', $this->formularioId);
            })
            ->when($this->data, function ($q) {
                $q->whereRaw('DATE(COALESCE(fim_resposta, created_at)) = ?', [$this->data]);
            })
            ->orderByRaw('COALESCE(fim_resposta, created_at) DESC')
            ->get()
            ->map(function (FormularioEnvio $envio) use ($timezone) {
                $momento = Carbon::parse($envio->fim_resposta ?? $envio->created_at)->timezone($timezone);

                return [
                    'Data' => $momento->format('d/m/Y'),
                    'Horário' => $momento->format('H:i:s'),
                    'Usuário' => $envio->usuario->name ?? '',
                    'E-mail' => $envio->usuario->email ?? '',
                    'Formulário' => $envio->formulario->titulo ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Data',
            'Horário',
            'Usuário',
            'E-mail',
            'Formulário',
        ];
    }
}
