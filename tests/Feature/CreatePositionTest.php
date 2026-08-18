<?php

use App\Models\Organization;
use App\Models\Position;
use App\Models\Skill;
use App\Models\User;
use App\Shared\Enums\PositionLevel;
use App\Shared\Enums\PositionStatus;

function createUserWithOrganization(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

it('creates a position', function () {
    $user = createUserWithOrganization();

    $response = $this->actingAs($user)->postJson(route('positions.store'), [
        'title' => 'Senior Laravel Developer',
        'description' => 'A senior Laravel developer position',
        'level' => PositionLevel::Senior->value,
        'duration_minutes' => 60,
        'question_count' => 12,
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'position' => ['id', 'title', 'level', 'status'],
            'message',
        ]);

    $this->assertDatabaseHas('positions', [
        'organization_id' => $user->organization_id,
        'title' => 'Senior Laravel Developer',
        'level' => PositionLevel::Senior->value,
        'status' => PositionStatus::Active->value,
    ]);
});

it('creates a position with skills', function () {
    $user = createUserWithOrganization();
    $skills = Skill::factory()->count(3)->create();

    $response = $this->actingAs($user)->postJson(route('positions.store'), [
        'title' => 'Full Stack Developer',
        'level' => PositionLevel::Mid->value,
        'duration_minutes' => 45,
        'question_count' => 10,
        'skill_ids' => $skills->pluck('id')->toArray(),
    ]);

    $response->assertCreated();

    $position = Position::where('title', 'Full Stack Developer')->first();
    expect($position->skills)->toHaveCount(3);
});

it('rejects invalid level', function () {
    $user = createUserWithOrganization();

    $response = $this->actingAs($user)->postJson(route('positions.store'), [
        'title' => 'Developer',
        'level' => 'invalid',
        'duration_minutes' => 60,
        'question_count' => 10,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('level');
});

it('requires title', function () {
    $user = createUserWithOrganization();

    $response = $this->actingAs($user)->postJson(route('positions.store'), [
        'level' => PositionLevel::Senior->value,
        'duration_minutes' => 60,
        'question_count' => 10,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

it('requires authentication', function () {
    $response = $this->postJson(route('positions.store'), [
        'title' => 'Developer',
        'level' => PositionLevel::Senior->value,
        'duration_minutes' => 60,
        'question_count' => 10,
    ]);

    $response->assertUnauthorized();
});
