<?php

declare(strict_types=1);

use CmsOrbit\Blog\Http\Controllers\BlogInstanceSsoController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('admin')->as('blog.admin.')->group(function (): void {
    Route::get('sso', BlogInstanceSsoController::class)->name('sso');
});
