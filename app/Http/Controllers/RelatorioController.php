<?php

namespace App\Http\Controllers;

use App\Models\Baixa;
use App\Models\Distribuicao;
use App\Models\Instituicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    /**
     * Exibe a página com opções de relatórios
     */
    public function index()
    {
        $this->authorize('relatorio_visualizar');
        
        $instituicoes = Instituicao::orderBy('nome')->get();
        
        return view('relatorios.index', compact('instituicoes'));
    }
    
    /**
     * Gerar relatório de distribuição
     */
    public function distribuicao(Request $request)
    {
        $this->authorize('relatorio_gerar');
        
        $query = Distribuicao::with('instituicao');
        
        // Aplicar filtros
        if ($request->filled('instituicao_id')) {
            $query->where('instituicao_id', $request->instituicao_id);
        }
        
        if ($request->filled('tipo_certidao')) {
            $query->where('tipo_certidao', $request->tipo_certidao);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_entrega', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_entrega', '<=', $request->data_fim);
        }
        
        $distribuicoes = $query->orderBy('data_entrega', 'desc')->get();
        
        // Agrupar por instituição para o relatório
        $distribuicoesAgrupadas = $distribuicoes->groupBy('instituicao.nome');
        
        // Somar os totais
        $totalDistribuicoes = $distribuicoes->count();
        $totalFormularios = $distribuicoes->sum(function($d) {
            return ($d->numero_final - $d->numero_inicial) + 1;
        });
        
        // Preparar os dados para o PDF
        $data = [
            'titulo' => 'Relatório de Distribuição de Formulários',
            'dataGeracao' => now(),
            'filtros' => $this->formatarFiltrosRelatorio($request),
            'distribuicoesAgrupadas' => $distribuicoesAgrupadas,
            'totalDistribuicoes' => $totalDistribuicoes,
            'totalFormularios' => $totalFormularios,
        ];
        
        // Verificar se é para exibir ou baixar o PDF
        if ($request->tipo_saida === 'visualizar') {
            $pdf = PDF::loadView('relatorios.pdf.distribuicao', $data);
            return $pdf->stream('relatorio_distribuicao.pdf');
        } else {
            $pdf = PDF::loadView('relatorios.pdf.distribuicao', $data);
            return $pdf->download('relatorio_distribuicao.pdf');
        }
    }
    
    /**
     * Gerar relatório de utilização
     */
    public function utilizacao(Request $request)
    {
        $this->authorize('relatorio_gerar');
        
        $query = Baixa::with(['distribuicao', 'distribuicao.instituicao']);
        
        // Aplicar filtros
        if ($request->filled('instituicao_id')) {
            $query->whereHas('distribuicao', function($q) use ($request) {
                $q->where('instituicao_id', $request->instituicao_id);
            });
        }
        
        if ($request->filled('tipo_certidao')) {
            $query->whereHas('distribuicao', function($q) use ($request) {
                $q->where('tipo_certidao', $request->tipo_certidao);
            });
        }
        
        if ($request->filled('situacao')) {
            $query->where('situacao', $request->situacao);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_devolucao', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_devolucao', '<=', $request->data_fim);
        }
        
        $baixas = $query->orderBy('data_devolucao', 'desc')->get();
        
        // Agrupar por instituição e situação para o relatório
        $baixasAgrupadas = $baixas->groupBy('distribuicao.instituicao.nome')
            ->map(function ($grupo) {
                return $grupo->groupBy('situacao');
            });
        
        // Preparar os totais
        $totais = [
            'utilizada' => $baixas->where('situacao', 'utilizada')->count(),
            'cancelada' => $baixas->where('situacao', 'cancelada')->count(),
            'nao_utilizada' => $baixas->where('situacao', 'nao_utilizada')->count(),
            'total' => $baixas->count(),
        ];
        
        // Preparar os dados para o PDF
        $data = [
            'titulo' => 'Relatório de Utilização de Formulários',
            'dataGeracao' => now(),
            'filtros' => $this->formatarFiltrosRelatorio($request),
            'baixasAgrupadas' => $baixasAgrupadas,
            'totais' => $totais,
        ];
        
        // Verificar se é para exibir ou baixar o PDF
        if ($request->tipo_saida === 'visualizar') {
            $pdf = PDF::loadView('relatorios.pdf.utilizacao', $data);
            return $pdf->stream('relatorio_utilizacao.pdf');
        } else {
            $pdf = PDF::loadView('relatorios.pdf.utilizacao', $data);
            return $pdf->download('relatorio_utilizacao.pdf');
        }
    }
    
    /**
     * Gerar relatório de pendências
     */
    public function pendencias(Request $request)
    {
        $this->authorize('relatorio_gerar');
        
        $query = Distribuicao::with('instituicao')
            ->withCount('baixas');
        
        // Aplicar filtros
        if ($request->filled('instituicao_id')) {
            $query->where('instituicao_id', $request->instituicao_id);
        }
        
        if ($request->filled('tipo_certidao')) {
            $query->where('tipo_certidao', $request->tipo_certidao);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_entrega', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_entrega', '<=', $request->data_fim);
        }
        
        $distribuicoes = $query->orderBy('data_entrega', 'desc')->get();
        
        // Filtrar apenas as distribuições com pendências
        $distribuicoesComPendencias = $distribuicoes->filter(function($d) {
            $totalFaixa = ($d->numero_final - $d->numero_inicial) + 1;
            return $d->baixas_count < $totalFaixa;
        });
        
        // Adicionar os atributos de quantidade
        $distribuicoesComPendencias = $distribuicoesComPendencias->map(function($d) {
            $d->total_certidoes = ($d->numero_final - $d->numero_inicial) + 1;
            $d->pendencias = $d->total_certidoes - $d->baixas_count;
            return $d;
        });
        
        // Agrupar por instituição para o relatório
        $pendenciasAgrupadas = $distribuicoesComPendencias->groupBy('instituicao.nome');
        
        // Calcular totais
        $totalDistribuicoes = $distribuicoesComPendencias->count();
        $totalPendencias = $distribuicoesComPendencias->sum('pendencias');
        
        // Preparar os dados para o PDF
        $data = [
            'titulo' => 'Relatório de Pendências',
            'dataGeracao' => now(),
            'filtros' => $this->formatarFiltrosRelatorio($request),
            'pendenciasAgrupadas' => $pendenciasAgrupadas,
            'totalDistribuicoes' => $totalDistribuicoes,
            'totalPendencias' => $totalPendencias,
        ];
        
        // Verificar se é para exibir ou baixar o PDF
        if ($request->tipo_saida === 'visualizar') {
            $pdf = PDF::loadView('relatorios.pdf.pendencias', $data);
            return $pdf->stream('relatorio_pendencias.pdf');
        } else {
            $pdf = PDF::loadView('relatorios.pdf.pendencias', $data);
            return $pdf->download('relatorio_pendencias.pdf');
        }
    }
    
    /**
     * Gerar relatório de vendas
     */
    public function vendas(Request $request)
    {
        $this->authorize('relatorio_gerar');
        
        $query = Baixa::with(['distribuicao.instituicao', 'usuario'])
            ->where('situacao', 'utilizada');
            
        // Aplicar filtros
        if ($request->filled('instituicao_id')) {
            $query->whereHas('distribuicao', function($q) use ($request) {
                $q->where('instituicao_id', $request->instituicao_id);
            });
        }
            
        if ($request->filled('data_inicial') && $request->filled('data_final')) {
            $query->whereBetween('data_devolucao', [$request->data_inicial, $request->data_final]);
        }
            
        $baixas = $query->orderBy('data_devolucao')->get();
        
        // Agrupar por instituição para o relatório
        $baixasAgrupadas = $baixas->groupBy('distribuicao.instituicao.nome');
        
        $pdf = Pdf::loadView('relatorios.pdf.vendas', [
            'baixas' => $baixasAgrupadas,
            'dataInicial' => $request->data_inicial,
            'dataFinal' => $request->data_final
        ]);
        
        return $pdf->stream('relatorio-vendas.pdf');
    }
    
    /**
     * Gerar relatório de disponibilidade
     */
    public function disponibilidade()
    {
        $this->authorize('relatorio_gerar');
        
        $distribuicoes = Distribuicao::with(['instituicao', 'baixas'])
            ->orderBy('data_entrega', 'desc')
            ->get();
            
        // Calcular disponibilidade para cada distribuição
        foreach ($distribuicoes as $distribuicao) {
            $distribuicao->total_certidoes = ($distribuicao->numero_final - $distribuicao->numero_inicial) + 1;
            $distribuicao->utilizadas = $distribuicao->baixas->where('situacao', 'utilizada')->count();
            $distribuicao->disponiveis = $distribuicao->total_certidoes - $distribuicao->baixas->count();
        }
            
        $pdf = Pdf::loadView('relatorios.pdf.disponibilidade', [
            'distribuicoes' => $distribuicoes
        ]);
        
        return $pdf->stream('relatorio-disponibilidade.pdf');
    }
    
    /**
     * Formatar array de filtros para exibição no relatório
     */
    private function formatarFiltrosRelatorio($request): array
    {
        $filtros = [];
        
        if ($request->filled('instituicao_id')) {
            $instituicao = Instituicao::find($request->instituicao_id);
            if ($instituicao) {
                $filtros['Instituição'] = $instituicao->nome;
            }
        }
        
        if ($request->filled('tipo_certidao')) {
            $tipos = [
                'obito' => 'Declaração de Óbito (DO)',
                'nascidos_vivos' => 'Declaração de Nascidos Vivos (DNV)',
            ];
            $filtros['Tipo de Certidão'] = $tipos[$request->tipo_certidao] ?? $request->tipo_certidao;
        }
        
        if ($request->filled('situacao')) {
            $situacoes = [
                'utilizada' => 'Utilizada',
                'cancelada' => 'Cancelada',
                'nao_utilizada' => 'Não Utilizada',
            ];
            $filtros['Situação'] = $situacoes[$request->situacao] ?? $request->situacao;
        }
        
        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $filtros['Período'] = 'De ' . date('d/m/Y', strtotime($request->data_inicio)) . 
                                   ' a ' . date('d/m/Y', strtotime($request->data_fim));
        } elseif ($request->filled('data_inicio')) {
            $filtros['Data Inicial'] = date('d/m/Y', strtotime($request->data_inicio));
        } elseif ($request->filled('data_fim')) {
            $filtros['Data Final'] = date('d/m/Y', strtotime($request->data_fim));
        }
        
        return $filtros;
    }
}
