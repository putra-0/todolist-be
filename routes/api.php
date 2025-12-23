<?php

use App\Http\Controllers\Api\TodoController;
use Illuminate\Support\Facades\Route;

Route::controller(TodoController::class)
    ->prefix('todos')
    ->group(function () {
        Route::post('', 'store');
    });
