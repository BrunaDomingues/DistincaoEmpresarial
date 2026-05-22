<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioPergunta extends Model
{
    use HasFactory;

    protected $fillable = ['passo_id', 'pergunta', 'tipo', 'obrigatorio', 'usa_fatores_satisfacao', 'created_by', 'updated_by'];

    public function passo()
    {
        return $this->belongsTo(FormularioPasso::class);
    }

    public function opcoes()
    {
        return $this->hasMany(FormularioOpcao::class, 'pergunta_id');
    }

    public function usaFatoresSatisfacao()
    {
        return $this->usa_fatores_satisfacao; // boolean
    }
    
/*     public function respostasTratadas() {
        return $this->hasMany(FormularioRespostaTratada::class, 'pergunta_id');
    }
    
    public function respostas()
    {
        return $this->hasMany(FormularioResposta::class, 'pergunta_id');
    } */
}