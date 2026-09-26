<?php

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use App\Shared\Enums\InterviewStatus;

function resumeInterviewUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createPausedInterview(User $user): Interview
{
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    return Interview::factory()->paused()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
    ]);
}

test('guests are redirected to login when resuming an interview', function () {
    $interview = Interview::factory()->paused()->create();

    $this->post(route('interviews.resume', $interview))
        ->assertRedirect(route('login'));
});

test('it resumes a paused interview', function () {
    $user = resumeInterviewUser();
    $interview = createPausedInterview($user);

    $this->actingAs($user)
        ->post(route('interviews.resume', $interview))
        ->assertRedirect();

    $interview->refresh();
    expect($interview->status)->toBe(InterviewStatus::InProgress);
    expect($interview->paused_at)->toBeNull();
});

test('it adjusts started_at to account for paused duration', function () {
    $user = resumeInterviewUser();
    $interview = createPausedInterview($user);

    $startedAt = $interview->started_at;
    $pausedAt = $interview->paused_at;
    $pausedDuration = $pausedAt->diffInSeconds(now());

    $this->actingAs($user)
        ->post(route('interviews.resume', $interview));

    $interview->refresh();
    $expectedStartedAt = $startedAt->addSeconds($pausedDuration);
    expect($interview->started_at->timestamp)->toBe($expectedStartedAt->timestamp);
});

test('it cannot resume a scheduled interview', function () {
    $user = resumeInterviewUser();
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
        'status' => InterviewStatus::Scheduled,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.resume', $interview))
        ->assertStatus(422);
});

test('it cannot resume an in-progress interview', function () {
    $user = resumeInterviewUser();
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
    ]);
    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);
    $interview = Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.resume', $interview))
        ->assertStatus(422);
});

test('it cannot resume another organizations interview', function () {
    $user = resumeInterviewUser();
    $interview = createPausedInterview($user);

    $otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $this->actingAs($otherUser)
        ->post(route('interviews.resume', $interview))
        ->assertNotFound();
});
