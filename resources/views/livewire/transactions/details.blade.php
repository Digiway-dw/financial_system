<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50" dir="rtl" style="direction: rtl;">
    <div class="max-w-4xl mx-auto px-2 md:px-8 py-10">
        <div class="bg-white/95 rounded-3xl shadow-2xl p-6 md:p-12">
            <div class="flex items-center justify-between mb-10 flex-row-reverse">
                <div class="flex items-center gap-3 flex-row-reverse">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">تفاصيل المعاملة</h1>
                </div>
                <a href="{{ route('transactions.index') }}"
                    class="inline-flex items-center px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-2xl shadow transition-all duration-200">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    العودة للمعاملات
                </a>
            </div>

            @if ($transactionType === 'transaction' && $transaction)
                <!-- Regular Transaction Details -->
                <div class="space-y-6">
                    <!-- Transaction Header -->
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200 shadow-sm mb-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">رقم المرجع:
                                    {{ $transaction->reference_number ?? 'معاملة #' . $transaction->id }}</h2>
                                <p class="text-sm text-gray-600 mt-1">رقم المرجع:
                                    {{ $transaction->reference_number ?? 'غير متوفر' }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if (strtolower($transaction->status) === 'completed') bg-green-100 text-green-800
                                    @elseif(strtolower($transaction->status) === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif(strtolower($transaction->status) === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if (strtolower($transaction->status) === 'completed')
                                        مكتملة
                                    @elseif(strtolower($transaction->status) === 'pending')
                                        قيد الانتظار
                                    @elseif(strtolower($transaction->status) === 'rejected')
                                        مرفوضة
                                    @else
                                        {{ $transaction->status ?? 'غير معروف' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-blue-500 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                معلومات أساسية
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">نوع المعاملة:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->descriptive_transaction_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">المبلغ:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ format_int($transaction->amount) }}
                                        ج.م</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">العمولة:</span>
                                    <span class="text-sm text-gray-900">{{ format_int($transaction->commission ?? 0) }}
                                        ج.م</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">الخصم:</span>
                                    <span class="text-sm text-gray-900">{{ format_int($transaction->deduction ?? 0) }}
                                        ج.م</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">طريقة الدفع:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->payment_method ?? 'غير متوفر' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-green-500 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                بيانات العميل
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">اسم العميل:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->customer_name ?? 'غير متوفر' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">رقم الجوال:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->customer_mobile_number ?? 'غير متوفر' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">كود العميل:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->customer_code ?? 'غير متوفر' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">جوال المستلم:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->receiver_mobile_number ?? 'غير متوفر' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agent & Branch Information -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-purple-500 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                بيانات الوكيل والفرع
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">الوكيل:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->agent->name ?? 'غير متوفر' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">الفرع:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->branch->name ?? 'غير متوفر' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">الخط:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->line->mobile_number ?? 'غير متوفر' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">الخزنة:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->safe->name ?? 'غير متوفر' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-orange-500 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                التواريخ
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">تاريخ الإنشاء:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->created_at->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">تاريخ التحديث:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $transaction->updated_at->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</span>
                                </div>
                                @if ($transaction->transaction_date_time)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">تاريخ المعاملة:</span>
                                        <span
                                            class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($transaction->transaction_date_time)->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    @if ($transaction->notes || $transaction->discount_notes)
                        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-3">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                ملاحظات
                            </h3>
                            <div class="space-y-3">
                                @if ($transaction->notes)
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">ملاحظات عامة:</span>
                                        <p class="text-sm text-gray-900 mt-1">{{ $transaction->notes }}</p>
                                    </div>
                                @endif
                                @if ($transaction->discount_notes)
                                    <div>
                                        <span class="text-sm font-medium text-gray-600">ملاحظات الخصم:</span>
                                        <p class="text-sm text-gray-900 mt-1">{{ $transaction->discount_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-6">
                        <div class="flex flex-row-reverse gap-4">
                            <a href="{{ route('transactions.receipt', $transaction->reference_number) }}"
                                class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl shadow transition-colors duration-200 text-base">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2m-6 0v4m0 0h4m-4 0H8">
                                    </path>
                                </svg>
                                طباعة الإيصال
                            </a>
                            @can('edit-all-transactions')
                                <a href="{{ route('transactions.edit', $transaction->reference_number) }}"
                                    class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow transition-colors duration-200 text-base">
                                    
                                    تعديل المعاملة
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @elseif($transactionType === 'cash_transaction' && $cashTransaction)
                <!-- Cash Transaction Details -->
                <div class="space-y-6">
                    <!-- Transaction Header -->
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Reference:
                                    {{ $cashTransaction->reference_number ?? 'Cash Transaction #' . $cashTransaction->id }}
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">Type:
                                    {{ $cashTransaction->descriptive_transaction_name }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if (strtolower($cashTransaction->status) === 'completed') bg-green-100 text-green-800
                                    @elseif(strtolower($cashTransaction->status) === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif(strtolower($cashTransaction->status) === 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if (strtolower($cashTransaction->status) === 'completed')
                                        مكتملة
                                    @elseif(strtolower($cashTransaction->status) === 'pending')
                                        قيد الانتظار
                                    @elseif(strtolower($cashTransaction->status) === 'rejected')
                                        مرفوضة
                                    @else
                                        {{ $cashTransaction->status ?? 'غير معروف' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Basic Information
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Transaction Type:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->descriptive_transaction_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Amount:</span>
                                    <span
                                        class="text-sm font-bold text-gray-900">{{ format_int($cashTransaction->amount) }}
                                        EGP</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Commission:</span>
                                    <span class="text-sm text-gray-900">0 EGP</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Customer Code:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->customer_code ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Customer Name:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->customer_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agent & Safe Information -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Agent & Safe
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Agent:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->agent->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Safe:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->safe->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Branch:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->safe->branch->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Timestamps
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Created At:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->created_at->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Updated At:</span>
                                    <span
                                        class="text-sm text-gray-900">{{ $cashTransaction->updated_at->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Additional Information
                            </h3>
                            <div class="space-y-3">
                                @if ($cashTransaction->destination_branch_id)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Destination Branch:</span>
                                        <span
                                            class="text-sm text-gray-900">{{ $cashTransaction->destinationBranch->name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                                @if ($cashTransaction->destination_safe_id)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Destination Safe:</span>
                                        <span
                                            class="text-sm text-gray-900">{{ $cashTransaction->destinationSafe->name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                                @if ($cashTransaction->rejected_by)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Rejected By:</span>
                                        <span
                                            class="text-sm text-gray-900">{{ $cashTransaction->rejectedBy->name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                                @if ($cashTransaction->rejection_reason)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-600">Rejection Reason:</span>
                                        <span
                                            class="text-sm text-gray-900">{{ $cashTransaction->rejection_reason }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    @if ($cashTransaction->notes)
                        <div class="bg-white rounded-xl border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Notes
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Notes:</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $cashTransaction->notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <a href="{{ route('cash-transactions.receipt', $cashTransaction->reference_number) }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2m-6 0v4m0 0h4m-4 0H8">
                                    </path>
                                </svg>
                                Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
