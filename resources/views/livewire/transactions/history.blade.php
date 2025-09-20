<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50" dir="rtl"
    style="direction: rtl; text-align: right;" wire:poll.30s>
    <style>
        /* RTL fixes for history page */
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
    </style>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="bg-white shadow-sm border-b border-blue-200/20 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                تاريخ المعاملات</h3>
                            <p class="text-gray-600 text-sm">عرض المعاملات المعتمدة والمرفوضة</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('transactions.pending') }}" 
                           class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-lg hover:from-amber-600 hover:to-orange-700 transition-all duration-200 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            المعاملات المعلقة
                        </a>
                        <button wire:click="$refresh" 
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <button wire:click="setActiveTab('approved')"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            المعاملات المعتمدة
                        </div>
                    </button>
                    <button wire:click="setActiveTab('rejected')"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            المعاملات المرفوضة
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Filters -->
            <div class="p-6 bg-gray-50 rounded-b-xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                        <input wire:model.live.debounce.500ms="search" type="text" 
                               placeholder="اسم العميل، كود العميل"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Reference Number Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم المرجع</label>
                        <input wire:model.live.debounce.500ms="referenceNumber" type="text" 
                               placeholder="رقم المرجع"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Filter Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المعاملة</label>
                        <select wire:model.live="filterType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">جميع الأنواع</option>
                            <option value="transaction">معاملات عادية</option>
                            <option value="cash_transaction">معاملات نقدية</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                        <input wire:model.live="dateFrom" type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                        <input wire:model.live="dateTo" type="date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            @if ($activeTab === 'approved')
                <!-- Approved Transactions -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-green-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        المعاملات المعتمدة
                    </h3>

                    @if ($filterType === 'transaction' || $filterType === '')
                        @if ($approvedTransactions->count() > 0 || $this->approvedTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المرجع</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع المعاملة</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الاعتماد</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">معتمد بواسطة</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if ($filterType === 'transaction')
                                            @forelse ($this->approvedTransactions as $transaction)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $transaction->customer_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->reference_number ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                        {{ number_format($transaction->amount, 2) }} جنيه
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->approved_at?->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->approved_by ? \App\Domain\Entities\User::find($transaction->approved_by)?->name : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('transactions.details', $transaction->reference_number) }}" 
                                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            عرض
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات معتمدة</td>
                                                </tr>
                                            @endforelse
                                        @else
                                            {{-- Show combined results for filterType === '' --}}
                                            @forelse ($approvedTransactions as $transaction)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $transaction->customer_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->reference_number ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        <div class="flex items-center gap-2">
                                                            @if (isset($transaction->source_type) && $transaction->source_type === 'cash_transaction')
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">نقدي</span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">عادي</span>
                                                            @endif
                                                            {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                        {{ number_format($transaction->amount, 2) }} جنيه
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ ($transaction->approved_at ?? $transaction->updated_at)?->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->approved_by ? \App\Domain\Entities\User::find($transaction->approved_by)?->name : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if (isset($transaction->source_type) && $transaction->source_type === 'cash_transaction')
                                                            <a href="{{ route('cash-transactions.details', $transaction->reference_number) }}" 
                                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                عرض
                                                            </a>
                                                        @else
                                                            <a href="{{ route('transactions.details', $transaction->reference_number) }}" 
                                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                عرض
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات معتمدة</td>
                                                </tr>
                                            @endforelse
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            @if ($filterType === 'transaction')
                                <!-- Pagination for regular transactions -->
                                <div class="mt-4">
                                    {{ $this->approvedTransactions->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد معاملات معتمدة</h3>
                                <p class="mt-1 text-sm text-gray-500">لم يتم العثور على معاملات معتمدة مطابقة لمعايير البحث الخاصة بك.</p>
                            </div>
                        @endif
                    @endif

                    @if ($filterType === 'cash_transaction')
                        {{-- Show only cash transactions --}}
                        @if ($this->approvedCashTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع المعاملة</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الاعتماد</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($this->approvedCashTransactions as $transaction)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $transaction->customer_name }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">نقدي</span>
                                                    {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                    {{ number_format($transaction->amount, 2) }} جنيه
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                    {{ $transaction->updated_at?->format('Y-m-d H:i') }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <a href="{{ route('cash-transactions.details', $transaction->reference_number) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        عرض
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات نقدية معتمدة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $this->approvedCashTransactions->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد معاملات نقدية معتمدة</h3>
                                <p class="mt-1 text-sm text-gray-500">لم يتم العثور على معاملات نقدية معتمدة مطابقة لمعايير البحث الخاصة بك.</p>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <!-- Rejected Transactions -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        المعاملات المرفوضة
                    </h3>

                    @if ($filterType === 'transaction' || $filterType === '')
                        @if ($rejectedTransactions->count() > 0 || $this->rejectedTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المرجع</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع المعاملة</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الرفض</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مرفوض بواسطة</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سبب الرفض</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if ($filterType === 'transaction')
                                            @forelse ($this->rejectedTransactions as $transaction)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $transaction->customer_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->reference_number ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                        {{ number_format($transaction->amount, 2) }} جنيه
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->rejected_at?->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->rejected_by ? \App\Domain\Entities\User::find($transaction->rejected_by)?->name : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        <span class="max-w-xs truncate">{{ $transaction->rejection_reason ?? '-' }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('transactions.details', $transaction->reference_number) }}" 
                                                           class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            عرض
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات مرفوضة</td>
                                                </tr>
                                            @endforelse
                                        @else
                                            {{-- Show combined results for filterType === '' --}}
                                            @forelse ($rejectedTransactions as $transaction)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $transaction->customer_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->reference_number ?? '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        <div class="flex items-center gap-2">
                                                            @if (isset($transaction->source_type) && $transaction->source_type === 'cash_transaction')
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">نقدي</span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">عادي</span>
                                                            @endif
                                                            {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                        {{ number_format($transaction->amount, 2) }} جنيه
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ ($transaction->rejected_at ?? $transaction->updated_at)?->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        {{ $transaction->rejected_by ? \App\Domain\Entities\User::find($transaction->rejected_by)?->name : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                        <span class="max-w-xs truncate">{{ $transaction->rejection_reason ?? '-' }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if (isset($transaction->source_type) && $transaction->source_type === 'cash_transaction')
                                                            <a href="{{ route('cash-transactions.details', $transaction->reference_number) }}" 
                                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                عرض
                                                            </a>
                                                        @else
                                                            <a href="{{ route('transactions.details', $transaction->reference_number) }}" 
                                                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                عرض
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات مرفوضة</td>
                                                </tr>
                                            @endforelse
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            @if ($filterType === 'transaction')
                                <!-- Pagination for regular transactions -->
                                <div class="mt-4">
                                    {{ $this->rejectedTransactions->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد معاملات مرفوضة</h3>
                                <p class="mt-1 text-sm text-gray-500">لم يتم العثور على معاملات مرفوضة مطابقة لمعايير البحث الخاصة بك.</p>
                            </div>
                        @endif
                    @endif

                    @if ($filterType === 'cash_transaction')
                        {{-- Show only rejected cash transactions --}}
                        @if ($this->rejectedCashTransactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع المعاملة</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الرفض</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مرفوض بواسطة</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سبب الرفض</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($this->rejectedCashTransactions as $transaction)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $transaction->customer_name }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">نقدي</span>
                                                    {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                    {{ number_format($transaction->amount, 2) }} جنيه
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                    {{ $transaction->rejected_at?->format('Y-m-d H:i') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                    {{ $transaction->rejected_by ? \App\Domain\Entities\User::find($transaction->rejected_by)?->name : '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                                    <span class="max-w-xs truncate">{{ $transaction->rejection_reason ?? '-' }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <a href="{{ route('cash-transactions.details', $transaction->reference_number) }}" 
                                                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        عرض
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">لا توجد معاملات نقدية مرفوضة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $this->rejectedCashTransactions->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد معاملات نقدية مرفوضة</h3>
                                <p class="mt-1 text-sm text-gray-500">لم يتم العثور على معاملات نقدية مرفوضة مطابقة لمعايير البحث الخاصة بك.</p>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
