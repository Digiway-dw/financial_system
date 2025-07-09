<?php

use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Transaction;
use App\Livewire\Transactions\Receive;
use Livewire\Livewire;

beforeEach(function () {
    // Create a branch
    $this->branch = Branch::create([
        'name' => 'Test Branch',
        'location' => 'Test Location',
        'status' => 'active',
    ]);

    // Create a safe for the branch
    $this->safe = Safe::create([
        'branch_id' => $this->branch->id,
        'current_balance' => 10000.00,
        'status' => 'active',
    ]);

    // Create a user (agent)
    $this->user = User::create([
        'name' => 'Test Agent',
        'email' => 'agent@test.com',
        'password' => bcrypt('password'),
        'branch_id' => $this->branch->id,
    ]);

    // Create a line for the branch
    $this->line = Line::create([
        'mobile_number' => '01234567890',
        'network' => 'vodafone',
        'current_balance' => 5000.00,
        'branch_id' => $this->branch->id,
        'status' => 'active',
    ]);

    // Create an existing customer
    $this->customer = Customer::create([
        'name' => 'John Doe',
        'mobile_number' => '01111111111',
        'customer_code' => 'C123456',
        'gender' => 'male',
        'balance' => 1000.00,
        'is_client' => true,
        'branch_id' => $this->branch->id,
        'agent_id' => $this->user->id,
    ]);

    // Authenticate the user
    $this->actingAs($this->user);
});

test('receive transaction page can be rendered', function () {
    $response = $this->get('/transactions/receive');

    $response->assertStatus(200);
    $response->assertSee('Receive Transaction');
    $response->assertSee('Client Information');
    $response->assertSee('Sender Information');
    $response->assertSee('Transaction Details');
});

test('receive component initializes correctly', function () {
    Livewire::test(Receive::class)
        ->assertSet('clientMobile', '')
        ->assertSet('clientName', '')
        ->assertSet('senderMobile', '')
        ->assertSet('amount', 0)
        ->assertSet('commission', 0)
        ->assertSet('discount', 0)
        ->assertCount('availableLines', 1)
        ->assertSee('Select a line');
});

test('commission is calculated correctly for receive transactions', function () {
    Livewire::test(Receive::class)
        ->set('amount', 1000)
        ->assertSet('commission', 10) // floor(1000/500) * 5 = 10
        ->set('amount', 1500)
        ->assertSet('commission', 15) // floor(1500/500) * 5 = 15
        ->set('amount', 499)
        ->assertSet('commission', 0); // floor(499/500) * 5 = 0
});

test('commission calculation respects discount', function () {
    Livewire::test(Receive::class)
        ->set('amount', 1000)
        ->set('discount', 3)
        ->assertSet('commission', 7); // 10 - 3 = 7
});

test('client search works correctly', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '0111')
        ->assertCount('clientSuggestions', 1)
        ->assertSee('John Doe');
});

test('client auto-fill works on exact match', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01111111111')
        ->assertSet('clientName', 'John Doe')
        ->assertSet('clientCode', 'C123456')
        ->assertSet('clientGender', 'male')
        ->assertSet('clientBalance', 1000.00)
        ->assertSet('clientId', $this->customer->id);
});

test('safe balance warning appears when insufficient balance', function () {
    // Set safe balance to low amount
    $this->safe->update(['current_balance' => 100]);

    Livewire::test(Receive::class)
        ->set('amount', 1000)
        ->set('selectedLineId', $this->line->id)
        ->assertSet('safeBalanceWarning', 'Insufficient balance in safe. Available: 100.00 EGP, Required: 990.00 EGP.');
});

test('validation works correctly', function () {
    Livewire::test(Receive::class)
        ->call('submitTransaction')
        ->assertHasErrors([
            'clientMobile' => 'required',
            'clientName' => 'required',
            'senderMobile' => 'required',
            'amount' => 'required',
            'selectedLineId' => 'required',
        ]);
});

test('amount validation requires multiple of 5', function () {
    Livewire::test(Receive::class)
        ->set('amount', 7)
        ->call('submitTransaction')
        ->assertHasErrors(['amount' => 'multiple_of']);
});

test('discount notes required when discount is provided', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01111111111')
        ->set('clientName', 'John Doe')
        ->set('senderMobile', '01222222222')
        ->set('amount', 500)
        ->set('selectedLineId', $this->line->id)
        ->set('discount', 5)
        ->call('submitTransaction')
        ->assertHasErrors(['discountNotes' => 'required_if']);
});

test('successful receive transaction creates records correctly', function () {
    $initialSafeBalance = $this->safe->current_balance;
    $initialLineBalance = $this->line->current_balance;

    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('clientGender', 'female')
        ->set('senderMobile', '01444444444')
        ->set('amount', 1000)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction')
        ->assertHasNoErrors()
        ->assertSet('successMessage', 'Receive transaction created successfully!');

    // Check that line balance increased
    $this->line->refresh();
    expect($this->line->current_balance)->toBe($initialLineBalance + 1000);

    // Check that safe balance decreased by (amount - commission)
    $this->safe->refresh();
    $expectedSafeDeduction = 1000 - 10; // amount - commission
    expect($this->safe->current_balance)->toBe($initialSafeBalance - $expectedSafeDeduction);

    // Check that transaction was created
    $transaction = Transaction::latest()->first();
    expect($transaction)->not->toBeNull();
    expect($transaction->amount)->toBe(1000.0);
    expect($transaction->commission)->toBe(10.0);
    expect($transaction->transaction_type)->toBe('Receive');
    expect($transaction->agent_id)->toBe($this->user->id);
    expect($transaction->line_id)->toBe($this->line->id);
    expect($transaction->safe_id)->toBe($this->safe->id);

    // Check that customer was created
    $newCustomer = Customer::where('mobile_number', '01333333333')->first();
    expect($newCustomer)->not->toBeNull();
    expect($newCustomer->name)->toBe('Jane Smith');
    expect($newCustomer->gender)->toBe('female');
    expect($newCustomer->is_client)->toBe(true);
    expect($newCustomer->branch_id)->toBe($this->branch->id);
});

