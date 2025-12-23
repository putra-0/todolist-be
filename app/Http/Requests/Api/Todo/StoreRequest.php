<?php

namespace App\Http\Requests\Api\Todo;

use App\Enums\TodoPriority;
use App\Enums\TodoStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:100'],
            'assignee' => ['bail', 'sometimes', 'nullable', 'string', 'max:100'],
            'due_date' => ['bail', 'required', 'date', 'after_or_equal:today'],
            'time_tracked' => ['bail', 'sometimes', 'nullable', 'numeric', 'min:0'],
            'status' => ['bail', 'sometimes', 'nullable', Rule::enum(TodoStatus::class)],
            'priority' => ['bail', 'required', Rule::enum(TodoPriority::class)],
        ];
    }
}
