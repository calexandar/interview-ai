<?php

use App\AI\Contracts\InterviewAI;
use App\AI\Fakes\FakeInterviewAI;
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
use App\Shared\Enums\QuestionSource;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Swaps the real provider for the controllable fake and returns it, so a test
 * can both steer the AI and assert on what the application handed it.
 *
 * Any test that touches question generation or answer evaluation should use
 * this rather than the real provider, so the suite never depends on a network
 * call or an API key.
 */
function fakeAi(): FakeInterviewAI
{
    $fake = new FakeInterviewAI;

    app()->instance(FakeInterviewAI::class, $fake);
    app()->instance(InterviewAI::class, $fake);

    return $fake;
}

function interviewUser(): User
{
    return User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);
}

/**
 * A scheduled interview for a single required skill, with a question-bank pool
 * already selected the way Phase 1 selected it.
 *
 * @return array{interview: Interview, position: Position, skill: Skill}
 */
function interviewWithSkill(User $user, int $questionCount = 3): array
{
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
        'question_count' => $questionCount,
    ]);

    $skill = Skill::factory()->create();
    $position->skills()->attach($skill->id, ['weight' => 5, 'required' => true]);

    $interview = Interview::factory()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => Candidate::factory()->create(['organization_id' => $user->organization_id])->id,
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
            'source' => QuestionSource::QuestionBank,
        ]);
    }

    return compact('interview', 'position', 'skill');
}

/**
 * An in-progress interview whose first question is being asked and has no
 * answer yet. This is the state a candidate is in when they submit.
 *
 * @return array{0: Interview, 1: InterviewQuestion, 2: Skill}
 */
function interviewAwaitingAnswer(User $user): array
{
    ['interview' => $interview, 'skill' => $skill] = interviewWithSkill($user);

    $interview->update([
        'status' => InterviewStatus::InProgress,
        'started_at' => now(),
    ]);

    $question = $interview->interviewQuestions()->orderBy('position')->firstOrFail();

    $question->update([
        'status' => QuestionStatus::Asking,
        'asked_at' => now(),
    ]);

    $interview->update(['current_question_id' => $question->id]);

    return [$interview->fresh(), $question->fresh(), $skill];
}

/**
 * The same interview, but with the candidate's answer already recorded. Used
 * where a test starts from an answer rather than submitting one.
 *
 * @return array{0: Interview, 1: InterviewQuestion, 2: Answer, 3: Skill}
 */
function interviewWithAnswer(
    User $user,
    string $answerContent = 'I would enable eager loading and inspect the query log.',
): array {
    [$interview, $question, $skill] = interviewAwaitingAnswer($user);

    $answer = Answer::create([
        'interview_question_id' => $question->id,
        'candidate_id' => $interview->candidate_id,
        'content' => $answerContent,
        'submitted_at' => now(),
    ]);

    $question->update([
        'status' => QuestionStatus::Answered,
        'answered_at' => now(),
    ]);

    return [$interview, $question->fresh(), $answer, $skill];
}
