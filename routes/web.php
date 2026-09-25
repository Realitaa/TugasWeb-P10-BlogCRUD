<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/posts');
Route::get('posts/search', [PostController::class, 'search'])->name('posts.search');
Route::resource('posts', PostController::class)->withTrashed(['show']);
