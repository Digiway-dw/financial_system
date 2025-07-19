<div>
    @if (isset($showAdminAgentToggle) && $showAdminAgentToggle && request()->query('as_agent'))
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="get" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <input type="hidden" name="as_agent" value="1">
                <label for="branches" class="font-semibold text-gray-700">Select Branches:</label>
                <select name="branches[]" id="branches" multiple
                    class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                    onchange="
                if ([...this.options].find(o => o.value === 'all' && o.selected)) {
                    [...this.options].forEach(o => o.selected = true);
                }
                this.form.submit();">
                    <option value="all" @if (empty($selectedBranches) || (isset($branches) && count($selectedBranches) == $branches->count())) selected @endif>All Branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @if (in_array($branch->id, $selectedBranches ?? [])) selected @endif>
                            {{ $branch->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Apply</button>
            </form>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-800 transition">
                @if(auth()->user()->hasRole('admin'))
                    Switch to Main Admin Dashboard
                @else
                    Switch to Supervisor Dashboard
                @endif
            </a>
        </div>
    @endif

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

    <!-- Agent Summary Table: Safe Name, Safe Balance, Startup Balance, Today's Transactions -->
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border">Safe Name</th>
                <th class="px-4 py-2 border">Safe Balance</th>
                <th class="px-4 py-2 border">Today's Transactions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($branchSafes as $safe)
                <tr class="text-center">
                    <td class="px-4 py-2 border font-semibold">{{ $safe['name'] }}</td>
                    <td class="px-4 py-2 border text-blue-700 font-bold">{{ number_format($safe['current_balance'], 2) }}</td>
                    <td class="px-4 py-2 border text-purple-700 font-bold">{{ $safe['todays_transactions'] ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-2 border text-center text-gray-500">No safes found for your branch.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-6">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                Quick Actions
            </h2>
            <p class="text-sm text-gray-500 mt-1">Create new transactions or access transaction tools</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('transactions.send') }}"
                class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-xl transition-all duration-200">
                <div
                    class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900">Send Money</h3>
                    <p class="text-sm text-blue-700">Create outgoing transfer</p>
                </div>
            </a>
            <a href="{{ route('transactions.receive') }}"
                class="group flex items-center p-4 bg-green-50 hover:bg-green-100 border border-green-100 rounded-xl transition-all duration-200">
                <div
                    class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">Receive Money</h3>
                    <p class="text-sm text-green-700">Process incoming transfer</p>
                </div>
            </a>

            @can('create-cash-transactions')
                <a href="{{ route('transactions.cash') }}"
                    class="group flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 border border-yellow-100 rounded-xl transition-all duration-200">
                    <div
                        class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-yellow-900">Cash Transaction</h3>
                        <p class="text-sm text-yellow-700">Handle cash operations</p>
                    </div>
                </a>
            @endcan
            @can('manage-customers')
            <a href="{{ route('customers.create') }}"
                class="group flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-xl transition-all duration-200">
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
        </div>
    </div>


    @if (isset($agentLines) && $agentLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">All Lines in Your Branch</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">Total Lines Balance: </span>
                <span class="text-2xl font-bold text-blue-700">{{ number_format($agentLinesTotalBalance ?? 0, 2) }}
                    EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Mobile Number</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Balance</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Daily Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Daily Usage</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Monthly Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Monthly Usage</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Network</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($agentLines as $line)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $line->mobile_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($line->current_balance, 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($line->daily_limit, 2) }} EGP</td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm {{ isset($line->daily_usage_class) ? $line->daily_usage_class : 'text-gray-900' }}">
                                    {{ number_format($line->daily_usage ?? 0, 2) }} EGP
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($line->monthly_limit, 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($line->monthly_usage ?? 0, 2) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->network }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($line->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
