<div>
    @if (isset($showAdminAgentToggle) && $showAdminAgentToggle)
        <div class="mb-6 flex justify-end">
            <a href="{{ route('agent-dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-800 transition">
                الانتقال للواجهة الرئيسية للموظف
            </a>
        </div>
    @endif
    <h2 class="text-2xl font-bold text-gray-900 mb-6">نظرة عامة على لوحة التحكم</h2>

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
                    <p class="text-gray-600 text-lg font-medium">إجمالي المستخدمين</p>
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
                    <p class="text-gray-600 text-lg font-medium">إجمالي الفروع</p>
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
                    <p class="text-gray-600 text-lg font-medium">جلسات العمل</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">عرض</p>
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
                    <p class="text-gray-600 text-lg font-medium">إجمالي الخطوط</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $totalLines }}</p>
                </div>
            </div>
        </a>

        <!-- Working Hours Management - Removed as not being used -->
        <!--
        <a href="{{ route('admin.working-hours') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="h-10 w-10 text-purple-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">ساعات العمل</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">إدارة</p>
                </div>
            </div>
        </a>
        -->

        <!-- Total Safes -->
        <a href="{{ route('safes.index') }}"
            class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 transform hover:scale-105 transition duration-300 ease-in-out hover:shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <x-heroicon-o-banknotes class="h-10 w-10 text-red-600" />
                </div>
                <div>
                    <p class="text-gray-600 text-lg font-medium">إجمالي الخزائن</p>
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
                    <p class="text-gray-600 text-lg font-medium">إجمالي العملاء</p>
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
                    <p class="text-gray-600 text-lg font-medium">إجمالي المعاملات</p>
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
                    <p class="text-gray-600 text-lg font-medium">إجمالي المبالغ المنقولة</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ format_int($totalTransferred) }}
                        EGP
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
                    <p class="text-gray-600 text-lg font-medium">الأرباح الصافية</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ format_int($netProfits) }} EGP</p>
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
                    <p class="text-gray-600 text-lg font-medium">المعاملات المعلقة</p>
                    <p class="text-gray-900 text-4xl font-extrabold mt-1">{{ $pendingTransactionsCount }}</p>
                </div>
            </div>
        </a>
    </div>

    @if (isset($adminLines) && $adminLines->count())
        <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
            <h4 class="text-xl font-bold text-gray-900 mb-4">جميع الخطوط في النظام</h4>
            <div class="mb-4">
                <span class="text-lg font-semibold text-gray-700">إجمالي رصيد الخطوط: </span>
                <span class="text-2xl font-bold text-blue-700">{{ format_int($adminLinesTotalBalance ?? 0) }} EGP</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-3 text-center">
                                <input type="checkbox" id="select-all-admin-lines" onclick="toggleAllAdminLines(this)" />
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('mobile_number')" style="cursor: pointer;">
                                رقم الهاتف
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('current_balance')" style="cursor: pointer;">
                                الرصيد
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('daily_limit')" style="cursor: pointer;">
                                المتبقي اليومي
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('daily_usage')" style="cursor: pointer;">
                                المستلم اليومي
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_limit')" style="cursor: pointer;">
                                المتبقي الشهري
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('monthly_usage')" style="cursor: pointer;">
                                المستلم الشهري
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('network')" style="cursor: pointer;">
                                الشبكة
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sortable-header" wire:click="sortBy('status')" style="cursor: pointer;">
                                الحالة
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($adminLines as $line)
                            @php
                                $dailyLimit = $line->daily_limit ?? 0;
                                $monthlyLimit = $line->monthly_limit ?? 0;
                                $currentBalance = $line->current_balance ?? 0;
                                $dailyStartingBalance = $line->daily_starting_balance ?? 0;
                                $monthlyStartingBalance = $line->starting_balance ?? 0;
                                $dailyRemaining = isset($line->daily_remaining) ? $line->daily_remaining : max(0, $dailyLimit - $currentBalance);
                                $monthlyRemaining = max(0, $monthlyLimit - $currentBalance);
                                $dailyUsage = $line->daily_usage ?? 0;
                                $monthlyUsage = $line->monthly_usage ?? 0;
                                $circleColor = 'bg-green-400';
                                if ($dailyRemaining <= 240) {
                                    $circleColor = 'bg-red-500';
                                } elseif ($dailyRemaining <= 1800) {
                                    $circleColor = 'bg-yellow-400';
                                }
                            @endphp
                            <tr>
                                <td class="px-2 py-4 text-center">
                                    <input type="checkbox" class="admin-line-checkbox" value="{{ $line->id }}" onclick="updateAdminLinesSum()" />
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
                            <td class="px-2 py-3 text-center" colspan="2">Selected Total</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-admin-current-balance">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-admin-daily-remaining">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-admin-daily-usage">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-admin-monthly-remaining">0 EGP</td>
                            <td class="px-6 py-3 text-sm text-blue-700" id="selected-admin-monthly-usage">0 EGP</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                <script>
                    function parseEGP(str) {
                        return parseInt((str || '').replace(/,/g, '').replace(/\s*EGP/, '')) || 0;
                    }
                    function updateAdminLinesSum() {
                        let checkboxes = document.querySelectorAll('.admin-line-checkbox');
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
                        document.getElementById('selected-admin-current-balance').innerText = totalCurrent.toLocaleString() + ' EGP';
                        document.getElementById('selected-admin-daily-remaining').innerText = totalDailyRem.toLocaleString() + ' EGP';
                        document.getElementById('selected-admin-daily-usage').innerText = totalDailyUsage.toLocaleString() + ' EGP';
                        document.getElementById('selected-admin-monthly-remaining').innerText = totalMonthlyRem.toLocaleString() + ' EGP';
                        document.getElementById('selected-admin-monthly-usage').innerText = totalMonthlyUsage.toLocaleString() + ' EGP';
                    }
                    function toggleAllAdminLines(source) {
                        let checkboxes = document.querySelectorAll('.admin-line-checkbox');
                        checkboxes.forEach(cb => {
                            cb.checked = source.checked;
                        });
                        updateAdminLinesSum();
                    }
                </script>
            </div>
        </div>
    @endif
</div>
