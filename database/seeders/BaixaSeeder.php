<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Baixa;
use App\Models\Distribuicao;
use Illuminate\Database\Seeder;

class BaixaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        
        if (!$user) {
            $user = User::factory()->create();
        }
        
        // Obter todas as distribuições
        $distribuicoes = Distribuicao::all();
        
        // Situações possíveis para uma baixa
        $situacoes = ['utilizada', 'cancelada', 'nao_utilizada'];
        
        foreach ($distribuicoes as $distribuicao) {
            // Definir quantas baixas serão feitas (entre 30% e 70% do total)
            $totalNumeracoes = ($distribuicao->numero_final - $distribuicao->numero_inicial) + 1;
            $quantidadeBaixas = rand($totalNumeracoes * 0.3, $totalNumeracoes * 0.7);
            
            // Conjunto para rastrear números já utilizados
            $numerosUtilizados = [];
            
            // Criar baixas aleatórias
            for ($i = 0; $i < $quantidadeBaixas; $i++) {
                // Escolher um número dentro da faixa que ainda não foi usado
                do {
                    $numero = rand($distribuicao->numero_inicial, $distribuicao->numero_final);
                } while (in_array($numero, $numerosUtilizados));
                
                $numerosUtilizados[] = $numero;
                
                // Registrar a baixa
                Baixa::create([
                    'distribuicao_id' => $distribuicao->id,
                    'numero' => $numero,
                    'data_devolucao' => fake()->dateTimeBetween($distribuicao->data_entrega, now()),
                    'situacao' => $situacoes[array_rand($situacoes)],
                    'observacao' => fake()->optional(0.5)->sentence(),
                    'user_id' => $user->id,
                ]);
            }
        }
        
        // Adicionar algumas baixas aleatórias
        Baixa::factory()->count(20)->create();
    }
}
