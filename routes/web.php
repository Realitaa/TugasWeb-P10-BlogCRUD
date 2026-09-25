<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/posts');
Route::resource('posts', PostController::class)->withTrashed(['show']);
