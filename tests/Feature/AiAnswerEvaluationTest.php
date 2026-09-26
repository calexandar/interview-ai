<?php

use App\AI\Data\AnswerEvaluation;
use App\AI\Exceptions\AIUnavailableException;
use App\Interviewing\EvaluateAnswer\EvaluateAnswerHandler;
use App\Models\Evaluation;
use App\Models\SkillAssessment;
use App\Shared\Enums\EvaluationStatus;
use App\Shared\Enums\QuestionStatus;
use RuntimeException;

test('submitting an answer evaluates it with the AI', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    $ai = fakeAi()->nextEvaluation(new AnswerEvaluation(
        score: 8.5,
        confidence: 0.9,
        strengths: ['Correctly identified eager loading'],
        weaknesses: ['Did not mention indexes'],
        missingConcepts: ['indexing'],
        evidence: ['Candidate named eager loading unprompted.'],
        followUpRequired: true,
        reasoningSummary: 'Strong practical answer.',
    ));

    $this->actingAs($user)->post(route('interviews.answer', $interview), [
        'question_id' => $question->id,
        'content' => 'I would enable eager loading.',
    ])->assertRedirect();

    $evaluation = Evaluation::where('answer_id', $question->fresh()->answer->id)->firstOrFail();

    expect($evaluation->status)->toBe(EvaluationStatus::Completed)
        ->and((float) $evaluation->score)->toBe(8.5)
        ->and((float) $evaluation->confidence)->toBe(0.9)
        ->and($evaluation->strengths)->toBe(['Correctly identified eager loading'])
        ->and($evaluation->weaknesses)->toBe(['Did not mention indexes'])
        ->and($evaluation->missing_topics)->toBe(['indexing'])
        ->and($evaluation->evidence)->toBe(['Candidate named eager loading unprompted.'])
        ->and($evaluation->follow_up_required)->toBeTrue()
        ->and($evaluation->attempts)->toBe(1)
        ->and($evaluation->failed_at)->toBeNull();

    expect($ai->evaluateAnswerCount())->toBe(1);
});

test('the answer is saved even when the provider is unavailable', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    fakeAi()->throwOnEvaluate();

    // The candidate is not shown an error: their answer is already safe.
    $this->actingAs($user)->post(route('interviews.answer', $interview), [
        'question_id' => $question->id,
        'content' => 'My answer that must not be lost.',
    ])->assertRedirect();

    $answer = $question->fresh()->answer;

    expect($answer)->not->toBeNull()
        ->and($answer->content)->toBe('My answer that must not be lost.')
        ->and($question->fresh()->status)->toBe(QuestionStatus::Answered);
});

test('a failed first attempt leaves no fabricated result and can be retried', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    $ai = fakeAi();
    $ai->throwOnEvaluate(category: 'rate_limited');

    $this->actingAs($user)->post(route('interviews.answer', $interview), [
        'question_id' => $question->id,
        'content' => 'First attempt.',
    ])->assertRedirect();

    $answer = $question->fresh()->answer;

    // Nothing was invented: with no row there is simply no evaluation yet.
    expect(Evaluation::where('answer_id', $answer->id)->exists())->toBeFalse();

    $ai->nextEvaluation(new AnswerEvaluation(
        score: 6.0,
        confidence: 0.7,
        strengths: [],
        weaknesses: ['Vague'],
        missingConcepts: [],
        evidence: [],
    ));

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ])->assertRedirect();

    $evaluation = Evaluation::where('answer_id', $answer->id)->firstOrFail();

    expect($evaluation->status)->toBe(EvaluationStatus::Completed)
        ->and((float) $evaluation->score)->toBe(6.0);
});

test('a failed retry records the reason against the existing evaluation', function () {
    $user = interviewUser();
    [$interview, , $answer] = interviewWithAnswer($user);

    Evaluation::factory()->failed()->create([
        'answer_id' => $answer->id,
        'score' => 5.0,
        'confidence' => 0.4,
        'attempts' => 1,
    ]);

    fakeAi()->throwOnEvaluate(category: 'provider_timeout');

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ]);

    $evaluation = $answer->fresh()->evaluation;

    expect($evaluation->status)->toBe(EvaluationStatus::Failed)
        ->and($evaluation->failure_reason)->toBe('provider_timeout')
        ->and($evaluation->failed_at)->not->toBeNull()
        ->and($evaluation->attempts)->toBe(2);
});

