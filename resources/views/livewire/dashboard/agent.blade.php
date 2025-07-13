<div>
@if(isset($showAdminAgentToggle) && $showAdminAgentToggle && request()->query('as_agent'))
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="get" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <input type="hidden" name="as_agent" value="1">
            <label for="branches" class="font-semibold text-gray-700">Select Branches:</label>
            <select name="branches[]" id="branches" multiple class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" onchange="
                if ([...this.options].find(o => o.value === 'all' && o.selected)) {
                    [...this.options].forEach(o => o.selected = true);
                }
                this.form.submit();">
                <option value="all" @if(empty($selectedBranches) || (isset($branches) && count($selectedBranches) == $branches->count())) selected @endif>All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @if(in_array($branch->id, $selectedBranches ?? [])) selected @endif>{{ $branch->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Apply</button>
        </form>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-800 transition">
            Switch to Main Admin Dashboard
        </a>
    </div>
@endif
<h2 class="text-2xl font-bold text-gray-900 mb-6">Agent Dashboard Overview</h2>
@if(isset($totalSafeBalance))
    <div class="text-lg text-gray-700 mb-1">Total Safe Balance: <span class="font-bold">{{ number_format($totalSafeBalance, 2) }} EGP</span></div>
@endif
@if(isset($branchSafeBalance))
    <div class="text-lg text-gray-700 mb-4">Branch Safe Balance: <span class="font-bold">{{ number_format($branchSafeBalance, 2) }} EGP</span></div>
@endif

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

    @if(isset($agentLines) && $agentLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">All Lines in Your Branch</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
                <span class="text-2xl font-bold text-blue-700">{{ number_format($agentLinesTotalBalance ?? 0, 2) }} EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mobile Number</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Daily Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Monthly Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Network</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($agentLines as $line)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->mobile_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($line->current_balance, 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($line->daily_limit, 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($line->monthly_limit, 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->network }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($line->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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
