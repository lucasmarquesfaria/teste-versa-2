# Documentação para Filtragem de Recursos com Atributos Acessores

## Visão Geral

Esta documentação fornece uma visão geral sobre como implementar um sistema de filtragem para recursos Eloquent que utilizam atributos acessores (accessors), com base na funcionalidade implementada no sistema de gestão de Declarações de Óbito e Nascidos Vivos.

## Classes Relevantes

### 1. Modelo (`Distribuicao.php`)

O modelo contém os acessores (accessors) que calculam valores dinâmicos:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distribuicao extends Model
{
    use HasFactory;

    protected $table = 'distribuicoes';

    protected $fillable = [
        'instituicao_id',
        'tipo_certidao',
        'numero_inicial',
        'numero_final',
        'data_entrega',
        'observacao',
        'user_id',
    ];

    protected $casts = [
        'data_entrega' => 'date',
    ];

    // Relações
    public function instituicao(): BelongsTo
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function baixas(): HasMany
    {
        return $this->hasMany(Baixa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Acessores para cálculos dinâmicos
    public function getTotalCertidoesAttribute(): int
    {
        return ($this->numero_final - $this->numero_inicial) + 1;
    }
    
    public function getQuantidadeBaixasAttribute(): int
    {
        return $this->baixas()->count();
    }
    
    public function getQuantidadePendentesAttribute(): int
    {
        return $this->total_certidoes - $this->quantidade_baixas;
    }

    public function getNumerosPendentesAttribute()
    {
        $todos = range($this->numero_inicial, $this->numero_final);
        $baixados = $this->baixas->pluck('numero')->toArray();
        return array_values(array_diff($todos, $baixados));
    }
}
```

### 2. Controller (`ResourceController.php`)

O controller mostra como fazer a filtragem de maneira eficiente:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource with filters applied.
     */
    public function index(Request $request)
    {
        // Inicializa a consulta base com relacionamentos necessários
        $query = Resource::with('relationships');
        
        // Aplica filtros básicos (que funcionam diretamente com o banco de dados)
        if ($request->filled('field_one')) {
            $query->where('field_one', $request->field_one);
        }
        
        if ($request->filled('field_two')) {
            $query->where('field_two', $request->field_two);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('date_field', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('date_field', '<=', $request->date_to);
        }
        
        // Adiciona contadores para uso nos acessores se necessário
        $query = $query->withCount('related_models');
        
        // Aplica a ordenação base
        $query = $query->orderBy('date_field', 'desc');
        
        // Aplica o filtro que depende de acessores (calculados após consulta)
        if ($request->filled('accessor_filter')) {
            // 1. Executa a consulta para obter todos os resultados correspondentes aos filtros básicos
            $allItems = $query->get();
            
            // 2. Filtra com base no acessor dinâmico
            $filteredItems = $allItems->filter(function ($item) {
                return $item->calculated_attribute > 0; // Filtro baseado no acessor
            });
            
            // 3. Extrai os IDs para nova consulta
            $ids = $filteredItems->pluck('id')->toArray();
            
            // 4. Garante que a consulta não quebrará se nenhum item corresponder
            if (empty($ids)) {
                $ids = [0]; // ID inexistente para garantir conjunto vazio
            }
            
            // 5. Cria nova consulta apenas com os IDs filtrados
            $query = Resource::with('relationships')
                ->withCount('related_models')
                ->whereIn('id', $ids)
                ->orderBy('date_field', 'desc');
        }
        
        // Aplica paginação ao resultado final
        $resources = $query->paginate(10);
        
        // Retorna a view com os dados
        return view('resources.index', compact('resources'));
    }
}
```

### 3. View Template (`index.blade.php`)

Template para exibir a lista com filtros:

```blade
<form action="{{ route('resources.index') }}" method="GET" class="filters-form">
    <div class="filters-grid">
        <!-- Campos de filtro básicos -->
        <div>
            <label for="field_one">Campo Um</label>
            <select id="field_one" name="field_one">
                <option value="">Todos</option>
                @foreach($options as $option)
                    <option value="{{ $option->id }}" @selected(request('field_one') == $option->id)>
                        {{ $option->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Filtros de data -->
        <div>
            <label for="date_from">Data Inicial</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
        </div>
        
        <div>
            <label for="date_to">Data Final</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
        </div>
        
        <!-- Filtro que usa acessor -->
        <div>
            <label for="accessor_filter">Filtro Especial</label>
            <input type="checkbox" id="accessor_filter" name="accessor_filter" value="1" 
                   {{ request('accessor_filter') ? 'checked' : '' }} onchange="this.form.submit()">
            <span class="checkbox-label">Mostrar apenas itens com condição especial</span>
        </div>
        
        <div class="submit-wrapper">
            <button type="submit">Aplicar Filtros</button>
            <a href="{{ route('resources.index') }}">Limpar</a>
        </div>
    </div>
</form>

<!-- Exibição dos resultados -->
<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Data</th>
            <th>Atributo Calculado</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($resources as $resource)
            <tr>
                <td>{{ $resource->name }}</td>
                <td>{{ $resource->date_field->format('d/m/Y') }}</td>
                <td>{{ $resource->calculated_attribute }}</td>
                <td>
                    <a href="{{ route('resources.show', $resource) }}">Ver</a>
                    <a href="{{ route('resources.edit', $resource) }}">Editar</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Nenhum registro encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination">
    {{ $resources->links() }}
</div>
```

## O que você precisa para reutilizar este código

1. **Definir seus modelos Eloquent**:
   - Crie modelos com suas relações apropriadas
   - Defina os acessores (accessors) necessários para cálculos dinâmicos

2. **Configurar seu controller**:
   - Implemente a lógica de filtragem em etapas:
     1. Filtros diretos de banco de dados (where, whereIn, etc.)
     2. Execução da consulta para obter objetos
     3. Filtragem adicional utilizando acessores
     4. Nova consulta apenas com os IDs filtrados
     5. Paginação do resultado final

3. **Criar views**:
   - Formulário de filtro com método GET
   - Exibição da listagem de recursos
   - Paginação dos resultados

4. **Otimizações possíveis**:
   - Para sistemas com grande volume de dados, considere:
     - Adicionar índices nas colunas filtradas frequentemente
     - Armazenar valores calculados no banco de dados
     - Implementar cache para consultas comuns
     - Utilizar jobs em fila para cálculos pesados

## Diagramas

### Fluxo do Filtro com Acessores

```
[Request] → [Filtros Básicos] → [Consulta Inicial] → [Obter Objetos] 
  → [Filtrar por Acessores] → [Extrair IDs] → [Nova Consulta] → [Paginação] → [View]
```

### Ciclo de Vida do Filtro

1. Usuário submete formulário de filtro
2. Controller recebe os parâmetros da requisição
3. Filtros básicos são aplicados via query builder
4. Atributos acessores são calculados após carregar objetos
5. Nova consulta é feita apenas com os IDs filtrados
6. Resultados paginados são enviados para a view

## Considerações de Performance

- Esta técnica carrega todos os registros em memória antes de aplicar o filtro por acessor, o que pode ser problemático para conjuntos de dados muito grandes.
- Para otimizar a performance com grandes volumes:
  1. Adicione um campo calculado na tabela e mantenha-o atualizado
  2. Use eventos/observers para atualizar este campo quando relacionamentos mudarem
  3. Filtre diretamente por este campo no banco de dados

## Exemplo de Implementação Simplificada

```php
// Em um controller:
$query = Model::with('relations');

// Aplicar filtros básicos
if ($request->filled('filter1')) {
    $query->where('field', $request->filter1);
}

// Verificar se precisamos filtrar por um accessor
if ($request->filled('accessor_filter')) {
    $items = $query->get();
    $filtered = $items->filter(fn($item) => $item->accessor_property > 0);
    $ids = $filtered->pluck('id')->toArray() ?: [0];
    $query = Model::with('relations')->whereIn('id', $ids);
}

$results = $query->paginate(10);
```
