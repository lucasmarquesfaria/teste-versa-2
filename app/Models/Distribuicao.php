<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distribuicao extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'distribuicoes';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instituicao_id',
        'tipo_certidao',
        'numero_inicial',
        'numero_final',
        'data_entrega',
        'observacao',
        'user_id',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_entrega' => 'date',
    ];

    /**
     * Obtém a instituição relacionada à distribuição.
     */
    public function instituicao(): BelongsTo
    {
        return $this->belongsTo(Instituicao::class);
    }

    /**
     * Obtém as baixas relacionadas à distribuição.
     */
    public function baixas(): HasMany
    {
        return $this->hasMany(Baixa::class);
    }

    /**
     * Obtém o usuário que criou a distribuição.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Calcula a quantidade total de certidões distribuídas.
     */
    public function getTotalCertidoesAttribute(): int
    {
        return ($this->numero_final - $this->numero_inicial) + 1;
    }
    
    /**
     * Calcula a quantidade de certidões com baixa.
     */
    public function getQuantidadeBaixasAttribute(): int
    {
        return $this->baixas()->count();
    }
    
    /**
     * Calcula a quantidade de certidões pendentes.
     */
    public function getQuantidadePendentesAttribute(): int
    {
        return $this->total_certidoes - $this->quantidade_baixas;
    }

    /**
     * Números de declarações ainda não devolvidas (pendentes) desta distribuição.
     * Uso: $distribuicao->numeros_pendentes
     * @return array
     */
    public function getNumerosPendentesAttribute()
    {
        $todos = range($this->numero_inicial, $this->numero_final);
        $baixados = $this->baixas->pluck('numero')->toArray();
        return array_values(array_diff($todos, $baixados));
    }
    
    /**
     * Retorna a data limite para devolução (baixa) desta distribuição.
     * Por padrão, 30 dias após a data_entrega.
     */
    public function getDataLimiteBaixaAttribute()
    {
        $prazoDias = 30; // Pode ser tornado configurável
        return $this->data_entrega ? $this->data_entrega->copy()->addDays($prazoDias) : null;
    }
}
