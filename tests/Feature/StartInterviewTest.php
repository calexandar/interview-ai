<?php

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

function startInterviewUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createInterviewWithQuestions(User $user, int $questionCount = 3): Interview
{
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
        'question_count' => $questionCount,
        'duration_minutes' => 30,
    ]);

    $skill = Skill::factory()->create();
    $position->skills()->attach($skill->id);

    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $interview = Interview::factory()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'status' => InterviewStatus::Scheduled,
        'total_questions' => $questionCount,
    ]);

    for ($i = 1; $i <= $questionCount; $i++) {
        $question = Question::factory()->forSkill($skill)->create();
        InterviewQuestion::create([
            'interview_id' => $interview->id,
            'question_id' => $question->id,
            'position' => $i,
            'skill_id' => $skill->id,
            'difficulty' => $question->difficulty,
            'question_text' => $question->question,
            'status' => QuestionStatus::Pending,
        ]);
    }

    return $interview;
}

test('guests are redirected to login when starting an interview', function () {
    $interview = Interview::factory()->create();

    $this->post(route('interviews.start', $interview))
        ->assertRedirect(route('login'));
});

test('it starts a scheduled interview', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertRedirect();

    $interview->refresh();
    expect($interview->status)->toBe(InterviewStatus::InProgress);
    expect($interview->started_at)->not->toBeNull();
});

test('it activates the first question when starting', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview));

    $interview->refresh();
    $firstQuestion = $interview->interviewQuestions()->orderBy('position')->first();

    expect($interview->current_question_id)->toBe($firstQuestion->id);
    expect($firstQuestion->status)->toBe(QuestionStatus::Asking);
    expect($firstQuestion->asked_at)->not->toBeNull();
});

test('it selects questions from position skills', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user, 5);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview));

    $interview->refresh();
    expect($interview->interviewQuestions()->count())->toBe(5);
});

test('it cannot start a completed interview', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user);
    $interview->update(['status' => InterviewStatus::Completed]);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertStatus(422);
});

test('it cannot start a cancelled interview', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user);
    $interview->update(['status' => InterviewStatus::Cancelled]);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertStatus(422);
});

test('it cannot start an already in-progress interview', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user);
    $interview->update(['status' => InterviewStatus::InProgress, 'started_at' => now()]);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertStatus(422);
});

test('it cannot start interviews from another organization', function () {
    $user = startInterviewUser();
    $interview = createInterviewWithQuestions($user);

    $otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $this->actingAs($otherUser)
        ->post(route('interviews.start', $interview))
        ->assertNotFound();
});
