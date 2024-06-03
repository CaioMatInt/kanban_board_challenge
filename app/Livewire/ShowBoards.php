<?php

namespace App\Livewire;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\ListTask;
use Livewire\Component;

class ShowBoards extends Component
{
    public $colorHash = '#bd054e';

    public $board;
    public $boardId;

    public $listNames = [];
    public $lists;
    public $data = [];

    public $name;

    public $createListModalIsOpen = 0;
    public $createTaskModalIsOpen = 0;
    public $deleteListModalIsOpen = 0;

    public $listName;

    public $newTaskName;
    public $newTaskDescription;

    public $taskDetailName;
    public $taskDetailDescription;

    public $currentListIdToAddTask;

    public $editTaskModalIsOpen = 0;
    public ListTask $taskDetails;

    public $currentListIdToDelete;

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
        return view('livewire.boards.show');
    }

    public function openModal()
    {
        $this->name = '';
        $this->createListModalIsOpen = true;
    }

    public function closeModal()
    {
        $this->name = '';
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

    public function deleteList()
    {
        $list = BoardList::find($this->currentListIdToDelete);
        ListTask::where('board_list_id', $list->id)->delete();

        $list->delete();
        $this->closeDeleteListModal();
        $this->groupData();
    }

    public function openCreateTaskModal($listId)
    {
        $this->currentListIdToAddTask = $listId;
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

        $list = BoardList::find($this->currentListIdToAddTask);

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
        if ($value === 'colorHash') {
            $this->board->update(['color_hash' => $this->colorHash]);
            $this->groupData();
            $this->dispatch('att');
        }
    }

    public function deleteTask()
    {
        $this->taskDetails->delete();

        session()->flash('message','Task deleted successfully.');

        $this->closeTaskDetails();
        $this->groupData();
    }

    public function openDeleteListModal($id)
    {
        $this->deleteListModalIsOpen = true;
        $this->currentListIdToDelete = $id;
    }

    public function closeDeleteListModal()
    {
        $this->deleteListModalIsOpen = false;
        $this->currentListIdToDelete = null;
    }
}
