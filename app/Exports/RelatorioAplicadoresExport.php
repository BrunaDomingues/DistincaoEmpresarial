<?php
namespace App\Exports;

use App\Models\FormularioEnvio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RelatorioAplicadoresExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return FormularioEnvio::selectRaw('DATE(created_at) as data, usuario_id, COUNT(*) as total')
            ->when($this->data, function ($query) {
                $query->whereDate('created_at', $this->data);
            })
            ->groupBy('data', 'usuario_id')
            ->with('usuario')
            ->get()
            ->map(function ($envio) {
                return [
                    'Data' => \Carbon\Carbon::parse($envio->data)->format('d/m/Y'),
                    'Aplicador' => $envio->usuario->name ?? 'N/A',
                    'Formulários Enviados' => $envio->total,
                ];
            });
    }
    

    public function headings(): array
    {
        return [
            'Data',
            'Aplicador',
            'Formulários Enviados',
        ];
    }
}