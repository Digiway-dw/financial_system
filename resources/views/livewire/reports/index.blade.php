<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">التقارير</h1>
                    <p class="text-sm text-gray-500">جميع المعاملات (عادية ونقدية) مع فلترة وترتيب كامل</p>
                </div>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filter Section -->
        <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-8">
            <style>
                /* RTL adjustments */
                [dir="rtl"] .text-left {
                    text-align: right !important;
                }

                [dir="rtl"] .text-right {
                    text-align: left !important;
                }

                [dir="rtl"] .mr-2 {
                    margin-left: 0.5rem !important;
                    margin-right: 0 !important;
                }

                [dir="rtl"] .ml-2 {
                    margin-right: 0.5rem !important;
                    margin-left: 0 !important;
                }

                [dir="rtl"] .space-x-1> :not([hidden])~ :not([hidden]) {
                    --tw-space-x-reverse: 1;
                    margin-left: calc(0.25rem * var(--tw-space-x-reverse));
                    margin-right: calc(0.25rem * calc(1 - var(--tw-space-x-reverse)));
                }

                [dir="rtl"] .space-x-2> :not([hidden])~ :not([hidden]) {
                    --tw-space-x-reverse: 1;
                    margin-left: calc(0.5rem * var(--tw-space-x-reverse));
                    margin-right: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
                }

                [dir="rtl"] .space-x-3> :not([hidden])~ :not([hidden]) {
                    --tw-space-x-reverse: 1;
                    margin-left: calc(0.75rem * var(--tw-space-x-reverse));
                    margin-right: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
                }

                [dir="rtl"] .pl-4 {
                    padding-right: 1rem !important;
                    padding-left: 0 !important;
                }

                [dir="rtl"] .pr-4 {
                    padding-left: 1rem !important;
                    padding-right: 0 !important;
                }

                [dir="rtl"] .text-xs,
                [dir="rtl"] .text-sm,
                [dir="rtl"] .text-base,
                [dir="rtl"] .text-lg,
                [dir="rtl"] .text-xl,
                [dir="rtl"] .text-2xl,
                [dir="rtl"] .text-3xl {
                    direction: rtl;
                }
            </style>
            <form wire:submit.prevent="generateReport" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">التاريخ البدء</label>
                    <input wire:model="startDate" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">التاريخ النهاية</label>
                    <input wire:model="endDate" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">الموظف</label>
                    <select wire:model="selectedUser" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">جميع</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">الفرع</label>
                    <select wire:model="selectedBranch" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">جميع</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">اسم العميل</label>
                    <input wire:model="selectedCustomer" type="text"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="ادخل اسم العميل" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">كود العميل</label>
                    <input wire:model="selectedCustomerCode" type="text"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="ادخل كود العميل" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">نوع المعاملة</label>
                    <select wire:model="selectedTransactionType"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        <option value="">جميع</option>
                        @foreach ($transactionTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex flex-wrap gap-3 w-full">
                        <button type="submit"
                            class="min-w-[160px] px-6 py-3 bg-blue-600 text-white rounded-2xl font-semibold hover:bg-blue-700 transition shadow-md">توليد
                            التقرير</button>
                        <button type="button" wire:click="exportSummaryPdf"
                            class="min-w-[160px] px-6 py-3 bg-indigo-600 text-white rounded-2xl font-semibold hover:bg-indigo-700 transition shadow-md">تصدير
                            ملخص النظام</button>
                        <button type="button" wire:click="exportExcel"
                            class="min-w-[160px] px-6 py-3 bg-green-600 text-white rounded-2xl font-semibold hover:bg-green-700 transition shadow-md">تصدير
                            الـ Excel</button>
                        <button type="button" wire:click="exportPdf"
                            class="min-w-[160px] px-6 py-3 bg-red-600 text-white rounded-2xl font-semibold hover:bg-red-700 transition shadow-md">تصدير
                            ألPDF</button>
                        <button type="button" wire:click="exportAllPdf"
                            class="min-w-[160px] px-6 py-3 bg-purple-600 text-white rounded-2xl font-semibold hover:bg-purple-700 transition shadow-md">تصدير
                            الكل</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Financial Summary Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">ملخص مالي</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 rounded-xl">
                    <div class="text-xs text-gray-500">المبلغ المستلم</div>
                    <div class="text-lg font-bold">
                        {{ $financialSummary['total_transfer'] == 0 ? '0' : number_format($financialSummary['total_transfer'], 0) }}
                        EGP</div>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <div class="text-xs text-gray-500">العمولة</div>
                    <div class="text-lg font-bold">
                        {{ $financialSummary['commission_earned'] == 0 ? '0' : number_format($financialSummary['commission_earned'], 0) }}
                        EGP</div>
                </div>
                <div class="p-4 bg-yellow-50 rounded-xl">
                    <div class="text-xs text-gray-500">الخصم</div>
                    <div class="text-lg font-bold">
                        {{ $financialSummary['total_discounts'] == 0 ? '0' : number_format($financialSummary['total_discounts'], 0) }}
                        EGP</div>
                </div>
                <div class="p-4 bg-purple-50 rounded-xl">
                    <div class="text-xs text-gray-500">الربح الصافي</div>
                    <div class="text-lg font-bold">
                        {{ $financialSummary['net_profit'] == 0 ? '0' : number_format($financialSummary['net_profit'], 0) }}
                        EGP</div>
                </div>
            </div>
        </div>
        <!-- Safe Balances Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">الرصيد الخزني بالفروع</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($safeBalances as $safe)
                    <div class="p-4 bg-gray-50 rounded-xl flex justify-between">
                        <span>{{ $safe['branch'] }}</span>
                        <span class="font-bold">{{ $safe['balance'] == 0 ? '0' : number_format($safe['balance'], 0) }}
                            EGP</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Line Balances Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">الرصيد الخطي بالفروع</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($lineBalances as $line)
                    <div class="p-4 bg-gray-50 rounded-xl flex justify-between">
                        <span>{{ $line['branch'] }}</span>
                        <span class="font-bold">{{ $line['balance'] == 0 ? '0' : number_format($line['balance'], 0) }}
                            EGP</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Customer Balances Section -->
        <div class="bg-white rounded-2xl shadow border border-gray-200 mb-8 p-6">
            <h3 class="text-lg font-semibold mb-4">الرصيد المحفظة العميل</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($customerBalances as $customer)
                    <div class="p-4 bg-gray-50 rounded-xl flex justify-between">
                        <span>{{ $customer['customer'] }}</span>
                        <span
                            class="font-bold">{{ $customer['balance'] == 0 ? '0' : number_format($customer['balance'], 0) }}
                            EGP</span>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Transactions Table -->
        <div class="bg-white rounded-2xl shadow border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between px-8 pt-8 mb-4 gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7v4a1 1 0 001 1h3m10-5v4a1 1 0 01-1 1h-3m-4 4h6m2 4H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">جميع المعاملات</h3>
                        <p class="text-sm text-gray-500 mt-1">جدول تفصيلي لكل المعاملات المالية مع خيارات الفرز والبحث
                        </p>
                    </div>
                </div>
                <div class="hidden md:block">
                    <span
                        class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-4 py-2 rounded-full shadow"></span>
                </div>
            </div>
            <div class="overflow-x-auto px-8 pb-8">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('customer_name')">
                                <div class="flex items-center space-x-1">
                                    <span>اسم العميل</span>
                                    @if ($sortField === 'customer_name')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th
                                class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                كود العميل
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('amount')">
                                <div class="flex items-center space-x-1">
                                    <span>المبلغ</span>
                                    @if ($sortField === 'amount')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('commission')">
                                <div class="flex items-center space-x-1">
                                    <span>العمولة</span>
                                    @if ($sortField === 'commission')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('transaction_type')">
                                <div class="flex items-center space-x-1">
                                    <span>نوع المعاملة</span>
                                    @if ($sortField === 'transaction_type')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('agent_name')">
                                <div class="flex items-center space-x-1">
                                    <span>الموظف</span>
                                    @if ($sortField === 'agent_name')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('status')">
                                <div class="flex items-center space-x-1">
                                    <span>الحالة</span>
                                    @if ($sortField === 'status')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider sortable-header"
                                wire:click="sortBy('transaction_date_time')">
                                <div class="flex items-center space-x-1">
                                    <span>التاريخ</span>
                                    @if ($sortField === 'transaction_date_time')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th
                                class="px-3 py-2 bg-gray-50 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                الرقم المرجعي
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">
                                    {{ $transaction['customer_name'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $transaction['customer_code'] ?? '' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $transaction['amount'] == 0 ? '0' : number_format($transaction['amount'], 0) }}
                                    EGP</td>
                                @php
                                    $isWithdrawalOrDepositOrAdjustment = in_array($transaction['transaction_type'], [
                                        'Withdrawal',
                                        'Deposit',
                                        'Adjustment',
                                    ]);
                                @endphp
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    @if ($isWithdrawalOrDepositOrAdjustment)
                                        -
                                    @else
                                        {{ $transaction['commission'] == 0 ? '0' : number_format($transaction['commission'], 0) }}
                                        EGP
                                    @endif
                                </td>
                                {{-- <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    @if ($isWithdrawalOrDepositOrAdjustment)
                                        -
                                    @else
                                        {{ isset($transaction['discount']) && $transaction['discount'] == 0 ? '0' : (isset($transaction['discount']) ? number_format($transaction['discount'], 0) : '-') }}
                                        EGP
                                    @endif
                                </td> --}}
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $transaction['transaction_type'] }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['agent_name'] }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $transaction['status'] }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ \Carbon\Carbon::parse($transaction['transaction_date_time'])->format('d/m/y h:i A') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    {{ $transaction['reference_number'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-gray-500 text-center">لا يوجد معاملات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="flex justify-center mt-4">
                    @if ($hasMore)
                        <button wire:click="loadMore"
                            class="px-6 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
                            تحميل المزيد
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
