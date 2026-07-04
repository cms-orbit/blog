<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('blog::welcome', [
        'message' => 'Blog instance is running.',
    ]);
})->name('home');
