<?php

namespace App\Services;

use App\Models\BoardList;
use App\Models\ListTask;

class ListTaskService
{
    public function __construct(
        private readonly ListTask $model,
        private BoardListService $boardListService
    )
    { }

    public function getOrderedByBoardListId(int $boardListId): object
    {
        return $this->model->where('board_list_id', $boardListId)->orderBy('order')->get();
    }

    public function updateTaskOrderOrGroup(array $orderedData): void
    {
        foreach ($orderedData as $group) {
            foreach ($group['items'] as $item)
            {
                $boardList = $this->boardListService->find((int) $group['value']);

                ListTask::find($item['value'])->update([
                    "order" => $item['order'],
                    "board_list_id" => $boardList->id
                ]);

            }
        }
    }

    public function deleteByBoardListId(int $boardListId): void
    {
        $this->model->where('board_list_id', $boardListId)->delete();
    }
}
