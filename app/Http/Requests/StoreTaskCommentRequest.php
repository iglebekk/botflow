<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('comment', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
            'is_delivery' => ['sometimes', 'boolean'],
        ];
    }
}
