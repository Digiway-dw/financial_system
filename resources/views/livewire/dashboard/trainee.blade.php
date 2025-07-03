<div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Trainee Dashboard Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Your Total Line Balance -->
        <a href="{{ route('lines.index') }}" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-wallet class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Total Line Balance</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($agentTotalBalance ?? 0, 2) }} EGP</p>
                </div>
            </div>
        </a>

        <!-- Your Total Transferred Amount -->
        <div class="bg-gradient-to-br from-emerald-500 to-lime-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-currency-dollar class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Total Transferred Amount</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($agentTotalTransferred ?? 0, 2) }} EGP</p>
                </div>
            </div>
        </div>

        <!-- Your Pending Transactions -->
        <a href="{{ route('transactions.pending') }}" class="bg-gradient-to-br from-amber-500 to-yellow-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Pending Transactions</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $agentPendingTransactionsCount ?? 0 }}</p>
                </div>
            </div>
        </a>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 p-6 shadow-xl sm:rounded-lg">
        <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Your Lines Overview</h4>
        <div class="mt-4 overflow-x-auto">
            @if ($agentLines && count($agentLines) > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Mobile Number</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Daily Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Daily Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Monthly Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Monthly Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($agentLines as $line)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $line['mobile_number'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($line['current_balance'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($line['daily_limit'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($line['daily_usage'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($line['monthly_limit'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ number_format($line['monthly_usage'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold @if($line['status'] === 'active') text-green-500 @else text-red-500 @endif">{{ ucfirst($line['status']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No lines assigned to you.</p>
            @endif
        </div>
    </div>
</div>