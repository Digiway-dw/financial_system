<div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Branch Manager Dashboard Overview - {{ $branchName ?? 'N/A' }}</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Branch Safe Balance -->
        <a href="{{ route('safes.index') }}" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-banknotes class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Branch Safe Balance</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($branchSafeBalance ?? 0, 2) }} EGP</p>
                </div>
            </div>
        </a>

        <!-- Pending Transactions (Branch) -->
        <a href="{{ route('transactions.pending') }}" class="bg-gradient-to-br from-amber-500 to-yellow-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Pending Transactions (Branch)</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $branchPendingTransactionsCount ?? 0 }}</p>
                </div>
            </div>
        </a>

        <!-- Branch Users -->
        <a href="{{ route('users.index') }}" class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-users class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Branch Users</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $branchUsersCount ?? 0 }}</p>
                </div>
            </div>
        </a>
    </div>
</div> 