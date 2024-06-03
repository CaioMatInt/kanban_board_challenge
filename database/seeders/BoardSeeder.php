<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    public function run(): void
    {
        $boardsData = [
            [
                'name' => 'Weekend Tasks Board',
                'color_hash' => '#309ee3',
                'order' => 1
            ],
            [
                'name' => 'Work Tasks',
                'color_hash' => '#86b594',
                'order' => 2
            ],
            [
                'name' => 'Parallel Tasks Board',
                'color_hash' => '#8b7194',
                'order' => 3
            ],
            [
                'name' => 'Personal Tasks',
                'color_hash' => '#f7c274',
                'order' => 4
            ],
            [
                'name' => 'Shopping List',
                'color_hash' => '#43b59c',
                'order' => 5
            ],
            [
                'name' => 'Books to Read',
                'color_hash' => '#916682',
                'order' => 6
            ],
            [
                'name' => 'Movies to Watch',
                'color_hash' => '#668291',
                'order' => 7
            ],
            [
                'name' => 'Grocery List',
                'color_hash' => '#4b4a7d',
                'order' => 8
            ],
            [
                'name' => 'Home Tasks',
                'color_hash' => '#27477adb',
                'order' => 9
            ],
            [
                'name' => 'Gym Routine',
                'color_hash' => '#784f31',
                'order' => 10
            ]
        ];

        foreach ($boardsData as $boardData) {
            Board::factory()->create($boardData);
        }
    }
}
