<!-- Trainee Summary Table: Startup Balance, Today's Transactions, Safe Balance -->
<table class="min-w-max w-full table-auto border border-gray-300 mb-6">
    <thead>
        <tr class="bg-gray-100 text-center">
            <th class="px-4 py-2 border">Startup Balance</th>
            <th class="px-4 py-2 border">Today's Transactions</th>
            <th class="px-4 py-2 border">Safe Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr class="text-center">
            <td class="px-4 py-2 border text-blue-700 font-bold">{{ number_format($startupSafeBalance ?? 0, 2) }}</td>
            <td class="px-4 py-2 border text-purple-700 font-bold">{{ $agentTodayTransactionsCount ?? 0 }}</td>
            <td class="px-4 py-2 border font-bold">{{ number_format($safesBalance ?? 0, 2) }}</td>
        </tr>
    </tbody>
</table>
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
                    <a href="{{ route('transactions.print', $searchedTransaction->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
