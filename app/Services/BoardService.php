<?php

namespace App\Services;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Traits\Database\FindableTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BoardService
{
    use FindableTrait;

    public function __construct(private readonly Board $model)
    { }

    /**
     * Find a board by its order.
     *
     * @param int $order
     * @return Board
     */
    public function findByOrder(int $order): Board
    {
        return $this->model::where('order', $order)->first();
    }

    /**
     * Get all boards ordered by order.
     *
     * @return Collection
     */
    public function getAllOrderedByOrder(): Collection
    {
        return $this->model::orderBy('order')->get();
    }

    /**
     * Delete a board by its id. It also deletes all lists and tasks associated with the board.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $board = $this->model::find($id);
        $board->lists->each(function ($list) {
            $list->tasks->each(function ($task) {
                $task->delete();
            });
            $list->delete();
        });

        $board->delete();
    }

    /**
     * Create a new board.
     *
     * @param string $name
     * @param string $colorHash
     * @return Board
     */
    public function create(string $name, string $colorHash): Board
    {
        $data = [
            'name' => $name,
            'color_hash' => $colorHash
        ];

        $validator = Validator::make($data, (new StoreBoardRequest())->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();

        $order = $this->getMaxOrder() + 1;

        return Board::create([
            'name' => $validatedData['name'],
            'color_hash' => $validatedData['color_hash'],
            'order' => $order
        ]);
    }

    /**
     * Get the maximum order value of the board. If no records are found, return 0.
     *
     * @return int
     */
    public function getMaxOrder(): int
    {
        $currentMaxOrderBoard = Board::select('order')->orderBy('order', 'desc')->first();

        return $currentMaxOrderBoard ? $currentMaxOrderBoard->order : 0;
    }

    /**
     * Update a board.
     *
     * @param Board $board
     * @param string|null $name
     * @param string|null $colorHash
     * @return Board
     */
    public function update(Board $board, ?string $name, ?string $colorHash): Board
    {
        $data = [];

        if ($colorHash) {
            $data['color_hash'] = $colorHash;
        }

        if ($name) {
            $data['name'] = $name;
        }

        $updateRequest = new UpdateBoardRequest();
        $updateRequest->setBoardId($board->id);

        $validator = Validator::make($data, $updateRequest->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();

        $board->update($validatedData);

        return $board;
    }

    /**
     * @param array $orderedData
     * $orderedData follows the structure:
     * [
     *    [
     *       'order' => 1,
     *      'items' => [
     *         ['value' => '1'],
     *        ['value' => '2']
     *    ]
     * ]
     * ]
     *
     * Documentation: https://github.com/livewire/sortable
     * @return void
     */
    public function updateBoardOrder(array $orderedData): void
    {
        [$itemToBeReplaced, $replacingItem] = $this->findItemToBeReplacedAndReplacingItem($orderedData);

        if ($replacingItem && $itemToBeReplaced) {
            $this->switchBoardsOrders($itemToBeReplaced, $replacingItem);
        }
    }

    /**
     * @param array $orderedData
     * $orderedData follows the structure:
     * [
     *    [
     *       'order' => 1,
     *      'items' => [
     *         ['value' => '1'],
     *        ['value' => '2']
     *    ]
     * ]
     * ]
     *
     * Documentation: https://github.com/livewire/sortable
     * @return void
     */
    private function findItemToBeReplacedAndReplacingItem(array $orderedData): array
    {
        foreach ($orderedData as $group) {
            if (count($group['items']) > 1) {
                $itemToBeReplaced = $this->findByOrder($group['order']);

                foreach($group['items'] as $item) {
                    if ($item['value'] !== (string) $itemToBeReplaced->id) {
                        $replacingItem = $this->find((int) $item['value']);
                    }
                }
            }
        }

        if (!isset($itemToBeReplaced) || !isset($replacingItem)) {
            return [null, null];
        }

        return [$itemToBeReplaced, $replacingItem];
    }

    /**
     * Switch the orders of two boards.
     *
     * @param Board $itemToBeReplaced
     * @param Board $replacingItem
     * @return void
     */
    private function switchBoardsOrders(Board $itemToBeReplaced, Board $replacingItem): void
    {
        $replacingItemOrder = $replacingItem->order;

        $replacingItem->order = $itemToBeReplaced->order;
        $replacingItem->save();

        $itemToBeReplaced->order = $replacingItemOrder;
        $itemToBeReplaced->save();
    }
}
