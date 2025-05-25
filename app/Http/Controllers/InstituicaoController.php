<?php

namespace App\Http\Controllers;

use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InstituicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('instituicao_listar');
        
        $instituicoes = Instituicao::query();
        
        // Aplicar filtros se existirem
        if (request()->has('search')) {
            $search = request()->get('search');
            $instituicoes->where('nome', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }
        
        $instituicoes = $instituicoes->orderBy('nome')->paginate(10);
        
        return view('instituicoes.index', compact('instituicoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('instituicao_criar');
        
        return view('instituicoes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('instituicao_criar');
        
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        $instituicao = Instituicao::create($validated);
        
        return redirect()
            ->route('instituicoes.index')
            ->with('success', 'Instituição cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('instituicao_visualizar');
        
        $instituicao = Instituicao::with(['distribuicoes' => function ($query) {
            $query->orderBy('data_entrega', 'desc');
        }])->findOrFail($id);
        
        return view('instituicoes.show', compact('instituicao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('instituicao_editar');
        
        $instituicao = Instituicao::findOrFail($id);
        
        return view('instituicoes.edit', compact('instituicao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('instituicao_editar');
        
        $instituicao = Instituicao::findOrFail($id);
        
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        $instituicao->update($validated);
        
        return redirect()
            ->route('instituicoes.index')
            ->with('success', 'Instituição atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('instituicao_excluir');
        
        $instituicao = Instituicao::findOrFail($id);
        
        // Verificar se a instituição tem distribuições associadas
        if ($instituicao->distribuicoes()->count() > 0) {
            return redirect()
                ->route('instituicoes.index')
                ->with('error', 'Não é possível excluir esta instituição pois possui distribuições associadas.');
        }
        
        $instituicao->delete();
        
        return redirect()
            ->route('instituicoes.index')
            ->with('success', 'Instituição excluída com sucesso.');
    }
}
