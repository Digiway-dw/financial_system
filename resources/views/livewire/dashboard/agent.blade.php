<div>
    <style>
        .sortable-header {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .sortable-header:hover {
            background-color: #f3f4f6 !important;
            transform: translateY(-1px);
        }

        .sortable-header:active {
            transform: translateY(0);
        }
    </style>
    @if (isset($showAdminAgentToggle) && $showAdminAgentToggle && request()->query('as_agent'))
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="get" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <input type="hidden" name="as_agent" value="1">
                <label for="branches" class="font-semibold text-gray-700">اختر الفروع:</label>
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
                @if (auth()->user()->hasRole('admin'))
                    الانتقال للواجهة الرئيسية للادمن
                @else
                    الانتقال للواجهة الرئيسية للمشرف
                @endif
            </a>
        </div>
    @endif

    @if (request()->query('search_transaction'))
        <div class="mb-6 bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <form method="GET" action="{{ route('dashboard') }}" class="w-full max-w-md flex flex-col gap-4">
                <input type="hidden" name="search_transaction" value="1">
                <label for="reference_number" class="block text-sm font-medium text-gray-700">بحث المعاملة برقم
                    المرجع</label>
                <div class="flex gap-2">
                    <input type="text" name="reference_number" id="reference_number"
                        value="{{ request('reference_number') }}"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                        placeholder="Enter reference number...">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
                </div>
            </form>
            @if (isset($searchedTransaction))
                <div class="mt-6 w-full max-w-md bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-bold text-blue-800 mb-2">تفاصيل المعاملة</h4>
                    <div class="text-sm text-gray-700">
                        <div><span class="font-semibold">الرقم المرجعي:</span>
                            {{ $searchedTransaction->reference_number }}</div>
                        <div><span class="font-semibold">المبلغ:</span> {{ format_int($searchedTransaction->amount) }}
                        </div>
                        <div><span class="font-semibold">الحالة:</span> {{ $searchedTransaction->status }}</div>
                        @if (isset($searchedTransaction->transaction_date_time) && $searchedTransaction->transaction_date_time)
                            <div><span class="font-semibold">تاريخ و وقت المعاملة:</span>
                                {{ \Carbon\Carbon::parse($searchedTransaction->transaction_date_time)->format('d/m/y h:i A') }}
                            </div>
                        @endif
                        <div><span class="font-semibold">تاريخ الانشاء:</span>
                            {{ \Carbon\Carbon::parse($searchedTransaction->created_at)->format('d/m/y h:i A') }}</div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('transactions.print', $searchedTransaction->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v9a2 2 0 01-2 2h-2" />
                                <rect width="12" height="8" x="6" y="14" rx="2" />
                            </svg>
                            طباعة
                        </a>
                    </div>
                </div>
            @elseif(request('reference_number'))
                <div class="mt-4 text-red-600 font-semibold">لا يوجد معاملة بهذا الرقم المرجعي.</div>
            @endif
        </div>
    @endif

    <!-- Agent Summary Table: Safe Name, Safe Balance, Startup Balance, Today's Transactions -->
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border">اسم الخزنة</th>
                <th class="px-4 py-2 border">رصيد الخزنة</th>
                <th class="px-4 py-2 border">المعاملات اليومية</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($branchSafes as $safe)
                <tr class="text-center">
                    <td class="px-4 py-2 border font-semibold">{{ $safe['name'] }}</td>
                    <td class="px-4 py-2 border text-blue-700 font-bold">{{ format_int($safe['current_balance']) }}
                    </td>
                    <td class="px-4 py-2 border text-purple-700 font-bold">{{ $safe['todays_transactions'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 border text-center text-gray-500">لا يوجد خزنات لهذا الفرع.</td>
                </tr>
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
                الإجراءات السريعة
            </h2>
            <p class="text-sm text-gray-500 mt-1">إنشاء معاملات جديدة أو الوصول إلى أدوات المعاملات</p>
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
                    <h3 class="font-semibold text-blue-900">إرسال المال</h3>
                    <p class="text-sm text-blue-700">إنشاء معاملة ارسال</p>
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
                    <h3 class="font-semibold text-green-900">استلام المال</h3>
                    <p class="text-sm text-green-700">معاملة استلام</p>
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
                        <h3 class="font-semibold text-yellow-900">المعاملة النقدية</h3>
                        <p class="text-sm text-yellow-700">التعامل مع المعاملات النقدية</p>
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
                        <h3 class="font-semibold text-indigo-900">اضافة عميل</h3>
                        <p class="text-sm text-indigo-700">اضافة عميل جديد</p>
                    </div>
                </a>
            @endcan
        </div>
    </div>


    @if (isset($agentLines) && $agentLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">جميع الخطوط في فرعك</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">إجمالي رصيد الخطوط: </span>
                <span class="text-2xl font-bold text-blue-700">{{ format_int($agentLinesTotalBalance ?? 0) }}
                    EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-3 text-center">
                                <input type="checkbox" id="select-all-lines" onclick="toggleAllAgentLines(this)" />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('mobile_number')" style="cursor: pointer;">
                                رقم الهاتف
                                @if ($sortField === 'mobile_number')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('current_balance')" style="cursor: pointer;">
                                الرصيد
                                @if ($sortField === 'current_balance')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('daily_limit')" style="cursor: pointer;">
                                المتبقي اليومي
                                @if ($sortField === 'daily_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('daily_usage')" style="cursor: pointer;">
                                المستلم اليومي
                                @if ($sortField === 'daily_usage')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('monthly_limit')" style="cursor: pointer;">
                                المتبقي الشهري
                                @if ($sortField === 'monthly_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('monthly_usage')" style="cursor: pointer;">
                                المستلم الشهري
                                @if ($sortField === 'monthly_usage')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('network')" style="cursor: pointer;">
                                الشبكة
                                @if ($sortField === 'network')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('status')" style="cursor: pointer;">
                                الحالة
                                @if ($sortField === 'status')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($agentLines as $line)
                            @php
                                $dailyLimit = $line->daily_limit ?? 0;
                                $monthlyLimit = $line->monthly_limit ?? 0;
                                $currentBalance = $line->current_balance ?? 0;
                                $dailyStartingBalance = $line->daily_starting_balance ?? 0;
                                $monthlyStartingBalance = $line->starting_balance ?? 0;
                                // Calculate daily and monthly remaining
                                $dailyRemaining = isset($line->daily_remaining) ? $line->daily_remaining : max(0, $dailyLimit - $currentBalance);
                                $monthlyRemaining = max(0, $monthlyLimit - $currentBalance);
                                $dailyUsage = max(0, $currentBalance - $dailyStartingBalance);
                                $monthlyUsage = max(0, $currentBalance - $monthlyStartingBalance);
                                $circleColor = 'bg-green-400';
                                if ($dailyRemaining <= 240) {
                                    $circleColor = 'bg-red-500';
                                } elseif ($dailyRemaining <= 1800) {
                                    $circleColor = 'bg-yellow-400';
                                }
                            @endphp
                            <tr>
                                <td class="px-2 py-4 text-center">
                                    <input type="checkbox" class="agent-line-checkbox" value="{{ $line->id }}"
                                        onclick="updateAgentLinesSum()" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 w-3 h-3 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $circleColor }}"></div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $line->mobile_number }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ format_int($line->current_balance) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ format_int($dailyRemaining) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ format_int($dailyUsage) }} EGP
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ format_int($monthlyRemaining) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ format_int($monthlyUsage) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->network }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($line->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td class="px-2 py-3 text-center" colspan="2">Selected Total</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-current-balance">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-daily-remaining">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-daily-usage">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-monthly-remaining">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-monthly-usage">0 EGP</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                <script>
                    function parseEGP(str) {
                        // Remove commas and EGP, then parse as int
                        return parseInt((str || '').replace(/,/g, '').replace(/\s*EGP/, '')) || 0;
                    }

                    function updateAgentLinesSum() {
                        let checkboxes = document.querySelectorAll('.agent-line-checkbox');
                        let totalCurrent = 0,
                            totalDailyRem = 0,
                            totalDailyUsage = 0,
                            totalMonthlyRem = 0,
                            totalMonthlyUsage = 0;
                        checkboxes.forEach(cb => {
                            if (cb.checked) {
                                let row = cb.closest('tr');
                                let tds = row.querySelectorAll('td');
                                totalCurrent += parseEGP(tds[2].innerText);
                                totalDailyRem += parseEGP(tds[3].innerText);
                                totalDailyUsage += parseEGP(tds[4].innerText);
                                totalMonthlyRem += parseEGP(tds[5].innerText);
                                totalMonthlyUsage += parseEGP(tds[6].innerText);
                            }
                        });
                        document.getElementById('selected-current-balance').innerText = totalCurrent.toLocaleString() + ' EGP';
                        document.getElementById('selected-daily-remaining').innerText = totalDailyRem.toLocaleString() + ' EGP';
                        document.getElementById('selected-daily-usage').innerText = totalDailyUsage.toLocaleString() + ' EGP';
                        document.getElementById('selected-monthly-remaining').innerText = totalMonthlyRem.toLocaleString() + ' EGP';
                        document.getElementById('selected-monthly-usage').innerText = totalMonthlyUsage.toLocaleString() + ' EGP';
                    }

                    function toggleAllAgentLines(source) {
                        let checkboxes = document.querySelectorAll('.agent-line-checkbox');
                        checkboxes.forEach(cb => {
                            cb.checked = source.checked;
                        });
                        updateAgentLinesSum();
                    }
                </script>
            </div>
        </div>
    @endif

</div>
