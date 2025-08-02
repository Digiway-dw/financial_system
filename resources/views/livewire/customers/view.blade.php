<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1
                            class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                            {{ $customer->name }}
                        </h1>
                        <p class="text-slate-600 mt-1">تفاصيل العميل وسجل المعاملات</p>
                    </div>
                </div>
                <div class="mt-4 lg:mt-0 flex space-x-3">
                    @php
                        $cannotEditRoles = ['agent', 'trainee', 'auditor'];
                    @endphp
                    @if (!auth()->user()->hasAnyRole($cannotEditRoles))
                        <a href="{{ route('customers.edit', $customer->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl hover:bg-indigo-200 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            تعديل العميل
                        </a>
                    @endif
                    <a href="{{ route('customers.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة إلى العملاء
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Personal Information -->
        <div class="lg:col-span-2">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    المعلومات الشخصية
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">الاسم الكامل</label>
                        <p class="text-slate-800 font-medium">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">رمز العميل</label>
                        <p class="text-slate-800 font-mono">{{ $customer->customer_code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">رقم الهاتف</label>
                        <div class="space-y-1">
                            @forelse($customer->mobileNumbers as $mobile)
                                <p class="text-slate-800 font-mono text-sm">{{ $mobile->mobile_number }}</p>
                            @empty
                                <p class="text-slate-800 font-mono text-sm">{{ $customer->mobile_number }}</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">الجنس</label>
                        <p class="text-slate-800 capitalize">{{ $customer->gender ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">المنطقة</label>
                        <p class="text-slate-800">{{ $customer->region ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">تاريخ الإضافة</label>
                        <p class="text-slate-800">
                            {{ $customer->created_at ? $customer->created_at->format('d/m/y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 mb-1">الوكيل</label>
                        <div class="text-slate-800 font-semibold">
                            {{ $customer->agent ? $customer->agent->name : 'N/A' }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-500 mb-1">الملاحظات</label>
                        <p class="text-slate-800">{{ $customer->notes ?? 'No notes available' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Summary -->
        <div>
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    ملخص الحساب
                </h2>
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
                        <div class="text-sm font-medium text-green-700 mb-1">الرصيد الحالي</div>
                        <div class="text-2xl font-bold text-green-800">{{ format_int($customer->balance) }} EGP
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-xl border border-blue-200">
                        <div class="text-sm font-medium text-blue-700 mb-1">إجمالي المعاملات</div>
                        <div class="text-xl font-bold text-blue-800">{{ format_int($totalTransactions) }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-xl border border-purple-200">
                        <div class="text-sm font-medium text-purple-700 mb-1">إجمالي المبالغ المنقولة</div>
                        <div class="text-lg font-bold text-purple-800">{{ format_int($totalTransferred) }} EGP
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-4 rounded-xl border border-yellow-200">
                        <div class="text-sm font-medium text-yellow-700 mb-1">إجمالي العمولة</div>
                        <div class="text-lg font-bold text-yellow-800">{{ format_int($totalCommission) }} EGP
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Section -->
    <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                سجل المعاملات
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">تاريخ</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">النوع</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">المبلغ</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">العمولة</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">الخط</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">الحالة</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customer->transactions as $transaction)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $transaction->transaction_date_time ? \Carbon\Carbon::parse($transaction->transaction_date_time)->format('d/m/y h:i A') : \Carbon\Carbon::parse($transaction->created_at)->format('d/m/y h:i A') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $transaction->transaction_type === 'send'
                                            ? 'bg-red-100 text-red-700'
                                            : ($transaction->transaction_type === 'receive'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-blue-100 text-blue-700') }}">
                                        {{ $transaction->descriptive_transaction_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-slate-900">
                                    {{ format_int($transaction->amount) }} EGP</td>
                                <td class="px-6 py-4 text-sm font-mono text-slate-700">
                                    {{ format_int($transaction->commission) }} EGP</td>
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $transaction->line->mobile_number ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ strtolower($transaction->status) === 'completed'
                                            ? 'bg-green-100 text-green-700'
                                            : (strtolower($transaction->status) === 'pending'
                                                ? 'bg-yellow-100 text-yellow-700'
                                                : 'bg-red-100 text-red-700') }}">
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
                                </td>
                                <td class="px-6 py-4">
                                    @if ($transaction instanceof \App\Models\Domain\Entities\CashTransaction)
                                        <a href="{{ route('cash-transactions.details', $transaction->id) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                    @else
                                        <a href="{{ route('transactions.details', $transaction->id) }}"
                                            class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-400 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <h3 class="text-sm font-medium text-slate-900 mb-1">لا يوجد معاملات</h3>
                                        <p class="text-sm text-slate-500">هذا العميل لم يقم بإجراء أي معاملات بعد.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
