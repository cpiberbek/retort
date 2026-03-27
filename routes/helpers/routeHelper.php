<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('registerRecycleRoutes')) {
    function registerRecycleRoutes(string $prefix, string $controller): void
    {
        Route::prefix($prefix)->name("$prefix.")->group(function () use ($controller) {

            if (method_exists($controller, 'recyclebin')) {
                Route::get('recycle-bin', [$controller, 'recyclebin'])
                ->name('recyclebin');
            }

            if (method_exists($controller, 'restore')) {
                Route::post('restore/{uuid}', [$controller, 'restore'])
                ->name('restore');
            }

            if (method_exists($controller, 'deletePermanent')) {
                Route::delete('delete-permanent/{uuid}', [$controller, 'deletePermanent'])
                ->name('deletePermanent');
            }
        });
    }
}

