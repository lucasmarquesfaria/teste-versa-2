<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetar permissões em cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            // Instituições
            'instituicao_listar',
            'instituicao_visualizar',
            'instituicao_criar',
            'instituicao_editar',
            'instituicao_excluir',
            
            // Distribuições
            'distribuicao_listar',
            'distribuicao_visualizar',
            'distribuicao_criar',
            'distribuicao_editar',
            'distribuicao_excluir',
            
            // Baixas
            'baixa_listar',
            'baixa_visualizar',
            'baixa_criar',
            'baixa_editar',
            'baixa_excluir',
            
            // Relatórios
            'relatorio_visualizar',
            'relatorio_gerar',
            
            // Usuários
            'usuario_listar',
            'usuario_visualizar',
            'usuario_criar',
            'usuario_editar',
            'usuario_excluir',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar papéis e atribuir permissões
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::all());
        
        $role = Role::create(['name' => 'fiscal', 'guard_name' => 'web']);
        $role->givePermissionTo([
            'instituicao_listar',
            'instituicao_visualizar',
            'distribuicao_listar',
            'distribuicao_visualizar',
            'distribuicao_criar',
            'distribuicao_editar',
            'baixa_listar',
            'baixa_visualizar',
            'baixa_criar',
            'baixa_editar',
            'relatorio_visualizar',
            'relatorio_gerar',
        ]);
        
        // Criar usuário admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $admin->assignRole('admin');
        
        // Criar usuário fiscal
        $fiscal = User::create([
            'name' => 'Fiscal',
            'email' => 'fiscal@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $fiscal->assignRole('fiscal');
    }
}
