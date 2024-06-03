<?php

namespace App\Livewire;

use App\Models\Board;
use App\Models\BoardList;
use App\Models\ListTask;
use App\Services\BoardListService;
use App\Services\BoardService;
use App\Services\ListTaskService;
use Illuminate\Support\Collection;
use Livewire\Component;

class ShowBoards extends Component
{
    private BoardService $boardService;
    private BoardListService $boardListService;
    private ListTaskService $listTaskService;

    public $colorHash = '#bd054e';
    public string $name;
    public array $tasksByListNames = [];

    public Board $board;
    public int $boardId;

    public Collection $lists;
    public string $newListName;

    public string $newTaskName;
    public string $newTaskDescription;

    public ListTask $taskDetails;
    public string $taskDetailName;
    public string $taskDetailDescription;

    public int $currentListIdToAddTask;

    public int $currentListIdToDelete;

    public int $createListModalIsOpen = 0;
    public int $createTaskModalIsOpen = 0;
    public int $deleteListModalIsOpen = 0;
    public int $editTaskModalIsOpen = 0;


    //@@TODO: Bug when there are two lists with the same name
    public function boot(
        BoardService $boardService,
        BoardListService $boardListService,
        ListTaskService $listTaskService
    ): void
    {
        $this->boardService = $boardService;
        $this->boardListService = $boardListService;
        $this->listTaskService = $listTaskService;
    }

    public function mount($id): void
    {
        $this->boardId = $id;
        $this->groupData();
    }

    public function render()
    {
        return view('livewire.boards.show');
    }

    public function groupData(): void
    {
        $this->board = $this->boardService->find($this->boardId);
        $this->name = $this->board->name;
        $this->lists = $this->boardListService->getOrderedByBoardId($this->boardId);
        $this->tasksByListNames = [];

        foreach ($this->lists as $list) {
            $this->tasksByListNames[$list->name] = $this->listTaskService->getOrderedByBoardListId($list->id);
        }
    }

    public function updateTaskOrderOrGroup($orderedData): void
    {
        $this->listTaskService->updateTaskOrderOrGroup($orderedData);
        $this->groupData();
    }

    public function storeList(): void
    {
        $this->boardListService->create($this->newListName, $this->boardId);

        session()->flash('message','List created successfully.');

        $this->closeCreateListModal();
        $this->groupData();
    }

    public function updateListName($id, $newName): void
    {
        $this->boardListService->update($id, $newName);
        $this->groupData();
    }

    public function deleteList(): void
    {
        $list = $this->boardListService->find($this->currentListIdToDelete);
        $this->listTaskService->deleteByBoardListId($list->id);

        $this->boardListService->delete($list);
        $this->closeDeleteListModal();
        $this->groupData();
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

    public function openCreateListModal()
    {
        $this->newListName = '';
        $this->createListModalIsOpen = true;
    }

    public function closeCreateListModal()
    {
        $this->newListName = '';
        $this->createListModalIsOpen = false;
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

    public function openDeleteListModal($id)
    {
        $this->deleteListModalIsOpen = true;
        $this->currentListIdToDelete = $id;
    }

    public function closeDeleteListModal()
    {
        $this->deleteListModalIsOpen = false;
    }
}
