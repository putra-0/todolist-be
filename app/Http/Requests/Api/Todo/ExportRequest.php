<?php

namespace App\Http\Requests\Api\Todo;

use Illuminate\Foundation\Http\FormRequest;

class ExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['bail', 'nullable', 'string'],
            'assignees' => ['bail', 'nullable', 'string'],
            'statuses' => ['bail', 'nullable', 'string'],
            'priorities' => ['bail', 'nullable', 'string'],
            'start' => ['bail', 'nullable','required_with:end', 'date', 'before_or_equal:end'],
            'end' => ['bail', 'nullable','required_with:start', 'date'],
            'min' => ['bail', 'nullable', 'numeric', 'min:0'],
            'max' => ['bail', 'nullable', 'numeric', 'gte:min'],
        ];
    }
}
