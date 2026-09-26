<?php

use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Models\Organization;
use App\Models\User;

function authorizationUser(): User
{
    $organization = Organization::factory()->create();

    return User::factory()->create([
        'organization_id' => $organization->id,
    ]);
}

test('users cannot view interviews from other organizations', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->inProgress()->create();

    $this->actingAs($user)
        ->get(route('interviews.conduct', $interview))
        ->assertNotFound();
});

test('users cannot start interviews from other organizations', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->create();

    $this->actingAs($user)
        ->post(route('interviews.start', $interview))
        ->assertNotFound();
});

test('users cannot submit answers to other organizations interviews', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->inProgress()->create();
    $question = InterviewQuestion::factory()->create([
        'interview_id' => $interview->id,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.answer', $interview), [
            'question_id' => $question->id,
            'content' => 'Answer',
        ])->assertNotFound();
});

test('users cannot pause interviews from other organizations', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->inProgress()->create();

    $this->actingAs($user)
        ->post(route('interviews.pause', $interview))
        ->assertNotFound();
});

test('users cannot resume interviews from other organizations', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->paused()->create();

    $this->actingAs($user)
        ->post(route('interviews.resume', $interview))
        ->assertNotFound();
});

test('users cannot skip questions in other organizations interviews', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->inProgress()->create();
    $question = InterviewQuestion::factory()->create([
        'interview_id' => $interview->id,
    ]);

    $this->actingAs($user)
        ->post(route('interviews.skip', $interview), [
            'question_id' => $question->id,
        ])->assertNotFound();
});

test('users cannot end interviews from other organizations', function () {
    $user = authorizationUser();
    $interview = Interview::factory()->inProgress()->create();

    $this->actingAs($user)
        ->post(route('interviews.end', $interview))
        ->assertNotFound();
});
