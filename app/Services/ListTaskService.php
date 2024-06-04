<?php

namespace App\Services;

use App\Http\Requests\StoreListTaskRequest;
use App\Models\ListTask;
use App\Traits\Database\ModelDeletableTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ListTaskService
{
    use ModelDeletableTrait;

    public function __construct(
        private readonly ListTask $model,
        private BoardListService $boardListService
    )
    { }

    public function find(int $id): object
    {
        return $this->model::find($id);
    }

    public function getOrderedByBoardListId(int $boardListId): object
    {
        return $this->model->where('board_list_id', $boardListId)->orderBy('order')->get();
    }

    public function deleteByBoardListId(int $boardListId): void
    {
        $this->model->where('board_list_id', $boardListId)->delete();
    }

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

        $order = $this->getMaxOrder() + 1;

        $this->model::create([
            'name' => $validatedData['newTaskName'],
            'description' => $validatedData['description'],
            'board_list_id' => $boardListId,
            'order' => $order
        ]);
    }

    public function getMaxOrder(): int
    {
        $task = $this->model::select('order')->orderBy('order', 'desc')->first();

        return $task ? $task->order : 0;
    }

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

    public function delete(ListTask $task): void
    {
        $this->deleteRecord($task);
    }
}
