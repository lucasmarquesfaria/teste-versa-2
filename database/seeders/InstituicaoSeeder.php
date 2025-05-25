<?php

namespace Database\Seeders;

use App\Models\Instituicao;
use Illuminate\Database\Seeder;

class InstituicaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar algumas instituições padrão para testes
        $instituicoes = [
            [
                'nome' => 'Hospital Municipal',
                'endereco' => 'Av. Principal, 1000',
                'telefone' => '(11) 3333-4444',
                'email' => 'hospital@municipal.gov.br',
            ],
            [
                'nome' => 'UPA Central',
                'endereco' => 'Rua das Flores, 500',
                'telefone' => '(11) 3333-5555',
                'email' => 'upa@saude.gov.br',
            ],
            [
                'nome' => 'Maternidade Santa Maria',
                'endereco' => 'Av. das Acácias, 300',
                'telefone' => '(11) 3333-6666',
                'email' => 'contato@maternidade.org',
            ],
            [
                'nome' => 'Funerária São Pedro',
                'endereco' => 'Rua dos Crisântemos, 123',
                'telefone' => '(11) 3333-7777',
                'email' => 'contato@funeraria.com',
            ],
            [
                'nome' => 'Posto de Saúde Central',
                'endereco' => 'Praça da República, 45',
                'telefone' => '(11) 3333-8888',
                'email' => 'posto@saude.gov.br',
            ],
        ];

        foreach ($instituicoes as $instituicao) {
            Instituicao::create($instituicao);
        }

        // Criar mais 10 instituições aleatórias
        Instituicao::factory()->count(10)->create();
    }
}
