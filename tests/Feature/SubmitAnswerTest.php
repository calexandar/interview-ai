<?php

use App\Models\Answer;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Organization;
use App\Models\Position;
use App\Models\Question;
use App\Models\Skill;
use App\Models\User;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;

function submitAnswerUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createInProgressInterviewWithQuestion(User $user): array
{
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
        'question_count' => 3,
    ]);

    $skill = Skill::factory()->create();
    $position->skills()->attach($skill->id);

    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $interview = Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'total_questions' => 3,
    ]);

    $question = Question::factory()->forSkill($skill)->create();
    $interviewQuestion = InterviewQuestion::create([
        'interview_id' => $interview->id,
        'question_id' => $question->id,
        'position' => 1,
        'skill_id' => $skill->id,
        'difficulty' => $question->difficulty,
        'question_text' => $question->question,
        'status' => QuestionStatus::Asking,
        'asked_at' => now(),
    ]);

    $interview->update(['current_question_id' => $interviewQuestion->id]);

    return compact('interview', 'interviewQuestion', 'candidate');
}

test('guests are redirected to login when submitting an answer', function () {
    $interview = Interview::factory()->create();
    $question = InterviewQuestion::factory()->create(['interview_id' => $interview->id]);

    $this->post(route('interviews.answer', $interview), [
        'question_id' => $question->id,
        'content' => 'Test answer',
    ])->assertRedirect(route('login'));
});

test('it submits an answer for the current question', function () {
    $user = submitAnswerUser();
    ['interview' => $interview, 'interviewQuestion' => $question, 'candidate' => $candidate] =
        createInProgressInterviewWithQuestion($user);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'My answer to the question',
        ])->assertRedirect();

    $question->refresh();
    expect($question->status)->toBe(QuestionStatus::Answered);
    expect($question->answered_at)->not->toBeNull();

    $answer = Answer::where('interview_question_id', $question->id)->first();
    expect($answer)->not->toBeNull();
    expect($answer->content)->toBe('My answer to the question');
    expect($answer->candidate_id)->toBe($candidate->id);

    $interview->refresh();
    expect($interview->question_index)->toBe(1);
});

test('it prevents duplicate answers for the same question', function () {
    $user = submitAnswerUser();
    ['interview' => $interview, 'interviewQuestion' => $question] =
        createInProgressInterviewWithQuestion($user);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'First answer',
        ]);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Second answer',
        ])->assertStatus(422);
});

test('it rejects answers for questions not in asking state', function () {
    $user = submitAnswerUser();
    ['interview' => $interview] = createInProgressInterviewWithQuestion($user);

    $pendingQuestion = InterviewQuestion::factory()->create([
        'interview_id' => $interview->id,
        'status' => QuestionStatus::Pending,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $pendingQuestion->id,
            'content' => 'Answer',
        ])->assertStatus(422);
});

test('it rejects answers after interview completion', function () {
    $user = submitAnswerUser();
    ['interview' => $interview, 'interviewQuestion' => $question] =
        createInProgressInterviewWithQuestion($user);

    $interview->update(['status' => InterviewStatus::Completed]);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Answer',
        ])->assertStatus(422);
});

test('it rejects answers after interview expiration', function () {
    $user = submitAnswerUser();
    ['interview' => $interview, 'interviewQuestion' => $question] =
        createInProgressInterviewWithQuestion($user);

    $interview->update([
        'started_at' => now()->subHours(2),
    ]);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Answer',
        ])->assertStatus(422);
});

test('it rejects answers for questions from another interview', function () {
    $user = submitAnswerUser();
    ['interview' => $interview] = createInProgressInterviewWithQuestion($user);

    $otherInterview = Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
    ]);
    $otherQuestion = InterviewQuestion::factory()->create([
        'interview_id' => $otherInterview->id,
        'status' => QuestionStatus::Asking,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $otherQuestion->id,
            'content' => 'Answer',
        ])->assertNotFound();
});

test('it cannot submit answers for another organizations interview', function () {
    $user = submitAnswerUser();
    ['interview' => $interview, 'interviewQuestion' => $question] =
        createInProgressInterviewWithQuestion($user);

    $otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $this->actingAs($otherUser)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Answer',
        ])->assertNotFound();
});
