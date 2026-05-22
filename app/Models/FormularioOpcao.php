<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioOpcao extends Model
{
    use HasFactory;

    protected $table = 'formulario_opcoes';

    protected $fillable = ['pergunta_id', 'opcao', 'created_by', 'updated_by'];

    public function pergunta()
    {
        return $this->belongsTo(FormularioPergunta::class);
    }
}
