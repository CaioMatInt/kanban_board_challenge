<?php

namespace App\Traits\Database;

use Illuminate\Database\Eloquent\Model;

trait FindableTrait
{

    /**
     * Find a model by its primary key.
     *
     * @param int $id
     * @return object
     */
    public function find(int $id): object
    {
        return $this->model->find($id);
    }
}
