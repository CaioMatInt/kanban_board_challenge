<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    protected $model = Board::class;

    public function definition(): array
    {
        $maxOrder = Board::max('order');
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;

        return [
            'name' => $this->faker->name,
            'color_hash' => $this->faker->hexColor,
            'order' => $newOrder,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
