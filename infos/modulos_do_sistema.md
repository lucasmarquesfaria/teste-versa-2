# Módulos do Sistema de Filtragem com Atributos Acessores

Este documento detalha os módulos do sistema de filtragem com atributos acessores, explicando sua arquitetura, componentes e como você pode reutilizá-los em outros projetos Laravel.

## Arquitetura do Sistema

O sistema é composto por vários módulos interligados que trabalham juntos para fornecer uma solução de filtragem eficiente:

```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│                 │      │                 │      │                 │
│  Model Layer    │◄────►│ Controller Layer│◄────►│   View Layer    │
│  (Accessors)    │      │  (Filtragem)    │      │  (Interface)    │
│                 │      │                 │      │                 │
└────────┬────────┘      └────────┬────────┘      └────────┬────────┘
         │                        │                        │
         │                        │                        │
         ▼                        ▼                        ▼
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│                 │      │                 │      │                 │
│  Database Layer │      │  Service Layer  │      │  Helper Layer   │
│  (Migrations)   │      │  (Lógica)       │      │  (Utilitários)  │
│                 │      │                 │      │                 │
└─────────────────┘      └─────────────────┘      └─────────────────┘
```

## 1. Módulo de Modelos (Model Layer)

### Componentes Principais

#### Modelos Principais
- `Distribuicao.php`: Gerencia a distribuição de formulários numerados
- `Baixa.php`: Registra as devoluções (baixas) de formulários
- `Instituicao.php`: Gerencia as instituições que recebem os formulários

#### Acessores (Accessors)
Os acessores são o coração do sistema, permitindo cálculos dinâmicos:

```php
// Exemplo de acessores do modelo Distribuicao
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
```

### Como Reutilizar

1. **Padrão de Acessores Calculados**:
   - Defina acessores que seguem a convenção `getNomeAttribute()`
   - Crie acessores compostos que usam outros acessores (como `quantidade_pendentes` usando `total_certidoes` e `quantidade_baixas`)
   - Use `return $this->relacao()->count()` para contagens eficientes
   - Use `$this->relacao` (sem parênteses) para acessar coleções carregadas

2. **Hierarquia de Cálculos**:
   - Acessores de base (calculam usando atributos do banco)
   - Acessores de nível médio (usam relacionamentos)
   - Acessores compostos (combinam outros acessores)

## 2. Módulo de Controladores (Controller Layer)

### Componentes Principais

#### DistribuicaoController
O controller principal que implementa a lógica de filtragem em duas etapas:

```php
public function index(Request $request)
{
    // Construção da consulta base
    $query = Distribuicao::with('instituicao');
    
    // Aplicação de filtros diretos (banco de dados)
    if ($request->filled('instituicao_id')) {
        $query->where('instituicao_id', $request->instituicao_id);
    }
    
    if ($request->filled('tipo_certidao')) {
        $query->where('tipo_certidao', $request->tipo_certidao);
    }
    
    // ... outros filtros diretos ...
    
    // Adição de contadores necessários para acessores
    $query = $query->withCount('baixas');
    
    // Aplicação de filtros baseados em acessores
    if ($request->filled('pendentes')) {
        // Etapa 1: Obter dados
        $todasDistribuicoes = $query->get();
        
        // Etapa 2: Filtrar usando acessores
        $distribuicoesComPendencias = $todasDistribuicoes->filter(function ($distribuicao) {
            return $distribuicao->quantidade_pendentes > 0;
        });
        
        // Etapa 3: Extrair IDs e refazer a consulta
        $ids = $distribuicoesComPendencias->pluck('id')->toArray() ?: [0];
        
        // Etapa 4: Nova consulta com IDs filtrados
        $query = Distribuicao::with('instituicao')
            ->withCount('baixas')
            ->whereIn('id', $ids);
    }
    
    // Etapa 5: Paginação
    $distribuicoes = $query->paginate(10)->withQueryString();
    
    // Carregamento de dados para filtros
    $instituicoes = Instituicao::orderBy('nome')->get();
    
    return view('distribuicoes.index', compact('distribuicoes', 'instituicoes'));
}
```

### Método de Filtragem em Etapas

1. **Construção da consulta base**:
   - Inicie com `Model::with()` para eager loading de relacionamentos
   
