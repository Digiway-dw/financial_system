<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Branch Manager Dashboard</h3>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Welcome, Branch Manager ({{ $branchName ?? 'N/A' }})! You can manage branch-specific users and review trainee operations.
    </p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Branch Safe Balance</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($branchSafeBalance ?? 0, 2) }} EGP</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Pending Transactions (Branch)</h4>
            <p class="mt-1 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $branchPendingTransactionsCount ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Branch Users</h4>
            <p class="mt-1 text-3xl font-bold text-teal-600 dark:text-teal-400">{{ $branchUsersCount ?? 0 }}</p>
        </div>
    </div>
</div> 