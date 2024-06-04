<?php

namespace App\Services;

use App\Http\Requests\StoreListTaskRequest;
use App\Models\ListTask;
use App\Traits\Database\FindableTrait;
use App\Traits\Database\ModelDeletableTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ListTaskService
{
    use ModelDeletableTrait, FindableTrait;

    public function __construct(
        private readonly ListTask $model,
        private BoardListService $boardListService
    )
    { }

    /**
     * Get all list tasks by board list id ordered by order.
     *
     * @param int $boardListId
     * @return object
     */
    public function getOrderedByBoardListId(int $boardListId): object
    {
        return $this->model->where('board_list_id', $boardListId)->orderBy('order')->get();
    }

    /**
     * Delete all list tasks by board list id.
     *
     * @param int $boardListId
     * @return void
     */
    public function deleteByBoardListId(int $boardListId): void
    {
        $this->model->where('board_list_id', $boardListId)->delete();
    }

    /**
     * Create a new list task.
     *
     * @param string $name
     * @param string $description
     * @param int $boardListId
     * @return void
     */
    public function create(string $name, string $description, int $boardListId): void
    {
        $data = [
            'newTaskName' => $name,
            'description' => $description,
        ];

        $validator = Validator::make($data, (new StoreListTaskRequest())->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();

        $order = $this->getMaxOrder($boardListId) + 1;

        $this->model::create([
            'name' => $validatedData['newTaskName'],
            'description' => $validatedData['description'],
            'board_list_id' => $boardListId,
            'order' => $order
        ]);
    }

    /**
     * Get the maximum order value by board list id. If no records are found, return 0.
     *
     * @param int $boardListId
     * @return int
     */
    public function getMaxOrder(int $boardListId): int
    {
        $task = $this->model::select('order')->where('board_list_id', $boardListId)->orderBy('order', 'desc')->first();

        return $task ? $task->order : 0;
    }

    /**
     * Update a list task order or its group. $orderedData is an array of groups, each group has an array of items.
     *  Documentation: https://github.com/livewire/sortable
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @return void
     */
    public function updateTaskOrderOrGroup(array $orderedData): void
    {
        foreach ($orderedData as $group) {
            foreach ($group['items'] as $item)
            {
                $boardList = $this->boardListService->find((int) $group['value']);

                $this->model::find($item['value'])->update([
                    "order" => $item['order'],
                    "board_list_id" => $boardList->id
                ]);

            }
        }
    }

    /**
     * Delete a list task.
     *
     * @param ListTask $task
     * @return void
     */
    public function delete(ListTask $task): void
    {
        $this->deleteRecord($task);
    }
}