2. **Aplicação de filtros diretos**:
   - Use `where`, `whereDate`, etc. para condições diretas no banco
   
3. **Adição de contadores**:
   - Use `withCount()` para carregar contagens necessárias para acessores
   
4. **Processo de filtragem por acessores**:
   - Execute a consulta com `.get()`
   - Aplique filtros com `Collection::filter()`
   - Extraia IDs com `pluck('id')`
   - Construa nova consulta com `whereIn('id', $ids)`
   
5. **Finalização da consulta**:
   - Aplique paginação e mantenha parâmetros de URL com `withQueryString()`

### Como Reutilizar

1. **Padrão para Métodos de Filtragem**:
   ```php
   public function index(Request $request)
   {
       // 1. Construa consulta base
       $query = SeuModelo::with(['relacao1', 'relacao2']);
       
       // 2. Aplique filtros diretos (banco de dados)
       if ($request->filled('campo1')) {
           $query->where('campo1', $request->campo1);
       }
       
       // 3. Adicione contadores necessários
       $query = $query->withCount('relacaoParaContagem');
       
       // 4. Aplique ordenação base
       $query = $query->orderBy('campo_data', 'desc');
       
       // 5. Filtragem por acessor (se necessário)
       if ($request->filled('filtro_acessor')) {
           $todosItens = $query->get();
           $itensFiltrados = $todosItens->filter(function ($item) {
               return $item->acessor_calculado > 0;
           });
           $ids = $itensFiltrados->pluck('id')->toArray() ?: [0];
           $query = SeuModelo::with(['relacao1', 'relacao2'])
               ->withCount('relacaoParaContagem')
               ->whereIn('id', $ids)
               ->orderBy('campo_data', 'desc');
       }
       
       // 6. Paginação e retorno
       $resultados = $query->paginate(10)->withQueryString();
       $dadosParaFiltros = ModeloParaFiltro::orderBy('nome')->get();
       
       return view('sua.view', compact('resultados', 'dadosParaFiltros'));
   }
   ```

## 3. Módulo de Views (View Layer)

### Componentes Principais

#### Formulário de Filtros
Template de formulário que aplica os filtros:

```blade
<form action="{{ route('distribuicoes.index') }}" method="GET">
    <div class="filtros-grid">
        <!-- Filtro dropdown -->
        <div>
            <label for="instituicao_id">Instituição</label>
            <select id="instituicao_id" name="instituicao_id">
                <option value="">Todas</option>
                @foreach($instituicoes as $instituicao)
                    <option value="{{ $instituicao->id }}" @selected(request('instituicao_id') == $instituicao->id)>
                        {{ $instituicao->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Filtro de tipo -->
        <div>
            <label for="tipo_certidao">Tipo de Formulário</label>
            <select id="tipo_certidao" name="tipo_certidao">
                <option value="">Todos</option>
                <option value="obito" @selected(request('tipo_certidao') == 'obito')>DO</option>
                <option value="nascidos_vivos" @selected(request('tipo_certidao') == 'nascidos_vivos')>DNV</option>
            </select>
        </div>
        
        <!-- Filtros de data -->
        <div>
            <label for="data_inicio">Data Início</label>
            <input type="date" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
        </div>
        
        <!-- Filtro por accessor -->
        <div>
            <label for="pendentes">Apenas Pendentes</label>
            <input type="checkbox" id="pendentes" name="pendentes" value="1" 
                   {{ request('pendentes') ? 'checked' : '' }} onchange="this.form.submit()">
        </div>
        
        <!-- Botões -->
        <div>
            <button type="submit">Aplicar</button>
            <a href="{{ route('distribuicoes.index') }}">Limpar</a>
        </div>
    </div>
</form>
```

#### Tabela de Resultados
Template que exibe os resultados filtrados:

```blade
<table>
    <thead>
        <tr>
            <th>Instituição</th>
            <th>Tipo</th>
            <th>Numeração</th>
            <th>Data</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($distribuicoes as $distribuicao)
            <tr>
                <td>{{ $distribuicao->instituicao->nome }}</td>
                <td>{{ $distribuicao->tipo_certidao }}</td>
                <td>{{ $distribuicao->numero_inicial }} a {{ $distribuicao->numero_final }}</td>
                <td>{{ $distribuicao->data_entrega->format('d/m/Y') }}</td>
                <td>
                    <div>
                        <span>{{ $distribuicao->quantidade_baixas }}</span> baixas
                        <span>{{ $distribuicao->quantidade_pendentes }}</span> pendentes
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Nenhum registro encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Paginação -->
<div class="paginacao">
    {{ $distribuicoes->links() }}
</div>
```

