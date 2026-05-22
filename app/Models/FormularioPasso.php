<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormularioPasso extends Model
{
    use HasFactory;

    protected $fillable = ['formulario_id', 'titulo', 'ordem', 'created_by', 'updated_by'];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class);
    }

    public function perguntas()
    {
        return $this->hasMany(FormularioPergunta::class, 'passo_id');
    }
}
