<?php

namespace App\Exports;

use App\Models\FormularioEnvio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class RelatorioAplicadoresAcumuladoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return FormularioEnvio::selectRaw('usuario_id, COUNT(*) as total')
            ->when($this->data, function ($q) {
                $q->whereDate('created_at', '<=', $this->data);
            })
            ->where('invalido', 'false')
            ->groupBy('usuario_id')
            ->with('usuario')
            ->get();
    }

    public function map($envio): array
    {
        return [
            $envio->usuario->name ?? 'N/A',
            $envio->total,
            $this->data ? Carbon::parse($this->data)->format('d/m/Y') : 'Todas'
        ];
    }

    public function headings(): array
    {
        return [
            'Aplicador',
            'Total de Formulários Enviados',
            'Data Limite'
        ];
    }
}
