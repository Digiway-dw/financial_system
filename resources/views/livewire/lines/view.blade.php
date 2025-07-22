<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 p-8 mb-8">
            <h1 class="text-2xl font-bold text-blue-900 mb-2">Line Details</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm text-gray-500">Mobile Number</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $line->mobile_number }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Network</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $line->network }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Current Balance</div>
                    <div class="text-lg font-semibold text-blue-700">{{ format_int($line->current_balance) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Branch</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $line->branch->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Daily Limit</div>
                    <div class="text-lg font-semibold text-gray-900">{{ format_int($line->daily_limit) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Monthly Limit</div>
                    <div class="text-lg font-semibold text-gray-900">{{ format_int($line->monthly_limit) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="text-lg font-semibold {{ $line->status === 'active' ? 'text-green-700' : 'text-red-700' }}">
                        {{ ucfirst($line->status) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/50 p-8">
            <h2 class="text-xl font-bold text-blue-900 mb-4">Transaction History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $tx)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($tx->transaction_date_time)->format('d/m/y h:i A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tx->transaction_type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">{{ format_int($tx->amount) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $tx->status === 'completed' ? 'text-green-700' : ($tx->status === 'pending' ? 'text-yellow-700' : 'text-red-700') }}">{{ ucfirst($tx->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">No transactions found for this line.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
