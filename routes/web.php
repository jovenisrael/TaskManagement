<?php

use App\Http\Controllers\ProjectPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProjectPageController::class, 'index'])->name('projects.index');
