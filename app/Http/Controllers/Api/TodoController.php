<?php

namespace App\Http\Controllers\Api;

use App\Exports\TodoExport;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Todo\ExportRequest;
use App\Http\Requests\Api\Todo\StoreRequest;
use App\Models\Todo;

class TodoController extends Controller
{
    public function store(StoreRequest $request)
    {
        $todo = Todo::create($request->validated());

        return response()->json($todo, 201);
    }

    public function export(ExportRequest $request)
    {
        $assignees = StringHelper::explodeCommaSeparated(
            $request->input('assignees'),
        );

        $statuses = StringHelper::explodeCommaSeparated(
            $request->input('statuses'),
        );

        $priorities = StringHelper::explodeCommaSeparated(
            $request->input('priorities'),
        );

        return (new TodoExport(
            title: $request->input('title'),
            assignees: $assignees,
            statuses: $statuses,
            priorities: $priorities,
            start: $request->date('start'),
            end: $request->date('end'),
            min: $request->filled('min') ? $request->integer('min') : null,
            max: $request->filled('max') ? $request->integer('max') : null,
        ))->download('todos.csv');
    }
}
