<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar papéis e permissões necessários para os testes
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Executar o seeder de papéis e permissões
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }
}
