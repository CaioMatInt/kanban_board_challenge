<?php

use App\Models\Board;
use App\Services\BoardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('BoardServiceTest', function () {

    beforeEach(function () {
        $this->boardService = app(BoardService::class);
    });

    test('can find a board', function () {
        $board = Board::factory()->create();
        $this->boardService->find($board->id);

        expect($this->boardService->find($board->id)->id)->toBe($board->id)
            ->and($this->boardService->find($board->id)->name)->toBe($board->name)
            ->and($this->boardService->find($board->id)->color_hash)->toBe($board->color_hash);
    });

    test('can find a board by order', function () {
        $board = Board::factory()->create();
        $this->boardService->findByOrder($board->order);

        expect($this->boardService->findByOrder($board->order)->id)->toBe($board->id)
            ->and($this->boardService->findByOrder($board->order)->name)->toBe($board->name)
            ->and($this->boardService->findByOrder($board->order)->color_hash)->toBe($board->color_hash);
    });

    test('can get all boards ordered by order', function () {
        $boards = [];
        for ($i = 0; $i < 3; $i++) {
            $boards[] = Board::factory()->create([
                'order' => $i + 1
            ]);
        }
        $orderedBoards = $this->boardService->getAllOrderedByOrder();

        expect($orderedBoards->count())->toBe(3)
            ->and($orderedBoards[0]->id)->toBe($boards[0]->id)
            ->and($orderedBoards[1]->id)->toBe($boards[1]->id)
            ->and($orderedBoards[2]->id)->toBe($boards[2]->id);
    });

    test('can create a board', function () {
        $board = Board::factory()->make();
        $this->boardService->create($board->name, $board->color_hash);

        expect($this->boardService->find(1)->name)->toBe($board->name)
            ->and($this->boardService->find(1)->color_hash)->toBe($board->color_hash);
    });

    test('should return validation error when creating a board without name', function () {
        $board = Board::factory()->make(['name' => '']);
        $this->expectException(ValidationException::class);
        $this->boardService->create($board->name, $board->color_hash);
    });

    test('should return validation error when creating a board without color_hash', function () {
        $board = Board::factory()->make(['color_hash' => '']);
        $this->expectException(ValidationException::class);
        $this->boardService->create($board->name, $board->color_hash);
    });

    test('should attribute the correct order to a new board', function () {
        for ($i = 0; $i < 3; $i++) {
            Board::factory()->create([
                'order' => $i + 1
            ]);
        }
        $newBoard = Board::factory()->make();
        $createdBoard = $this->boardService->create($newBoard->name, $newBoard->color_hash);

        expect($createdBoard->order)->toBe(4);
    });

    test('should return max order', function () {
        for ($i = 0; $i < 3; $i++) {
            Board::factory()->create([
                'order' => $i + 1
            ]);
        }
        $maxOrder = $this->boardService->getMaxOrder();

        expect($maxOrder)->toBe(3);
    });

    test('should return 0 while getting max order if there is no board', function () {
        $maxOrder = $this->boardService->getMaxOrder();

        expect($maxOrder)->toBe(0);
    });

    test('should be able to update a board', function () {
        $board = Board::factory()->create();
        $updatedBoard = $this->boardService->update($board, 'new name', '#fffff');

        expect($updatedBoard->name)->toBe('new name')
            ->and($updatedBoard->color_hash)->toBe('#fffff');
    });

    test('should be able to only update color_hash', function () {
        $board = Board::factory()->create();
        $updatedBoard = $this->boardService->update($board, null, '#fffff');

        expect($updatedBoard->name)->toBe($board->name)
            ->and($updatedBoard->color_hash)->toBe('#fffff');
    });

    test('should be able to only update name', function () {
        $board = Board::factory()->create();
        $updatedBoard = $this->boardService->update($board, 'new name', null);

        expect($updatedBoard->name)->toBe('new name')
            ->and($updatedBoard->color_hash)->toBe($board->color_hash);
    });
});
