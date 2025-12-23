<?php

namespace App\Http\Requests\Api\Chart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetSummaryTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['bail', 'required', 'string', Rule::in(['status', 'priority', 'assignee'])],
        ];
    }
}
