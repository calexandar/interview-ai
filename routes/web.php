<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::apiResource('positions', PositionController::class)->only(['store']);
    Route::apiResource('candidates', CandidateController::class)->only(['store']);
    Route::apiResource('interviews', InterviewController::class)->only(['store']);
});

require __DIR__.'/settings.php';
