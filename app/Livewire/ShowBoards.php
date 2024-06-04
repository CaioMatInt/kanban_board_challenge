<?php

namespace App\Livewire;

use App\Models\Board;
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

    public $colorHash;
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
            $this->tasksByListNames[$list->id] = $this->listTaskService->getOrderedByBoardListId($list->id);
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

    public function storeNewTask(): void
    {
        $this->listTaskService->create($this->newTaskName, $this->newTaskDescription, $this->currentListIdToAddTask);

        session()->flash('message','Task created successfully.');

        $this->closeCreateTaskModal();
        $this->groupData();
    }

    public function updateTaskDetails(): void
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

    public function updateBoardName(): void
    {
        $this->boardService->update($this->board, $this->name, $this->colorHash);
        $this->groupData();
    }

    //@@TODO: This is very problematic in terms of performance since the user can change the color quickly
    // using the picker, which results in many requests. Ideally, we should refactor this by adding a confirmation
    // button to the color picker element, so the update request is made only after the user has chosen the final color.
    public function updated($value): void
    {
        if ($value === 'colorHash') {
            $this->boardService->update($this->board, null, $this->colorHash);
            $this->groupData();
        }
    }

    public function deleteTask(): void
    {
        $this->listTaskService->delete($this->taskDetails);

        session()->flash('message','Task deleted successfully.');

        $this->closeTaskDetails();
        $this->groupData();
    }

    public function openCreateListModal(): void
    {
        $this->newListName = '';
        $this->createListModalIsOpen = true;
    }

    public function closeCreateListModal(): void
    {
        $this->newListName = '';
        $this->createListModalIsOpen = false;
    }

    public function openCreateTaskModal($listId): void
    {
        $this->currentListIdToAddTask = $listId;
        $this->newTaskDescription = '';
        $this->newTaskName = '';
        $this->createTaskModalIsOpen = true;
    }

    public function closeCreateTaskModal(): void
    {
        $this->newTaskDescription = '';
        $this->newTaskName = '';
        $this->createTaskModalIsOpen = false;
    }

    public function openTaskDetails($id): void
    {
        $this->editTaskModalIsOpen = true;
        $this->taskDetails = $this->listTaskService->find($id);
        $this->taskDetailName = $this->taskDetails->name;
        $this->taskDetailDescription = $this->taskDetails->description;
    }

    public function closeTaskDetails(): void
    {
        $this->editTaskModalIsOpen = false;
    }

    public function openDeleteListModal($id): void
    {
        $this->deleteListModalIsOpen = true;
        $this->currentListIdToDelete = $id;
    }

    public function closeDeleteListModal(): void
    {
        $this->deleteListModalIsOpen = false;
    }
}
