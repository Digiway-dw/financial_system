<div class="p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Profile</h3>
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-900 dark:text-white">
        <div><strong>Name:</strong> {{ $customer->name }}</div>
        <div>
            <strong>Mobile Numbers:</strong>
            <ul class="list-disc list-inside">
                @forelse($customer->mobileNumbers as $mobile)
                    <li>{{ $mobile->mobile_number }}</li>
                @empty
                    <li>{{ $customer->mobile_number }}</li>
                @endforelse
            </ul>
        </div>
        <div><strong>Customer Code:</strong> {{ $customer->customer_code }}</div>
        <div><strong>Region:</strong> {{ $customer->region ?? 'N/A' }}</div>
        <div><strong>Created At:</strong> {{ $customer->created_at ? $customer->created_at->format('Y-m-d') : 'N/A' }}</div>
        <div><strong>Gender:</strong> {{ $customer->gender ?? 'N/A' }}</div>
        <div><strong>Balance:</strong> {{ number_format($customer->balance, 2) }}</div>
        <div><strong>Notes:</strong> {{ $customer->notes ?? 'N/A' }}</div>
    </div>
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4 flex flex-col md:flex-row gap-6 justify-between items-center">
            <div class="text-center">
                <div class="font-bold text-gray-700 dark:text-gray-200 text-lg">Total Transactions</div>
                <div class="text-2xl font-semibold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($totalTransactions) }}</div>
            </div>
            <div class="text-center">
                <div class="font-bold text-gray-700 dark:text-gray-200 text-lg">Total Sent</div>
                <div class="text-2xl font-semibold text-green-600 dark:text-green-400 mt-1">{{ number_format($totalTransferred, 2) }} EGP</div>
            </div>
            <div class="text-center">
                <div class="font-bold text-gray-700 dark:text-gray-200 text-lg">Total Commission</div>
                <div class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400 mt-1">{{ number_format($totalCommission, 2) }} EGP</div>
            </div>
        </div>
    </div>
    <div class="mb-6">
        <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-2">Transactions</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900 text-xs md:text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="px-4 py-2 font-bold text-gray-900 dark:text-gray-100">Date</th>
                        <th class="px-4 py-2 font-bold text-gray-900 dark:text-gray-100">Type</th>
                        <th class="px-4 py-2 font-bold text-gray-900 dark:text-gray-100">Amount</th>
                        <th class="px-4 py-2 font-bold text-gray-900 dark:text-gray-100">Commission</th>
                        <th class="px-4 py-2 font-bold text-gray-900 dark:text-gray-100">Line</th>
                        <th class="px-4 py-2 font-bold text-gray-900 dark:text-gray-100">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->transactions as $transaction)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $transaction->transaction_date_time ?? $transaction->created_at }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $transaction->transaction_type }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ number_format($transaction->commission, 2) }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $transaction->line_mobile_number ?? '-' }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ ucfirst($transaction->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 