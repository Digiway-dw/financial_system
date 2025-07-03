<div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Admin Dashboard Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Total Users -->
        <a href="{{ route('users.index') }}" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-users class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Users</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $totalUsers }}</p>
                </div>
            </div>
        </a>

        <!-- Total Branches -->
        <a href="{{ route('branches.index') }}" class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-building-office-2 class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Branches</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $totalBranches }}</p>
                </div>
            </div>
        </a>

        <!-- Total Lines -->
        <a href="{{ route('lines.index') }}" class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-phone class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Lines</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $totalLines }}</p>
                </div>
            </div>
        </a>

        <!-- Total Safes -->
        <a href="{{ route('safes.index') }}" class="bg-gradient-to-br from-red-500 to-pink-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-banknotes class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Safes</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $totalSafes }}</p>
                </div>
            </div>
        </a>

        <!-- Total Customers -->
        <a href="{{ route('customers.index') }}" class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-identification class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Customers</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $totalCustomers }}</p>
                </div>
            </div>
        </a>

        <!-- Total Transactions -->
        <a href="{{ route('transactions.index') }}" class="bg-gradient-to-br from-fuchsia-500 to-purple-700 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-receipt-percent class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Transactions</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $totalTransactions }}</p>
                </div>
            </div>
        </a>

        <!-- Total Amount Transferred -->
        <div class="bg-gradient-to-br from-emerald-500 to-lime-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-currency-dollar class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Total Amount Transferred</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($totalTransferred, 2) }} EGP</p>
                </div>
            </div>
        </div>

        <!-- Net Profits -->
        <div class="bg-gradient-to-br from-rose-500 to-orange-700 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-chart-bar class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Net Profits</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($netProfits, 2) }} EGP</p>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <a href="{{ route('transactions.pending') }}" class="bg-gradient-to-br from-amber-500 to-yellow-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Pending Transactions</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $pendingTransactionsCount }}</p>
                </div>
            </div>
        </a>
    </div>
</div> 