<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reports Section</h3>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        View various financial reports and insights here.
    </p>

    <div class="mt-6 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Filter Reports</h4>
        <form wire:submit.prevent="generateReport" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="startDate" :value="__('Start Date')" />
                    <x-text-input wire:model="startDate" id="startDate" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="endDate" :value="__('End Date')" />
                    <x-text-input wire:model="endDate" id="endDate" type="date" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="selectedUser" :value="__('Filter by Agent')" />
                    <select wire:model="selectedUser" id="selectedUser" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">All Agents</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="selectedBranch" :value="__('Filter by Branch')" />
                    <select wire:model="selectedBranch" id="selectedBranch" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="selectedCustomer" :value="__('Filter by Customer (Name)')" />
                    <x-text-input wire:model="selectedCustomer" id="selectedCustomer" type="text" class="mt-1 block w-full" placeholder="Enter customer name" />
                </div>
                <div>
                    <x-input-label for="selectedTransactionType" :value="__('Filter by Transaction Type')" />
                    <select wire:model="selectedTransactionType" id="selectedTransactionType" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">All Types</option>
                        @foreach ($transactionTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Generate Report') }}</x-primary-button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Report Summary</h4>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transferred</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalTransferred, 2) }} EGP</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Commission Earned</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalCommission, 2) }} EGP</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Deductions</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalDeductions, 2) }} EGP</dd>
            </div>
            <div class="sm:col-span-1">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Profits</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($netProfits, 2) }} EGP</dd>
            </div>
        </dl>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Filtered Transactions</h4>
        <div class="mt-4">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Commission</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $transaction['customer_name'] }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($transaction['amount'], 2) }} EGP</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($transaction['commission'], 2) }} EGP</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['transaction_type'] }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['agent_name'] }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['status'] }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No transactions found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
