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

    //@@TODO: Implementar esses dois e por a validacao da name unico
/*    public function edit($id)
    {
        $board = Board::findOrFail($id);
        $this->board_id = $id;
        $this->name = $board->name;
        $this->color_hash = $board->color_hash;

        $this->openModal();
    }

    public function delete($id)
    {
        Board::find($id)->delete();
        session()->flash('message', 'Board Deleted Successfully.');
    }*/
}
