<?php

use App\Http\Controllers\BaixaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\DistribuicaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Instituições
    Route::resource('instituicoes', InstituicaoController::class);
    
    // Distribuições
    Route::resource('distribuicoes', DistribuicaoController::class);
    Route::get('/pendencias', [DistribuicaoController::class, 'pendencias'])->name('distribuicoes.pendencias');
    
    // Baixas
    Route::resource('baixas', BaixaController::class);
    Route::get('/baixas-lote/create', [BaixaController::class, 'createLote'])->name('baixas.create-lote');
    Route::post('/baixas-lote', [BaixaController::class, 'storeLote'])->name('baixas.store-lote');
    Route::get('/distribuicoes/{instituicao_id}/get', [BaixaController::class, 'getDistribuicoes'])
        ->name('baixas.get-distribuicoes');
    
    // Relatórios
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::post('/relatorios/vendas', [RelatorioController::class, 'vendas'])
        ->name('relatorio.vendas');
    Route::post('/relatorios/disponibilidade', [RelatorioController::class, 'disponibilidade'])
        ->name('relatorio.disponibilidade');
    Route::get('/relatorios/distribuicao', [RelatorioController::class, 'distribuicao'])
        ->name('relatorios.distribuicao');
    Route::get('/relatorios/utilizacao', [RelatorioController::class, 'utilizacao'])
        ->name('relatorios.utilizacao');
    Route::get('/relatorios/pendencias', [RelatorioController::class, 'pendencias'])
        ->name('relatorios.pendencias');
});

require __DIR__.'/auth.php';
