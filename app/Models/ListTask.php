<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'order',
        'board_list_id'
    ];

    public function list()
    {
        return $this->belongsTo(BoardList::class, 'board_list_id', 'id');
    }
}
