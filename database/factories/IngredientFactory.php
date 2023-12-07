<?php

namespace Database\Factories;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currentStock = $this->faker->numberBetween(300, 3000);
        return [
            'name' => $this->faker->word(),
            'original_stock' => $currentStock,
            'current_stock' => $currentStock * $this->faker->randomFloat(1, 0.1, 1),
            'merchant_id' => Merchant::factory()->create(),
            'merchant_notified_at' => null, // $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}

