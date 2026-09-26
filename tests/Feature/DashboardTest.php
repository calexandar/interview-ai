<?php

use App\Models\Assessment;
use App\Models\Candidate;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Organization;
use App\Models\Position;
use App\Models\Skill;
use App\Models\SkillAssessment;
use App\Models\User;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\QuestionStatus;
use Illuminate\Testing\Fluent\AssertableJson;

function dashboardUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = dashboardUser();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

it('passes recent interviews and user name to the dashboard', function () {
    $user = dashboardUser();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->component('Dashboard/Index')
            ->where('recentInterviews', [])
            ->where('userName', $user->name)
            ->where('aiAgentStatus', 'online')
            ->missing('session'));
});

it('defers and loads the interview session payload', function () {
    $user = dashboardUser();

    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
        'title' => 'Senior Frontend Developer',
        'duration_minutes' => 45,
    ]);

    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
        'name' => 'David Johnson',
    ]);

    Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'total_questions' => 10,
        'question_index' => 6,
        'started_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->component('Dashboard/Index')
            ->missing('session')
            ->loadDeferredProps(function (AssertableJson $reload) {
                $reload
                    ->has('session.currentQuestion')
                    ->where('session.activeInterview.candidateName', 'David Johnson')
                    ->where('session.activeInterview.jobTitle', 'Senior Frontend Developer')
                    ->where('session.activeInterview.status', InterviewStatus::InProgress->value)
                    ->where('session.activeInterview.durationSeconds', 2700)
                    ->whereType('session.activeInterview.remainingSeconds', 'integer')
                    ->where('session.progress.percentage', 60)
                    ->has('session.statistics.interviews')
                    ->has('session.interviewsOverTime', 7);
            }));
});

it('builds progress sections from interview questions grouped by skill', function () {
    $user = dashboardUser();
    $skill = Skill::factory()->create(['name' => 'JavaScript']);

    $interview = Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
        'total_questions' => 2,
        'question_index' => 1,
    ]);

    InterviewQuestion::factory()->answered()->create([
        'interview_id' => $interview->id,
        'skill_id' => $skill->id,
        'status' => QuestionStatus::Answered,
    ]);

    InterviewQuestion::factory()->asking()->create([
        'interview_id' => $interview->id,
        'skill_id' => $skill->id,
        'status' => QuestionStatus::Asking,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->loadDeferredProps(fn (AssertableJson $reload) => $reload
                ->where('session.progress.sections.0.name', $skill->name)
                ->where('session.progress.sections.0.completed', 1)
                ->where('session.progress.sections.0.total', 2)
                ->where('session.progress.sections.0.status', 'in_progress')));
});

it('maps skill assessment scores to percentages', function () {
    $user = dashboardUser();
    $skill = Skill::factory()->create(['name' => 'Vue']);

    $interview = Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
    ]);

    SkillAssessment::query()->create([
        'interview_id' => $interview->id,
        'skill_id' => $skill->id,
        'score' => 8.5,
        'confidence' => 0.8,
        'questions_answered' => 3,
    ]);

    Assessment::factory()->create([
        'interview_id' => $interview->id,
        'overall_score' => 7.0,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->loadDeferredProps(fn (AssertableJson $reload) => $reload
                ->where('session.skills.0.skill', $skill->name)
                ->where('session.skills.0.score', 85)
                ->where('session.statistics.averageScorePercent.value', 70)));
});

it('does not expose other organizations interviews', function () {
    $user = dashboardUser();

    Interview::factory()->inProgress()->create([
        'total_questions' => 10,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->loadDeferredProps(fn (AssertableJson $reload) => $reload
                ->where('session.activeInterview', null)
                ->where('session.skills', [])));
});
