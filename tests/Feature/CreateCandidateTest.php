<?php

use App\Models\Candidate;
use App\Models\Organization;
use App\Models\User;

function createCandidateUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

it('creates a candidate', function () {
    $user = createCandidateUser();

    $response = $this->actingAs($user)->postJson(route('candidates.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'candidate' => ['id', 'name', 'email'],
            'message',
        ]);

    $this->assertDatabaseHas('candidates', [
        'organization_id' => $user->organization_id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('rejects duplicate email within organization', function () {
    $user = createCandidateUser();

    Candidate::factory()->create([
        'organization_id' => $user->organization_id,
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($user)->postJson(route('candidates.store'), [
        'name' => 'John Smith',
        'email' => 'john@example.com',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('allows same email in different organizations', function () {
    $user1 = createCandidateUser();
    $user2 = createCandidateUser();

    Candidate::factory()->create([
        'organization_id' => $user1->organization_id,
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($user2)->postJson(route('candidates.store'), [
        'name' => 'John Smith',
        'email' => 'john@example.com',
    ]);

    $response->assertCreated();
});

it('requires name and email', function () {
    $user = createCandidateUser();

    $response = $this->actingAs($user)->postJson(route('candidates.store'), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email']);
});

it('requires authentication', function () {
    $response = $this->postJson(route('candidates.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertUnauthorized();
});
