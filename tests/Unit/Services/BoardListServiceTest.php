<?php

use App\Models\Board;
use App\Models\BoardList;
use App\Services\BoardListService;
use App\Services\BoardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('BoardServiceTest', function () {

    beforeEach(function () {
        $this->boardListService = app(BoardListService::class);
    });

    test('can find a board list', function () {
        $board = Board::factory()->create();
        $boardListFromFactory = BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1,
        ]);
        $boardList = $this->boardListService->find($boardListFromFactory->id);
        expect($boardList->id)->toBe($boardListFromFactory->id)
            ->and($boardList->name)->toBe($boardListFromFactory->name);
    });

    test('should return ordered board lists by board id', function () {
        $board = Board::factory()->create();

        $boardLists = [];

        for ($i = 1; $i <= 3; $i++) {
            $boardLists[] = BoardList::factory()->create([
                'board_id' => $board->id,
                'order' => $i,
            ]);
        }

        $orderedBoardLists = $this->boardListService->getOrderedByBoardId($board->id);

        expect($orderedBoardLists->count())->toBe(3)
            ->and($orderedBoardLists[0]->id)->toBe($boardLists[0]->id)
            ->and($orderedBoardLists[1]->id)->toBe($boardLists[1]->id)
            ->and($orderedBoardLists[2]->id)->toBe($boardLists[2]->id);
    });

    test('should be able to create a board list', function () {
        $board = Board::factory()->create();
        $boardList = $this->boardListService->create('New List', $board->id);
        expect($boardList->name)->toBe('New List')
            ->and($boardList->board_id)->toBe($board->id);
    });

    test('should attribute order 1 to the first board list created', function () {
        $board = Board::factory()->create();
        $boardList = $this->boardListService->create('New List', $board->id);
        expect($boardList->order)->toBe(1);
    });

    test('should return a validation error when creating a board list with an empty name', function () {
        $board = Board::factory()->create();
        $this->expectException(ValidationException::class);
        $this->boardListService->create('', $board->id);
    });

    test('should be able to get the max order of a board list', function () {
        $board = Board::factory()->create();
        BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 1,
        ]);
        BoardList::factory()->create([
            'board_id' => $board->id,
            'order' => 2,
        ]);
        $maxOrder = $this->boardListService->getMaxOrder($board->id);
        expect($maxOrder)->toBe(2);
    });

    test('should return 0 when getting the max order of a board list with no lists', function () {
        $board = Board::factory()->create();
        $maxOrder = $this->boardListService->getMaxOrder($board->id);
        expect($maxOrder)->toBe(0);
    });

    test('should be able to update a board list', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory([
            'order' => 1,
            'board_id' => $board->id,
        ])->create();
        $this->boardListService->update($boardList->id, 'Updated List');
        $updatedBoardList = BoardList::find($boardList->id);
        expect($updatedBoardList->name)->toBe('Updated List');
    });

    test('should be able to delete a board list', function () {
        $board = Board::factory()->create();
        $boardList = BoardList::factory([
            'order' => 1,
            'board_id' => $board->id,
        ])->create();
        $this->boardListService->delete($boardList);
        $deletedBoardList = BoardList::find($boardList->id);
        expect($deletedBoardList)->toBeNull();
        
        $this->assertDatabaseMissing('board_lists', [
            'id' => $boardList->id,
        ]);
    });
});