### Como Reutilizar

1. **Padrão para Formulários de Filtro**:
   - Use método GET para permitir compartilhamento de URLs
   - Mantenha estado dos filtros com `@selected` e `{{ request('campo') }}`
   - Use `onchange="this.form.submit()"` para filtros que devem aplicar imediatamente
   - Inclua botão para limpar filtros

2. **Padrão para Exibição de Dados**:
   - Use `@forelse` para tratar casos sem resultados
   - Acesse acessores diretamente como propriedades: `{{ $item->accessor_property }}`
   - Use `format()` para datas: `{{ $item->data->format('d/m/Y') }}`
   - Use o helper de paginação: `{{ $items->links() }}`

## 4. Módulo de Banco de Dados (Database Layer)

### Componentes Principais

#### Migrações
As migrações definem a estrutura do banco de dados:

```php
// Migração para tabela principal
Schema::create('distribuicoes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('instituicao_id')->constrained('instituicoes');
    $table->enum('tipo_certidao', ['obito', 'nascidos_vivos']);
    $table->unsignedInteger('numero_inicial');
    $table->unsignedInteger('numero_final');
    $table->date('data_entrega');
    $table->text('observacao')->nullable();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});

// Migração para tabela de relacionamento
Schema::create('baixas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('distribuicao_id')->constrained('distribuicoes')->onDelete('cascade');
    $table->unsignedInteger('numero');
    $table->date('data_devolucao');
    $table->enum('situacao', ['utilizada', 'cancelada', 'nao_utilizada']);
    $table->text('observacao')->nullable();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

### Como Reutilizar

1. **Estrutura de Tabelas**:
   - Tabela principal com campos numéricos para cálculos
   - Tabela de relacionamento para registros individuais
   - Chaves estrangeiras com `constrained()` e `onDelete('cascade')`
   - Campos para auditoria (user_id, timestamps)

2. **Campos Calculados (Opcional)**:
   Para otimização de performance em grandes conjuntos de dados:
   
   ```php
   Schema::create('tabela_principal', function (Blueprint $table) {
       // ... outros campos ...
       $table->unsignedInteger('campo_calculado')->default(0);
   });
   ```

## 5. Módulo de Serviços (Service Layer)

Este módulo é opcional mas pode ajudar a organizar melhor a lógica de filtragem para sistemas maiores.

### Exemplo de Service Class

```php
<?php

namespace App\Services;

use App\Models\SeuModelo;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FiltroService
{
    /**
     * Aplica filtros diretos ao query builder
     */
    public function aplicarFiltrosDirectos(Builder $query, Request $request): Builder
    {
        if ($request->filled('campo1')) {
            $query->where('campo1', $request->campo1);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_campo', '>=', $request->data_inicio);
        }
        
        // ... outros filtros ...
        
        return $query;
    }
    
    /**
     * Aplica filtros baseados em accessors
     */
    public function aplicarFiltrosAcessores(Builder $query, Request $request): Builder
    {
        if (!$request->filled('filtro_acessor')) {
            return $query;
        }
        
        $todosItens = $query->get();
        
        $itensFiltrados = $todosItens->filter(function ($item) use ($request) {
            return $item->acessor_calculado > 0;
        });
        
        $ids = $itensFiltrados->pluck('id')->toArray() ?: [0];
        
        return SeuModelo::whereIn('id', $ids);
    }
    
    /**
     * Processa toda a lógica de filtragem
     */
    public function filtrar(Request $request)
    {
        $query = SeuModelo::with(['relacao1', 'relacao2']);
        
        $query = $this->aplicarFiltrosDirectos($query, $request);
        $query = $query->withCount('relacaoParaContagem');
        $query = $this->aplicarFiltrosAcessores($query, $request);
        
        return $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();
    }
}
```

### Como Reutilizar

1. **Criação da Service Class**:
   - Crie uma pasta `app/Services` se não existir
   - Implemente métodos para separar diferentes tipos de filtros
   - Use injeção de dependência se necessário

2. **Uso no Controller**:
   ```php
   public function index(Request $request, FiltroService $filtroService)
   {
       $resultados = $filtroService->filtrar($request);
       $dadosParaFiltros = ModeloParaFiltro::orderBy('nome')->get();
       
       return view('sua.view', compact('resultados', 'dadosParaFiltros'));
   }
   ```

## 6. Módulo de Utilitários (Helper Layer)

### Components Blade

Para reuso da interface de filtros:

```php
// app/View/Components/FilterPanel.php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilterPanel extends Component
{
    public $route;
    
    public function __construct($route)
    {
        $this->route = $route;
    }
    
    public function render()
    {
        return view('components.filter-panel');
    }
}

