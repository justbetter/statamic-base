<?php

use Illuminate\Support\Facades\Route;
use JustBetter\StatamicBase\Http\Controllers\CP\PackagesController;

Route::prefix('justbetter')
    ->middleware('justbetter.packages')
    ->group(function () {
        Route::get('/packages', [PackagesController::class, 'index'])->name('justbetter.packages.index');
    });