test('a completed evaluation is never demoted by a later failure', function () {
    $user = interviewUser();
    [$interview, , $answer] = interviewWithAnswer($user);

    $evaluation = Evaluation::factory()->create([
        'answer_id' => $answer->id,
        'score' => 9.0,
        'confidence' => 0.95,
    ]);

    fakeAi()->throwOnEvaluate();

    // The retry route blocks this outright, so the invariant is asserted on
    // the handler that owns it: a good result survives a later outage.
    app(EvaluateAnswerHandler::class)->recordFailure(
        $answer,
        new AIUnavailableException('unavailable', 'The AI provider is unavailable.'),
    );

    $evaluation->refresh();

    expect($evaluation->status)->toBe(EvaluationStatus::Completed)
        ->and((float) $evaluation->score)->toBe(9.0)
        ->and($evaluation->failure_reason)->toBeNull()
        ->and($evaluation->failed_at)->toBeNull();
});

test('an answer cannot be retried once it already has a result', function () {
    $user = interviewUser();
    [$interview, , $answer] = interviewWithAnswer($user);

    Evaluation::factory()->create([
        'answer_id' => $answer->id,
        'score' => 7.0,
        'confidence' => 0.8,
    ]);

    $ai = fakeAi();

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ])->assertStatus(422);

    expect($ai->evaluateAnswerCount())->toBe(0);
});

test('an answer from another interview cannot be retried', function () {
    $user = interviewUser();
    [$interview] = interviewWithAnswer($user);
    [, , $otherAnswer] = interviewWithAnswer($user);

    fakeAi();

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $otherAnswer->id,
    ])->assertStatus(404);
});

test('another organization cannot retry an evaluation', function () {
    $user = interviewUser();
    [$interview, , $answer] = interviewWithAnswer($user);

    $otherUser = interviewUser();

    fakeAi();

    $this->actingAs($otherUser)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ])->assertStatus(404);
});

test('an answer to a question that was never asked is out of scope', function () {
    $user = interviewUser();
    [$interview, , $answer] = interviewWithAnswer($user);

    // The question went back to Pending, so it was never actually put to the
    // candidate. Its answer is not one this interview will assess.
    $answer->interviewQuestion()->update(['status' => QuestionStatus::Pending]);

    $ai = fakeAi();

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ])->assertStatus(404);

    expect($ai->evaluateAnswerCount())->toBe(0);
});

test('a skipped question can still be evaluated', function () {
    $user = interviewUser();
    [$interview, $question, $answer] = interviewWithAnswer($user);

    $question->update(['status' => QuestionStatus::Skipped]);

    $ai = fakeAi();
    $ai->nextEvaluation(new AnswerEvaluation(
        score: 4.0,
        confidence: 0.5,
        strengths: [],
        weaknesses: ['Thin'],
        missingConcepts: [],
        evidence: [],
    ));

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ])->assertRedirect();

    expect($answer->fresh()->evaluation->status)->toBe(EvaluationStatus::Completed);
});

test('the AI is given the question, the rubric and the answer', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    $ai = fakeAi();

    $this->actingAs($user)->post(route('interviews.answer', $interview), [
        'question_id' => $question->id,
        'content' => 'A careful, specific answer.',
    ]);

    $context = $ai->lastEvaluationContext();

    expect($context->candidateAnswer)->toBe('A careful, specific answer.')
        ->and($context->question)->toBe($question->question_text)
        ->and($context->skill)->toBe($question->skill->name)
        ->and($context->jobTitle)->toBe($interview->position->title);
});

