<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Trainee Dashboard</h3>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Welcome, Trainee! You can perform transactions that require manager/admin approval.
    </p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Your Total Line Balance</h4>
            <p class="mt-1 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($agentTotalBalance ?? 0, 2) }} EGP</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Your Total Transferred Amount</h4>
            <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($agentTotalTransferred ?? 0, 2) }} EGP</p>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Your Pending Transactions</h4>
            <p class="mt-1 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $agentPendingTransactionsCount ?? 0 }}</p>
        </div>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Your Lines Overview</h4>
        <div class="mt-4">
            @if (count($agentLines) > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Mobile Number</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Daily Limit</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Daily Usage</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Monthly Limit</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Monthly Usage</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($agentLines as $line)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $line['mobile_number'] }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['current_balance'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['daily_limit'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['daily_usage'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['monthly_limit'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['monthly_usage'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $line['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">No lines assigned to you.</p>
            @endif
        </div>
    </div>
</div> 