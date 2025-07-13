<div>
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Branch Manager Dashboard Overview - {{ $branchName ?? 'N/A' }}</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Branch Safe Balance -->
        <a href="{{ route('safes.index') }}" class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-banknotes class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Branch Safe Balance</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($branchSafeBalance ?? 0, 2) }} EGP</p>
                </div>
            </div>
        </a>

        <!-- Branch Users -->
        <a href="{{ route('users.index') }}" class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-users class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Branch Users</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ $branchUsersCount ?? 0 }}</p>
                </div>
            </div>
        </a>
    </div>

    @if(isset($branchLines) && $branchLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">All Lines in This Branch</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
                <span class="text-2xl font-bold text-blue-700">{{ number_format($branchLinesTotalBalance ?? 0, 2) }} EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mobile Number</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Daily Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Monthly Limit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Network</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
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