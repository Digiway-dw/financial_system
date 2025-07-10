<?php

use App\Domain\Entities\User;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Safe;
use Livewire\Livewire;
use App\Livewire\Transactions\Send;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'agent']);
    Role::create(['name' => 'admin']);

    // Create branch
    $this->branch = Branch::create([
        'name' => 'Test Branch',
        'address' => 'Test Address',
        'phone' => '+201234567890'
    ]);

    // Create safe
    $this->safe = Safe::create([
        'name' => 'Test Safe',
        'current_balance' => 10000,
        'type' => 'branch',
        'branch_id' => $this->branch->id
    ]);

    // Create line
    $this->line = Line::create([
        'mobile_number' => '+201234567890',
        'current_balance' => 5000,
        'daily_limit' => 10000,
        'monthly_limit' => 100000,
        'daily_usage' => 0,
        'monthly_usage' => 0,
        'network' => 'vodafone',
        'status' => 'active',
        'branch_id' => $this->branch->id
    ]);

    // Create agent user
    $this->agent = User::create([
        'name' => 'Test Agent',
        'email' => 'agent@test.com',
        'password' => bcrypt('password'),
        'branch_id' => $this->branch->id
    ]);
    $this->agent->assignRole('agent');
});

test('it can render send transaction page', function () {
    $this->actingAs($this->agent);

    $response = $this->get('/transactions/send');

    $response->assertStatus(200);
    $response->assertSeeLivewire(Send::class);
});

test('it validates required fields', function () {
    $this->actingAs($this->agent);

    Livewire::test(Send::class)
        ->call('submitTransaction')
        ->assertHasErrors([
            'clientMobile' => 'required',
            'clientName' => 'required',
            'receiverMobile' => 'required',
            'selectedLineId' => 'required'
        ]);
});

test('it calculates commission correctly', function () {
    $this->actingAs($this->agent);

    $component = Livewire::test(Send::class)
        ->set('amount', 1000);

    // Commission should be 5 EGP per 500 EGP = 10 EGP for 1000 EGP
    $component->assertSet('commission', 10);
});

test('it can search and select existing client', function () {
    $this->actingAs($this->agent);

    // Create existing customer
    $customer = Customer::create([
        'name' => 'Jane Doe',
        'mobile_number' => '+201234567893',
        'customer_code' => 'C250101',
        'gender' => 'female',
        'balance' => 1000,
        'is_client' => true,
        'agent_id' => $this->agent->id,
        'branch_id' => $this->branch->id
    ]);

    $component = Livewire::test(Send::class)
        ->set('clientMobile', '+201234567893');

    // Should auto-fill client data
    $component->assertSet('clientName', 'Jane Doe')
        ->assertSet('clientCode', 'C250101')
        ->assertSet('clientGender', 'female')
        ->assertSet('clientBalance', 1000);
});
