<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FormularioPergunta;
use App\Models\FormularioPasso;
use App\Models\User;

class Formulario extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'data_inicio',
        'data_fim',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function scopeDisponivel(Builder $query, ?Carbon $data = null): Builder
    {
        $hoje = ($data ?? now())->toDateString();

        return $query
            ->where(function (Builder $q) use ($hoje) {
                $q->whereNull('data_inicio')
                    ->orWhereDate('data_inicio', '<=', $hoje);
            })
            ->where(function (Builder $q) use ($hoje) {
                $q->whereNull('data_fim')
                    ->orWhereDate('data_fim', '>=', $hoje);
            });
    }

    public function estaDisponivel(?Carbon $data = null): bool
    {
        $hoje = ($data ?? now())->toDateString();

        if ($this->data_inicio && $hoje < $this->data_inicio->toDateString()) {
            return false;
        }

        if ($this->data_fim && $hoje > $this->data_fim->toDateString()) {
            return false;
        }

        return true;
    }

    public function mensagemIndisponibilidade(): string
    {
        $hoje = now()->toDateString();

        if ($this->data_inicio && $hoje < $this->data_inicio->toDateString()) {
            return 'Este formulário estará disponível a partir de '.$this->data_inicio->format('d/m/Y').'.';
        }

        if ($this->data_fim && $hoje > $this->data_fim->toDateString()) {
            return 'O prazo para responder este formulário encerrou em '.$this->data_fim->format('d/m/Y').'.';
        }

        return 'Este formulário não está disponível no momento.';
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

/*     public function respostas()
    {
        return $this->hasMany(Resposta::class);
    } */

    public function perguntas()
    {
        return $this->hasMany(FormularioPergunta::class);
    }

    public function passos()
    {
        return $this->hasMany(FormularioPasso::class);
    }

    public function fatoresSatisfacao()
    {
        return $this->hasMany(FormularioFatorSatisfacao::class);
    }
}
