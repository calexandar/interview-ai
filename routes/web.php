<?php

use App\Candidates\CreateCandidate\CandidateController;
use App\Dashboard\DashboardController;
use App\Interviewing\CreateInterview\InterviewController;
use App\Interviewing\EndInterview\EndInterviewController;
use App\Positions\CreatePosition\PositionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::apiResource('positions', PositionController::class)->only(['store']);
    Route::apiResource('candidates', CandidateController::class)->only(['store']);
    Route::apiResource('interviews', InterviewController::class)->only(['store']);
    Route::post('interviews/{interview}/end', EndInterviewController::class)
        ->name('interviews.end');
});

require __DIR__.'/settings.php';
