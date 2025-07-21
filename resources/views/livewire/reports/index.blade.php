<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
                    <p class="text-sm text-gray-500">All transactions (ordinary & cash) with full filtering and sorting</p>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filter Section -->
        <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-8">
            <form wire:submit.prevent="generateReport" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input wire:model="startDate" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl" />
                    </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                    <input wire:model="endDate" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-xl" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Agent</label>
                    <select wire:model="selectedUser" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">All</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Branch</label>
                    <select wire:model="selectedBranch" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">All</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer Name</label>
                    <input wire:model="selectedCustomer" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="Enter customer name" />
                        </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer Code</label>
                    <input wire:model="selectedCustomerCode" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="Enter customer code" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                    <select wire:model="selectedTransactionType" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">All</option>
                                @foreach ($transactionTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">Generate Report</button>
                    <button type="button" wire:click="exportExcel" class="px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition">Export Excel</button>
                    <button type="button" wire:click="exportPdf" class="px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition">Export PDF</button>
                </div>
            </form>
        </div>
        <!-- Financial Summary Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">Financial Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 rounded-xl">
                    <div class="text-xs text-gray-500">Total Transfer</div>
                    <div class="text-lg font-bold">{{ number_format($financialSummary['total_transfer'] ?? 0, 2) }} EGP</div>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <div class="text-xs text-gray-500">Commission Earned</div>
                    <div class="text-lg font-bold">{{ number_format($financialSummary['commission_earned'] ?? 0, 2) }} EGP</div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-xl">
                    <div class="text-xs text-gray-500">Total Discounts</div>
                    <div class="text-lg font-bold">{{ number_format($financialSummary['total_discounts'] ?? 0, 2) }} EGP</div>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl">
                    <div class="text-xs text-gray-500">Net Profit</div>
                    <div class="text-lg font-bold">{{ number_format($financialSummary['net_profit'] ?? 0, 2) }} EGP</div>
                </div>
            </div>
        </div>
        <!-- Safe Balances Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">Safe Balances by Branch</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($safeBalances as $safe)
                    <div class="p-4 bg-gray-50 rounded-xl flex justify-between">
                        <span>{{ $safe['branch'] }}</span>
                        <span class="font-bold">{{ number_format($safe['balance'], 2) }} EGP</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Line Balances Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">Line Balances by Branch</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($lineBalances as $line)
                    <div class="p-4 bg-gray-50 rounded-xl flex justify-between">
                        <span>{{ $line['branch'] }}</span>
                        <span class="font-bold">{{ number_format($line['balance'], 2) }} EGP</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Customer Balances Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">Customer Wallet Balances</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($customerBalances as $customer)
                    <div class="p-4 bg-gray-50 rounded-xl flex justify-between">
                        <span>{{ $customer['customer'] }}</span>
                        <span class="font-bold">{{ number_format($customer['balance'], 2) }} EGP</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Transactions Table -->
        <div class="bg-white rounded-2xl shadow border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 px-8 pt-8">All Transactions</h3>
            <div class="overflow-x-auto px-8 pb-8">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('customer_name')">
                                <div class="flex items-center space-x-1">
                                    <span>Customer</span>
                                @if ($sortField === 'customer_name')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Customer Code</th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('amount')">
                                <div class="flex items-center space-x-1">
                                    <span>Amount</span>
                                @if ($sortField === 'amount')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('commission')">
                                <div class="flex items-center space-x-1">
                                    <span>Commission</span>
                                @if ($sortField === 'commission')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('transaction_type')">
                                <div class="flex items-center space-x-1">
                                    <span>Type</span>
                                @if ($sortField === 'transaction_type')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('agent_name')">
                                <div class="flex items-center space-x-1">
                                    <span>Agent</span>
                                @if ($sortField === 'agent_name')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('status')">
                                <div class="flex items-center space-x-1">
                                    <span>Status</span>
                                @if ($sortField === 'status')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header" wire:click="sortBy('transaction_date_time')">
                                <div class="flex items-center space-x-1">
                                    <span>Date</span>
                                    @if ($sortField === 'transaction_date_time')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">{{ $transaction['customer_name'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['customer_code'] ?? '' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ number_format($transaction['amount'], 2) }} EGP</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ number_format($transaction['commission'], 2) }} EGP</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['transaction_type'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['agent_name'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['status'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ \Carbon\Carbon::parse($transaction['transaction_date_time'])->format('Y-m-d h:i A') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['reference_number'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-gray-500 text-center">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