test('receive transaction with existing customer updates customer info', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01111111111') // Existing customer
        ->set('clientName', 'John Updated')
        ->set('clientGender', 'male')
        ->set('senderMobile', '01444444444')
        ->set('amount', 500)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction')
        ->assertHasNoErrors()
        ->assertSet('successMessage', 'Receive transaction created successfully!');

    // Check that customer was updated
    $this->customer->refresh();
    expect($this->customer->name)->toBe('John Updated');
});

test('receive transaction with discount creates transaction correctly', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', 1000)
        ->set('selectedLineId', $this->line->id)
        ->set('discount', 5)
        ->set('discountNotes', 'Special customer discount')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::latest()->first();
    expect($transaction->commission)->toBe(5.0); // 10 - 5 discount
    expect($transaction->deduction)->toBe(5.0);
    expect($transaction->discount_notes)->toBe('Special customer discount');
});

test('receive transaction fails with insufficient safe balance', function () {
    // Set safe balance lower than required
    $this->safe->update(['current_balance' => 500]);

    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', 1000) // Requires 990 from safe (1000 - 10 commission)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction')
        ->assertSet('errorMessage', 'Please resolve safe balance issues before submitting.');
});

test('receive transaction fails when line not found', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', 500)
        ->set('selectedLineId', 99999) // Non-existent line
        ->call('submitTransaction')
        ->assertSet('errorMessage', 'Failed to create receive transaction: Selected line not found.');
});

test('receive transaction fails when no safe exists for branch', function () {
    // Delete the safe
    $this->safe->delete();

    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', 500)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction')
        ->assertSet('errorMessage', 'Failed to create receive transaction: No safe found for this branch.');
});

test('form resets correctly after successful transaction', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', 500)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction')
        ->assertSet('clientMobile', '')
        ->assertSet('clientName', '')
        ->assertSet('senderMobile', '')
        ->assertSet('amount', 0)
        ->assertSet('commission', 0)
        ->assertSet('discount', 0)
        ->assertSet('selectedLineId', '');
});

test('available lines shows only active lines from user branch', function () {
    // Create another branch with a line
    $otherBranch = Branch::create([
        'name' => 'Other Branch',
        'location' => 'Other Location',
        'status' => 'active',
    ]);

    $otherLine = Line::create([
        'mobile_number' => '01999999999',
        'network' => 'etisalat',
        'current_balance' => 3000.00,
        'branch_id' => $otherBranch->id,
        'status' => 'active',
    ]);

    // Create inactive line in same branch
    $inactiveLine = Line::create([
        'mobile_number' => '01888888888',
        'network' => 'orange',
        'current_balance' => 2000.00,
        'branch_id' => $this->branch->id,
        'status' => 'inactive',
    ]);

    Livewire::test(Receive::class)
        ->assertCount('availableLines', 1) // Only the active line from user's branch
        ->assertSee('01234567890'); // Our line
});

test('transaction summary shows correct calculations', function () {
    $component = Livewire::test(Receive::class)
        ->set('amount', 1500)
        ->set('discount', 5);

    $component->assertSee('1,500.00 EGP') // Amount received
        ->assertSee('10.00 EGP') // Commission (15 - 5 discount)
        ->assertSee('5.00 EGP') // Discount
        ->assertSee('1,490.00 EGP'); // From safe (1500 - 10 commission)
});

test('client code is auto-generated for new clients', function () {
    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', 500)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction');

    $newCustomer = Customer::where('mobile_number', '01333333333')->first();
    expect($newCustomer->customer_code)->toMatch('/^C\d{6}$/'); // Pattern: C followed by 6 digits
});

test('receive transaction business logic is correct', function () {
    $amount = 1000;
    $expectedCommission = 10; // floor(1000/500) * 5
    $expectedSafeDeduction = $amount - $expectedCommission; // 990

    $initialSafeBalance = $this->safe->current_balance;
    $initialLineBalance = $this->line->current_balance;

    Livewire::test(Receive::class)
        ->set('clientMobile', '01333333333')
        ->set('clientName', 'Jane Smith')
        ->set('senderMobile', '01444444444')
        ->set('amount', $amount)
        ->set('selectedLineId', $this->line->id)
        ->call('submitTransaction');

    // Verify business logic:
    // 1. Line balance should increase by full amount
    $this->line->refresh();
    expect($this->line->current_balance)->toBe($initialLineBalance + $amount);

    // 2. Safe balance should decrease by (amount - commission)
    $this->safe->refresh();
    expect($this->safe->current_balance)->toBe($initialSafeBalance - $expectedSafeDeduction);

    // 3. Commission is logged correctly
    $transaction = Transaction::latest()->first();
    expect($transaction->commission)->toBe((float)$expectedCommission);
    expect($transaction->amount)->toBe((float)$amount);
});
