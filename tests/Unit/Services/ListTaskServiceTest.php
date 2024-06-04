<?php

use App\Models\Board;
use App\Models\BoardList;
use App\Models\ListTask;
use App\Services\BoardService;
use App\Services\ListTaskService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('ListTaskServiceTest', function () {

    beforeEach(function () {
        $this->listTaskService = app(ListTaskService::class);
    });

    test('can find a list task', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $listTaskFactory = ListTask::factory([
            'board_list_id' => $boardList->id,
            'order' => 1,
        ])->create();

        $listTask = $this->listTaskService->find($listTaskFactory->id);

        expect($listTask->id)->toBe($listTaskFactory->id)
            ->and($listTask->name)->toBe($listTaskFactory->name);
    });

    test('should be able to get ordered list tasks by board list id', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $listTasksFromFactory = [];

        for ($i = 1; $i <= 3; $i++) {
            $listTasksFromFactory[] = ListTask::factory([
                'board_list_id' => $boardList->id,
                'order' => $i,
            ])->create();
        }

        $listTasks = $this->listTaskService->getOrderedByBoardListId($boardList->id);

        expect($listTasks->count())->toBe(3)
            ->and($listTasks->first()->id)->toBe($listTasksFromFactory[0]->id)
            ->and($listTasks->first()->name)->toBe($listTasksFromFactory[0]->name)
            ->and($listTasks[1]->id)->toBe($listTasksFromFactory[1]->id)
            ->and($listTasks[1]->name)->toBe($listTasksFromFactory[1]->name)
            ->and($listTasks->last()->id)->toBe($listTasksFromFactory[2]->id)
            ->and($listTasks->last()->name)->toBe($listTasksFromFactory[2]->name);
    });

    test('should be able to delete by board id', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $listTasksFromFactory = [];

        for ($i = 1; $i <= 3; $i++) {
            $listTasksFromFactory[] = ListTask::factory([
                'board_list_id' => $boardList->id,
                'order' => $i,
            ])->create();
        }

        $this->listTaskService->deleteByBoardListId($boardList->id);

        $listTasks = $this->listTaskService->getOrderedByBoardListId($boardList->id);

        expect($listTasks->count())->toBe(0);

        foreach ($listTasksFromFactory as $listTask) {
            $this->assertDatabaseMissing('list_tasks', ['id' => $listTask->id]);
        }
    });

    test('should be able to create a list task', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $this->listTaskService->create('Task 1', 'Description 1', $boardList->id);

        $listTask = ListTask::where('name', 'Task 1')->first();

        expect($listTask->name)->toBe('Task 1')
            ->and($listTask->description)->toBe('Description 1')
            ->and($listTask->board_list_id)->toBe($boardList->id);
    });

    test('should attribute order 1 to the first task created', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $this->listTaskService->create('Task 1', 'Description 1', $boardList->id);

        $listTask = ListTask::where('name', 'Task 1')->first();

        expect($listTask->order)->toBe(1);
    });

    test('should attribute the correct order to the tasks created', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $this->listTaskService->create('Task 1', 'Description 1', $boardList->id);
        $this->listTaskService->create('Task 2', 'Description 2', $boardList->id);
        $this->listTaskService->create('Task 3', 'Description 3', $boardList->id);

        $listTasks = ListTask::where('board_list_id', $boardList->id)->orderBy('order')->get();

        expect($listTasks->count())->toBe(3)
            ->and($listTasks->first()->order)->toBe(1)
            ->and($listTasks[1]->order)->toBe(2)
            ->and($listTasks->last()->order)->toBe(3);
    });

    test('should be able to get the max order', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        for ($i = 1; $i <= 3; $i++) {
            ListTask::factory([
                'board_list_id' => $boardList->id,
                'order' => $i,
            ])->create();
        }

        $maxOrder = $this->listTaskService->getMaxOrder($boardList->id);

        expect($maxOrder)->toBe(3);
    });

    test('should return 0 while getting the max order if there are no tasks', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);

        $maxOrder = $this->listTaskService->getMaxOrder($boardList->id);

        expect($maxOrder)->toBe(0);
    });

    test('should be able to delete a list task', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1
        ]);
        $listTaskFactory = ListTask::factory([
            'board_list_id' => $boardList->id,
            'order' => 1,
        ])->create();

        $this->listTaskService->delete($listTaskFactory);

        $this->assertDatabaseMissing('list_tasks', ['id' => $listTaskFactory->id]);
    });
});
