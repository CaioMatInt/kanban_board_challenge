<?php

namespace App\Services;

use App\Http\Requests\StoreBoardListRequest;
use App\Models\BoardList;
use App\Traits\Database\FindableTrait;
use App\Traits\Database\ModelDeletableTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BoardListService
{
    use ModelDeletableTrait, FindableTrait;

    public function __construct(private readonly BoardList $model)
    { }

    /**
     * Get all lists ordered by order by board id.
     *
     * @param int $boardId
     * @return object
     */
    public function getOrderedByBoardId(int $boardId): object
    {
        return $this->model->where('board_id', $boardId)->orderBy('order')->get();
    }

    /**
     * Create a new list.
     *
     * @param string $name
     * @param int $boardId
     * @return BoardList
     */
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

    /**
     * Get the maximum order value by board id. If no records are found, return 0.
     *
     * @param int $boardId
     * @return int
     */
    public function getMaxOrder(int $boardId): int
    {
        $order = $this->model->where('board_id', $boardId)->max('order');
        return $order ?: 0;
    }

    /**
     * Update a list.
     *
     * @param int $id
     * @param string $name
     * @return void
     */
    public function update(int $id, string $name): void
    {
        $this->model->find($id)->update(['name' => $name]);
    }

    /**
     * Delete a list.
     *
     * @param BoardList $list
     * @return void
     */
    public function delete(BoardList $list): void
    {
        $this->deleteRecord($list);
    }
}
