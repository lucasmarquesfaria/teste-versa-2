<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Instituicao;
use App\Models\Distribuicao;
use Illuminate\Database\Seeder;

class DistribuicaoSeeder extends Seeder
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
        
        // Obter todas as instituições
        $instituicoes = Instituicao::all();
        
        // Para cada instituição, criar distribuições de DOs e DNVs
        foreach ($instituicoes as $instituicao) {
            // Distribuição de Declarações de Óbito (DO)
            $doInicial = 100000 + ($instituicao->id * 1000);
            
            Distribuicao::create([
                'instituicao_id' => $instituicao->id,
                'tipo_certidao' => 'obito',
                'numero_inicial' => $doInicial,
                'numero_final' => $doInicial + 49,
                'data_entrega' => now()->subDays(rand(30, 180)),
                'observacao' => 'Distribuição inicial de DOs',
                'user_id' => $user->id,
            ]);
            
            // Segunda distribuição de DOs para algumas instituições
            if ($instituicao->id % 2 == 0) {
                Distribuicao::create([
                    'instituicao_id' => $instituicao->id,
                    'tipo_certidao' => 'obito',
                    'numero_inicial' => $doInicial + 50,
                    'numero_final' => $doInicial + 99,
                    'data_entrega' => now()->subDays(rand(15, 29)),
                    'observacao' => 'Segunda distribuição de DOs',
                    'user_id' => $user->id,
                ]);
            }
            
            // Distribuição de Declarações de Nascidos Vivos (DNV)
            $dnvInicial = 500000 + ($instituicao->id * 1000);
            
            Distribuicao::create([
                'instituicao_id' => $instituicao->id,
                'tipo_certidao' => 'nascidos_vivos',
                'numero_inicial' => $dnvInicial,
                'numero_final' => $dnvInicial + 49,
                'data_entrega' => now()->subDays(rand(30, 180)),
                'observacao' => 'Distribuição inicial de DNVs',
                'user_id' => $user->id,
            ]);
            
            // Segunda distribuição de DNVs para algumas instituições
            if ($instituicao->id % 3 == 0) {
                Distribuicao::create([
                    'instituicao_id' => $instituicao->id,
                    'tipo_certidao' => 'nascidos_vivos',
                    'numero_inicial' => $dnvInicial + 50,
                    'numero_final' => $dnvInicial + 99,
                    'data_entrega' => now()->subDays(rand(15, 29)),
                    'observacao' => 'Segunda distribuição de DNVs',
                    'user_id' => $user->id,
                ]);
            }
        }
        
        // Adicionar algumas distribuições aleatórias
        Distribuicao::factory()->count(10)->create();
    }
}
