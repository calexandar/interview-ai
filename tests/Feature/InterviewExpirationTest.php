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

function expirationUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createExpiredInterview(User $user): array
{
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
        'question_count' => 3,
        'duration_minutes' => 30,
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
        'started_at' => now()->subHours(2),
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
        'asked_at' => now()->subHours(2),
    ]);

    $interview->update(['current_question_id' => $interviewQuestion->id]);

    return compact('interview', 'interviewQuestion');
}

test('it rejects answers when interview has expired', function () {
    $user = expirationUser();
    ['interview' => $interview, 'interviewQuestion' => $question] =
        createExpiredInterview($user);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Answer',
        ])->assertStatus(422);

    $interview->refresh();
    expect($interview->status)->toBe(InterviewStatus::Expired);
});

test('expired interview does not accept new questions', function () {
    $user = expirationUser();
    ['interview' => $interview] = createExpiredInterview($user);

    $interview->update(['status' => InterviewStatus::Expired]);

    $this->actingAs($user)
        ->post(route('interviews.next', $interview))
        ->assertStatus(422);
});

test('expired interview cannot be started', function () {
    $user = expirationUser();
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
    ]);
    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);
    $interview = Interview::factory()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'status' => InterviewStatus::Expired,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertStatus(422);
});

test('it checks expiration on answer submission', function () {
    $user = expirationUser();
    ['interview' => $interview, 'interviewQuestion' => $question] =
        createExpiredInterview($user);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Answer',
        ])->assertStatus(422);

    $interview->refresh();
    expect($interview->status)->toBe(InterviewStatus::Expired);
});
