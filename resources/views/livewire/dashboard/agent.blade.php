<div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Agent Dashboard Overview</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Your Total Line Balance -->
        <a href="{{ route('lines.index') }}"
            class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-wallet class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Total Line Balance</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($agentTotalBalance ?? 0, 2) }}
                        EGP</p>
                    <p class="text-white text-sm mt-1">Available for transactions</p>
                </div>
            </div>
        </a>

        <!-- Your Total Transferred Amount -->
        <div
            class="bg-gradient-to-br from-emerald-500 to-lime-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-currency-dollar class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Total Transferred Amount</p>
                    <p class="text-white text-4xl font-extrabold mt-1">
                        {{ number_format($agentTotalTransferred ?? 0, 2) }} EGP</p>
                    <p class="text-white text-sm mt-1">All time total volume</p>
                </div>
            </div>
        </div>

        <!-- Your Pending Transactions -->
        <a href="{{ route('transactions.pending') }}"
            class="bg-gradient-to-br from-amber-500 to-yellow-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Pending Transactions</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $agentPendingTransactionsCount ?? 0 }}</p>
                    <p class="text-white text-sm mt-1">Require attention</p>
                </div>
            </div>
        </a>
    </div>

    <div class="mt-8 bg-white dark:bg-gray-800 p-6 shadow-xl sm:rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Your Lines Overview</h4>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3" />
                    </svg>
                    Agent-only view
                </span>
            </div>
        </div>
        <div class="mt-4 overflow-x-auto">
            @if ($agentLines && count($agentLines) > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Mobile Number</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Balance</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Daily Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Daily Usage</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Monthly Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Monthly Usage</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($agentLines as $line)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $line['mobile_number'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($line['current_balance'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($line['daily_limit'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($line['daily_usage'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($line['monthly_limit'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($line['monthly_usage'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                    @if ($line['status'] === 'active')
                                        <span class="text-emerald-500">{{ ucfirst($line['status']) }}</span>
                                    @else
                                        <span class="text-rose-500">{{ ucfirst($line['status']) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No lines assigned to you.</p>
            @endif
        </div>
    </div>

    <div class="mt-8 bg-white dark:bg-gray-800 p-6 shadow-xl sm:rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Quick Actions</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <a href="{{ route('transactions.send') }}"
                class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900 dark:hover:bg-blue-800 rounded-lg transition-colors duration-200">
                <div class="mr-4 bg-blue-100 dark:bg-blue-700 p-3 rounded-full">
                    <x-heroicon-o-paper-airplane class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                </div>
                <div>
                    <h5 class="font-medium text-blue-900 dark:text-blue-100">Send Money</h5>
                    <p class="text-sm text-blue-600 dark:text-blue-300">Create a new send transaction</p>
                </div>
            </a>

            <a href="{{ route('transactions.receive') }}"
                class="flex items-center p-4 bg-green-50 hover:bg-green-100 dark:bg-green-900 dark:hover:bg-green-800 rounded-lg transition-colors duration-200">
                <div class="mr-4 bg-green-100 dark:bg-green-700 p-3 rounded-full">
                    <x-heroicon-o-arrow-down-tray class="h-6 w-6 text-green-600 dark:text-green-300" />
                </div>
                <div>
                    <h5 class="font-medium text-green-900 dark:text-green-100">Receive Money</h5>
                    <p class="text-sm text-green-600 dark:text-green-300">Process incoming transfers</p>
                </div>
            </a>

            <a href="{{ route('transactions.index') }}"
                class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900 dark:hover:bg-purple-800 rounded-lg transition-colors duration-200">
                <div class="mr-4 bg-purple-100 dark:bg-purple-700 p-3 rounded-full">
                    <x-heroicon-o-list-bullet class="h-6 w-6 text-purple-600 dark:text-purple-300" />
                </div>
                <div>
                    <h5 class="font-medium text-purple-900 dark:text-purple-100">Your Transactions</h5>
                    <p class="text-sm text-purple-600 dark:text-purple-300">View all your transactions</p>
                </div>
            </a>
        </div>
    </div>
</div>
