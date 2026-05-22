<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormularioResposta extends Model
{
    use HasFactory;

    protected $table = 'formulario_respostas';

    protected $fillable = [
        'pergunta_id',
        'usuario_id',
        'resposta',
        'fator_id',
        'formulario_envio_id',
        'input_fator',
        'created_by',
        'updated_by'
    ];

    public function pergunta()
    {
        return $this->belongsTo(FormularioPergunta::class, 'pergunta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function respostaTratada()
    {
        return $this->hasOne(FormularioRespostaTratada::class, 'resposta_id');
    }

    public function envio()
    {
        return $this->belongsTo(FormularioEnvio::class, 'formulario_envio_id');
    }

    public function fator()
    {
        return $this->belongsTo(FormularioFatorSatisfacao::class, 'fator_id');
    }
}
