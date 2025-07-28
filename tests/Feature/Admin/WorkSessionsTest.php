<?php

use App\Domain\Entities\User;
use App\Models\Domain\Entities\WorkSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// Helper function to create users with specific roles
function createUserWithRole(string $role): User
{
    $user = User::factory()->create();

    // Get the role ID - the implementation might vary based on your role system
    // This assumes your roles table has a 'name' column
    $roleId = DB::table('roles')->where('name', $role)->first()->id;

    // Assign role to user - adjust based on your actual relationship implementation
    DB::table('role_user')->insert([
        'role_id' => $roleId,
        'user_id' => $user->id,
    ]);

    return $user;
}

test('admin can access work sessions page', function () {
    // Create an admin user
    $admin = createUserWithRole('admin');

    // Log in as admin
    $this->actingAs($admin);

    // Access the work sessions page
    $response = $this->get(route('admin.work-sessions'));

    // Assert successful response
    $response->assertSuccessful();

    // Assert the Livewire component is present
    $response->assertSeeLivewire('admin.work-sessions.index');
});

test('non-admin users cannot access work sessions page', function () {
    // Create a regular user
    $user = User::factory()->create();

    // Log in as regular user
    $this->actingAs($user);

    // Try to access the work sessions page
    $response = $this->get(route('admin.work-sessions'));

    // Assert access is denied
    $response->assertForbidden();
});

test('supervisor can access work sessions page', function () {
    // Create a supervisor user
    $supervisor = createUserWithRole('general_supervisor');

    // Log in as supervisor
    $this->actingAs($supervisor);

    // Access the work sessions page
    $response = $this->get(route('admin.work-sessions'));

    // Assert successful response
    $response->assertSuccessful();

    // Assert the Livewire component is present
    $response->assertSeeLivewire('admin.work-sessions.index');
});

test('branch manager can access work sessions page', function () {
    // Create a branch manager user
    $branchManager = createUserWithRole('branch_manager');

    // Log in as branch manager
    $this->actingAs($branchManager);

    // Access the work sessions page
    $response = $this->get(route('admin.work-sessions'));

    // Assert successful response
    $response->assertSuccessful();

    // Assert the Livewire component is present
    $response->assertSeeLivewire('admin.work-sessions.index');
});

test('agent cannot access work sessions page', function () {
    // Create an agent user
    $agent = createUserWithRole('agent');

    // Log in as agent
    $this->actingAs($agent);

    // Try to access the work sessions page
    $response = $this->get(route('admin.work-sessions'));

    // Assert access is denied
    $response->assertForbidden();
});

test('work sessions livewire component loads data correctly', function () {
    // Create an admin user
    $admin = createUserWithRole('admin');

    // Create some work sessions for testing
    $user1 = User::factory()->create(['name' => 'Test User 1']);
    $user2 = User::factory()->create(['name' => 'Test User 2']);

    WorkSession::factory()->count(3)->create(['user_id' => $user1->id]);
    WorkSession::factory()->count(2)->create(['user_id' => $user2->id]);

    // Log in as admin
    $this->actingAs($admin);

    // Test the Livewire component
    $component = Livewire::test('admin.work-sessions.index');

    // Assert the component rendered successfully
    $component->assertStatus(200);

    // Check if data is loaded
    $component->assertViewHas('workSessions');

    // Test the search filter
    $component->set('search', 'Test User 1')
        ->call('render')
        ->assertSee('Test User 1')
        ->assertDontSee('Test User 2');
});

test('work sessions can be exported to csv', function () {
    // Create an admin user
    $admin = createUserWithRole('admin');

    // Create some work sessions for testing
    $user = User::factory()->create();
    WorkSession::factory()->count(5)->create(['user_id' => $user->id]);

    // Log in as admin
    $this->actingAs($admin);

    // Test the export functionality
    $response = Livewire::test('admin.work-sessions.index')
        ->call('export');

    // Assert a download response
    $response->assertFileDownload();
});
