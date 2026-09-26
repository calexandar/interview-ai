<?php

use App\Candidates\CreateCandidate\CandidateController;
use App\Dashboard\DashboardController;
use App\Interviewing\Conduct\InterviewConductController;
use App\Interviewing\CreateInterview\InterviewController;
use App\Interviewing\EndInterview\EndInterviewController;
use App\Interviewing\GenerateQuestion\GenerateQuestionController;
use App\Interviewing\MoveToNextQuestion\MoveToNextQuestionController;
use App\Interviewing\PauseInterview\PauseInterviewController;
use App\Interviewing\ResumeInterview\ResumeInterviewController;
use App\Interviewing\RetryAnswerEvaluation\RetryAnswerEvaluationController;
use App\Interviewing\SkipQuestion\SkipQuestionController;
use App\Interviewing\StartInterview\StartInterviewController;
use App\Interviewing\SubmitAnswer\SubmitAnswerController;
use App\Positions\CreatePosition\PositionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::apiResource('positions', PositionController::class)->only(['store']);
    Route::apiResource('candidates', CandidateController::class)->only(['store']);
    Route::apiResource('interviews', InterviewController::class)->only(['store']);

    Route::get('interviews/{interview}/conduct', InterviewConductController::class)
        ->name('interviews.conduct');
    Route::post('interviews/{interview}/start', StartInterviewController::class)
        ->name('interviews.start');
    Route::post('interviews/{interview}/answer', SubmitAnswerController::class)
        ->name('interviews.answer');
    Route::post('interviews/{interview}/next', MoveToNextQuestionController::class)
        ->name('interviews.next');
    Route::post('interviews/{interview}/skip', SkipQuestionController::class)
        ->name('interviews.skip');
    Route::post('interviews/{interview}/pause', PauseInterviewController::class)
        ->name('interviews.pause');
    Route::post('interviews/{interview}/resume', ResumeInterviewController::class)
        ->name('interviews.resume');
    Route::post('interviews/{interview}/end', EndInterviewController::class)
        ->name('interviews.end');
    Route::post('interviews/{interview}/questions/generate', GenerateQuestionController::class)
        ->name('interviews.questions.generate');
    Route::post('interviews/{interview}/retry-evaluation', RetryAnswerEvaluationController::class)
        ->name('interviews.retry-evaluation');
});

require __DIR__.'/settings.php';
