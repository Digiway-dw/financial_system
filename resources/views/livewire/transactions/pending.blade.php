<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-red-50" dir="rtl"
    style="direction: rtl; text-align: right;">
    <style>
        /* RTL fixes for pending transactions */
        [dir="rtl"] .flex-row-reverse {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .gap-3> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 1;
            margin-left: calc(0.75rem * var(--tw-space-x-reverse));
            margin-right: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
        }

        [dir="rtl"] .mr-4 {
            margin-left: 1rem !important;
            margin-right: 0 !important;
        }

        [dir="rtl"] .ml-2 {
            margin-right: 0.5rem !important;
            margin-left: 0 !important;
        }

        [dir="rtl"] .text-left {
            text-align: right !important;
        }

        [dir="rtl"] .text-right {
            text-align: right !important;
        }

        [dir="rtl"] .text-center {
            text-align: center !important;
        }

        [dir="rtl"] .rounded-l-xl {
            border-top-right-radius: 0.75rem !important;
            border-bottom-right-radius: 0.75rem !important;
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        [dir="rtl"] .rounded-r-xl {
            border-top-left-radius: 0.75rem !important;
            border-bottom-left-radius: 0.75rem !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
    </style>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="bg-white shadow-sm border-b border-amber-200/20 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                            المعاملات المعلقة</h3>
                        <p class="text-gray-600 text-sm">مراجعة والموافقة على المعاملات المعلقة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Pending -->
            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي المعلقة</p>
                        <p class="text-2xl font-bold text-amber-700">{{ count($pendingTransactions) }}</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Amount -->
            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">المبلغ المعلق</p>
                        <p class="text-2xl font-bold text-orange-700">
                            {{ format_int(array_sum(array_column($pendingTransactions, 'amount'))) }} ج.م
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Actions Required -->
            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">تتطلب إجراء</p>
                        <p class="text-2xl font-bold text-red-700">{{ count($pendingTransactions) }}</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-red-400 to-red-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-gray-200/30">
                <h4 class="text-lg font-semibold text-gray-900">قائمة المعاملات المعلقة</h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-gray-200/30">
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                اسم العميل</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                المبلغ</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                النوع</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                رقم الخط</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                الوكيل</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                التاريخ</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/30">
                        @forelse ($pendingTransactions as $transaction)
                            @if ($transaction['status'] === 'pending' || $transaction['status'] === 'Pending')
                                <tr class="hover:bg-white/40 transition-colors duration-200">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $transaction['customer_name'] }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="font-semibold text-amber-700">{{ format_int($transaction['amount']) }}</span>
                                        <span class="text-gray-600 text-xs">ج.م</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium
                                            @if ($transaction['transaction_type'] === 'Transfer') bg-blue-100 text-blue-800
                                            @elseif($transaction['transaction_type'] === 'Withdrawal') bg-red-100 text-red-800
                                            @elseif($transaction['transaction_type'] === 'Deposit') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $transaction['transaction_type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if ($transaction['transaction_type'] === 'Withdrawal' && isset($transaction['line']))
                                            {{ $transaction['line']['mobile_number'] ?? '' }}
                                        @else
                                            <span class="text-gray-400">غير متاح</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction['agent_name'] ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d/m/y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($transaction['status'] === 'pending' || $transaction['status'] === 'Pending')
                                                <button wire:click="approve('{{ $transaction['id'] }}')"
                                                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold rounded-lg shadow-md hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200">
                                                    موافقة
                                                </button>
                                                <button wire:click="reject('{{ $transaction['id'] }}')"
                                                    class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-semibold rounded-lg shadow-md hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200">
                                                    رفض
                                                </button>
                                            @else
                                                <span class="text-gray-400">تمت المعالجة</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="p-3 bg-gray-100 rounded-full">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">لا توجد معاملات معلقة</p>
                                        <p class="text-gray-400 text-sm">جميع المعاملات تمت مراجعتها</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Messages Section -->
        @if (session('message') || session('error'))
            <div class="mt-6 space-y-4">
                @if (session('message'))
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                        class="p-4 bg-green-50/70 border border-green-200/50 rounded-xl backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-green-800 font-medium">{{ session('message') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                        class="p-4 bg-red-50/70 border border-red-200/50 rounded-xl backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-red-800 font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
