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
</div>
