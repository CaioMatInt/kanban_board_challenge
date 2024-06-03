<?php

namespace App\Livewire;

use App\Models\Board;
use Livewire\Component;

class Boards22 extends Component
{
    public $leads;
    public $order = ['new','contacted','converted', 'new2','contacted2','converted2', 'new3','contacted3','converted3'];
    public $data = [
        "new"=>[],
        "contacted"=>[],
        "converted"=>[],
        "new2"=>[],
        "contacted2"=>[],
        "converted2"=>[],
        "new3"=>[],
        "contacted3"=>[],
        "converted3"=>[],
    ];

    public $boards, $name, $color_hash, $board_id, $show = 0;
    public $isOpen = 0;


    public function mount()
    {
        $this->groupData();
    }

    public function groupData()
    {
        /*$this->reset('data');*/
        $this->leads = Board::all();
        foreach($this->leads as $lead)
        {
            $this->data['new'][] = $lead;
        }
    }

    public function updateLeadStatus($orderedData)
    {

        foreach($orderedData as $group){
            foreach($group['items'] as $item)
            {
                Board::find($item['value'])->update(["status"=>$group['value']]);
            }
        }
        $this->groupData();
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function render()
    {
        $this->boards = Board::all();
        return view('livewire.boards');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function openModal()
    {
        $this->isOpen = true;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function closeModal()
    {
        $this->isOpen = false;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->name = '';
        $this->color_hash = '';
        $this->board_id = '';
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);


        Board::updateOrCreate(['id' => $this->board_id], [
            'name' => $this->name,
            'color_hash' => $this->color_hash
        ]);

        session()->flash('message',
            $this->board_id ? 'Board Updated Successfully.' : 'board Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function edit($id)
    {
        $board = Board::findOrFail($id);
        $this->board_id = $id;
        $this->name = $board->name;
        $this->color_hash = $board->color_hash;

        $this->openModal();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function delete($id)
    {
        Board::find($id)->delete();
        session()->flash('message', 'Board Deleted Successfully.');
    }
}
