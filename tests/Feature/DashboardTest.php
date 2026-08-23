<?php

use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use App\Shared\Enums\PositionStatus;
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

it('passes stat counts and monthly trends to the dashboard', function () {
    $user = dashboardUser();

    Position::factory()->count(3)->create([
        'organization_id' => $user->organization_id,
        'status' => PositionStatus::Active,
    ]);
    Position::factory()->inactive()->create([
        'organization_id' => $user->organization_id,
        'created_at' => now()->subMonths(2),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->component('Dashboard')
            ->where('activePositionsCount', 3)
            ->where('activePositionsTrend.value', 3)
            ->where('activePositionsTrend.direction', 'up')
            ->where('candidatesTrend', null)
            ->where('interviewsTrend', null)
            ->where('strongCandidatesTrend', null)
            ->where('recentInterviews', [])
            ->where('userName', $user->name));
});

it('hides trends when nothing was created this month', function () {
    $user = dashboardUser();

    Position::factory()->count(2)->create([
        'organization_id' => $user->organization_id,
        'created_at' => now()->subMonths(2),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn (AssertableJson $page) => $page
            ->component('Dashboard')
            ->where('activePositionsCount', 2)
            ->where('activePositionsTrend', null)
            ->where('candidatesTrend', null)
            ->where('interviewsTrend', null)
            ->where('strongCandidatesTrend', null));
});
