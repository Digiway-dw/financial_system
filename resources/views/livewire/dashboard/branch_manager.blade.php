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
    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Branch Manager Dashboard Overview</h3>
    <div class="flex items-center justify-end mb-2">
        <span class="ml-2 text-base font-bold text-gray-900">{{ $branchName }}</span>
        <span class="text-sm font-medium text-gray-700"> : الفرع</span>

    </div>
    <div class="mb-4">
        <div class="text-lg font-bold text-gray-800">
            {{ auth()->user()->name }}
        </div>
        <div class="text-sm text-gray-500">
            مدير فرع
        </div>
    </div>
    <!-- Remove branch selector and branch details section -->
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border"> عدد المعاملات الفرع</th>
                <th class="px-4 py-2 border">رصيد الخزينة</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td class="px-4 py-2 border text-purple-700 font-bold">{{ $totalTransactionsCount }}</td>
                <td class="px-4 py-2 border font-bold">{{ format_int($safesBalance) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Quick Actions Section -->
    <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-6">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                الإجراءات السريعة
            </h2>
            <p class="text-sm text-gray-500 mt-1">إنشاء معاملات جديدة أو الوصول إلى أدوات المعاملات</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('transactions.send') }}" class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-xl transition-all duration-200">
                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900">إرسال المال</h3>
                    <p class="text-sm text-blue-700">إنشاء معاملة ارسال</p>
                </div>
            </a>
            <a href="{{ route('transactions.receive') }}" class="group flex items-center p-4 bg-green-50 hover:bg-green-100 border border-green-100 rounded-xl transition-all duration-200">
                <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">استلام المال</h3>
                    <p class="text-sm text-green-700">معاملة استلام</p>
                </div>
            </a>
            <a href="{{ route('transactions.line-transfer') }}" class="group flex items-center p-4 bg-purple-50 hover:bg-purple-100 border border-purple-100 rounded-xl transition-all duration-200">
                <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-purple-900">تحويل خط</h3>
                    <p class="text-sm text-purple-700">تحويل رصيد بين الخطوط</p>
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
                        <h3 class="font-semibold text-yellow-900">المعاملة النقدية</h3>
                        <p class="text-sm text-yellow-700">التعامل مع المعاملات النقدية</p>
                    </div>
                </a>
            @endcan
        </div>
    </div>



    @if(isset($branchLines) && $branchLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">إجمالي رصيد الخطوط: </span>
                <span class="text-2xl font-bold text-blue-700">{{ format_int($branchLinesTotalBalance ?? 0) }} EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-3 text-center">
                                <input type="checkbox" id="select-all-branch-lines" onclick="toggleAllBranchLines(this)" />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('mobile_number')" style="cursor: pointer;">
                                رقم الهاتف
                                @if ($sortField === 'mobile_number')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('current_balance')" style="cursor: pointer;">
                                الرصيد
                                @if ($sortField === 'current_balance')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('daily_limit')" style="cursor: pointer;">
                                المتبقي اليومي
                                @if ($sortField === 'daily_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('daily_usage')" style="cursor: pointer;">
                                المستلم اليومي
                                @if ($sortField === 'daily_usage')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_limit')" style="cursor: pointer;">
                                المتبقي الشهري
                                @if ($sortField === 'monthly_limit')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_usage')" style="cursor: pointer;">
                                المستلم الشهري
                                @if ($sortField === 'monthly_usage')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('network')" style="cursor: pointer;">
                                الشبكة
                                @if ($sortField === 'network')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('status')" style="cursor: pointer;">
                                الحالة
                                @if ($sortField === 'status')
                                    <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($branchLines as $line)
                            @php
                                $dailyRemaining = $line->daily_remaining ?? 0;
                                $monthlyRemaining = $line->monthly_remaining ?? 0;
                                $dailyLimit = $line->daily_limit ?? 0;
                                $monthlyLimit = $line->monthly_limit ?? 0;
                                $currentBalance = $line->current_balance ?? 0;
                                $dailyStartingBalance = $line->daily_starting_balance ?? 0;
                                $monthlyStartingBalance = $line->starting_balance ?? 0;
                                $dailyUsage = $line->daily_usage ?? max(0, $currentBalance - $dailyStartingBalance);
                                $monthlyUsage = $line->monthly_usage ?? max(0, $currentBalance - $monthlyStartingBalance);
                                $circleColor = 'bg-green-400';
                                if ($dailyRemaining <= 240) {
                                    $circleColor = 'bg-red-500';
                                } elseif ($dailyRemaining <= 1800) {
                                    $circleColor = 'bg-yellow-400';
                                }
                            @endphp
                            <tr>
                                <td class="px-2 py-4 text-center">
                                    <input type="checkbox" class="branch-line-checkbox" value="{{ $line->id }}" onclick="updateBranchLinesSum()" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-3 h-3 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $circleColor }}"></div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $line->mobile_number }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($line->current_balance) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($dailyRemaining) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($dailyUsage) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($monthlyRemaining) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($monthlyUsage) }} EGP</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->network }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($line->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td class="px-2 py-3 text-center" colspan="2">المجموع المختار</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-branch-current-balance">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-branch-daily-remaining">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-branch-daily-usage">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-branch-monthly-remaining">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-branch-monthly-usage">0 EGP</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                <script>
                    function parseEGP(str) {
                        return parseInt((str || '').replace(/,/g, '').replace(/\s*EGP/, '')) || 0;
                    }
                    function updateBranchLinesSum() {
                        let checkboxes = document.querySelectorAll('.branch-line-checkbox');
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
                        document.getElementById('selected-branch-current-balance').innerText = totalCurrent.toLocaleString() + ' EGP';
                        document.getElementById('selected-branch-daily-remaining').innerText = totalDailyRem.toLocaleString() + ' EGP';
                        document.getElementById('selected-branch-daily-usage').innerText = totalDailyUsage.toLocaleString() + ' EGP';
                        document.getElementById('selected-branch-monthly-remaining').innerText = totalMonthlyRem.toLocaleString() + ' EGP';
                        document.getElementById('selected-branch-monthly-usage').innerText = totalMonthlyUsage.toLocaleString() + ' EGP';
                    }
                    function toggleAllBranchLines(source) {
                        let checkboxes = document.querySelectorAll('.branch-line-checkbox');
                        checkboxes.forEach(cb => {
                            cb.checked = source.checked;
                        });
                        updateBranchLinesSum();
                    }
                </script>
            </div>
        </div>
    @endif
</div> 