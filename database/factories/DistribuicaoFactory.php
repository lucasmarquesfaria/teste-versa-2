<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Instituicao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Distribuicao>
 */
class DistribuicaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numeroInicial = fake()->numberBetween(1000, 5000);
        $numeroFinal = $numeroInicial + fake()->numberBetween(10, 100);
        
        return [
            'instituicao_id' => Instituicao::factory(),
            'tipo_certidao' => fake()->randomElement(['obito', 'nascidos_vivos']),
            'numero_inicial' => $numeroInicial,
            'numero_final' => $numeroFinal,
            'data_entrega' => fake()->dateTimeBetween('-1 year', 'now'),
            'observacao' => fake()->optional(0.7)->text(100),
            'user_id' => User::factory(),
        ];
    }
}
