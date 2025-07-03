<div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">General Supervisor Dashboard Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
    </div>
</div> 