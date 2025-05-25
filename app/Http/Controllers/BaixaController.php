<?php

namespace App\Http\Controllers;

use App\Models\Baixa;
use App\Models\Distribuicao;
use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BaixaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('baixa_listar');
        
        $baixas = Baixa::with(['distribuicao', 'distribuicao.instituicao', 'usuario']);
        
        // Filtro por instituição (relacionamento aninhado)
        if (request()->has('instituicao_id') && !empty(request('instituicao_id'))) {
            $baixas->whereHas('distribuicao', function ($query) {
                $query->where('instituicao_id', request('instituicao_id'));
            });
        }
        
        // Filtro por tipo de certidão (através da distribuição)
        if (request()->has('tipo_certidao') && !empty(request('tipo_certidao'))) {
            $baixas->whereHas('distribuicao', function ($query) {
                $query->where('tipo_certidao', request('tipo_certidao'));
            });
        }
        
        // Filtro por situação
        if (request()->has('situacao') && !empty(request('situacao'))) {
            $baixas->where('situacao', request('situacao'));
        }
        
        // Filtro por data
        if (request()->has('data_inicio') && !empty(request('data_inicio'))) {
            $baixas->whereDate('data_devolucao', '>=', request('data_inicio'));
        }
        
        if (request()->has('data_fim') && !empty(request('data_fim'))) {
            $baixas->whereDate('data_devolucao', '<=', request('data_fim'));
        }
        
        $instituicoes = Instituicao::orderBy('nome')->get();
        $baixas = $baixas->orderBy('created_at', 'desc')->paginate(15);
        
        return view('baixas.index', compact('baixas', 'instituicoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('baixa_criar');
        
        $instituicoes = Instituicao::orderBy('nome')->get();
        $distribuicoes = collect(); // Coleção vazia inicial
        
        return view('baixas.create', compact('instituicoes', 'distribuicoes'));
    }
    
    /**
     * Obter distribuições para uma instituição (para select dinâmico via AJAX)
     */
    public function getDistribuicoes($instituicao_id)
    {
        $this->authorize('baixa_criar');
        
        $distribuicoes = Distribuicao::where('instituicao_id', $instituicao_id)
            ->orderBy('data_entrega', 'desc')
            ->get()
            ->map(function ($distribuicao) {
                $tipo = $distribuicao->tipo_certidao === 'obito' ? 'DO' : 'DNV';
                return [
                    'id' => $distribuicao->id,
                    'text' => "{$tipo} - {$distribuicao->numero_inicial} a {$distribuicao->numero_final} ({$distribuicao->data_entrega})"
                ];
            });
            
        return response()->json($distribuicoes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('baixa_criar');
        
        $validated = $request->validate([
            'distribuicao_id' => 'required|exists:distribuicoes,id',
            'numero' => 'required|integer|min:1',
            'data_devolucao' => 'required|date',
            'situacao' => 'required|in:utilizada,cancelada,nao_utilizada',
            'observacao' => 'nullable|string',
        ]);
        
        // Buscar a distribuição
        $distribuicao = Distribuicao::findOrFail($request->distribuicao_id);
        
        // Verificar se o número está dentro da faixa da distribuição
        if ($request->numero < $distribuicao->numero_inicial || $request->numero > $distribuicao->numero_final) {
            return back()
                ->withInput()
                ->withErrors(['numero' => 'O número informado está fora da faixa da distribuição selecionada.']);
        }
        
        // Verificar se o número já foi devolvido anteriormente
        $baixaExistente = Baixa::where('distribuicao_id', $request->distribuicao_id)
            ->where('numero', $request->numero)
            ->exists();
            
        if ($baixaExistente) {
            return back()
                ->withInput()
                ->withErrors(['numero' => 'Este número já possui baixa registrada.']);
        }
        
        // Adicionar o usuário logado
        $validated['user_id'] = Auth::id();
        
        $baixa = Baixa::create($validated);
        
        return redirect()
            ->route('baixas.index')
            ->with('success', 'Baixa registrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('baixa_visualizar');
        
        $baixa = Baixa::with(['distribuicao', 'distribuicao.instituicao', 'usuario'])
            ->findOrFail($id);
        
        return view('baixas.show', compact('baixa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('baixa_editar');
        
        $baixa = Baixa::with('distribuicao')->findOrFail($id);
        
        $distribuicao = $baixa->distribuicao;
        $instituicao = $distribuicao->instituicao;
        
        return view('baixas.edit', compact('baixa', 'distribuicao', 'instituicao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('baixa_editar');
        
        $baixa = Baixa::findOrFail($id);
        
        $validated = $request->validate([
            'data_devolucao' => 'required|date',
            'situacao' => 'required|in:utilizada,cancelada,nao_utilizada',
            'observacao' => 'nullable|string',
        ]);
        
        $baixa->update($validated);
        
        return redirect()
            ->route('baixas.index')
            ->with('success', 'Baixa atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('baixa_excluir');
        
        $baixa = Baixa::findOrFail($id);
        $baixa->delete();
        
        return redirect()
            ->route('baixas.index')
            ->with('success', 'Baixa excluída com sucesso.');
    }
    
    /**
     * Registrar baixa em lote para múltiplas certidões
     */
    public function createLote()
    {
        $this->authorize('baixa_criar');
        
        $instituicoes = Instituicao::orderBy('nome')->get();
        
        return view('baixas.create-lote', compact('instituicoes'));
    }
    
    /**
     * Salvar baixas em lote
     */
    public function storeLote(Request $request)
    {
        $this->authorize('baixa_criar');
        
        $validated = $request->validate([
            'distribuicao_id' => 'required|exists:distribuicoes,id',
            'numeros' => 'required|string',
            'data_devolucao' => 'required|date',
            'situacao' => 'required|in:utilizada,cancelada,nao_utilizada',
            'observacao' => 'nullable|string',
        ]);
        
        // Buscar a distribuição
        $distribuicao = Distribuicao::findOrFail($request->distribuicao_id);
        
        // Processar a string de números
        $numeros = $this->processarNumerosLote($request->numeros);
        
        // Verificar se todos os números estão dentro da faixa
        $numerosForaFaixa = [];
        foreach ($numeros as $numero) {
            if ($numero < $distribuicao->numero_inicial || $numero > $distribuicao->numero_final) {
                $numerosForaFaixa[] = $numero;
            }
        }
        
        if (!empty($numerosForaFaixa)) {
            return back()
                ->withInput()
                ->withErrors(['numeros' => 'Os seguintes números estão fora da faixa: ' . implode(', ', $numerosForaFaixa)]);
        }
        
        // Verificar se algum número já foi devolvido
        $numerosJaDevolvidos = Baixa::where('distribuicao_id', $request->distribuicao_id)
            ->whereIn('numero', $numeros)
            ->pluck('numero')
            ->toArray();
            
        if (!empty($numerosJaDevolvidos)) {
            return back()
                ->withInput()
                ->withErrors(['numeros' => 'Os seguintes números já possuem baixa: ' . implode(', ', $numerosJaDevolvidos)]);
        }
        
        // Registrar as baixas
        DB::beginTransaction();
        try {
            foreach ($numeros as $numero) {
                Baixa::create([
                    'distribuicao_id' => $distribuicao->id,
                    'numero' => $numero,
                    'data_devolucao' => $request->data_devolucao,
                    'situacao' => $request->situacao,
                    'observacao' => $request->observacao,
                    'user_id' => Auth::id(),
                ]);
            }
            DB::commit();
            
            return redirect()
                ->route('baixas.index')
                ->with('success', 'Foram registradas ' . count($numeros) . ' baixas com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['numeros' => 'Erro ao registrar as baixas: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Processa a string de números inserida pelo usuário
     * Aceita formatos como: 1,2,3 ou 1-5 ou 1,2,5-10
     */
    private function processarNumerosLote($numerosString): array
    {
        $result = [];
        $partes = explode(',', $numerosString);
        
        foreach ($partes as $parte) {
            $parte = trim($parte);
            if (empty($parte)) continue;
            
            if (strpos($parte, '-') !== false) {
                // É um intervalo
                list($inicio, $fim) = explode('-', $parte);
                $inicio = (int) trim($inicio);
                $fim = (int) trim($fim);
                
                if ($inicio <= $fim) {
                    for ($i = $inicio; $i <= $fim; $i++) {
                        $result[] = $i;
                    }
                }
            } else {
                // É um número individual
                $result[] = (int) $parte;
            }
        }
        
        // Remover duplicatas e ordenar
        $result = array_unique($result);
        sort($result);
        
        return $result;
    }
}
