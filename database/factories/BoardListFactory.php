<?php

namespace Database\Factories;

use App\Models\BoardList;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardListFactory extends Factory
{
    protected $model = BoardList::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'order' => null,
            'board_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
