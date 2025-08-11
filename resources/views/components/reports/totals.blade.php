{{-- Totals Footer Component --}}
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        الملخص المالي
    </h3>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Total Turnover --}}
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">إجمالي التداول</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totals['total_turnover'] ?? 0, 2) }} <span class="text-sm text-gray-500">EGP</span></p>
                </div>
            </div>
        </div>

        {{-- Total Commissions --}}
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">إجمالي العمولات</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totals['total_commissions'] ?? 0, 2) }} <span class="text-sm text-gray-500">EGP</span></p>
                </div>
            </div>
        </div>

        {{-- Total Deductions --}}
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">إجمالي الخصومات</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totals['total_deductions'] ?? 0, 2) }} <span class="text-sm text-gray-500">EGP</span></p>
                </div>
            </div>
        </div>

        {{-- Net Profit --}}
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">
                        @if(isset($showExpenses) && $showExpenses)
                            صافي الربح (بعد المصاريف)
                        @else
                            صافي الربح
                        @endif
                    </p>
                    @php
                        $netProfit = $totals['net_profit'] ?? 0;
                        if(isset($showExpenses) && $showExpenses) {
                            $netProfit -= ($totals['total_expenses'] ?? 0);
                        }
                        $profitClass = $netProfit >= 0 ? 'text-green-600' : 'text-red-600';
                    @endphp
                    <p class="text-lg font-semibold {{ $profitClass }}">{{ number_format($netProfit, 2) }} <span class="text-sm text-gray-500">EGP</span></p>
                </div>
            </div>
        </div>

        {{-- Total Expenses (if branch report) --}}
        @if(isset($showExpenses) && $showExpenses)
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">مصاريف الفروع</p>
                    <p class="text-lg font-semibold text-red-600">{{ number_format($totals['total_expenses'] ?? 0, 2) }} <span class="text-sm text-gray-500">EGP</span></p>
                </div>
            </div>
        </div>
        @endif

        {{-- Transaction Count --}}
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">عدد المعاملات</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($totals['transactions_count'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Note about expenses --}}
    @if(isset($showExpenses) && $showExpenses && ($totals['total_expenses'] ?? 0) > 0)
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                تم خصم مصاريف الفروع من صافي الربح. يشمل ذلك جميع المصاريف المسجلة في الفترة المحددة.
            </p>
        </div>
    @elseif(isset($showExpenses) && $showExpenses)
        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-sm text-gray-600">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                مصاريف الفروع: غير متاحة أو 0.00 EGP للفترة المحددة.
            </p>
        </div>
    @endif
</div>
