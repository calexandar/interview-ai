<?php

use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;

/**
 * The conduct page is reachable for an interview that has not been started
 * yet. It has to render, and it has to tell the truth about whether the
 * interview can be started from here.
 */
test('a scheduled interview renders a conduct page with no current question', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    $response = $this->actingAs($user)
        ->get(route('interviews.conduct', $interview))
        ->assertOk();

    $props = $response->viewData('page')['props'];

    expect($props['interview']['status'])->toBe(InterviewStatus::Scheduled->value)
        ->and($props['interview']['canStart'])->toBeTrue()
        ->and($props['interview']['startedAt'])->toBeNull()
        ->and($props['currentQuestion'])->toBeNull();
});

test('a started interview cannot be started again from the conduct page', function () {
    $user = interviewUser();
    [$interview] = interviewWithAnswer($user);

    $response = $this->actingAs($user)
        ->get(route('interviews.conduct', $interview))
        ->assertOk();

    $props = $response->viewData('page')['props'];

    expect($props['interview']['canStart'])->toBeFalse()
        ->and($props['currentQuestion'])->not->toBeNull();
});

test('a completed interview cannot be started', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    $interview->update([
        'status' => InterviewStatus::Completed,
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('interviews.conduct', $interview))
        ->assertOk();

    expect($response->viewData('page')['props']['interview']['canStart'])->toBeFalse();
});

test('starting from the conduct page leaves the interview running with a question', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    $ai = fakeAi()->nextQuestion(question: 'Explain query scopes.');

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertRedirect(route('interviews.conduct', $interview));

    $interview->refresh();

    $current = $interview->activeQuestion();

    expect($interview->status)->toBe(InterviewStatus::InProgress)
        ->and($interview->started_at)->not->toBeNull()
        ->and($current)->not->toBeNull()
        ->and($current->status)->toBe(QuestionStatus::Asking)
        ->and($current->source)->toBe(QuestionSource::AI)
        ->and($current->question_text)->toBe('Explain query scopes.');

    // The button must disappear once the interview is running.
    $this->actingAs($user)
        ->get(route('interviews.conduct', $interview))
        ->assertOk()
        ->assertViewHas('page', fn (array $page): bool => $page['props']['interview']['canStart'] === false);
});
