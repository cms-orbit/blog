<?php

declare(strict_types=1);

use CmsOrbit\Blog\Http\Controllers\CategoryController;
use CmsOrbit\Blog\Http\Controllers\FeedController;
use CmsOrbit\Blog\Http\Controllers\PageController;
use CmsOrbit\Blog\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/feed', FeedController::class)->name('feed');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/{slug}', [PostController::class, 'show'])
    ->name('posts.show')
    ->where('slug', '[a-z0-9-]+');
