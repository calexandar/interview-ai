<?php

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use App\Shared\Enums\InterviewStatus;
use App\Shared\Enums\InterviewType;
use App\Shared\Enums\PositionLevel;

function createInterviewUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

it('creates a scheduled interview', function () {
    $user = createInterviewUser();
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
        'level' => PositionLevel::Senior,
    ]);
    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $response = $this->actingAs($user)->postJson(route('interviews.store'), [
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'type' => InterviewType::Technical->value,
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'interview' => ['id', 'status', 'type', 'total_questions'],
            'message',
        ]);

    $this->assertDatabaseHas('interviews', [
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'status' => InterviewStatus::Scheduled->value,
        'type' => InterviewType::Technical->value,
    ]);
});

it('rejects inactive positions', function () {
    $user = createInterviewUser();
    $position = Position::factory()->inactive()->create([
        'organization_id' => $user->organization_id,
    ]);
    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $response = $this->actingAs($user)->postJson(route('interviews.store'), [
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'type' => InterviewType::Technical->value,
    ]);

    $response->assertStatus(422)
        ->assertJson(['message' => 'Position must be active to create an interview.']);
});

it('rejects candidates from another organization', function () {
    $user = createInterviewUser();
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
    ]);
    $otherOrganization = Organization::factory()->create();
    $candidate = Candidate::factory()->create([
        'organization_id' => $otherOrganization->id,
    ]);

    $response = $this->actingAs($user)->postJson(route('interviews.store'), [
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'type' => InterviewType::Technical->value,
    ]);

    $response->assertStatus(404);
});

it('rejects positions from another organization', function () {
    $user = createInterviewUser();
    $otherOrganization = Organization::factory()->create();
    $position = Position::factory()->create([
        'organization_id' => $otherOrganization->id,
    ]);
    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    $response = $this->actingAs($user)->postJson(route('interviews.store'), [
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'type' => InterviewType::Technical->value,
    ]);

    $response->assertStatus(404);
});

it('rejects duplicate active interview for same candidate and position', function () {
    $user = createInterviewUser();
    $position = Position::factory()->create([
        'organization_id' => $user->organization_id,
    ]);
    $candidate = Candidate::factory()->create([
        'organization_id' => $user->organization_id,
    ]);

    Interview::factory()->create([
        'organization_id' => $user->organization_id,
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'status' => InterviewStatus::Scheduled,
    ]);

    $response = $this->actingAs($user)->postJson(route('interviews.store'), [
        'position_id' => $position->id,
        'candidate_id' => $candidate->id,
        'type' => InterviewType::Technical->value,
    ]);

    $response->assertStatus(422)
        ->assertJson(['message' => 'Candidate already has an active interview for this position.']);
});

it('requires authentication', function () {
    $response = $this->postJson(route('interviews.store'), [
        'position_id' => 1,
        'candidate_id' => 1,
        'type' => InterviewType::Technical->value,
    ]);

    $response->assertUnauthorized();
});

it('requires valid position, candidate, and type', function () {
    $user = createInterviewUser();

    $response = $this->actingAs($user)->postJson(route('interviews.store'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['position_id', 'candidate_id', 'type']);
});
