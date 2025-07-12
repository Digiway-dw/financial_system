<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Cash Transaction Management</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($canDeposit)
                            <div
                                class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg shadow-md border border-blue-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-medium text-blue-800">Cash Deposit</h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <p class="text-blue-600 mb-4">Add funds to a safe, client wallet, or for administrative
                                    purposes.</p>
                                <a href="{{ route('transactions.cash.deposit') }}"
                                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                    Proceed to Deposit
                                </a>
                            </div>
                        @endif

                        @if ($canWithdraw)
                            <div
                                class="bg-gradient-to-r from-red-50 to-red-100 p-6 rounded-lg shadow-md border border-red-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-medium text-red-800">Cash Withdrawal</h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                </div>
                                <p class="text-red-600 mb-4">Withdraw funds from a safe, client wallet, or for
                                    administrative purposes.</p>
                                <a href="{{ route('transactions.cash.withdrawal') }}"
                                    class="inline-block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                                    Proceed to Withdrawal
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-10">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Recent Cash Transactions</h3>

                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            @if ($recentCashTransactions->count())
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Customer Name</th>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Amount</th>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Type</th>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recentCashTransactions as $tx)
                                            <tr>
                                                <td class="px-4 py-2 font-medium text-gray-900">{{ $tx->customer_name }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ number_format($tx->amount, 2) }} EGP</td>
                                                <td class="px-4 py-2 text-gray-700">{{ $tx->transaction_type }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ $tx->status }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ \Carbon\Carbon::parse($tx->transaction_date_time)->format('Y-m-d H:i') }}</td>
                                                <td class="px-4 py-2 text-right">
                                                    <a href="{{ route('cash-transactions.receipt', $tx->id) }}" target="_blank" class="inline-block text-green-600 hover:text-green-800 mr-2" title="Print Receipt">
                                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2m-6 0v4m0 0h4m-4 0H8" />
                                                        </svg>
                                                        Print
                                                    </a>
                                                    <button wire:click="deleteCashTransaction({{ $tx->id }})" class="text-red-600 hover:text-red-800">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                            <p class="text-gray-500 text-center py-6">
                                Cash transaction history will be displayed here.
                                <br>
                                <a href="{{ route('transactions.index') }}"
                                    class="text-indigo-600 hover:text-indigo-800">
                                    View all transactions
                                </a>
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 text-right">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800">
                            &larr; Back to All Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
