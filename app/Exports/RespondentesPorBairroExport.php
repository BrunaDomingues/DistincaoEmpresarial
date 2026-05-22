<?php

namespace App\Exports;

use App\Support\RelatorioBairroQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RespondentesPorBairroExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $data = null,
    ) {}

    public function collection()
    {
        $query = RelatorioBairroQuery::agrupadoPorBairro();

        if ($this->data) {
            $query->whereDate('fe.created_at', $this->data);
        }

        return $query
            ->orderByDesc('total_respondentes')
            ->get();
    }

    public function headings(): array
    {
        return ['Bairro', 'Total de Respondentes'];
    }
}