test('a provider outage never bounces the candidate back with a validation error', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    fakeAi()->throwOnEvaluate(category: 'rate_limited');

    // An outage is not the candidate's mistake, so the submission is accepted
    // rather than returned as invalid input.
    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'A good answer.',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('an unexpected provider error does not destroy the submitted answer', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    // The provider boundary normalizes every failure into
    // AIUnavailableException, so a raw throw means a bug rather than an
    // outage. It must still surface loudly.
    fakeAi()->throwRawOnEvaluate(new RuntimeException('Boom.'));

    $this->withoutExceptionHandling()->expectException(RuntimeException::class);

    try {
        $this->actingAs($user)->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Still saved.',
        ]);
    } finally {
        $answer = $question->fresh()->answer;

        expect($answer)->not->toBeNull()
            ->and($answer->content)->toBe('Still saved.')
            ->and($question->fresh()->status)->toBe(QuestionStatus::Answered)
            ->and(Evaluation::where('answer_id', $answer->id)->exists())->toBeFalse();
    }
});

test('a failed evaluation flashes so the page stays put and offers a retry', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    fakeAi()->throwOnEvaluate();

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'A good answer.',
        ])
        ->assertRedirect()
        ->assertSessionHas('inertia.flash_data', ['evaluation_failed' => true]);
});

test('a successful evaluation does not flash a failure', function () {
    $user = interviewUser();
    [$interview, $question] = interviewAwaitingAnswer($user);

    $ai = fakeAi();
    $ai->nextEvaluation(new AnswerEvaluation(
        score: 7.5,
        confidence: 0.8,
        strengths: [],
        weaknesses: [],
        missingConcepts: [],
        evidence: [],
    ));

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'A good answer.',
        ])
        ->assertRedirect()
        ->assertSessionMissing('inertia.flash_data');
});

test('a completed evaluation rolls a skill assessment forward', function () {
    $user = interviewUser();
    [$interview, $question, $skill] = interviewAwaitingAnswer($user);

    $ai = fakeAi();
    $ai->nextEvaluation(new AnswerEvaluation(
        score: 8.0,
        confidence: 0.85,
        strengths: [],
        weaknesses: [],
        missingConcepts: [],
        evidence: ['Named the right tool.'],
    ));

    $this->actingAs($user)->post(route('interviews.answer', $interview), [
        'question_id' => $question->id,
        'content' => 'I would reach for the query log first.',
    ])->assertRedirect();

    $assessment = SkillAssessment::where('interview_id', $interview->id)
        ->where('skill_id', $skill->id)
        ->firstOrFail();

    expect((float) $assessment->score)->toBe(8.0)
        ->and((float) $assessment->confidence)->toBe(0.85)
        ->and($assessment->questions_answered)->toBe(1)
        ->and($assessment->evidence)->toBe(['Named the right tool.']);
});

test('a failed evaluation does not move the skill assessment', function () {
    $user = interviewUser();
    [$interview, , $answer, $skill] = interviewWithAnswer($user);

    fakeAi()->throwOnEvaluate();

    $this->actingAs($user)->post(route('interviews.retry-evaluation', $interview), [
        'answer_id' => $answer->id,
    ]);

    expect(SkillAssessment::where('interview_id', $interview->id)->exists())->toBeFalse();
});

test('the conduct page never exposes the rubric or the score', function () {
    $user = interviewUser();
    [$interview, , $answer] = interviewWithAnswer($user);

    Evaluation::factory()->create([
        'answer_id' => $answer->id,
        'score' => 3.25,
        'confidence' => 0.42,
    ]);

    $response = $this->actingAs($user)
        ->get(route('interviews.conduct', $interview))
        ->assertOk();

    $payload = $response->viewData('page')['props']['currentQuestion'];

    expect($payload)->toHaveKeys(['id', 'text', 'skill', 'difficulty', 'status', 'evaluationState'])
        ->and($payload)->not->toHaveKey('evaluationCriteria')
        ->and($payload)->not->toHaveKey('score')
        ->and($payload)->not->toHaveKey('confidence')
        ->and($payload['evaluationState'])->toBe('completed');

    expect($response->getContent())->not->toContain('3.25');
});
