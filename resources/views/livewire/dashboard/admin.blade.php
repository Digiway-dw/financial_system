<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Admin Dashboard</h3>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Welcome, Admin! You have full access to manage users, branches, safes, lines, reports, and approvals.
    </p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Users</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Branches</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalBranches }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Lines</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalLines }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Safes</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalSafes }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Customers</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Transactions</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalTransactions }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Total Amount Transferred</h4>
            <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalTransferred, 2) }} EGP</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Net Profits</h4>
            <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($netProfits, 2) }} EGP</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Pending Transactions</h4>
            <p class="mt-1 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingTransactionsCount }}</p>
        </div>
    </div>
</div> 