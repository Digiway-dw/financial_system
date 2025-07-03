<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

    // Transaction Management Routes
    Route::get('transactions', \App\Livewire\Transactions\Index::class)
        ->name('transactions.index');
    Route::get('transactions/create', \App\Livewire\Transactions\Create::class)
        ->name('transactions.create');
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
    Route::get('safes/create', \App\Livewire\Safes\Create::class)
        ->name('safes.create');
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
        ->name('users.index');
    Route::get('users/create', \App\Livewire\Users\Create::class)
        ->name('users.create');

    // Reports Routes
    Route::get('reports', \App\Livewire\Reports\Index::class)
        ->name('reports.index');

    // Audit Log Routes
    Route::get('audit-log', \App\Livewire\AuditLog\Index::class)
        ->name('audit-log.index');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
