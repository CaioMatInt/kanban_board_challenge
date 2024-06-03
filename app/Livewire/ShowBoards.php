<?php

namespace App\Livewire;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\ListTask;
use Livewire\Component;

class ShowBoards extends Component
{
    public $color_hash = '#bd054e';

    public $board;
    public $boardId;

    public $listNames = [];
    public $lists;
    public $data = [];

    public $name;

    public $createListModalIsOpen = 0;
    public $createTaskModalIsOpen = 0;

    public $listName;

    public $newTaskName;
    public $newTaskDescription;

    public $taskDetailName;
    public $taskDetailDescription;

    public $currentListNameToAddTask;

    public $editTaskModalIsOpen = 0;
    public ListTask $taskDetails;

    public function mount($id)
    {
        $this->boardId = $id;
        $this->groupData();
    }

    //@@TODO: reset fields
    //@@TODO: renomear onde tiver elementos repetidos (id="exampleFormControlInput1") exemplo
    //@@TODO resetar os fields sempre, verificar onde n resetei
    public function groupData()
    {
        $this->board = Board::find($this->boardId);
        $this->name = $this->board->name;
        $this->listNames = BoardList::where('board_id', $this->boardId)->orderBy('order')->pluck('name')->toArray();
        $this->lists = BoardList::where('board_id', $this->boardId)->orderBy('order')->get();
        $this->data = [];

        foreach($this->lists as $list)
        {
            $this->data[$list->name] = ListTask::where('board_list_id', $list->id)->orderBy('order')->get();
        }
    }

    public function updateTaskStatus($orderedData)
    {
        foreach ($orderedData as $group) {
            foreach ($group['items'] as $item)
            {
                $boardList = BoardList::find($group['value']);
                ListTask::find($item['value'])->update([
                    "order" => $item['order'],
                    "board_list_id" => $boardList->id
                ]);

            }
        }
        $this->groupData();
    }

    public function render()
    {
        return view('livewire.show-boards');
    }

    public function openModal()
    {
        $this->createListModalIsOpen = true;
    }

    public function closeModal()
    {
        $this->createListModalIsOpen = false;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
        ]);


        $currentMaxOrderList = BoardList::select('order')->orderBy('order', 'desc')->first();

        if ($currentMaxOrderList) {
            $order = $currentMaxOrderList->order + 1;
        } else {
            $order = 1;
        }

        //@@TODO: e se der erro?
        BoardList::create([
            'name' => $this->name,
            'board_id' => $this->boardId,
            'order' => $order
        ]);

        session()->flash('message','List created successfully.');

        $this->closeModal();
        $this->groupData();
    }

    public function updateListName($id, $newName)
    {
        BoardList::find($id)->update(['name' => $newName]);
        $this->groupData();
    }

    public function deleteList($id)
    {
        $list = BoardList::find($id);
        ListTask::where('board_list_id', $list->id)->delete();

        $list->delete();
        $this->groupData();
    }

    public function openCreateTaskModal($currentListNameToAddTask)
    {
        $this->currentListNameToAddTask = $currentListNameToAddTask;
        $this->newTaskDescription = '';
        $this->newTaskName = '';
        $this->createTaskModalIsOpen = true;
    }

    public function closeCreateTaskModal()
    {
        $this->newTaskDescription = '';
        $this->newTaskName = '';
        $this->createTaskModalIsOpen = false;
    }

    public function storeNewTask()
    {
        $this->validate([
            'newTaskName' => 'required',
        ]);

        $currentMaxOrderTask = ListTask::select('order')->orderBy('order', 'desc')->first();

        if ($currentMaxOrderTask) {
            $order = $currentMaxOrderTask->order + 1;
        } else {
            $order = 1;
        }

        $list = BoardList::find($this->currentListNameToAddTask);

        ListTask::create([
            'name' => $this->newTaskName,
            'description' => $this->newTaskDescription,
            'board_list_id' => $list->id,
            'order' => $order
        ]);

        session()->flash('message','Task created successfully.');

        $this->closeCreateTaskModal();
        $this->groupData();
    }

    public function openTaskDetails($id)
    {
        $this->editTaskModalIsOpen = true;
        $this->taskDetails = ListTask::find($id);
        $this->taskDetailName = $this->taskDetails->name;
        $this->taskDetailDescription = $this->taskDetails->description;
    }

    public function closeTaskDetails()
    {
        $this->editTaskModalIsOpen = false;
    }

    public function updateTaskDetails()
    {
        $this->validate([
            'taskDetailName' => 'required',
        ]);

        $this->taskDetails->update([
            'name' => $this->taskDetailName,
            'description' => $this->taskDetailDescription
        ]);

        session()->flash('message','Task updated successfully.');

        $this->closeTaskDetails();
        $this->groupData();
    }

    public function updateBoardName()
    {
        $this->validate([
            'name' => 'required|min:5',
        ]);

        Board::find($this->boardId)->update(['name' => $this->name]);
        $this->groupData();
    }

    public function updated($value)
    {
        if ($value === 'color_hash') {
            $this->board->update(['color_hash' => $this->color_hash]);
            $this->groupData();
            $this->dispatch('att');
        }
    }
}
