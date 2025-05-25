<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Baixa extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'distribuicao_id',
        'numero',
        'data_devolucao',
        'situacao',
        'observacao',
        'user_id',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_devolucao' => 'date',
    ];

    /**
     * Obtém a distribuição relacionada à baixa.
     */
    public function distribuicao(): BelongsTo
    {
        return $this->belongsTo(Distribuicao::class);
    }

    /**
     * Obtém o usuário que registrou a baixa.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Verifica se o número está dentro da faixa da distribuição.
     */
    public function isNumeroValido(): bool
    {
        $distribuicao = $this->distribuicao;
        return $this->numero >= $distribuicao->numero_inicial && $this->numero <= $distribuicao->numero_final;
    }
}
