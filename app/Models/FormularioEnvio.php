<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormularioEnvio extends Model
{
    protected $casts = [
        'geo_info' => 'array',
    ];
    
    protected $fillable = [
        'formulario_id',
        'usuario_id',
        'ip',
        'user_agent',
        'geo_info',
        'latitude',
        'longitude',
        'rua',
        'bairro',
        'cidade',
        'estado',
        'inicio_resposta',
        'fim_resposta',
        'duracao_em_segundos',
    ];

    public function respostas()
    {
        return $this->hasMany(FormularioResposta::class);
    }

    public function respostasTratadas()
    {
        return $this->hasManyThrough(
            FormularioRespostaTratada::class,
            FormularioResposta::class,
            'formulario_envio_id', // Foreign key on FormularioResposta
            'resposta_id', // Foreign key on FormularioRespostaTratada
            'id', // Local key on FormularioEnvio
            'id'  // Local key on FormularioResposta
        );
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function getDuracaoFormatadaAttribute()
    {
        return gmdate('H\h i\m s\s', $this->duracao_em_segundos ?? 0);
    }

    public function getStatusValidacaoAttribute()
    {
        if ($this->invalido) {
            return 'Inválido';
        }

        if ($this->pendentes > 0) {
            return 'Pendente';
        }

        return 'Válido';
    }
}
