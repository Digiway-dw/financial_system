<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Trainee Dashboard Overview</h2>
    @if(isset($totalSafeBalance))
        <div class="text-lg text-gray-700 mb-1">Total Safe Balance: <span class="font-bold">{{ number_format($totalSafeBalance, 2) }} EGP</span></div>
    @endif
    @if(isset($branchSafeBalance))
        <div class="text-lg text-gray-700 mb-4">Branch Safe Balance: <span class="font-bold">{{ number_format($branchSafeBalance, 2) }} EGP</span></div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Your Total Line Balance -->
        <a href="{{ route('lines.index') }}"
            class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-wallet class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Total Line Balance</p>
                    <p class="text-white text-4xl font-extrabold mt-1">{{ number_format($agentTotalBalance ?? 0, 2) }}
                        EGP</p>
                </div>
            </div>
        </a>

        <!-- Your Total Transferred Amount -->
        <div
            class="bg-gradient-to-br from-emerald-500 to-lime-600 rounded-lg shadow-xl p-6 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-currency-dollar class="h-10 w-10 text-white" />
                </div>
                <div>
                    <p class="text-white text-lg font-medium">Your Total Transferred Amount</p>
                    <p class="text-white text-4xl font-extrabold mt-1">
                        {{ number_format($agentTotalTransferred ?? 0, 2) }} EGP</p>
                </div>
            </div>
        </div>

        <!-- Your Pending Transactions -->
        {{-- Removed: Only admins should see pending transactions --}}
    </div>

    <!-- Quick Access Cards -->
    <h4 class="text-xl font-bold text-gray-900 mt-8 mb-4">Quick Access</h4>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-blue-500">
            <x-heroicon-o-home class="h-8 w-8 text-blue-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Dashboard</span>
        </a>

        <!-- Customers -->
        <a href="{{ route('customers.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-indigo-500">
            <x-heroicon-o-users class="h-8 w-8 text-indigo-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Customers</span>
        </a>

        <!-- Transactions -->
        <a href="{{ route('transactions.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-green-500">
            <x-heroicon-o-arrow-path class="h-8 w-8 text-green-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Transactions</span>
        </a>

        <!-- Lines -->
        <a href="{{ route('lines.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-yellow-500">
            <x-heroicon-o-phone class="h-8 w-8 text-yellow-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Lines</span>
        </a>

        <!-- Safes -->
        <a href="{{ route('safes.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-red-500">
            <x-heroicon-o-lock-closed class="h-8 w-8 text-red-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Safes</span>
        </a>

        <!-- Move Cash -->
        <a href="{{ route('safes.move') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-purple-500">
            <x-heroicon-o-banknotes class="h-8 w-8 text-purple-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Move Cash</span>
        </a>

        <!-- Branches -->
        <a href="{{ route('branches.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-emerald-500">
            <x-heroicon-o-building-office class="h-8 w-8 text-emerald-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Branches</span>
        </a>

        <!-- Users -->
        <a href="{{ route('users.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-sky-500">
            <x-heroicon-o-user-group class="h-8 w-8 text-sky-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Users</span>
        </a>

        <!-- Reports -->
        <a href="{{ route('reports.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-amber-500">
            <x-heroicon-o-chart-bar class="h-8 w-8 text-amber-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Reports</span>
        </a>

        <!-- Audit Log -->
        <a href="{{ route('audit-log.index') }}"
            class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center justify-center transform hover:scale-105 transition duration-300 ease-in-out border-b-4 border-teal-500">
            <x-heroicon-o-clipboard-document-list class="h-8 w-8 text-teal-500 mb-2" />
            <span class="text-sm font-medium text-gray-700">Audit Log</span>
        </a>
    </div>

    @if(isset($agentLines) && $agentLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">All Lines in Your Branch</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
                <span class="text-2xl font-bold text-blue-700">{{ number_format($agentLinesTotalBalance ?? 0, 2) }} EGP</span>
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
                        @foreach($agentLines as $line)
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

    <!-- Quick Actions -->
    <div class="mt-8 bg-white p-6 shadow-xl sm:rounded-lg">
        <h4 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('transactions.send') }}"
                class="flex items-center p-4 bg-red-50 rounded-lg border border-red-100 hover:bg-red-100 transition-colors">
                <div class="rounded-full bg-red-100 p-3 mr-4">
                    <x-heroicon-o-paper-airplane class="h-6 w-6 text-red-600" />
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900">Send Money</h5>
                    <p class="text-sm text-gray-600">Create a new outgoing transaction</p>
                </div>
            </a>

            <a href="{{ route('transactions.receive') }}"
                class="flex items-center p-4 bg-green-50 rounded-lg border border-green-100 hover:bg-green-100 transition-colors">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <x-heroicon-o-arrow-down-tray class="h-6 w-6 text-green-600" />
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900">Receive Money</h5>
                    <p class="text-sm text-gray-600">Process an incoming transaction</p>
                </div>
            </a>

            <a href="{{ route('customers.create') }}"
                class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                    <x-heroicon-o-user-plus class="h-6 w-6 text-blue-600" />
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900">New Customer</h5>
                    <p class="text-sm text-gray-600">Register a new customer</p>
                </div>
            </a>
        </div>
    </div>
</div>
