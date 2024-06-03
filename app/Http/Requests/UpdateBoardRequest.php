<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoardRequest extends FormRequest
{
    protected $boardId;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|min:5|unique:boards,name,' . $this->boardId . ',id',
            'color_hash' => 'sometimes|string',
        ];
    }

    public function setBoardId($id)
    {
        $this->boardId = $id;
    }
}
