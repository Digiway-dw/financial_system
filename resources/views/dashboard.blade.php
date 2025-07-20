<x-app-layout>
    {{-- Removed the <x-slot name="header"> section with the large welcome message --}}

    @php $user = auth()->user(); @endphp
    @if($user && $user->hasRole('agent'))
        <!-- Search Transactions Section for Agents -->
        <div class="mb-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center border border-blue-100">
                <h2 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Search Transactions by Reference Number
                </h2>
                <form method="GET" action="{{ url()->current() }}" class="w-full max-w-md flex flex-col gap-4">
                    <div class="flex gap-2">
                        <input type="text" name="reference_number" id="reference_number" value="{{ request('reference_number') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="Enter reference number...">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
                    </div>
                </form>
                @php
                    $searchedTransaction = null;
                    $ref = request('reference_number');
                    if ($ref) {
                        $searchedTransaction = \App\Models\Domain\Entities\Transaction::where('reference_number', $ref)->first();
                        if (!$searchedTransaction) {
                            $searchedTransaction = \App\Models\Domain\Entities\CashTransaction::where('reference_number', $ref)->first();
                        }
                    }
                @endphp
                @if(request('reference_number'))
                    @if($searchedTransaction)
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
                    @else
                        <div class="mt-4 text-red-600 font-semibold">No transaction found with this reference number.</div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100">
                <div class="p-6 text-gray-900">

                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-blue-100 to-transparent h-20 rounded-t-xl -mx-6 -mt-6">
                        </div>
                        <div class="relative">
                            @livewire('dashboard')
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
