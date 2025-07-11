<?php

use App\Domain\Entities\User;
use App\Models\Domain\Entities\WorkSession;
use App\Application\Services\WorkSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

uses(RefreshDatabase::class);

test('work session is created when user logs in', function () {
    // Create a user
    $user = User::factory()->create();

    // Mock request data
    $ip = '192.168.1.1';
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    $request = \Mockery::mock(\Illuminate\Http\Request::class);
    $request->shouldReceive('ip')->andReturn($ip);
    $request->shouldReceive('userAgent')->andReturn($userAgent);

    // Use the service to create a session
    $service = new WorkSessionService();
    $session = $service->startSession($user, $request);

    // Assert the session was created with correct data
    expect($session)->toBeInstanceOf(WorkSession::class)
        ->and($session->user_id)->toBe($user->id)
        ->and($session->login_at)->not->toBeNull()
        ->and($session->logout_at)->toBeNull()
        ->and($session->ip_address)->toBe($ip)
        ->and($session->user_agent)->toBe($userAgent);

    // Assert it's in the database
    $this->assertDatabaseHas('work_sessions', [
        'user_id' => $user->id,
        'ip_address' => $ip,
        'user_agent' => $userAgent,
    ]);
});

test('work session is updated when user logs out', function () {
    // Create a user and an active session
    $user = User::factory()->create();
    $session = WorkSession::factory()->active()->create([
        'user_id' => $user->id,
        'login_at' => now()->subMinutes(30), // Logged in 30 minutes ago
    ]);

    // Use the service to end the session
    $service = new WorkSessionService();
    $updatedSession = $service->endSession($user);

    // Assert the session was updated
    expect($updatedSession->id)->toBe($session->id)
        ->and($updatedSession->logout_at)->not->toBeNull()
        ->and($updatedSession->duration_minutes)->toBeGreaterThanOrEqual(29) // Allow for slight timing differences
        ->and($updatedSession->duration_minutes)->toBeLessThanOrEqual(31); // Allow for slight timing differences
});

test('login event listener creates work session', function () {
    // Mock dependencies
    $user = User::factory()->create();
    $workSessionService = $this->mock(WorkSessionService::class);

    // Set expectations
    $workSessionService->shouldReceive('startSession')
        ->once()
        ->with($user, \Mockery::type(\Illuminate\Http\Request::class))
        ->andReturn(new WorkSession());

    // Create the listener and fire the event
    $listener = new \App\Listeners\LogSuccessfulLogin($workSessionService);
    $event = new Login('web', $user, false);

    $listener->handle($event);
});

test('logout event listener updates work session', function () {
    // Mock dependencies
    $user = User::factory()->create();
    $workSessionService = $this->mock(WorkSessionService::class);

    // Set expectations
    $workSessionService->shouldReceive('endSession')
        ->once()
        ->with($user)
        ->andReturn(new WorkSession());

    // Create the listener and fire the event
    $listener = new \App\Listeners\LogSuccessfulLogout($workSessionService);
    $event = new Logout('web', $user);

    $listener->handle($event);
});

test('actual login creates work session through event', function () {
    // Create a user
    $user = User::factory()->create();

    // Manually log in the user (this should trigger the Login event)
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Assert the user is authenticated
    $this->assertAuthenticated();

    // Assert a work session was created
    $this->assertDatabaseHas('work_sessions', [
        'user_id' => $user->id,
        'logout_at' => null,
    ]);
});

test('actual logout updates work session through event', function () {
    // Create a user and log them in
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create an active session
    $session = WorkSession::factory()->active()->create([
        'user_id' => $user->id,
        'login_at' => now()->subMinutes(10),
    ]);

    // Perform logout (this should trigger the Logout event)
    $this->post('/logout');

    // Assert the user is logged out
    $this->assertGuest();

    // Assert the work session was updated
    $this->assertDatabaseHas('work_sessions', [
        'id' => $session->id,
        'user_id' => $user->id,
        'logout_at' => now()->format('Y-m-d H:i'),
    ]);

    // Get the updated session and check duration
    $updatedSession = WorkSession::find($session->id);
    expect($updatedSession->duration_minutes)->toBeGreaterThan(0);
});

test('multiple logins are handled correctly', function () {
    // Create a user
    $user = User::factory()->create();

    // Mock request data
    $request = \Mockery::mock(\Illuminate\Http\Request::class);
    $request->shouldReceive('ip')->andReturn('192.168.1.1');
    $request->shouldReceive('userAgent')->andReturn('Test Agent');

    // First login (without logout)
    $service = new WorkSessionService();
    $session1 = $service->startSession($user, $request);

    // Second login (without logout from first session)
    $session2 = $service->startSession($user, $request);

    // Assert two different sessions were created
    expect($session1->id)->not->toBe($session2->id);

    // Assert we have two active sessions in the database
    $this->assertDatabaseCount('work_sessions', 2);

    // When user logs out, only the most recent session should be closed
    $service->endSession($user);

    // Reload sessions from database
    $session1 = WorkSession::find($session1->id);
    $session2 = WorkSession::find($session2->id);

    // First session should still be open
    expect($session1->logout_at)->toBeNull();

    // Second session should be closed
    expect($session2->logout_at)->not->toBeNull()
        ->and($session2->duration_minutes)->not->toBeNull();
});

test('work session service can retrieve user sessions', function () {
    // Create a user
    $user = User::factory()->create();

    // Create several sessions for the user
    WorkSession::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    // Create an active session
    WorkSession::factory()->active()->create([
        'user_id' => $user->id,
    ]);

    // Create sessions for another user
    WorkSession::factory()->count(2)->create();

    // Get the user's sessions
    $service = new WorkSessionService();
    $sessions = $service->getUserSessions($user);

    // Assert we get only the user's sessions
    expect($sessions)->toHaveCount(4)
        ->and($sessions->pluck('user_id')->unique()->toArray())->toBe([$user->id]);

    // Get the user's active session
    $activeSession = $service->getActiveSession($user);

    // Assert we get the active session
    expect($activeSession)->not->toBeNull()
        ->and($activeSession->user_id)->toBe($user->id)
        ->and($activeSession->logout_at)->toBeNull();
});
