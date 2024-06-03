<?php

namespace Database\Factories;

use App\Models\ListTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListTaskFactory extends Factory
{
    protected $model = ListTask::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'order' => null,
            'board_list_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
