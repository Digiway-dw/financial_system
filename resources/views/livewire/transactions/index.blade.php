<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
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
                    <h1 class="text-2xl font-bold text-gray-900">Transaction Management</h1>
                    <p class="text-sm text-gray-500">Monitor, process, and manage all financial transactions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Transaction Actions Section -->
        <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-6">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    Quick Actions
                </h2>
                <p class="text-sm text-gray-500 mt-1">Create new transactions or access transaction tools</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('transactions.send') }}" class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-xl transition-all duration-200">
                    <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-blue-900">Send Money</h3>
                        <p class="text-sm text-blue-700">Create outgoing transfer</p>
                    </div>
                </a>
                <a href="{{ route('transactions.receive') }}" class="group flex items-center p-4 bg-green-50 hover:bg-green-100 border border-green-100 rounded-xl transition-all duration-200">
                    <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-green-900">Receive Money</h3>
                        <p class="text-sm text-green-700">Process incoming transfer</p>
                    </div>
                </a>
                @can('create-cash-transactions')
                    <a href="{{ route('transactions.cash') }}" class="group flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 border border-yellow-100 rounded-xl transition-all duration-200">
                        <div class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-yellow-900">Cash Transaction</h3>
                            <p class="text-sm text-yellow-700">Handle cash operations</p>
                        </div>
                    </a>
                @endcan
            </div>
        </div>
        <!-- Transaction Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Total Transactions</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ count($transactions) }}</p>
                        @if (auth()->user()->hasRole('agent'))
                            <p class="text-xs text-gray-400">Your transactions only</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Total Volume</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format(array_sum(array_column($transactions, 'amount')), 2) }} EGP</p>
                        @if (auth()->user()->hasRole('agent'))
                            <p class="text-xs text-gray-400">Your volume only</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Pending</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ count(array_filter($transactions, fn($t) => $t['status'] === 'pending')) }}</p>
                        @if (auth()->user()->hasRole('agent'))
                            <p class="text-xs text-gray-400">Your pending transactions</p>
                        @endif
                    </div>
                </div>
            </div>
            @can('view-commission-data')
                <div class="bg-white rounded-2xl shadow border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Commission</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format(array_sum(array_column($transactions, 'commission')), 2) }} EGP</p>
                            @if (auth()->user()->hasRole('agent'))
                                <p class="text-xs text-gray-400">Your commissions only</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan
        </div>
        <!-- Advanced Filter Section -->
        <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-8">
            <div class="border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                        </svg>
                    </div>
                    Advanced Filters
                </h2>
                <p class="text-sm text-gray-500 mt-1">Filter transactions by multiple criteria for detailed analysis</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Customer Code -->
                <div class="space-y-2">
                    <label for="customer_code" class="block text-sm font-medium text-gray-700">Customer Code</label>
                    <input wire:model.defer="customer_code" id="customer_code" type="text" placeholder="Enter customer code..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-white" />
                </div>
                <!-- Receiver Mobile -->
                <div class="space-y-2">
                    <label for="receiver_mobile" class="block text-sm font-medium text-gray-700">Receiver
                        Mobile</label>
                    <input wire:model.defer="receiver_mobile" id="receiver_mobile" type="text"
                        placeholder="Enter mobile number..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- Transfer Line -->
                <div class="space-y-2">
                    <label for="transfer_line" class="block text-sm font-medium text-gray-700">Transfer Line</label>
                    <input wire:model.defer="transfer_line" id="transfer_line" type="text"
                        placeholder="Enter line number..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- Amount -->
                <div class="space-y-2">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <input wire:model.defer="amount" id="amount" type="number" step="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- Commission -->
                <div class="space-y-2">
                    <label for="commission" class="block text-sm font-medium text-gray-700">Commission</label>
                    <input wire:model.defer="commission" id="commission" type="number" step="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- Transaction Type -->
                <div class="space-y-2">
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700">Transaction
                        Type</label>
                    <select wire:model.defer="transaction_type" id="transaction_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                        <option value="">All Types</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Deposit">Deposit</option>
                        <option value="Withdrawal">Withdrawal</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="space-y-2">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input wire:model.defer="start_date" id="start_date" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- End Date -->
                <div class="space-y-2">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input wire:model.defer="end_date" id="end_date" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- Branch Filter -->
                <div class="space-y-2">
                    <label for="branch_ids" class="block text-sm font-medium text-gray-700">Branches</label>
                    <select wire:model.defer="branch_ids" id="branch_ids" multiple
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm"
                        @cannot('view-all-branches-data') disabled @endcannot>
                        @foreach (\App\Models\Domain\Entities\Branch::all() as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @cannot('view-all-branches-data')
                        <p class="text-xs text-gray-500 mt-1">You can only view data from your assigned branch.</p>
                    @endcannot
                </div>

                <!-- Employee Filter -->
                <div class="space-y-2">
                    <label for="employee_ids" class="block text-sm font-medium text-gray-700">Employees</label>
                    <select wire:model.defer="employee_ids" id="employee_ids" multiple
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-white"
                        @cannot('view-other-employees-data') disabled @endcannot>
                        <option value="" @if(empty($employee_ids) || in_array('', (array)$employee_ids)) selected @endif>All Employees</option>
                        @foreach (\App\Domain\Entities\User::all() as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @cannot('view-other-employees-data')
                        <p class="text-xs text-gray-500 mt-1">You can only view your own transactions.</p>
                    @endcannot
                </div>

                <!-- Filter Actions -->
                <div class="space-y-2 flex flex-col justify-end lg:col-span-2">
                    <div class="flex space-x-3">
                        <button wire:click="filter"
                            class="flex-1 px-4 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                                </path>
                            </svg>
                            Apply Filters
                        </button>
                        <button wire:click="resetFilters"
                            type="button"
                            class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition-all duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5 9A7 7 0 0119 15M19 15a7 7 0 01-14 0"></path>
                            </svg>
                            Reset Filters
                        </button>
                        <a href="{{ route('transactions.pending') }}"
                            class="px-4 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 font-medium rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pending
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <div class="bg-white rounded-2xl shadow border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mobile Number</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Commission</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Agent</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 bg-gray-50">Actions</th>
                    </tr>
                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $transaction['customer_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction['customer_mobile_number'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ number_format($transaction['amount'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ number_format($transaction['commission'], 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction['transaction_type'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction['agent_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaction['status'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('transactions.edit', $transaction['id']) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                    @can('edit-all-transactions')
                                        Edit
                                    @elsecan('edit-own-transactions')
                                        @if ($transaction['agent_id'] == auth()->id())
                                            Edit
                                        @endif
                                    @endcan
                                </a>
                                @can('delete-transactions')
                                        <button wire:click="deleteTransaction('{{ $transaction['id'] }}')" class="text-red-600 hover:text-red-800">Delete</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                                <td colspan="9" class="px-6 py-4 text-gray-500 text-center">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
