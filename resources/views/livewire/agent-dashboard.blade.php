<div>
    <style>
        .sortable-header {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .sortable-header:hover {
            background-color: #f3f4f6 !important;
            transform: translateY(-1px);
        }
        .sortable-header:active {
            transform: translateY(0);
        }
    </style>

    <!-- Search Transactions Section (always at the top) -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-blue-100">
            <h2 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Search Transactions by Reference Number
            </h2>
            <form method="GET" action="{{ route('agent-dashboard') }}" class="w-full max-w-md flex flex-col gap-4">
                <div class="flex gap-2">
                    <input type="text" name="reference_number" id="reference_number" value="{{ request('reference_number') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="Enter reference number...">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
                </div>
            </form>
            @if(request('reference_number'))
                @if(isset($searchedTransaction) && $searchedTransaction)
                    <div class="mt-6 w-full max-w-md bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-bold text-blue-800 mb-2">Transaction Details</h4>
                        <div class="text-sm text-gray-700">
                            <div><span class="font-semibold">Reference:</span> {{ $searchedTransaction->reference_number }}</div>
                            <div><span class="font-semibold">Amount:</span> {{ format_int($searchedTransaction->amount) }}</div>
                            <div><span class="font-semibold">Status:</span> {{ $searchedTransaction->status }}</div>
                            <div><span class="font-semibold">Date:</span> {{ \Carbon\Carbon::parse($searchedTransaction->created_at)->format('d/m/y h:i A') }}</div>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('transactions.print', $searchedTransaction->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v9a2 2 0 01-2 2h-2" />
                                    <rect width="12" height="8" x="6" y="14" rx="2" />
                                </svg>
                                Print
                            </a>
                            <a href="{{ $searchedTransaction instanceof \App\Models\Domain\Entities\Transaction ? route('transactions.details', $searchedTransaction->id) : route('transactions.cash.details', $searchedTransaction->id) }}" class="ml-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0A9 9 0 11 3 12a9 9 0 0118 0z" />
                                </svg>
                                View
                            </a>
                        </div>
                    </div>
                @else
                    <div class="mt-4 text-red-600 font-semibold">No transaction found with this reference number.</div>
                @endif
            @endif
        </div>
    </div>

    @if($isAdminOrSupervisor)
    <!-- Branch Selection and Switch Button for Admin/Supervisor only -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-2">
            <label for="branches" class="font-semibold text-gray-700">Select Branches:</label>
            <select wire:model.live="selectedBranches" id="branches" multiple
                class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                <option value="all" wire:click="selectAllBranches">All Branches</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @if (in_array($branch->id, $selectedBranches)) selected @endif>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <button type="button" wire:click="selectAllBranches" 
                class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs">
                Select All
            </button>
        </div>
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-800 transition">
            Switch to Admin Dashboard
        </a>
    </div>
    @endif

    <!-- Transaction Search -->
    @if(request()->query('search_transaction'))
        <div class="mb-6 bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <form method="GET" action="{{ route('agent-dashboard') }}" class="w-full max-w-md flex flex-col gap-4">
                <input type="hidden" name="search_transaction" value="1">
                <label for="reference_number" class="block text-sm font-medium text-gray-700">Search Transaction by Reference Number</label>
                <div class="flex gap-2">
                    <input type="text" name="reference_number" id="reference_number" value="{{ request('reference_number') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="Enter reference number...">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
                </div>
            </form>
            @if(isset($searchedTransaction))
                <div class="mt-6 w-full max-w-md bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-bold text-blue-800 mb-2">Transaction Details</h4>
                    <div class="text-sm text-gray-700">
                        <div><span class="font-semibold">Reference:</span> {{ $searchedTransaction->reference_number }}</div>
                        <div><span class="font-semibold">Amount:</span> {{ format_int($searchedTransaction->amount) }}</div>
                        <div><span class="font-semibold">Status:</span> {{ $searchedTransaction->status }}</div>
                        <div><span class="font-semibold">Date:</span> {{ \Carbon\Carbon::parse($searchedTransaction->created_at)->format('d/m/y h:i A') }}</div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('transactions.print', $searchedTransaction->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v9a2 2 0 01-2 2h-2" />
                                <rect width="12" height="8" x="6" y="14" rx="2" />
                            </svg>
                            Print
                        </a>
                        <a href="{{ $searchedTransaction instanceof \App\Models\Domain\Entities\Transaction ? route('transactions.details', $searchedTransaction->id) : route('transactions.cash.details', $searchedTransaction->id) }}" class="ml-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0A9 9 0 11 3 12a9 9 0 0118 0z" />
                            </svg>
                            View
                        </a>
                    </div>
                </div>
            @elseif(request('reference_number'))
                <div class="mt-4 text-red-600 font-semibold">No transaction found with this reference number.</div>
            @endif
        </div>
    @endif

    <!-- Agent Summary Table: Safe Name, Safe Balance, Today's Transactions -->
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border">Safe Name</th>
                <th class="px-4 py-2 border">Safe Balance</th>
                <th class="px-4 py-2 border">Today's Transactions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($branchSafes as $safe)
                @if($isAdminOrSupervisor || auth()->user()->branch_id == ($safe['branch_id'] ?? null))
                <tr class="text-center">
                    <td class="px-4 py-2 border font-semibold">{{ $safe['name'] }}</td>
                    <td class="px-4 py-2 border text-blue-700 font-bold">{{ format_int($safe['current_balance']) }}</td>
                    <td class="px-4 py-2 border text-purple-700 font-bold">{{ $safe['todays_transactions'] ?? 0 }}</td>
                </tr>
                @endif
            @empty
                <tr><td colspan="4" class="px-4 py-2 border text-center text-gray-500">No safes found for your branch.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            @if($isAdminOrSupervisor)
            <tr class="bg-gray-50 text-center font-bold">
                <td class="px-4 py-2 border">Total</td>
                <td class="px-4 py-2 border text-blue-700">{{ format_int($totalSafesBalance) }}</td>
                <td class="px-4 py-2 border"></td>
            </tr>
            @endif
        </tfoot>
    </table>

    <!-- Quick Actions -->
    <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-6">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                Quick Actions
            </h2>
            <p class="text-sm text-gray-500 mt-1">Create new transactions or access transaction tools</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('transactions.send') }}"
                class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-xl transition-all duration-200">
                <div
                    class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900">Send Money</h3>
                    <p class="text-sm text-blue-700">Create outgoing transfer</p>
                </div>
            </a>
            <a href="{{ route('transactions.receive') }}"
                class="group flex items-center p-4 bg-green-50 hover:bg-green-100 border border-green-100 rounded-xl transition-all duration-200">
                <div
                    class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">Receive Money</h3>
                    <p class="text-sm text-green-700">Process incoming transfer</p>
                </div>
            </a>

            @can('create-cash-transactions')
                <a href="{{ route('transactions.cash') }}"
                    class="group flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 border border-yellow-100 rounded-xl transition-all duration-200">
                    <div
                        class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-yellow-900">Cash Transaction</h3>
                        <p class="text-sm text-yellow-700">Handle cash operations</p>
                    </div>
                </a>
            @endcan
            @can('manage-customers')
            <a href="{{ route('customers.create') }}"
                class="group flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-xl transition-all duration-200">
                <div
                    class="w-12 h-12 bg-indigo-100 group-hover:bg-indigo-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-indigo-900">Add Customer</h3>
                    <p class="text-sm text-indigo-700">Register a new customer</p>
                </div>
            </a>
            @endcan
        </div>
    </div>

    <!-- Lines Table -->
    @if (isset($agentLines) && $agentLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">All Lines in Selected Branches</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
                <span class="text-2xl font-bold text-blue-700">{{ format_int($agentLinesTotalBalance ?? 0) }}
                    EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-3 text-center">
                                <input type="checkbox" wire:click="toggleSelectAllLines" @if(count($selectedLineIds) === $agentLines->count() && $agentLines->count() > 0) checked @endif />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('mobile_number')" style="cursor: pointer;">
                                Mobile Number
                                @if ($sortField === 'mobile_number')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('current_balance')" style="cursor: pointer;">
                                Balance
                                @if ($sortField === 'current_balance')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('daily_limit')" style="cursor: pointer;">
                                Daily Remaining
                                @if ($sortField === 'daily_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('daily_usage')" style="cursor: pointer;">
                                Daily Receive
                                @if ($sortField === 'daily_usage')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_limit')" style="cursor: pointer;">
                                Monthly Remaining
                                @if ($sortField === 'monthly_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_usage')" style="cursor: pointer;">
                                Monthly Receive
                                @if ($sortField === 'monthly_usage')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('network')" style="cursor: pointer;">
                                Network
                                @if ($sortField === 'network')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('status')" style="cursor: pointer;">
                                Status
                                @if ($sortField === 'status')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($agentLines as $line)
                            @php
                                $dailyLimit = $line->daily_limit ?? 0;
                                $monthlyLimit = $line->monthly_limit ?? 0;
                                $currentBalance = $line->current_balance ?? 0;
                                $dailyStartingBalance = $line->daily_starting_balance ?? 0;
                                $monthlyStartingBalance = $line->starting_balance ?? 0;
                                $dailyRemaining = max(0, $dailyLimit - $currentBalance);
                                $monthlyRemaining = max(0, $monthlyLimit - $currentBalance);
                                $dailyUsage = max(0, $currentBalance - $dailyStartingBalance);
                                $monthlyUsage = max(0, $currentBalance - $monthlyStartingBalance);
                                $usagePercent = $dailyLimit > 0 ? ($dailyUsage / $dailyLimit) * 100 : 0;
                                $circleColor = 'bg-green-400';
                                if ($usagePercent >= 98) {
                                    $circleColor = 'bg-red-500';
                                } elseif ($usagePercent >= 80) {
                                    $circleColor = 'bg-yellow-400';
                                }
                            @endphp
                            <tr>
                                <td class="px-2 py-4 text-center">
                                    <input type="checkbox" wire:click="toggleSelectLine({{ $line->id }})" @if(in_array($line->id, $selectedLineIds)) checked @endif />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-3 h-3 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $circleColor }}"></div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $line->mobile_number }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($line->current_balance) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($dailyRemaining) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($dailyUsage) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($monthlyRemaining) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($monthlyUsage) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->network }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($line->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td class="px-2 py-3 text-center" colspan="2">Selected Total</td>
                            <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($this->selectedTotals['current_balance'] ?? 0) }} EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($this->selectedTotals['daily_limit'] ?? 0) }} EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($this->selectedTotals['daily_usage'] ?? 0) }} EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($this->selectedTotals['monthly_limit'] ?? 0) }} EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($this->selectedTotals['monthly_usage'] ?? 0) }} EGP</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

</div> 