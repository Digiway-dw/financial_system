<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">التقارير المحسنة</h1>
                    <p class="mt-2 text-gray-600">تقارير شاملة مع إمكانيات البحث والتصدير المتقدمة</p>
                </div>

                {{-- Report Type Selector --}}
                <div class="flex space-x-2 rtl:space-x-reverse">
                    <button wire:click="$set('reportType', 'transactions')"
                        class="px-4 py-2 rounded-lg {{ $reportType === 'transactions' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                        تقرير المعاملات
                    </button>
                    <button wire:click="$set('reportType', 'employee')"
                        class="px-4 py-2 rounded-lg {{ $reportType === 'employee' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                        تقرير الموظف
                    </button>
                    <button wire:click="$set('reportType', 'customer')"
                        class="px-4 py-2 rounded-lg {{ $reportType === 'customer' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                        تقرير العميل
                    </button>
                    <button wire:click="$set('reportType', 'branch')"
                        class="px-4 py-2 rounded-lg {{ $reportType === 'branch' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">
                        تقرير الفرع
                    </button>
                </div>
            </div>
        </div>

        {{-- Universal Filters --}}
        <x-reports.filters :branches="$branches" :employees="$employees" :showEmployeeFilter="$showEmployeeFilter" :showCustomerFilter="$showCustomerFilter" :lines="$lines" />

        {{-- Employee Summary Card --}}
        @if ($reportType === 'employee' && $employeeDetails)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    معلومات الموظف
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">الاسم</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $employeeDetails['name'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">الهاتف</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $employeeDetails['phone'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">الفرع</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $employeeDetails['branch'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">فترة التقرير</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $startDate }} إلى {{ $endDate }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Customer Summary Card --}}
        @if ($reportType === 'customer' && $customerDetails)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    معلومات العميل
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">اسم العميل</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $customerDetails['name'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">كود العميل</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $customerDetails['customer_code'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">رقم الجوال</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $customerDetails['mobile_number'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">رصيد المحفظة</p>
                        <p
                            class="text-lg font-semibold {{ $customerDetails['is_client'] ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $customerDetails['is_client'] ? number_format($customerDetails['balance'], 2) . ' EGP' : 'غير متاح' }}
                        </p>
                    </div>
                    @if ($customerDetails['safe_balance'] !== null)
                        <div>
                            <p class="text-sm font-medium text-gray-500">رصيد الخزينة المربوطة</p>
                            <p class="text-lg font-semibold text-blue-600">
                                {{ number_format($customerDetails['safe_balance'], 2) }} EGP</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Branch Balances Summary --}}
        @if ($reportType === 'branch' && !empty($branchDetails))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Safe Balances --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        أرصدة الخزائن بالفروع
                    </h3>
                    <div class="space-y-3">
                        @foreach ($branchDetails['safe_balances'] as $safe)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">{{ $safe['branch'] }}</span>
                                <span
                                    class="text-lg font-semibold text-green-600">{{ number_format($safe['balance'], 2) }}
                                    EGP</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Line Balances --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        أرصدة الخطوط بالفروع
                    </h3>
                    <div class="space-y-3">
                        @foreach ($branchDetails['line_balances'] as $line)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">{{ $line['branch'] }}</span>
                                <span
                                    class="text-lg font-semibold text-blue-600">{{ number_format($line['balance'], 2) }}
                                    EGP</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Totals Summary --}}
        @if (!empty($totals))
            <x-reports.totals :totals="$totals" :showExpenses="$showExpenses" />
        @endif

        {{-- Transactions Table --}}
        <div class="mt-6">
            <x-reports.transactions-table :transactions="$transactions" :totalCount="$totalCount" :hasMore="$hasMore" :sortField="$sortField"
                :sortDirection="$sortDirection" />
        </div>

        {{-- Export Actions --}}
        @if (!empty($transactions))
            <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    تصدير التقرير
                </h3>
                <div class="flex flex-wrap gap-3">
                    <button wire:click="exportExcel"
                        class="px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition shadow-md">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3" />
                        </svg>
                        تصدير Excel
                    </button>

                    <button wire:click="exportPdf"
                        class="px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 transition shadow-md">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3" />
                        </svg>
                        تصدير PDF
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    سيتم تصدير التقرير بنفس الفلاتر والترتيب المطبقة حاليًا.
                    @if ($totalCount > count($transactions))
                        سيتم تصدير جميع السجلات ({{ number_format($totalCount) }}) وليس فقط المعروضة.
                    @endif
                </p>
            </div>
        @endif

        {{-- Loading States --}}
        @if ($showLoading)
            <div class="fixed inset-0 z-50 flex items-center justify-center min-h-screen"
                style="background:rgba(0,0,0,0.25);">
                <div class="backdrop-blur-lg bg-white/60 border border-gray-600 shadow-xl rounded-2xl p-8 flex flex-col items-center justify-center space-y-6"
                    style="min-width:320px;min-height:180px;">
                    <svg class="animate-spin h-10 w-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-xl font-bold text-gray-900 drop-shadow-lg">جاري تحميل التقرير...</span>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        // Auto-refresh every 5 minutes for real-time data
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                @this.call('generateReport');
            }
        }, 300000);
    </script>
@endpush
