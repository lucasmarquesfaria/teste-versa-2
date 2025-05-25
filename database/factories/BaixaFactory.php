<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Distribuicao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Baixa>
 */
class BaixaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $distribuicao = Distribuicao::factory()->create();
        $numero = fake()->numberBetween($distribuicao->numero_inicial, $distribuicao->numero_final);
        
        return [
            'distribuicao_id' => $distribuicao->id,
            'numero' => $numero,
            'data_devolucao' => fake()->dateTimeBetween($distribuicao->data_entrega, '+3 months'),
            'situacao' => fake()->randomElement(['utilizada', 'cancelada', 'nao_utilizada']),
            'observacao' => fake()->optional(0.5)->text(100),
            'user_id' => User::factory(),
        ];
    }
}
