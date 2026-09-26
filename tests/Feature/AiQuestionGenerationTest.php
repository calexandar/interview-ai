<?php

use App\Models\InterviewQuestion;
use App\Models\Skill;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;

test('it generates the first question with the AI when starting an interview', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    $ai = fakeAi()->nextQuestion(
        question: 'Walk me through diagnosing an N+1 query in a queued job.',
        difficulty: 'medium',
        evaluationCriteria: ['query log', 'eager loading'],
    );

    $this->actingAs($user)->post(route('interviews.start', $interview))->assertRedirect();

    $interview->refresh();

    $current = $interview->currentQuestion()->first();

    expect($current)->not->toBeNull()
        ->and($current->source)->toBe(QuestionSource::AI)
        ->and($current->question_text)->toBe('Walk me through diagnosing an N+1 query in a queued job.')
        ->and($current->status)->toBe(QuestionStatus::Asking)
        ->and($current->asked_at)->not->toBeNull()
        ->and($interview->current_question_id)->toBe($current->id);

    expect($ai->generateQuestionCount())->toBe(1);
});

test('the generated question is attributed to the AI source and carries the rubric', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    fakeAi()->nextQuestion(evaluationCriteria: ['query log', 'eager loading']);

    $this->actingAs($user)->post(route('interviews.start', $interview));

    $generated = InterviewQuestion::where('interview_id', $interview->id)
        ->where('source', QuestionSource::AI)
        ->firstOrFail();

    // An AI question has no question-bank question behind it.
    expect($generated->question_id)->toBeNull()
        ->and($generated->evaluationCriteria())->toBe(['query log', 'eager loading'])
        ->and($generated->generation_metadata['provider'])->toBe('fake');
});

test('it falls back to a question bank question when the provider is unavailable', function () {
    $user = interviewUser();
    ['interview' => $interview, 'skill' => $skill] = interviewWithSkill($user);

    $ai = fakeAi();
    $ai->throwOnGenerate();

    $this->actingAs($user)->post(route('interviews.start', $interview))->assertRedirect();

    $interview->refresh();
    $current = $interview->currentQuestion()->first();

    expect($current)->not->toBeNull()
        ->and($current->source)->toBe(QuestionSource::QuestionBank)
        ->and($current->skill_id)->toBe($skill->id)
        ->and($current->status)->toBe(QuestionStatus::Asking);

    // The candidate is never shown a provider error: the interview started.
    expect($interview->status)->toBe(InterviewStatus::InProgress);
});

test('the AI cannot introduce a skill the position does not require', function () {
    $user = interviewUser();
    ['interview' => $interview, 'skill' => $skill] = interviewWithSkill($user);

    $otherSkill = Skill::factory()->create(['name' => 'Kubernetes']);

    fakeAi()->nextQuestion(skill: $otherSkill->name);

    $this->actingAs($user)->post(route('interviews.start', $interview));

    $generated = InterviewQuestion::where('interview_id', $interview->id)
        ->where('source', QuestionSource::AI)
        ->firstOrFail();

    // The model named a skill this role does not require, so the plan wins.
    expect($generated->skill_id)->toBe($skill->id);
});

test('it keeps asking the AI for questions as the interview advances', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    $ai = fakeAi();
    $ai->nextQuestion(question: 'First AI question.');
    $ai->nextQuestion(question: 'Second AI question.');

    $this->actingAs($user)->post(route('interviews.start', $interview));

    $current = $interview->fresh()->currentQuestion()->first();
    expect($current->question_text)->toBe('First AI question.');

    $this->actingAs($user)->post(route('interviews.next', $interview))->assertRedirect();

    expect($ai->generateQuestionCount())->toBe(2);

    $current = $interview->fresh()->currentQuestion()->first();
    expect($current->question_text)->toBe('Second AI question.');
});

test('an unconfigured provider never leaves the interview without a question', function () {
    $user = interviewUser();
    ['interview' => $interview] = interviewWithSkill($user);

    // No fake is bound, so the real provider is used and cannot reach anyone.
    // The interview must still start rather than surface a provider failure.
    config()->set('ai.default', 'nonexistent-driver');

    $this->actingAs($user)->post(route('interviews.start', $interview));

    $interview->refresh();

    expect($interview->status)->toBe(InterviewStatus::InProgress)
        ->and($interview->current_question_id)->not->toBeNull();
});
