<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100" dir="rtl" style="direction: rtl;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8 mb-8">
            <h1 class="text-2xl font-bold text-blue-900 mb-2">تفاصيل الخط</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div>
                    <div class="text-sm text-gray-500">رقم الهاتف</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $line->mobile_number }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الشبكة</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $line->network }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الفرع</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $line->branch->name ?? 'N/A' }}</div>
                </div>
                
                <!-- Balance Information -->
                <div>
                    <div class="text-sm text-gray-500">الرصيد الحالي</div>
                    <div class="text-lg font-semibold text-blue-700">{{ format_int($line->current_balance) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الرصيد الابتدائي</div>
                    <div class="text-lg font-semibold text-gray-900">{{ format_int($line->starting_balance) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الرصيد الابتدائي اليومي</div>
                    <div class="text-lg font-semibold text-gray-900">{{ format_int($line->daily_starting_balance) }} EGP</div>
                </div>
                
                <!-- Daily Limits and Usage -->
                <div>
                    <div class="text-sm text-gray-500">الحد اليومي</div>
                    <div class="text-lg font-semibold text-gray-900">{{ format_int($line->daily_limit) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الاستخدام اليومي</div>
                    <div class="text-lg font-semibold text-orange-600">{{ format_int($line->daily_usage) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">المتبقي اليومي</div>
                    <div class="text-lg font-semibold {{ $line->daily_remaining > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ format_int($line->daily_remaining) }} EGP
                    </div>
                </div>
                
                <!-- Monthly Limits and Usage -->
                <div>
                    <div class="text-sm text-gray-500">الحد الشهري</div>
                    <div class="text-lg font-semibold text-gray-900">{{ format_int($line->monthly_limit) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الاستخدام الشهري</div>
                    <div class="text-lg font-semibold text-orange-600">{{ format_int($line->monthly_usage) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">المتبقي الشهري</div>
                    <div class="text-lg font-semibold {{ $line->monthly_remaining > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ format_int($line->monthly_remaining) }} EGP
                    </div>
                </div>
                
                <!-- Receive Amounts -->
                <div>
                    <div class="text-sm text-gray-500">المستلم اليوم</div>
                    <div class="text-lg font-semibold text-green-600">{{ format_int($dailyReceive) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">المستلم هذا الشهر</div>
                    <div class="text-lg font-semibold text-green-600">{{ format_int($monthlyReceive) }} EGP</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">الحالة</div>
                    <div class="text-lg font-semibold {{ $line->status === 'active' ? 'text-green-700' : 'text-red-700' }}">
                        {{ ucfirst($line->status === 'active' ? 'مفعل' : 'معطل') }}
                    </div>
                </div>
                
                <!-- Reset Information -->
                <div>
                    <div class="text-sm text-gray-500">آخر إعادة تعيين يومي</div>
                    <div class="text-sm font-semibold text-gray-700">
                        {{ $line->last_daily_reset ? \Carbon\Carbon::parse($line->last_daily_reset)->format('d/m/Y H:i') : 'غير محدد' }}
                    </div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">آخر إعادة تعيين شهري</div>
                    <div class="text-sm font-semibold text-gray-700">
                        {{ $line->last_monthly_reset ? \Carbon\Carbon::parse($line->last_monthly_reset)->format('d/m/Y H:i') : 'غير محدد' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8">
            <h2 class="text-xl font-bold text-blue-900 mb-4">سجل المعاملات</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                التاريخ</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                النوع</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                المبلغ</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $tx)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ \Carbon\Carbon::parse($tx->transaction_date_time)->format('d/m/y h:i A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ $tx->transaction_type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700 text-center">
                                    {{ format_int($tx->amount) }} EGP</td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-center {{ $tx->status === 'completed' ? 'text-green-700' : ($tx->status === 'pending' ? 'text-yellow-700' : 'text-red-700') }}">
                                    {{ ucfirst($tx->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">لا يوجد معاملات لهذه
                                    الخطة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasMorePages())
                <div class="flex justify-center mt-6">
                    <button wire:click="loadMore" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                        تحميل المزيد
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
