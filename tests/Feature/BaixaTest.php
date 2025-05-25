<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Baixa;
use App\Models\Distribuicao;
use App\Models\Instituicao;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaixaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar um usuário com permissões adequadas
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
          // Criar uma instituição e uma distribuição para usar nos testes
        $this->instituicao = Instituicao::factory()->create();
        $this->distribuicao = Distribuicao::factory()->create([
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => 5001,
            'numero_final' => 5020,
            'data_entrega' => now()->subDays(5), // Definir uma data recente para que a validação de prazo permita registrar baixas
        ]);
    }

    /**
     * Cenário 1 - Devolução bem-sucedida:
     * O sistema salva corretamente os formulários devolvidos.
     */    public function test_devolucao_bem_sucedida(): void
    {
        $this->actingAs($this->user);
        
        $dados = [
            'distribuicao_id' => $this->distribuicao->id,
            'numero' => 5005, // Um número dentro do range da distribuição
            'data_devolucao' => $this->distribuicao->data_entrega->format('Y-m-d'), // Usar a mesma data da entrega para evitar erro de prazo
            'situacao' => 'utilizada',
            'observacao' => 'Teste de devolução',
        ];
        
        $response = $this->post(route('baixas.store'), $dados);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('baixas', [
            'distribuicao_id' => $this->distribuicao->id,
            'numero' => 5005,
            'situacao' => 'utilizada',
        ]);
    }
    
    /**
     * Cenário 2 - Numeração inexistente:
     * O sistema alerta se a numeração informada não foi previamente distribuída.
     */    public function test_numeracao_inexistente(): void
    {
        $this->actingAs($this->user);
        
        $dados = [
            'distribuicao_id' => $this->distribuicao->id,
            'numero' => 6000, // Um número fora do range da distribuição
            'data_devolucao' => $this->distribuicao->data_entrega->format('Y-m-d'), // Usar a mesma data da entrega para evitar erro de prazo
            'situacao' => 'utilizada',
        ];
        
        $response = $this->post(route('baixas.store'), $dados);
        
        // Verificar que o sistema retornou um erro de validação
        $response->assertSessionHasErrors(['numero']);
        
        // Verificar que a baixa não foi registrada
        $this->assertDatabaseMissing('baixas', [
            'distribuicao_id' => $this->distribuicao->id,
            'numero' => 6000,
        ]);
    }
    
    /**
     * Cenário 3 - Erro ao tentar devolver uma numeração já registrada:
     * O sistema impede a duplicação de registros.
     */
    public function test_numeracao_ja_registrada(): void
    {
        $this->actingAs($this->user);
        
        // Primeiro criamos uma baixa
        Baixa::factory()->create([
            'distribuicao_id' => $this->distribuicao->id,
            'numero' => 5010,
            'data_devolucao' => now()->format('Y-m-d'),
            'situacao' => 'utilizada',
        ]);
          // Tentamos criar outra baixa com a mesma numeração
        $dados = [
            'distribuicao_id' => $this->distribuicao->id,
            'numero' => 5010, // Número já cadastrado
            // Usar uma data dentro do prazo, para garantir que o erro será pela numeração duplicada, não pela data
            'data_devolucao' => $this->distribuicao->data_entrega->format('Y-m-d'),
            'situacao' => 'cancelada', // Mesmo que seja com situação diferente
        ];
        
        $response = $this->post(route('baixas.store'), $dados);
        
        // Verificar que o sistema retornou um erro de validação
        $response->assertSessionHasErrors(['numero']);
        
        // Verificar que apenas a primeira baixa foi registrada
        $this->assertDatabaseCount('baixas', 1);
    }
}
