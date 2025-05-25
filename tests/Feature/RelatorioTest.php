<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Baixa;
use App\Models\Distribuicao;
use App\Models\Instituicao;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelatorioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar um usuário com permissões adequadas
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        $this->user->givePermissionTo('relatorio_visualizar');
        $this->user->givePermissionTo('relatorio_gerar');
        
        // Criar dados de teste
        $this->instituicao = Instituicao::factory()->create();
    }

    /**
     * Cenário 1 - Relatório gerado corretamente:
     * O sistema exibe informações completas e detalhadas.
     */
    public function test_relatorio_gerado_corretamente(): void
    {
        $this->actingAs($this->user);
        
        // Criar dados para o relatório
        $distribuicao = Distribuicao::factory()->create([
            'instituicao_id' => $this->instituicao->id,
            'tipo_certidao' => 'obito',
            'numero_inicial' => 7001,
            'numero_final' => 7010,
            'data_entrega' => '2025-01-15',
        ]);
        
        // Criar algumas baixas
        for ($i = 7001; $i <= 7005; $i++) {
            Baixa::factory()->create([
                'distribuicao_id' => $distribuicao->id,
                'numero' => $i,
                'data_devolucao' => '2025-02-01',
                'situacao' => 'utilizada',
            ]);
        }
        
        // Solicitar o relatório de distribuição
        $response = $this->get(route('relatorios.distribuicao', [
            'data_inicio' => '2025-01-01',
            'data_fim' => '2025-01-31',
            'tipo_saida' => 'visualizar',
        ]));
        
        // Verificar que o relatório foi gerado com sucesso
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }
    
    /**
     * Cenário 2 - Dados insuficientes:
     * O sistema alerta se não houver registros suficientes para gerar um relatório.
     */
    public function test_dados_insuficientes(): void
    {
        $this->actingAs($this->user);
        
        // Tentar gerar um relatório com um período sem dados
        $response = $this->get(route('relatorios.utilizacao', [
            'data_inicio' => '2023-01-01',
            'data_fim' => '2023-01-31', // Período sem dados
            'tipo_saida' => 'visualizar',
        ]));
        
        // Verificar que o sistema alerta sobre a falta de dados
        $response->assertRedirect();
        $response->assertSessionHas('info', 'Não foram encontrados dados para gerar o relatório no período selecionado.');
    }
    
    /**
     * Cenário 3 - Exportação falha:
     * O sistema deve impedir exportações corrompidas ou inválidas.
     */
    public function test_exportacao_falha(): void
    {
        $this->actingAs($this->user);
        
        // Testar formato de saída inválido
        $response = $this->get(route('relatorios.pendencias', [
            'data_inicio' => '2025-01-01',
            'data_fim' => '2025-01-31',
            'tipo_saida' => 'formato_invalido', // Formato inválido
        ]));
        
        // Verificar que o sistema retorna um erro de validação
        $response->assertSessionHasErrors(['tipo_saida']);
    }
}
