<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormularioRespostaTratada extends Model
{
    use HasFactory;

    protected $table = 'formulario_respostas_tratadas';

    protected $fillable = [
        'resposta_id',
        'resposta_tratada',
        'conferida',
        'created_by',
        'updated_by'
    ];

    public function resposta()
    {
        return $this->belongsTo(FormularioResposta::class);
    }
}
