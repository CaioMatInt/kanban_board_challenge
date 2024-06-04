<?php

use App\Livewire\Boards;
use App\Models\Board;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('BoardsTest', function () {

    it('can see the new board button', function () {
        Livewire::test(Boards::class)
            ->call('groupData')
            ->assertSee('+ New Board');
    });

    it('can see the class board-container multiple times depending on the number of boards', function () {
        Board::factory(3)->create();

        $component = Livewire::test(Boards::class)
            ->call('groupData');

        $html = $component->html();

        $count = substr_count($html, 'board-container');

        expect($count)->toBe(3);
    });

    it('can see the icon fas fa-edit multiple times depending on the number of boards', function () {
        Board::factory(3)->create();

        $component = Livewire::test(Boards::class)
            ->call('groupData');

        $html = $component->html();

        $count = substr_count($html, 'fas fa-edit');

        expect($count)->toBe(3);
    });

    it('can see the icon fas fa-trash-alt multiple times depending on the number of boards', function () {
        Board::factory(3)->create();

        $component = Livewire::test(Boards::class)
            ->call('groupData');

        $html = $component->html();

        $count = substr_count($html, 'fas fa-trash-alt');

        expect($count)->toBe(3);
    });

    it('can open the delete modal and set current board ID', function () {
        $boardId = 123;

        Livewire::test(Boards::class)
            ->call('openDeleteModal', $boardId)
            ->assertSet('currentBoardId', $boardId)
            ->assertSet('deleteModalIsOpen', true);
    });

    it('can close the delete modal', function () {
        Livewire::test(Boards::class)
            ->set('deleteModalIsOpen', true)
            ->call('closeDeleteModal')
            ->assertSet('deleteModalIsOpen', false);
    });

    it('can open the edit modal and set current board', function () {
        $board = Board::factory()->create();
        $boardId = $board->id;
        $boardName = $board->name;
        $boardColorHash = $board->color_hash;

        Livewire::test(Boards::class)
            ->call('openEditModal', $boardId)
            ->assertSet('name', $boardName)
            ->assertSet('colorHash', $boardColorHash)
            ->assertSet('editModalIsOpen', true)
            ->assertSet('currentBoard.id', $boardId)
            ->assertSet('currentBoard.name', $boardName)
            ->assertSet('currentBoard.color_hash', $boardColorHash);
    });

    it('can close the edit modal', function () {
        Livewire::test(Boards::class)
            ->set('editModalIsOpen', true)
            ->call('closeEditModal')
            ->assertSet('editModalIsOpen', false);
    });

    it('can open the create modal', function () {
        Livewire::test(Boards::class)
            ->call('openCreateModal')
            ->assertSet('name', '')
            ->assertSet('colorHash', '')
            ->assertSet('createModalIsOpen', true);
    });

    it('can close the create modal', function () {
        Livewire::test(Boards::class)
            ->set('createModalIsOpen', true)
            ->call('closeCreateModal')
            ->assertSet('createModalIsOpen', false);
    });

    it('can delete a board', function () {
        $board = Board::factory()->create();
        $boardId = $board->id;

        Livewire::test(Boards::class)
            ->set('currentBoardId', $boardId)
            ->call('delete', $boardId)
            ->assertSet('deleteModalIsOpen', false)
            ->assertSee('Board deleted successfully.');
    });

    it('can update a board', function () {
        $board = Board::factory()->create();
        $boardId = $board->id;
        $newName = 'New Name';
        $newColorHash = '#000000';

        Livewire::test(Boards::class)
            ->set('currentBoard', $board)
            ->set('name', $newName)
            ->set('colorHash', $newColorHash)
            ->call('update')
            ->assertSet('editModalIsOpen', false)
            ->assertSee('Board updated successfully.');
    });

    it('can store a board', function () {
        $name = 'New Board';
        $colorHash = '#000000';

        Livewire::test(Boards::class)
            ->set('name', $name)
            ->set('colorHash', $colorHash)
            ->call('store')
            ->assertSet('createModalIsOpen', false);
    });

    it('can update the board order', function () {
        //@@TODO
    });

    it('can group data', function () {
        $boards = Board::factory(3)->create();

        Livewire::test(Boards::class)
            ->call('groupData')
            ->assertSet('boards.0.id', $boards[0]['id'])
            ->assertSet('boards.0.name', $boards[0]['name'])
            ->assertSet('boards.0.color_hash', $boards[0]['color_hash'])
            ->assertSet('boards.1.id', $boards[1]['id'])
            ->assertSet('boards.1.name', $boards[1]['name'])
            ->assertSet('boards.1.color_hash', $boards[1]['color_hash'])
            ->assertSet('boards.2.id', $boards[2]['id'])
            ->assertSet('boards.2.name', $boards[2]['name'])
            ->assertSet('boards.2.color_hash', $boards[2]['color_hash']);
    });

    it('can see all the board names', function () {
        $boards = Board::factory(3)->create();

        Livewire::test(Boards::class)
            ->call('groupData')
            ->assertSee($boards[0]['name'])
            ->assertSee($boards[1]['name'])
            ->assertSee($boards[2]['name']);
    });
});
