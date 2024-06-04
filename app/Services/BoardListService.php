<?php

namespace App\Services;

use App\Http\Requests\StoreBoardListRequest;
use App\Models\BoardList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BoardListService
{
    public function __construct(private readonly BoardList $model)
    { }

    public function find(int $id): object
    {
        return $this->model->find($id);
    }

    public function getOrderedByBoardId(int $boardId): object
    {
        return $this->model->where('board_id', $boardId)->orderBy('order')->get();
    }

    public function create(string $name, int $boardId): BoardList
    {
        $data = [
            'newListName' => $name
        ];

        $validator = Validator::make($data, (new StoreBoardListRequest())->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();

        $order = $this->getMaxOrder($boardId) + 1;

        return $this->model->create([
            'name' => $validatedData['newListName'],
            'board_id' => $boardId,
            'order' => $order
        ]);
    }

    public function getMaxOrder(int $boardId): int
    {
        $order = $this->model->where('board_id', $boardId)->max('order');
        return $order ?: 0;
    }

    public function update(int $id, string $name): void
    {
        $this->model->find($id)->update(['name' => $name]);
    }

    public function delete(BoardList $list): void
    {
        $list->delete();
    }
}
