<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    // Customer Management Routes
    Route::get('customers', \App\Livewire\Customers\Index::class)
        ->name('customers.index');
    Route::get('customers/create', \App\Livewire\Customers\Create::class)
        ->name('customers.create');
    Route::get('customers/{customerId}/edit', \App\Livewire\Customers\Edit::class)
        ->name('customers.edit');
    Route::get('customers/{customerId}/view', \App\Livewire\Customers\View::class)->name('customers.view');

    // Transaction Management Routes
    Route::get('transactions', \App\Livewire\Transactions\Index::class)
        ->name('transactions.index');
    Route::get('transactions/send', \App\Livewire\Transactions\Send::class)
        ->name('transactions.send');
    Route::get('transactions/receive', \App\Livewire\Transactions\Receive::class)
        ->name('transactions.receive');
    Route::get('transactions/cash', \App\Livewire\Transactions\Cash::class)
        ->name('transactions.cash');
    Route::get('transactions/cash/withdrawal', \App\Livewire\Transactions\Withdrawal::class)
        ->name('transactions.cash.withdrawal');
    Route::get('transactions/cash/deposit', \App\Livewire\Transactions\Deposit::class)
        ->name('transactions.cash.deposit');
    Route::get('transactions/pending', \App\Livewire\Transactions\Pending::class)
        ->name('transactions.pending');
    Route::get('transactions/{transactionId}/edit', \App\Livewire\Transactions\Edit::class)
        ->name('transactions.edit');

    // Line Management Routes
    Route::get('lines', \App\Livewire\Lines\Index::class)
        ->name('lines.index');
    Route::get('lines/create', \App\Livewire\Lines\Create::class)
        ->name('lines.create');
    Route::get('lines/{lineId}/edit', \App\Livewire\Lines\Edit::class)
        ->name('lines.edit');
    Route::get('lines/{lineId}/transfer', \App\Livewire\Lines\Transfer::class)
        ->name('lines.transfer');
    Route::get('lines/{lineId}/change-provider', \App\Livewire\Lines\ChangeProvider::class)
        ->name('lines.change-provider');

    // Safe Management Routes
    Route::get('safes', \App\Livewire\Safes\Index::class)
        ->name('safes.index');
    Route::get('safes/{safeId}/edit', \App\Livewire\Safes\Edit::class)
        ->name('safes.edit');
    Route::get('safes/move', \App\Livewire\Safes\Move::class)
        ->name('safes.move');

    // Branch Management Routes
    Route::get('branches', \App\Livewire\Branches\Index::class)
        ->name('branches.index');
    Route::get('branches/create', \App\Livewire\Branches\Create::class)
        ->name('branches.create');
    Route::get('branches/{branchId}/edit', \App\Livewire\Branches\Edit::class)
        ->name('branches.edit');

    // User Management Routes
    Route::get('users', \App\Livewire\Users\Index::class)
        ->name('users.index')
        ->withoutMiddleware(['auth']);
    Route::get('users/create', \App\Livewire\Users\Create::class)
        ->name('users.create')
        ->withoutMiddleware(['auth']);
    Route::get('users/{userId}/edit', \App\Livewire\Users\Edit::class)
        ->name('users.edit')
        ->withoutMiddleware(['auth']);
    Route::get('users/{userId}/view', \App\Livewire\Users\View::class)
        ->name('users.view')
        ->withoutMiddleware(['auth']);

    // Reports Routes
    Route::get('reports', \App\Livewire\Reports\Index::class)
        ->name('reports.index');

    // Audit Log Routes
    Route::get('audit-log', \App\Livewire\AuditLog\Index::class)
        ->name('audit-log.index');

    // Permissions Routes
    Route::get('permissions', \App\Livewire\Permissions\Index::class)
        ->name('permissions.index')
        ->middleware('role:admin');
    Route::get('permissions/roles', \App\Livewire\Permissions\RolePermissions::class)
        ->name('permissions.roles')
        ->middleware('role:admin');

    // Notifications Routes
    Route::get('notifications', \App\Livewire\AdminNotificationsBox::class)->name('notifications.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('profile', function () {
        return view('profile', [
            'user' => Auth::user()
        ]);
    })->name('profile');

    Route::patch('/profile', function () {
        $user = \App\Domain\Entities\User::find(Auth::id());

        request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->name = request('name');
        $user->email = request('email');
        $user->save();

        return back()->with('status', 'profile-updated');
    })->name('profile.update');

    Route::put('/password', function () {
        $validated = request()->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = \App\Domain\Entities\User::find(Auth::id());
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('status', 'password-updated');
    })->name('password.update');

    Route::delete('/profile', function () {
        request()->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = \App\Domain\Entities\User::find(Auth::id());

        Auth::logout();

        $user->delete();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('profile.destroy');
});

Route::get('/test-icons', function () {
    return view('test-icons');
})->name('test-icons');

// Special route for user edit without middleware
Route::get('direct-user-edit/{userId}', function ($userId) {
    return app()->call(\App\Livewire\Users\Edit::class, ['userId' => $userId]);
})->name('direct-user-edit');

// Simple test route
Route::get('test-user-edit/{userId}', function ($userId) {
    $user = \App\Domain\Entities\User::findOrFail($userId);
    return view('test-user-edit', [
        'user' => $user
    ]);
})->name('test-user-edit');

// Route to handle the form submission
Route::post('test-user-update/{userId}', function ($userId) {
    $user = \App\Domain\Entities\User::findOrFail($userId);
    $user->name = request('name');
    $user->email = request('email');
    $user->save();
    return redirect()->route('test-user-edit', ['userId' => $userId])->with('success', 'User updated successfully!');
})->name('test-user-update');

require __DIR__ . '/auth.php';
