<?php

namespace Database\Seeders;

use App\Models\BoardList;
use App\Models\ListTask;
use Illuminate\Database\Seeder;

class TaskListSeeder extends Seeder
{
    public function run(): void
    {
        $boardLists = BoardList::all();

        foreach ($boardLists as $boardList) {
            //create tasks using ListTaskFactory, use for, add order field
            for ($i = 0; $i < 5; $i++) {
                ListTask::factory()->create([
                    'board_list_id' => $boardList->id,
                    'order' => $i + 1
                ]);
            }
        }
    }
}
