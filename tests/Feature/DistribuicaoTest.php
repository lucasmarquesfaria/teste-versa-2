<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Distribuicao;
use App\Models\Instituicao;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DistribuicaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar um usuário com permissões adequadas
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        
        // Criar uma instituição para usar nos testes
        $this->instituicao = Instituicao::factory()->create();
    }

    /** 
     * Cenário 1 - Distribuição com sucesso: 
     * O usuário registra corretamente a entrega e verifica que os dados foram salvos.
     */
    public function test_distribuicao_com_sucesso(): void
    {
        $this->actingAs($this->user);
        
        $dados = [
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => 1001,
            'numero_final' => 1020,
            'data_entrega' => now()->format('Y-m-d'),
            'observacao' => 'Teste de distribuição',
        ];
        
        $response = $this->post(route('distribuicoes.store'), $dados);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('distribuicoes', [
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => 1001,
            'numero_final' => 1020,
        ]);
    }
    
    /**
     * Cenário 2 - Numeração duplicada: 
     * O sistema impede a distribuição de uma numeração já utilizada.
     */
    public function test_numeracao_duplicada(): void
    {
        $this->actingAs($this->user);
        
        // Primeiro criamos uma distribuição
        Distribuicao::factory()->create([
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => 2001,
            'numero_final' => 2020,
        ]);
        
        // Tentamos criar uma distribuição com numeração já existente
        $dados = [
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => 2010, // Está dentro do range da distribuição anterior
            'numero_final' => 2030,
            'data_entrega' => now()->format('Y-m-d'),
            'observacao' => 'Teste de numeração duplicada',
        ];
        
        $response = $this->post(route('distribuicoes.store'), $dados);
        
        // Verificar que o sistema retornou um erro de validação
        $response->assertSessionHasErrors(['numero_inicial', 'numero_final']);
        
        // Verificar que a distribuição duplicada não foi salva
        $this->assertDatabaseMissing('distribuicoes', [
            'numero_inicial' => 2010,
            'numero_final' => 2030,
        ]);
    }
    
    /**
     * Cenário 3 - Campos obrigatórios em branco: 
     * O sistema exibe um erro se o fiscal esquecer de preencher algum campo.
     */
    public function test_campos_obrigatorios(): void
    {
        $this->actingAs($this->user);
        
        // Enviar dados incompletos (sem numeração e data)
        $dados = [
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => '', // Campo obrigatório vazio
            'numero_final' => '', // Campo obrigatório vazio
            'data_entrega' => '', // Campo obrigatório vazio
        ];
        
        $response = $this->post(route('distribuicoes.store'), $dados);
        
        // Verificar que o sistema retornou erros de validação
        $response->assertSessionHasErrors(['numero_inicial', 'numero_final', 'data_entrega']);
    }
}
