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

function moveNextUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createInterviewWithMultipleQuestions(User $user): array
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

    $questions = [];
    for ($i = 1; $i <= 3; $i++) {
        $question = Question::factory()->forSkill($skill)->create();
        $questions[$i] = InterviewQuestion::create([
            'interview_id' => $interview->id,
            'question_id' => $question->id,
            'position' => $i,
            'skill_id' => $skill->id,
            'difficulty' => $question->difficulty,
            'question_text' => $question->question,
            'status' => QuestionStatus::Pending,
        ]);
    }

    $questions[1]->update([
        'status' => QuestionStatus::Answered,
        'asked_at' => now()->subMinutes(5),
        'answered_at' => now(),
    ]);

    $questions[2]->update([
        'status' => QuestionStatus::Asking,
        'asked_at' => now(),
    ]);

    $interview->update([
        'current_question_id' => $questions[2]->id,
        'question_index' => 1,
    ]);

    return compact('interview', 'questions');
}

test('guests are redirected to login when moving to next question', function () {
    $interview = Interview::factory()->create();

    $this->post(route('interviews.next', $interview))
        ->assertRedirect(route('login'));
});

test('it moves to the next question after answering', function () {
    $user = moveNextUser();
    ['interview' => $interview, 'questions' => $questions] =
        createInterviewWithMultipleQuestions($user);

    $this->actingAs($user)
        ->post(route('interviews.next', $interview))
        ->assertRedirect();

    $questions[2]->refresh();
    $questions[3]->refresh();
    $interview->refresh();

    expect($questions[2]->status)->toBe(QuestionStatus::Answered);
    expect($questions[3]->status)->toBe(QuestionStatus::Asking);
    expect($interview->current_question_id)->toBe($questions[3]->id);
});

test('it completes the interview when all questions are answered', function () {
    $user = moveNextUser();
    ['interview' => $interview, 'questions' => $questions] =
        createInterviewWithMultipleQuestions($user);

    $questions[3]->update([
        'status' => QuestionStatus::Asking,
        'asked_at' => now(),
    ]);

    $questions[2]->update([
        'status' => QuestionStatus::Answered,
        'answered_at' => now(),
    ]);

    $interview->update([
        'current_question_id' => $questions[3]->id,
        'question_index' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.next', $interview))
        ->assertRedirect(route('dashboard'));

    $interview->refresh();
    expect($interview->status)->toBe(InterviewStatus::Completed);
    expect($interview->completed_at)->not->toBeNull();
});

test('it cannot move to next when interview is not active', function () {
    $user = moveNextUser();
    ['interview' => $interview] = createInterviewWithMultipleQuestions($user);

    $interview->update(['status' => InterviewStatus::Completed]);

    $this->actingAs($user)
        ->post(route('interviews.next', $interview))
        ->assertStatus(422);
});

test('it cannot move to next for another organizations interview', function () {
    $user = moveNextUser();
    ['interview' => $interview] = createInterviewWithMultipleQuestions($user);

    $otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $this->actingAs($otherUser)
        ->post(route('interviews.next', $interview))
        ->assertNotFound();
});
