<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardListSeeder extends Seeder
{
    public function run(): void
    {
        $boards = Board::all();

        foreach ($boards as $board) {
            $board->lists()->createMany([
                [
                    'name' => 'To Do',
                    'order' => 1
                ],
                [
                    'name' => 'In Progress',
                    'order' => 2
                ],
                [
                    'name' => 'Done',
                    'order' => 3
                ],
                [
                    'name' => 'Blocked',
                    'order' => 4
                ]
            ]);
        }
    }
}
