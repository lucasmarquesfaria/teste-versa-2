<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instituicao extends Model
{
    use HasFactory;
    
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'instituicoes';
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'endereco',
        'telefone',
        'email',
    ];
    
    /**
     * Obtém as distribuições relacionadas à instituição.
     */
    public function distribuicoes(): HasMany
    {
        return $this->hasMany(Distribuicao::class);
    }
}
