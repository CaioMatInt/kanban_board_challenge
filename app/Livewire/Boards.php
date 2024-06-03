<?php

namespace App\Livewire;

use App\Models\Board;
use Livewire\Component;

class Boards extends Component
{
    public $data = [
    ];

    public $boards, $name, $color_hash, $board_id, $show = 0;
    public $isOpen = 0;

    public $editModalIsOpen = 0;
    public $deleteModalIsOpen = 0;

    public $currentBoardDetails;


    public function mount()
    {
        $this->groupData();
    }

    public function groupData()
    {
        $this->reset('data');
        $this->boards = Board::orderBy('order')->get();
        foreach($this->boards as $board)
        {
            $this->data[$board->order] = $board;
        }
    }

    public function updateBoardOrder($orderedData)
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
        return;
    }

    public function render()
    {
        $this->boards = Board::all();
        return view('livewire.boards');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields(){
        $this->name = '';
        $this->color_hash = '';
        $this->board_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|unique:boards,name,' . $this->board_id,
        ]);


        $currentMaxOrderBoard = Board::select('order')->orderBy('order', 'desc')->first();

        if ($currentMaxOrderBoard) {
            $order = $currentMaxOrderBoard->order + 1;
        } else {
            $order = 1;
        }

        Board::updateOrCreate(['id' => $this->board_id], [
            'name' => $this->name,
            'color_hash' => $this->color_hash,
            'order' => $order
        ]);

        session()->flash('message',
            $this->board_id ? 'Board Updated Successfully.' : 'board Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
        $this->groupData();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:boards,name,' . $this->currentBoardDetails->id,
            'color_hash' => 'required|string'
        ]);

        Board::find($this->currentBoardDetails->id)->update([
            'name' => $this->name,
            'color_hash' => $this->color_hash
        ]);

        session()->flash('message', 'Board Updated Successfully.');

        $this->closeEditModal();
    }

    public function delete()
    {
        Board::find($this->currentBoardDetails->id)->delete();
        session()->flash('message', 'Board Deleted Successfully.');
        $this->closeDeleteModal();
        $this->groupData();
    }

    public function openEditModal($boardId)
    {
        $this->currentBoardDetails = Board::find($boardId);
        $this->name = $this->currentBoardDetails->name;
        $this->color_hash = $this->currentBoardDetails->color_hash;
        $this->editModalIsOpen = true;
    }

    public function closeEditModal()
    {
        $this->currentBoardDetails = null;
        $this->name = null;
        $this->color_hash = null;
        $this->editModalIsOpen = false;
        $this->groupData();
    }

    public function openDeleteModal($boardId)
    {
        $this->currentBoardDetails = Board::find($boardId);
        $this->deleteModalIsOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->currentBoardDetails = null;
        $this->deleteModalIsOpen = false;
    }
}
