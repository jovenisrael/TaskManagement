<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::get('/projects/{id}', [ProjectController::class, 'show'])->whereNumber('id');
Route::put('/projects/{id}', [ProjectController::class, 'update'])->whereNumber('id');
Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->whereNumber('id');
