# Documentação para Implementação de Filtragem com Atributos Acessores

## Visão Geral

Esta documentação fornece todos os detalhes necessários para implementar um sistema de filtragem para recursos Eloquent que utilizam atributos acessores (accessors) em aplicações Laravel. O sistema permite realizar filtros baseados tanto em campos do banco de dados quanto em valores calculados dinamicamente através de accessors.

## Classes e Componentes Necessários

### 1. Modelos Eloquent com Accessors

Para implementar este sistema, você precisará de modelos Eloquent com accessors bem definidos:

#### Exemplo de Modelo Principal (`Modelo.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeuModelo extends Model
{
    use HasFactory;

    protected $table = 'sua_tabela';

    protected $fillable = [
        'campo_id_relacionado',
        'campo_tipo',
        'campo_numerico_1',
        'campo_numerico_2',
        'campo_data',
        'campo_observacao',
        'user_id',
    ];

    protected $casts = [
        'campo_data' => 'date',
    ];

    // Relações
    public function relacao(): BelongsTo
    {
        return $this->belongsTo(ModeloRelacionado::class, 'campo_id_relacionado');
    }

    public function relacaoHasMany(): HasMany
    {
        return $this->hasMany(ModeloFilho::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Accessors para cálculos dinâmicos
    public function getValorTotalAttribute(): int
    {
        // Exemplo de cálculo baseado em campos do modelo
        return ($this->campo_numerico_2 - $this->campo_numerico_1) + 1;
    }
    
    public function getQuantidadeRelacionadosAttribute(): int
    {
        // Exemplo de cálculo baseado em relacionamentos
        return $this->relacaoHasMany()->count();
    }
    
    public function getQuantidadePendentesAttribute(): int
    {
        // Exemplo de cálculo que depende de outros acessores
        return $this->valor_total - $this->quantidade_relacionados;
    }

    public function getItemsPendentesAttribute()
    {
        // Exemplo de cálculo que compara arrays/coleções
        $todos = range($this->campo_numerico_1, $this->campo_numerico_2);
        $relacionados = $this->relacaoHasMany->pluck('numero')->toArray();
        return array_values(array_diff($todos, $relacionados));
    }
}
```

### 2. Controller para Implementar a Filtragem

Este é o componente principal que realiza a filtragem:

```php
<?php

namespace App\Http\Controllers;

use App\Models\SeuModelo;
use App\Models\ModeloRelacionado;
use Illuminate\Http\Request;

class SeuController extends Controller
{
    /**
     * Exibe a listagem com filtros aplicados
     */
    public function index(Request $request)
    {
        // Passo 1: Inicialize a consulta base com os relacionamentos necessários
        $query = SeuModelo::with(['relacao', 'relacaoHasMany']);
        
        // Passo 2: Aplique filtros diretos no banco de dados
        // Filtro por campo relacionado
        if ($request->filled('campo_id_relacionado')) {
            $query->where('campo_id_relacionado', $request->campo_id_relacionado);
        }
        
        // Filtro por tipo
        if ($request->filled('campo_tipo')) {
            $query->where('campo_tipo', $request->campo_tipo);
        }
        
        // Filtro por intervalo de data
        if ($request->filled('data_inicio')) {
            $query->whereDate('campo_data', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('campo_data', '<=', $request->data_fim);
        }
        
        // Passo 3: Adicione contadores que possam ser utilizados nos accessors
        $query = $query->withCount('relacaoHasMany');
        
        // Passo 4: Aplique ordenação base
        $query = $query->orderBy('campo_data', 'desc');
        
        // Passo 5: Filtragem baseada em accessors (valores calculados)
        if ($request->filled('filtro_accessor')) {
            // 5.1: Execute a consulta para obter todos os resultados que correspondem aos filtros básicos
            $todosItens = $query->get();
            
            // 5.2: Filtre com base no accessor dinâmico
            $itensFiltrados = $todosItens->filter(function ($item) use ($request) {
                // Aplique a lógica de filtragem baseada no accessor
                return $item->quantidade_pendentes > 0;
            });
            
            // 5.3: Extraia os IDs para uma nova consulta
            $ids = $itensFiltrados->pluck('id')->toArray();
            
            // 5.4: Garanta que a consulta não quebrará se nenhum item corresponder
            if (empty($ids)) {
                $ids = [0]; // ID inexistente para garantir conjunto vazio
            }
            
            // 5.5: Crie uma nova consulta apenas com os IDs filtrados
            $query = SeuModelo::with(['relacao', 'relacaoHasMany'])
                ->withCount('relacaoHasMany')
                ->whereIn('id', $ids)
                ->orderBy('campo_data', 'desc');
        }
        
        // Passo 6: Aplique a paginação ao resultado final
        $resultados = $query->paginate(10)->withQueryString();
        
        // Passo 7: Carregue dados para os filtros dropdown (se necessário)
        $itensRelacionados = ModeloRelacionado::orderBy('nome')->get();
        
        // Passo 8: Retorne a view com os dados
        return view('sua_pasta.index', compact('resultados', 'itensRelacionados'));
    }
}
```

### 3. View para Exibir Filtros e Resultados

```blade
<form action="{{ route('seu.recurso.index') }}" method="GET">
    <div class="grid-filtros">
        <!-- Campo de filtro para relacionamento -->
        <div>
            <label for="campo_id_relacionado">Item Relacionado</label>
            <select id="campo_id_relacionado" name="campo_id_relacionado">
                <option value="">Todos</option>
                @foreach($itensRelacionados as $item)
                    <option value="{{ $item->id }}" @selected(request('campo_id_relacionado') == $item->id)>
                        {{ $item->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Campo de filtro para tipo -->
        <div>
            <label for="campo_tipo">Tipo</label>
            <select id="campo_tipo" name="campo_tipo">
                <option value="">Todos</option>
                <option value="tipo_a" @selected(request('campo_tipo') == 'tipo_a')>Tipo A</option>
                <option value="tipo_b" @selected(request('campo_tipo') == 'tipo_b')>Tipo B</option>
            </select>
        </div>
        
        <!-- Campo de filtro para data inicial -->
        <div>
            <label for="data_inicio">Data Inicial</label>
            <input type="date" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
        </div>
        
        <!-- Campo de filtro para data final -->
        <div>
            <label for="data_fim">Data Final</label>
            <input type="date" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
        </div>
        
        <!-- Campo de filtro baseado em accessor -->
        <div>
            <label for="filtro_accessor">Apenas com Pendências</label>
            <input type="checkbox" id="filtro_accessor" name="filtro_accessor" value="1" 
                   {{ request('filtro_accessor') ? 'checked' : '' }} onchange="this.form.submit()">
        </div>
        
        <!-- Botões -->
        <div>
            <button type="submit">Aplicar Filtros</button>
            <a href="{{ route('seu.recurso.index') }}">Limpar</a>
        </div>
    </div>
</form>

<!-- Tabela de resultados -->
<table>
    <thead>
        <tr>
            <th>Campo 1</th>
            <th>Campo 2</th>
            <th>Valor Calculado</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($resultados as $item)
            <tr>
                <td>{{ $item->relacao->nome }}</td>
                <td>{{ $item->campo_data->format('d/m/Y') }}</td>
                <td>{{ $item->quantidade_pendentes }}</td>
                <td>
                    <a href="{{ route('seu.recurso.show', $item) }}">Visualizar</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Nenhum registro encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Paginação -->
<div>
    {{ $resultados->links() }}
</div>
```

## Estrutura do Banco de Dados

Para implementar esta solução, você precisará das seguintes tabelas:

### Tabela Principal

```php
Schema::create('sua_tabela', function (Blueprint $table) {
    $table->id();
    $table->foreignId('campo_id_relacionado')->constrained('tabela_relacionada');
    $table->enum('campo_tipo', ['tipo_a', 'tipo_b']);
    $table->unsignedInteger('campo_numerico_1');
    $table->unsignedInteger('campo_numerico_2');
    $table->date('campo_data');
    $table->text('campo_observacao')->nullable();
    $table->foreignId('user_id')->constrained('users');
    $table->timestamps();
});
```

### Tabela de Relacionamento HasMany

```php
Schema::create('tabela_filho', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seu_modelo_id')->constrained('sua_tabela')->onDelete('cascade');
    $table->unsignedInteger('numero');
    $table->date('data_relacionada');
    $table->enum('status', ['status_a', 'status_b', 'status_c']);
    $table->text('observacao')->nullable();
    $table->foreignId('user_id')->constrained('users');
    $table->timestamps();
});
```

## Passo a Passo para Implementação

1. **Defina seus modelos Eloquent**:
   - Crie os modelos com suas relações
   - Adicione os accessors para cálculos dinâmicos
   - Configure os fillable, casts e outras propriedades necessárias

2. **Configure suas migrações**:
   - Crie as migrações para suas tabelas
   - Execute as migrações para criar as tabelas no banco de dados

3. **Implemente o controller**:
   - Siga o padrão demonstrado para a filtragem em etapas:
     1. Filtros diretos no banco de dados
     2. Obtenção de resultados
     3. Filtragem adicional com acessores
     4. Nova consulta com IDs filtrados
     5. Paginação e exibição

4. **Crie as views**:
   - Formulário de filtro (com método GET)
   - Tabela para exibição dos resultados
   - Paginação

5. **Configure as rotas**:
   ```php
   // routes/web.php
   Route::resource('seu-recurso', SeuController::class);
   ```

## Considerações Sobre Performance

Este padrão de implementação apresenta algumas considerações importantes de performance:

1. **Carga de Dados em Memória**: Ao usar filtragem baseada em accessors, todos os registros que correspondem aos filtros iniciais são carregados em memória. Isso pode ser problemático com grandes volumes de dados.

2. **Otimizações Possíveis**:
   - **Campos Calculados**: Se a filtragem por accessor é frequente, considere adicionar campos calculados diretamente nas tabelas do banco de dados.
   - **Eventos e Observers**: Use eventos do Laravel para atualizar os campos calculados quando os relacionamentos mudarem.
   - **Cache**: Implemente cache para consultas que não mudam com frequência.
   - **Consultas de Contagem Eficientes**: Em vez de carregar todos os registros, você pode usar `whereHas` com `groupBy` e `having` em alguns casos.

3. **Paginação Eficiente**: Quando trabalhando com dados filtrados por accessors, a paginação sempre acontece após a filtragem completa, pois requer uma nova consulta com IDs específicos.

## Exemplo de Otimização para Grandes Conjuntos de Dados

```php
// Em vez de filtrar por accessors, você pode adicionar campos calculados na tabela:
Schema::create('sua_tabela', function (Blueprint $table) {
    // ... campos originais ...
    $table->unsignedInteger('quantidade_pendentes')->default(0);
});

// E atualizar esse campo usando observers:
class RelacionamentoObserver
{
    public function created($modelo)
    {
        $this->atualizarContadores($modelo->seu_modelo_id);
    }

    public function updated($modelo)
    {
        $this->atualizarContadores($modelo->seu_modelo_id);
    }

    public function deleted($modelo)
    {
        $this->atualizarContadores($modelo->seu_modelo_id);
    }

    private function atualizarContadores($modeloId)
    {
        $modelo = SeuModelo::find($modeloId);
        if ($modelo) {
            $total = ($modelo->campo_numerico_2 - $modelo->campo_numerico_1) + 1;
            $quantidade = $modelo->relacaoHasMany()->count();
            $modelo->quantidade_pendentes = $total - $quantidade;
            $modelo->save();
        }
    }
}
```

## Comparação com o Sistema Original

Este padrão de implementação é baseado no sistema de gestão de Declarações de Óbito e Nascidos Vivos, onde:

1. O modelo `Distribuicao` permite controlar números de formulários distribuídos
2. O modelo `Baixa` registra a devolução desses formulários
3. Os accessors calculam:
   - Total de formulários (`total_certidoes`)
   - Quantidade devolvida (`quantidade_baixas`)
   - Quantidade pendente (`quantidade_pendentes`)
   - Números específicos pendentes (`numeros_pendentes`)

## Resumo de Implementação

1. **Modelos com Accessors**: Defina modelos com cálculos dinâmicos via accessors
2. **Filtragem em Etapas**:
   - Filtros diretos no banco de dados primeiro
   - Filtros baseados em accessors depois
3. **Nova Consulta com IDs**: Após filtragem por accessors, crie uma nova consulta para paginação eficiente
4. **Parâmetros Persistentes**: Use `withQueryString()` para manter parâmetros de filtro durante a navegação entre páginas

Esta implementação oferece uma maneira eficiente e organizada de filtrar recursos baseados tanto em campos do banco de dados quanto em valores calculados dinamicamente.
