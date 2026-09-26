<?php

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use App\Shared\Enums\InterviewStatus;

function pauseInterviewUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

function createInProgressInterviewForPause(User $user): Interview
{
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    return Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
    ]);
}

test('guests are redirected to login when pausing an interview', function () {
    $interview = Interview::factory()->inProgress()->create();

    $this->post(route('interviews.pause', $interview))
        ->assertRedirect(route('login'));
});

test('it pauses an in-progress interview', function () {
    $user = pauseInterviewUser();
    $interview = createInProgressInterviewForPause($user);

    $this->actingAs($user)
        ->post(route('interviews.pause', $interview))
        ->assertRedirect();

    $interview->refresh();
    expect($interview->status)->toBe(InterviewStatus::Paused);
    expect($interview->paused_at)->not->toBeNull();
});

test('it cannot pause a scheduled interview', function () {
    $user = pauseInterviewUser();
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
        ->post(route('interviews.pause', $interview))
        ->assertStatus(422);
});

test('it cannot pause a completed interview', function () {
    $user = pauseInterviewUser();
    $interview = createInProgressInterviewForPause($user);
    $interview->update(['status' => InterviewStatus::Completed]);

    $this->actingAs($user)
        ->post(route('interviews.pause', $interview))
        ->assertStatus(422);
});

test('it cannot pause another organizations interview', function () {
    $user = pauseInterviewUser();
    $interview = createInProgressInterviewForPause($user);

    $otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $this->actingAs($otherUser)
        ->post(route('interviews.pause', $interview))
        ->assertNotFound();
});
