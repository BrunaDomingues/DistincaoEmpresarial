<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioFatorSatisfacao extends Model
{
    use HasFactory;

    protected $table = 'formularios_fator_satisfacao';

    protected $fillable = [
        'formulario_id',
        'titulo',
        'resposta_obrigatoria',
        'usa_input_extra',
        'created_by',
        'updated_by',
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
