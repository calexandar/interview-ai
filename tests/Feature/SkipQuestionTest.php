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

function skipQuestionUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createInterviewForSkip(User $user): array
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
        'status' => QuestionStatus::Asking,
        'asked_at' => now(),
    ]);

    $interview->update([
        'current_question_id' => $questions[1]->id,
    ]);

    return compact('interview', 'questions');
}

test('guests are redirected to login when skipping a question', function () {
    $interview = Interview::factory()->create();
    $question = InterviewQuestion::factory()->create(['interview_id' => $interview->id]);

    $this->post(route('interviews.skip', $interview), [
        'question_id' => $question->id,
    ])->assertRedirect(route('login'));
});

test('it skips the current question', function () {
    $user = skipQuestionUser();
    ['interview' => $interview, 'questions' => $questions] =
        createInterviewForSkip($user);

    $this->actingAs($user)
        ->post(route('interviews.skip', $interview), [
            'question_id' => $questions[1]->id,
        ])->assertRedirect();

    $questions[1]->refresh();
    expect($questions[1]->status)->toBe(QuestionStatus::Skipped);

    $interview->refresh();
    expect($interview->question_index)->toBe(1);
});

test('it cannot skip a pending question', function () {
    $user = skipQuestionUser();
    ['interview' => $interview, 'questions' => $questions] =
        createInterviewForSkip($user);

    $this->actingAs($user)
        ->post(route('interviews.skip', $interview), [
            'question_id' => $questions[2]->id,
        ])->assertStatus(422);
});

test('it cannot skip when interview is not active', function () {
    $user = skipQuestionUser();
    ['interview' => $interview, 'questions' => $questions] =
        createInterviewForSkip($user);

    $interview->update(['status' => InterviewStatus::Completed]);

    $this->actingAs($user)
        ->post(route('interviews.skip', $interview), [
            'question_id' => $questions[1]->id,
        ])->assertStatus(422);
});

test('it cannot skip questions from another organization', function () {
    $user = skipQuestionUser();
    ['interview' => $interview, 'questions' => $questions] =
        createInterviewForSkip($user);

    $otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $this->actingAs($otherUser)
        ->post(route('interviews.skip', $interview), [
            'question_id' => $questions[1]->id,
        ])->assertNotFound();
});