// resources/views/components/filter-panel.blade.php
<div class="filtro-container">
    <form action="{{ $route }}" method="GET">
        <div class="filtros-grid">
            {{ $slot }}
            
            <div class="botoes">
                <button type="submit">Aplicar Filtros</button>
                <a href="{{ $route }}">Limpar</a>
            </div>
        </div>
    </form>
</div>
```

### Como Reutilizar

1. **Registro de Components**:
   - Laravel 8+: Os componentes são auto-descobertos
   - Laravel < 8: Registre em `AppServiceProvider`

2. **Uso dos Components**:
   ```blade
   <x-filter-panel :route="route('seu.recurso.index')">
       <!-- Campos de filtro aqui -->
       <div>
           <label for="campo1">Campo 1</label>
           <input type="text" name="campo1" value="{{ request('campo1') }}">
       </div>
   </x-filter-panel>
   ```

## Padrões de Implementação e Boas Práticas

### 1. Evitar N+1 Queries

```php
// Uso correto de eager loading
$query = Modelo::with(['relacao1', 'relacao2']);

// Carregamento de contagens eficiente
$query->withCount('relacaoParaContagem');
```

### 2. Cache para Consultas Frequentes

```php
// Cache dos dados de filtro dropdown
$dadosParaFiltros = cache()->remember('filtro_dados', now()->addHour(), function() {
    return ModeloParaFiltro::orderBy('nome')->get();
});
```

### 3. Validação de Filtros

```php
// Validação dos parâmetros de filtro
$validated = $request->validate([
    'data_inicio' => 'nullable|date',
    'data_fim' => 'nullable|date|after_or_equal:data_inicio',
    'campo_id' => 'nullable|exists:tabela,id',
]);
```

### 4. Melhor Uso do Eloquent

```php
// Consulta eficiente para contagem
$count = $this->relacaoHasMany()
    ->selectRaw('COUNT(*) as count')
    ->value('count');

// Uso de Collection para operações avançadas
$numeros = $this->relacaoHasMany->pluck('numero')
    ->unique()
    ->values()
    ->all();
```

## Exemplo de Implementação Completa

Para implementar o sistema em um novo projeto, siga estas etapas:

1. **Crie os modelos com accessors**
2. **Implemente o controller com a lógica de filtragem**
3. **Crie as views com formulário de filtro e tabela de resultados**
4. **Configure as rotas no arquivo routes/web.php**
5. **Para sistemas maiores, considere usar Service Classes**

### Arquivos Necessários

1. `app/Models/SeuModelo.php` - Modelo principal com accessors
2. `app/Models/ModeloRelacionado.php` - Modelo para relacionamento
3. `app/Http/Controllers/SeuController.php` - Controller com lógica de filtro
4. `resources/views/sua_pasta/index.blade.php` - View com formulário e tabela
5. `database/migrations/create_suas_tabelas.php` - Migrações
6. `routes/web.php` - Configuração de rotas

## Conclusão

O sistema de filtragem com atributos acessores é uma solução elegante para filtrar dados com base em valores calculados dinamicamente. A arquitetura modular permite adaptar o sistema para diferentes necessidades e escalá-lo conforme o projeto cresce.

Ao seguir os padrões e boas práticas descritos neste documento, você pode reutilizar o sistema em diferentes projetos Laravel, adaptando-o às suas necessidades específicas sem precisar reinventar a lógica de filtragem.

Para sistemas com grande volume de dados, considere as otimizações mencionadas, especialmente a adição de campos calculados diretamente no banco de dados e o uso de observers para mantê-los atualizados.
