<?php

namespace App\Models;

use App\Support\EmpresaNomeNormalizer;
use Illuminate\Database\Eloquent\Model;

class InsightEmpresaAlias extends Model
{
    protected $fillable = [
        'termo',
        'termo_normalizado',
        'nome_canonico',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function normalizarTermo(string $termo): string
    {
        return EmpresaNomeNormalizer::normalize($termo);
    }
}
