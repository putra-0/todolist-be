<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Todo\StoreRequest;
use App\Http\Controllers\Controller;
use App\Models\Todo;

class TodoController extends Controller
{
    public function store(StoreRequest $request)
    {
        $todo = Todo::create($request->validated());

        return response()->json($todo, 201);
    }
}
