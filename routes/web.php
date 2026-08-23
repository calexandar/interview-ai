<?php

use App\Candidates\CreateCandidate\CandidateController;
use App\Interviewing\CreateInterview\InterviewController;
use App\Positions\CreatePosition\PositionController;
use App\Positions\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::apiResource('positions', PositionController::class)->only(['store']);
    Route::apiResource('candidates', CandidateController::class)->only(['store']);
    Route::apiResource('interviews', InterviewController::class)->only(['store']);
});

require __DIR__.'/settings.php';
