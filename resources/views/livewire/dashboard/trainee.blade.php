<div>
<!-- Trainee Summary Table: Safe Name, Safe Balance, Startup Balance, Today's Transactions -->


<!-- Add Customer Quick Action for Trainee -->
@can('manage-customers')
<a href="{{ route('customers.create') }}"
    class="group flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-xl transition-all duration-200 mb-6">
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

@if(request()->query('search_transaction'))
    <div class="mb-6 bg-white rounded-xl shadow p-6 flex flex-col items-center">
        <form method="GET" action="{{ route('dashboard') }}" class="w-full max-w-md flex flex-col gap-4">
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
                    <div><span class="font-semibold">Amount:</span> {{ number_format($searchedTransaction->amount, 2) }}</div>
                    <div><span class="font-semibold">Status:</span> {{ $searchedTransaction->status }}</div>
                    <div><span class="font-semibold">Date:</span> {{ $searchedTransaction->created_at }}</div>
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
                </div>
            </div>
        @elseif(request('reference_number'))
            <div class="mt-4 text-red-600 font-semibold">No transaction found with this reference number.</div>
        @endif
    </div>
@endif
@include('livewire.dashboard.agent', $data ?? [])
@if(isset($traineeLines) && count($traineeLines))
    <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
        <h4 class="text-xl font-bold text-gray-900 mb-4">All Lines in Your Branch</h4>
        <div class="mb-4">
            <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
            <span class="text-2xl font-bold text-blue-700">{{ format_int($traineeLinesTotalBalance ?? 0) }} EGP</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-3 text-center">
                            <input type="checkbox" wire:click="toggleSelectAllTraineeLines" @if(isset($selectedTraineeLineIds) && count($selectedTraineeLineIds) === count($traineeLines) && count($traineeLines) > 0) checked @endif />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mobile Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Daily Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Daily Receive</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Monthly Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Monthly Receive</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Network</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($traineeLines as $line)
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
                                <input type="checkbox" wire:click="toggleSelectTraineeLine({{ $line->id }})" @if(isset($selectedTraineeLineIds) && in_array($line->id, $selectedTraineeLineIds)) checked @endif />
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
                        <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($selectedTraineeTotals['current_balance'] ?? 0) }} EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($selectedTraineeTotals['daily_limit'] ?? 0) }} EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($selectedTraineeTotals['daily_usage'] ?? 0) }} EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($selectedTraineeTotals['monthly_limit'] ?? 0) }} EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700">{{ format_int($selectedTraineeTotals['monthly_usage'] ?? 0) }} EGP</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
</div>
