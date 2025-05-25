<?php

namespace App\Http\Controllers;

use App\Models\Distribuicao;
use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistribuicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('distribuicao_listar');
        
        $distribuicoes = Distribuicao::with('instituicao');
        
        // Filtro por instituição
        if (request()->has('instituicao_id') && !empty(request('instituicao_id'))) {
            $distribuicoes->where('instituicao_id', request('instituicao_id'));
        }
        
        // Filtro por tipo de certidão
        if (request()->has('tipo_certidao') && !empty(request('tipo_certidao'))) {
            $distribuicoes->where('tipo_certidao', request('tipo_certidao'));
        }
        
        // Filtro por data
        if (request()->has('data_inicio') && !empty(request('data_inicio'))) {
            $distribuicoes->whereDate('data_entrega', '>=', request('data_inicio'));
        }
        
        if (request()->has('data_fim') && !empty(request('data_fim'))) {
            $distribuicoes->whereDate('data_entrega', '<=', request('data_fim'));
        }
        
        // Obter as instituições para o dropdown do filtro
        $instituicoes = Instituicao::orderBy('nome')->get();
        
        // Carregar os relacionamentos e adicionar o withCount para contar as baixas
        $distribuicoes = $distribuicoes->withCount('baixas')->orderBy('data_entrega', 'desc');

        // Filtro: apenas distribuições pendentes de devolução
        // Primeiro, obtemos todos os IDs que precisamos consultar
        if (request()->has('pendentes') && request('pendentes')) {
            // Executamos a consulta para obter todas as distribuições
            $todasDistribuicoes = $distribuicoes->get();
            
            // Filtramos as distribuições com pendências
            $distribuicoesComPendencias = $todasDistribuicoes->filter(function ($distribuicao) {
                return $distribuicao->quantidade_pendentes > 0;
            });
            
            // Recuperamos os IDs para fazer uma nova consulta paginada
            $ids = $distribuicoesComPendencias->pluck('id')->toArray();
            
            // Se não houver IDs, garantimos pelo menos um ID inexistente para que a consulta retorne vazia
            if (empty($ids)) {
                $ids = [0];
            }
            
            // Criamos uma nova consulta apenas com os IDs filtrados
            $distribuicoes = Distribuicao::with('instituicao')
                ->withCount('baixas')
                ->whereIn('id', $ids)
                ->orderBy('data_entrega', 'desc');
        }
        
        // Aplicamos a paginação no final
        $distribuicoes = $distribuicoes->paginate(10);
        
        return view('distribuicoes.index', compact('distribuicoes', 'instituicoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('distribuicao_criar');
        
        $instituicoes = Instituicao::orderBy('nome')->get();
        
        return view('distribuicoes.create', compact('instituicoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('distribuicao_criar');
        
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'tipo_certidao' => 'required|in:obito,nascidos_vivos',
            'numero_inicial' => 'required|integer|min:1',
            'numero_final' => 'required|integer|gte:numero_inicial',
            'data_entrega' => 'required|date',
            'observacao' => 'nullable|string',
        ]);
        
        // Verificar se há sobreposição de numeração
        $sobreposicao = Distribuicao::where('tipo_certidao', $request->tipo_certidao)
            ->where(function ($query) use ($request) {
                $query->whereBetween('numero_inicial', [$request->numero_inicial, $request->numero_final])
                    ->orWhereBetween('numero_final', [$request->numero_inicial, $request->numero_final])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('numero_inicial', '<=', $request->numero_inicial)
                            ->where('numero_final', '>=', $request->numero_final);
                    });
            })
            ->exists();
        
        if ($sobreposicao) {
            return back()
                ->withInput()
                ->withErrors([
                    'numero_inicial' => 'Há sobreposição com a numeração de outra distribuição.',
                    'numero_final' => 'Há sobreposição com a numeração de outra distribuição.'
                ]);
        }
        
        // Adicionar o usuário logado
        $validated['user_id'] = Auth::id();
        
        $distribuicao = Distribuicao::create($validated);
        
        return redirect()
            ->route('distribuicoes.index')
            ->with('success', 'Distribuição cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('distribuicao_visualizar');
        
        $distribuicao = Distribuicao::with(['instituicao', 'usuario', 'baixas' => function($query) {
            $query->orderBy('numero');
        }])->findOrFail($id);
        
        return view('distribuicoes.show', compact('distribuicao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('distribuicao_editar');
        
        $distribuicao = Distribuicao::findOrFail($id);
        $instituicoes = Instituicao::orderBy('nome')->get();
        
        return view('distribuicoes.edit', compact('distribuicao', 'instituicoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('distribuicao_editar');
        
        $distribuicao = Distribuicao::findOrFail($id);
        
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'tipo_certidao' => 'required|in:obito,nascidos_vivos',
            'numero_inicial' => 'required|integer|min:1',
            'numero_final' => 'required|integer|gte:numero_inicial',
            'data_entrega' => 'required|date',
            'observacao' => 'nullable|string',
        ]);
        
        // Verificar se há sobreposição de numeração (excluindo a própria distribuição)
        $sobreposicao = Distribuicao::where('tipo_certidao', $request->tipo_certidao)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('numero_inicial', [$request->numero_inicial, $request->numero_final])
                    ->orWhereBetween('numero_final', [$request->numero_inicial, $request->numero_final])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('numero_inicial', '<=', $request->numero_inicial)
                            ->where('numero_final', '>=', $request->numero_final);
                    });
            })
            ->exists();
        
        if ($sobreposicao) {
            return back()
                ->withInput()
                ->withErrors(['numero_inicial' => 'Há sobreposição com a numeração de outra distribuição.']);
        }
        
        // Verificar se há baixas fora da nova faixa
        $baixasForaFaixa = $distribuicao->baixas()
            ->where(function ($query) use ($request) {
                $query->where('numero', '<', $request->numero_inicial)
                    ->orWhere('numero', '>', $request->numero_final);
            })
            ->exists();
        
        if ($baixasForaFaixa) {
            return back()
                ->withInput()
                ->withErrors(['numero_inicial' => 'Existem baixas registradas fora da nova faixa de numeração.']);
        }
        
        $distribuicao->update($validated);
        
        return redirect()
            ->route('distribuicoes.index')
            ->with('success', 'Distribuição atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('distribuicao_excluir');
        
        $distribuicao = Distribuicao::findOrFail($id);
        
        // Verificar se há baixas relacionadas
        if ($distribuicao->baixas()->count() > 0) {
            return redirect()
                ->route('distribuicoes.index')
                ->with('error', 'Não é possível excluir esta distribuição pois possui baixas associadas.');
        }
        
        $distribuicao->delete();
        
        return redirect()
            ->route('distribuicoes.index')
            ->with('success', 'Distribuição excluída com sucesso.');
    }
    
    /**
     * Listar pendências (numerações sem baixa)
     */
    public function pendencias(Request $request)
    {
        $this->authorize('distribuicao_listar');
        
        // Validação dos filtros de data
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $request->validate([
                'data_inicio' => 'date|before_or_equal:data_fim',
                'data_fim' => 'date|after_or_equal:data_inicio',
            ], [
                'data_inicio.before_or_equal' => 'A data inicial deve ser igual ou anterior à data final.',
                'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.'
            ]);
        }
        
        // Obtém as distribuições com suas instituições e contando baixas
        $query = Distribuicao::with('instituicao')->withCount('baixas');
        
        // Filtro por instituição
        if ($request->filled('instituicao_id')) {
            $query->where('instituicao_id', $request->instituicao_id);
        }
        
        // Filtro por tipo de certidão
        if ($request->filled('tipo_certidao')) {
            $query->where('tipo_certidao', $request->tipo_certidao);
        }
        
        // Busca as distribuições
        $distribuicoes = $query->get();
        
        // Filtra apenas aquelas que têm pendências
        $distribuicoes = $distribuicoes->filter(function ($distribuicao) {
            $totalNumeracao = ($distribuicao->numero_final - $distribuicao->numero_inicial) + 1;
            return $distribuicao->baixas_count < $totalNumeracao;
        });
        
        $instituicoes = Instituicao::orderBy('nome')->get();
        
        return view('distribuicoes.pendencias', compact('distribuicoes', 'instituicoes'));
    }
}
