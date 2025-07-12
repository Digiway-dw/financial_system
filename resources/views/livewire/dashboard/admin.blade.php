<div>
    @if (isset($showAdminAgentToggle) && $showAdminAgentToggle)
        <div class="mb-6 flex justify-end">
            <a href="{{ route('dashboard', ['as_agent' => 1]) }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-800 transition">
                Switch to Agent Dashboard View
            </a>
        </div>
    @endif
    <h3 class="text-2xl font-bold text-gray-900 mb-6">Admin Dashboard Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Total Users -->
        <a href="{{ route('users.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-users class="h-10 w-10 text-indigo-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Users</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalUsers }}</p>
                </div>
            </div>
        </a>

        <!-- Total Branches -->
        <a href="{{ route('branches.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-building-office-2 class="h-10 w-10 text-green-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Branches</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalBranches }}</p>
                </div>
            </div>
        </a>

        <!-- Work Sessions -->
        <a href="{{ route('work-sessions.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="h-10 w-10 text-teal-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Work Sessions</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">View</p>
                </div>
            </div>
        </a>

        <!-- Total Lines -->
        <a href="{{ route('lines.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-phone class="h-10 w-10 text-yellow-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Lines</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalLines }}</p>
                </div>
            </div>
        </a>

        <!-- Total Safes -->
        <a href="{{ route('safes.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-banknotes class="h-10 w-10 text-red-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Safes</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalSafes }}</p>
                </div>
            </div>
        </a>

        <!-- Total Customers -->
        <a href="{{ route('customers.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-identification class="h-10 w-10 text-blue-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Customers</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalCustomers }}</p>
                </div>
            </div>
        </a>

        <!-- Total Transactions -->
        <a href="{{ route('transactions.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-receipt-percent class="h-10 w-10 text-purple-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Transactions</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalTransactions }}</p>
                </div>
            </div>
        </a>

        <!-- Total Amount Transferred -->
        <div
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-currency-dollar class="h-10 w-10 text-emerald-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Amount Transferred</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ number_format($totalTransferred, 2) }} EGP
                    </p>
                </div>
            </div>
        </div>

        <!-- Net Profits -->
        <div
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-chart-bar class="h-10 w-10 text-rose-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Net Profits</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ number_format($netProfits, 2) }} EGP</p>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <a href="{{ route('transactions.pending') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 text-amber-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Pending Transactions</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $pendingTransactionsCount }}</p>
                </div>
            </div>
        </a>
    </div>
</div>
