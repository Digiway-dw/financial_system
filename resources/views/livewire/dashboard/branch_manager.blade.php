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
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Branch Manager Dashboard Overview</h3>
    <div class="flex items-center justify-end mb-2">
        <span class="ml-2 text-base font-bold text-gray-900">{{ $branchName }}</span>
        <span class="text-sm font-medium text-gray-700"> : الفرع</span>

    </div>
    <!-- Remove branch selector and branch details section -->
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border">رصيد افتتاحي</th>
                <th class="px-4 py-2 border">عدد المعاملات</th>
                <th class="px-4 py-2 border">الخزينة</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td class="px-4 py-2 border text-blue-700 font-bold">{{ number_format($startupSafeBalance, 2) }}</td>
                <td class="px-4 py-2 border text-purple-700 font-bold">{{ $totalTransactionsCount }}</td>
                <td class="px-4 py-2 border font-bold">{{ number_format($safesBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Quick Actions Section -->
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



    @if(isset($branchLines) && $branchLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
                <span class="text-2xl font-bold text-blue-700">{{ number_format($branchLinesTotalBalance ?? 0, 2) }} EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
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
                                Daily Limit
                                @if ($sortField === 'daily_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_limit')" style="cursor: pointer;">
                                Monthly Limit
                                @if ($sortField === 'monthly_limit')
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
                        @foreach($branchLines as $line)
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
</div> 