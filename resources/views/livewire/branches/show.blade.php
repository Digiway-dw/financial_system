<div dir="rtl" style="direction: rtl;">
    <div class="min-h-screen bg-gray-50">
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
                        <h1 class="text-2xl font-bold text-gray-900">الفرع: {{ $branch['name'] }}</h1>
                        <p class="text-sm text-gray-500">الموقع: {{ $branch['location'] ?? 'غير متوفر' }} | رصيد
                            الخزينة:
                            {{ isset($branch['safe']) ? format_int((int) $branch['safe']['current_balance']) . ' ج.م' : 'غير متوفر' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div><span class="font-semibold text-gray-700">الوصف:</span>
                        {{ $branch['description'] ?? 'غير متوفر' }}</div>
                    <div><span class="font-semibold text-gray-700">تاريخ الإنشاء:</span>
                        {{ \Carbon\Carbon::parse($branch['created_at'])->format('d/m/y h:i A') }}</div>
                    <div><span class="font-semibold text-gray-700">الحالة:</span>
                        {{ isset($branch['is_active']) && $branch['is_active'] ? 'نشط' : 'غير نشط' }}</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 px-8 pt-8">المعاملات الخاصة بهذا الفرع</h3>
                <div class="overflow-x-auto px-8 pb-8">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[110px] sortable-header"
                                    wire:click="sortBy('customer_name')">
                                    <div class="flex items-center space-x-1">
                                        <span>اسم العميل</span>
                                        @if ($sortField === 'customer_name')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[110px] sortable-header"
                                    wire:click="sortBy('customer_mobile_number')">
                                    <div class="flex items-center space-x-1">
                                        <span>رقم الجوال</span>
                                        @if ($sortField === 'customer_mobile_number')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[80px] sortable-header"
                                    wire:click="sortBy('amount')">
                                    <div class="flex items-center space-x-1">
                                        <span>المبلغ</span>
                                        @if ($sortField === 'amount')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[80px] sortable-header"
                                    wire:click="sortBy('commission')">
                                    <div class="flex items-center space-x-1">
                                        <span>العمولة</span>
                                        @if ($sortField === 'commission')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[70px] sortable-header"
                                    wire:click="sortBy('transaction_type')">
                                    <div class="flex items-center space-x-1">
                                        <span>النوع</span>
                                        @if ($sortField === 'transaction_type')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[90px] sortable-header"
                                    wire:click="sortBy('agent_name')">
                                    <div class="flex items-center space-x-1">
                                        <span>الموظف</span>
                                        @if ($sortField === 'agent_name')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[110px] sortable-header"
                                    wire:click="sortBy('created_at')">
                                    <div class="flex items-center space-x-1">
                                        <span>التاريخ</span>
                                        @if ($sortField === 'created_at')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[120px] sortable-header"
                                    wire:click="sortBy('reference_number')">
                                    <div class="flex items-center space-x-1">
                                        <span>رقم المرجع</span>
                                        @if ($sortField === 'reference_number')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th
                                    class="px-3 py-2 bg-gray-50 text-right text-sm font-semibold text-gray-600 uppercase tracking-wider min-w-[80px]">
                                    الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-xs">
                            @forelse($branchTransactions as $transaction)
                                <tr
                                    class="hover:bg-gray-50 transition-colors @if (strtolower($transaction['status']) === 'pending') bg-yellow-100 @endif">
                                    <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900 text-right">
                                        {{ $transaction['customer_name'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ $transaction['customer_mobile_number'] ?? '-' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ format_int($transaction['amount']) }} ج.م</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ isset($transaction['commission']) ? format_int($transaction['commission']) : '0' }}
                                        ج.م</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ $transaction['transaction_type'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ $transaction['agent_name'] ?? '-' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ \Carbon\Carbon::parse($transaction['transaction_date_time'] ?? $transaction['created_at'])->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 text-right">
                                        {{ $transaction['reference_number'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                        {{ $transaction['status'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-gray-500 text-center">لا توجد معاملات لهذا
                                        الفرع.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($hasMore)
                        <div class="flex justify-center mt-4">
                            <button wire:click="loadMore" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">تحميل المزيد</button>
                        </div>
                    @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
