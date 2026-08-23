<?php

use App\Models\Interview;
use App\Models\Organization;
use App\Models\User;
use App\Shared\Enums\InterviewStatus;

function endInterviewUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

test('guests are redirected to the login page when ending an interview', function () {
    $interview = Interview::factory()->inProgress()->create();

    $this->post(route('interviews.end', ['interview' => $interview->id]))
        ->assertRedirect(route('login'));
});

it('ends an in-progress interview for the same organization', function () {
    $user = endInterviewUser();

    $interview = Interview::factory()->inProgress()->create([
        'organization_id' => $user->organization_id,
    ]);

    $response = $this->actingAs($user)->post(
        route('interviews.end', ['interview' => $interview->id]),
    );

    $response->assertRedirect(route('dashboard'));

    expect($interview->refresh()->status)
        ->toBe(InterviewStatus::Completed)
        ->and($interview->completed_at)
        ->not->toBeNull();
});

it('rejects interviews from other organizations', function () {
    $user = endInterviewUser();

    $interview = Interview::factory()->inProgress()->create([
        'total_questions' => 10,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.end', ['interview' => $interview->id]))
        ->assertNotFound();

    expect($interview->refresh()->status)
        ->toBe(InterviewStatus::InProgress);
});

it('rejects interviews that are not in progress', function () {
    $user = endInterviewUser();

    $interview = Interview::factory()->create([
        'organization_id' => $user->organization_id,
        'status' => InterviewStatus::Completed,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.end', ['interview' => $interview->id]))
        ->assertNotFound();
});
