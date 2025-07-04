<div class="p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">User Details</h3>
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-900 dark:text-white">
        <div>
            <strong>Name:</strong> {{ $user->name }}
        </div>
        <div>
            <strong>Email:</strong> {{ $user->email }}
        </div>
        <div>
            <strong>Phone Number:</strong> {{ $user->phone_number }}
        </div>
        <div>
            <strong>National Number:</strong> {{ $user->national_number }}
        </div>
        <div>
            <strong>Salary:</strong> {{ $user->salary }}
        </div>
        <div>
            <strong>Address:</strong> {{ $user->address }}
        </div>
        <div>
            <strong>Land Number:</strong> {{ $user->land_number }}
        </div>
        <div>
            <strong>Relative's Phone Number:</strong> {{ $user->relative_phone_number }}
        </div>
        <div class="md:col-span-2">
            <strong>Notes:</strong> {{ $user->notes }}
        </div>
        <div>
            <strong>Branch:</strong> {{ $branch ? $branch->name : 'N/A' }}
        </div>
        <div>
            <strong>Role:</strong> {{ ucfirst($role) }}
        </div>
    </div>

    <h4 class="text-md font-semibold text-gray-800 dark:text-white mt-8 mb-2">Transaction History</h4>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="dark:text-white">
                <tr>
                    <th class="px-4 py-2 dark:text-white">#</th>
                    <th class="px-4 py-2 dark:text-white">Type</th>
                    <th class="px-4 py-2 dark:text-white">Amount</th>
                    <th class="px-4 py-2 dark:text-white">Status</th>
                    <th class="px-4 py-2 dark:text-white">Date</th>
                </tr>
            </thead>
            <tbody class="dark:text-white">
                @forelse ($transactions as $transaction)
                    <tr>
                        <td class="px-4 py-2 dark:text-white">{{ $transaction->id }}</td>
                        <td class="px-4 py-2 dark:text-white">{{ $transaction->type ?? '-' }}</td>
                        <td class="px-4 py-2 dark:text-white">{{ $transaction->amount ?? '-' }}</td>
                        <td class="px-4 py-2 dark:text-white">{{ $transaction->status ?? '-' }}</td>
                        <td class="px-4 py-2 dark:text-white">{{ $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center dark:text-white">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h4 class="text-md font-semibold text-gray-800 dark:text-white mt-8 mb-2">Login History</h4>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="dark:text-white">
                <tr>
                    <th class="px-4 py-2 dark:text-white">Login Time</th>
                    <th class="px-4 py-2 dark:text-white">Logout Time</th>
                    <th class="px-4 py-2 dark:text-white">Session Duration</th>
                </tr>
            </thead>
            <tbody class="dark:text-white">
                @forelse ($loginHistories as $history)
                    <tr>
                        <td class="px-4 py-2 dark:text-white">{{ $history->login_at ? \Carbon\Carbon::parse($history->login_at)->format('Y-m-d H:i') : '-' }}</td>
                        <td class="px-4 py-2 dark:text-white">{{ $history->logout_at ? \Carbon\Carbon::parse($history->logout_at)->format('Y-m-d H:i') : '-' }}</td>
                        <td class="px-4 py-2 dark:text-white">
                            @if ($history->session_duration)
                                {{ gmdate('i\m\ s\s', $history->session_duration) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center dark:text-white">No login history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div> 