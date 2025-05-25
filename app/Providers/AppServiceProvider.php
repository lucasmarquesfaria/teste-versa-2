<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Formatar data para exibição
        Blade::directive('data', function ($expression) {
            return "<?php echo ($expression) ? date('d/m/Y', strtotime($expression)) : ''; ?>";
        });
        
        // Formatar tipo de certidão
        Blade::directive('tipoCertidao', function ($expression) {
            return "<?php 
                if ($expression == 'obito') {
                    echo 'Declaração de Óbito (DO)';
                } elseif ($expression == 'nascidos_vivos') {
                    echo 'Declaração de Nascidos Vivos (DNV)';
                } else {
                    echo $expression;
                }
            ?>";
        });
        
        // Formatar situação de baixa
        Blade::directive('situacao', function ($expression) {
            return "<?php 
                if ($expression == 'utilizada') {
                    echo 'Utilizada';
                } elseif ($expression == 'cancelada') {
                    echo 'Cancelada';
                } elseif ($expression == 'nao_utilizada') {
                    echo 'Não Utilizada';
                } else {
                    echo $expression;
                }
            ?>";
        });
    }
}
