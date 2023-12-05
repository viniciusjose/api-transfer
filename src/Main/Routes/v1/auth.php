<?php

use Illuminate\Support\Facades\Route;
use Src\Application\Controllers\Auth\AuthController;

Route::controller(AuthController::class)
    ->prefix('auth/')
    ->name('auth.')
    ->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::post('user-info', 'me')->name('me');
    });
