<?php

namespace App\Livewire;

use App\Models\Board;
use Illuminate\Support\Collection;
use Livewire\Component;

class Boards extends Component
{
    public Collection $boards;
    public string $name;
    public string $color_hash;
    
    public Board $currentBoard;

    public int $createModalIsOpen = 0;
    public int $editModalIsOpen = 0;
    public int $deleteModalIsOpen = 0;

    public function mount(): void
    {
        $this->groupData();
    }

    public function groupData(): void
    {
        $this->boards = Board::orderBy('order')->get();
    }

    public function render()
    {
        $this->boards = Board::orderBy('order')->get();
        return view('livewire.boards.index');
    }

    public function store(): void
    {
        $this->validate([
            'name' => 'required|unique:boards,name',
        ]);

        $currentMaxOrderBoard = Board::select('order')->orderBy('order', 'desc')->first();

        if ($currentMaxOrderBoard) {
            $order = $currentMaxOrderBoard->order + 1;
        } else {
            $order = 1;
        }

        Board::create([
            'name' => $this->name,
            'color_hash' => $this->color_hash,
            'order' => $order
        ]);

        $this->closeCreateModal();
        $this->groupData();
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|unique:boards,name,' . $this->currentBoard->id,
            'color_hash' => 'required|string'
        ]);

        $this->currentBoard->update([
            'name' => $this->name,
            'color_hash' => $this->color_hash
        ]);

        session()->flash('message', 'Board updated successfully.');

        $this->closeEditModal();
    }

    public function updateBoardOrder($orderedData): void
    {
        foreach ($orderedData as $group) {
            if (count($group['items']) > 1) {
                $itemToBeReplaced = Board::where('order', $group['order'])->first();

                $itemToBeReplacedOrder = $itemToBeReplaced->order;

                foreach($group['items'] as $item) {
                    if ($item['value'] !== (string)$itemToBeReplaced->id) {
                        $replacingItem = Board::find($item['value']);
                        $replacingItemOrder = $replacingItem->order;
                    }
                }
            }
        }

        if (isset($replacingItem) && isset($itemToBeReplaced)) {
            $replacingItem->order = $itemToBeReplacedOrder;
            $replacingItem->save();

            $itemToBeReplaced->order = $replacingItemOrder;
            $itemToBeReplaced->save();
        }

        $this->groupData();
    }

    public function delete(): void
    {
        $this->currentBoard->delete();
        session()->flash('message', 'Board deleted successfully.');
        $this->closeDeleteModal();
        $this->groupData();
    }

    public function openCreateModal(): void
    {
        $this->name = '';
        $this->color_hash = '';
        $this->createModalIsOpen = true;
    }

    public function closeCreateModal(): void
    {
        $this->createModalIsOpen = false;
    }

    public function openEditModal($boardId): void
    {
        $this->currentBoard = Board::find($boardId);
        $this->name = $this->currentBoard->name;
        $this->color_hash = $this->currentBoard->color_hash;
        $this->editModalIsOpen = true;
    }

    public function closeEditModal(): void
    {
        $this->editModalIsOpen = false;
        $this->groupData();
    }

    public function openDeleteModal($boardId): void
    {
        $this->currentBoard = Board::find($boardId);
        $this->deleteModalIsOpen = true;
    }

    public function closeDeleteModal(): void
    {
        $this->deleteModalIsOpen = false;
    }
}
