<div>
    @if (isset($showAdminAgentToggle) && $showAdminAgentToggle)
        <div class="mb-6 flex justify-end">
            <a href="{{ route('dashboard', ['as_agent' => 1]) }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-800 transition">
                Switch to Agent Dashboard View
            </a>
        </div>
    @endif
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Admin Dashboard Overview</h2>
    <!-- Admin and Supervisor Names -->
    <div class="flex flex-col items-end mb-2 space-y-1">
        <div class="flex items-center justify-end">
            <span class="text-sm font-medium text-gray-700">الأدمن</span>
        </div>
    </div>
    <!-- Branch Selector as HTML form -->
    <form method="GET" action="{{ url()->current() }}" class="flex items-center justify-end mb-4">
        <label for="branch" class="ml-2 text-sm font-medium text-gray-700">فرع:</label>
        <select name="branch" id="branch" class="form-select rounded-md shadow-sm block w-auto px-2 py-1 border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="this.form.submit()">
            <option value="all" {{ request('branch', 'all') == 'all' ? 'selected' : '' }}>كل الفروع</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
    </form>
    <!-- Branch Details -->
    @if(isset($selectedBranchDetails) && $selectedBranchDetails)
        <div class="mb-4 text-right">
            <div class="text-base font-bold text-gray-800">{{ $selectedBranchDetails['name'] }}</div>
        </div>
    @endif
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border">رصيد افتتاحي</th>
                <th class="px-4 py-2 border">عدد المعاملات</th>
                <th class="px-4 py-2 border">الخزينة</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td class="px-4 py-2 border text-blue-700 font-bold">{{ number_format($startupSafeBalance, 2) }}</td>
                <td class="px-4 py-2 border text-purple-700 font-bold">{{ $totalTransactionsCount }}</td>
                <td class="px-4 py-2 border font-bold">{{ number_format($safesBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-6">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            Quick Actions
        </h2>
        <p class="text-sm text-gray-500 mt-1">Create new transactions or access transaction tools</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('transactions.send') }}" class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-xl transition-all duration-200">
            <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900">Send Money</h3>
                <p class="text-sm text-blue-700">Create outgoing transfer</p>
            </div>
        </a>
        <a href="{{ route('transactions.receive') }}" class="group flex items-center p-4 bg-green-50 hover:bg-green-100 border border-green-100 rounded-xl transition-all duration-200">
            <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-green-900">Receive Money</h3>
                <p class="text-sm text-green-700">Process incoming transfer</p>
            </div>
        </a>
        @can('create-cash-transactions')
            <a href="{{ route('transactions.cash') }}" class="group flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 border border-yellow-100 rounded-xl transition-all duration-200">
                <div class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-yellow-900">Cash Transaction</h3>
                    <p class="text-sm text-yellow-700">Handle cash operations</p>
                </div>
            </a>
        @endcan
    </div>
</div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Total Users -->
        <a href="{{ route('users.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-users class="h-10 w-10 text-indigo-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Users</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalUsers }}</p>
                </div>
            </div>
        </a>

        <!-- Total Branches -->
        <a href="{{ route('branches.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-building-office-2 class="h-10 w-10 text-green-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Branches</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalBranches }}</p>
                </div>
            </div>
        </a>

        <!-- Work Sessions -->
        <a href="{{ route('work-sessions.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="h-10 w-10 text-teal-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Work Sessions</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">View</p>
                </div>
            </div>
        </a>

        <!-- Total Lines -->
        <a href="{{ route('lines.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-phone class="h-10 w-10 text-yellow-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Lines</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalLines }}</p>
                </div>
            </div>
        </a>

        <!-- Total Safes -->
        <a href="{{ route('safes.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-banknotes class="h-10 w-10 text-red-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Safes</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalSafes }}</p>
                </div>
            </div>
        </a>

        <!-- Total Customers -->
        <a href="{{ route('customers.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-identification class="h-10 w-10 text-blue-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Customers</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalCustomers }}</p>
                </div>
            </div>
        </a>

        <!-- Total Transactions -->
        <a href="{{ route('transactions.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-receipt-percent class="h-10 w-10 text-purple-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Transactions</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalTransactions }}</p>
                </div>
            </div>
        </a>

        <!-- Total Amount Transferred -->
        <div
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-currency-dollar class="h-10 w-10 text-emerald-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Total Amount Transferred</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ number_format($totalTransferred, 2) }} EGP
                    </p>
                </div>
            </div>
        </div>

        <!-- Net Profits -->
        <div
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-chart-bar class="h-10 w-10 text-rose-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Net Profits</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ number_format($netProfits, 2) }} EGP</p>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <a href="{{ route('transactions.pending') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 text-amber-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">Pending Transactions</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $pendingTransactionsCount }}</p>
                </div>
            </div>
        </a>
    </div>
</div>
