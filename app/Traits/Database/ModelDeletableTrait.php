<?php

namespace App\Traits\Database;

use Illuminate\Database\Eloquent\Model;

trait ModelDeletableTrait
{

    /**
     * Delete a record from the database.
     *
     * @param mixed $model
     * @return bool
     */
    public function deleteRecord(Model $model): bool
    {
        return $model->delete();
    }
}
