<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar as permissões do Spatie como Gates
        $permissions = [
            'instituicao_listar',
            'instituicao_visualizar',
            'instituicao_criar',
            'instituicao_editar',
            'instituicao_excluir',
            'distribuicao_listar',
            'distribuicao_visualizar',
            'distribuicao_criar',
            'distribuicao_editar',
            'distribuicao_excluir',
            'baixa_listar',
            'baixa_visualizar',
            'baixa_criar',
            'baixa_editar',
            'baixa_excluir',
            'relatorio_visualizar',
            'relatorio_gerar',
            'usuario_listar',
            'usuario_visualizar',
            'usuario_criar',
            'usuario_editar',
            'usuario_excluir',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermissionTo($permission);
            });
        }
    }
}
