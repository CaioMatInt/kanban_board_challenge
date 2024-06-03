<?php

namespace App\Livewire;

use App\Models\Board;
use App\Services\BoardService;
use App\Trait\Livewire\ValidationHandlingTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Boards extends Component
{
    use ValidationHandlingTrait;

    private BoardService $boardService;

    public Collection $boards;
    public string $name;
    public string $colorHash;

    public Board $currentBoard;

    public int $createModalIsOpen = 0;
    public int $editModalIsOpen = 0;
    public int $deleteModalIsOpen = 0;

    public function boot(BoardService $boardService)
    {
        $this->boardService = $boardService;
    }

    public function mount(): void
    {
        $this->groupData();
    }

    public function groupData(): void
    {
        $this->boards = $this->boardService->getAllOrderedByOrder();
    }

    public function render()
    {
        return view('livewire.boards.index');
    }

    public function store(): void
    {
        try {
            $this->boardService->create($this->name, $this->colorHash);
            $this->closeCreateModal();
            $this->groupData();
        } catch (ValidationException $e) {
            $this->handleValidationError($e);
        }
    }

    public function update(): void
    {
        $this->boardService->update($this->currentBoard, $this->name, $this->colorHash);

        session()->flash('message', 'Board updated successfully.');

        $this->closeEditModal();
    }

    public function updateBoardOrder(array $orderedData): void
    {
        $this->boardService->updateBoardOrder($orderedData);
        $this->groupData();
    }

    public function delete(): void
    {
        $this->boardService->destroy($this->currentBoard->id);
        session()->flash('message', 'Board deleted successfully.');
        $this->closeDeleteModal();
        $this->groupData();
    }

    public function openCreateModal(): void
    {
        $this->name = '';
        $this->colorHash = '';
        $this->createModalIsOpen = true;
    }

    public function closeCreateModal(): void
    {
        $this->createModalIsOpen = false;
    }

    public function openEditModal($boardId): void
    {
        $this->currentBoard = $this->boardService->find($boardId);
        $this->name = $this->currentBoard->name;
        $this->colorHash = $this->currentBoard->color_hash;
        $this->editModalIsOpen = true;
    }

    public function closeEditModal(): void
    {
        $this->editModalIsOpen = false;
        $this->groupData();
    }

    public function openDeleteModal($boardId): void
    {
        $this->currentBoard = $this->boardService->find($boardId);
        $this->deleteModalIsOpen = true;
    }

    public function closeDeleteModal(): void
    {
        $this->deleteModalIsOpen = false;
    }
}
