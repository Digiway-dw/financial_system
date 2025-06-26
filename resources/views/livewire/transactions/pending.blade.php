<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pending Transactions Review</h3>

    <div class="mt-4">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Customer Name</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Agent</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($pendingTransactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $transaction['customer_name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($transaction['amount'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['transaction_type'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['agent_name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                            <x-primary-button wire:click="approve('{{ $transaction['id'] }}')">Approve</x-primary-button>
                            <x-danger-button wire:click="reject('{{ $transaction['id'] }}')">Reject</x-danger-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No pending transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (session('message'))
        <p
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 2000)"
            class="text-sm text-gray-600 dark:text-gray-400 mt-4"
        >{{ session('message') }}</p>
    @endif

    @if (session('error'))
        <p
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 2000)"
            class="text-sm text-red-600 dark:text-red-400 mt-4"
        >{{ session('error') }}</p>
    @endif
</div>
