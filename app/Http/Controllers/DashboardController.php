<?php

namespace App\Http\Controllers;

use App\Models\Baixa;
use App\Models\Instituicao;
use App\Models\Distribuicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar o dashboard do sistema com indicadores.
     */
    public function index()
    {
        // Total de instituições
        $totalInstituicoes = Instituicao::count();
        
        // Total de distribuições por tipo
        $totalDistribuicoes = [
            'obito' => Distribuicao::where('tipo_certidao', 'obito')->count(),
            'nascidos_vivos' => Distribuicao::where('tipo_certidao', 'nascidos_vivos')->count(),
            'total' => Distribuicao::count(),
        ];
        
        // Total de baixas por situação
        $totalBaixas = [
            'utilizada' => Baixa::where('situacao', 'utilizada')->count(),
            'cancelada' => Baixa::where('situacao', 'cancelada')->count(),
            'nao_utilizada' => Baixa::where('situacao', 'nao_utilizada')->count(),
            'total' => Baixa::count(),
        ];
        
        // Total de certidões distribuídas
        $totalCertidoes = DB::table('distribuicoes')
            ->selectRaw('SUM(numero_final - numero_inicial + 1) as total')
            ->first()
            ->total ?? 0;
        
        // Total de pendências
        $totalPendencias = $totalCertidoes - $totalBaixas['total'];
        
        // Distribuições recentes
        $distribuicoesRecentes = Distribuicao::with('instituicao')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Baixas recentes
        $baixasRecentes = Baixa::with(['distribuicao', 'distribuicao.instituicao'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact(
            'totalInstituicoes',
            'totalDistribuicoes',
            'totalBaixas',
            'totalCertidoes',
            'totalPendencias',
            'distribuicoesRecentes',
            'baixasRecentes'
        ));
    }
}
