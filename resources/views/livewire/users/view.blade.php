<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50" dir="rtl" style="direction: rtl;">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">ملف المستخدم</h1>
                    <p class="mt-2 text-sm text-gray-600">عرض معلومات المستخدم: {{ $user->name }}</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 ml-1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                        العودة إلى قائمة المستخدمين
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- User Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-8">
                <div class="flex flex-col sm:flex-row">
                    <!-- User Avatar and Role Badge -->
                    <div class="flex flex-col items-center mb-6 sm:mb-0 sm:mr-8">
                        <div class="relative">
                            <div
                                class="h-32 w-32 bg-indigo-100 rounded-full flex items-center justify-center text-2xl font-bold text-indigo-600 mb-3">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="absolute -bottom-1 -right-1">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if ($role == 'admin') bg-red-100 text-red-800
                                @elseif($role == 'general_supervisor') bg-blue-100 text-blue-800
                                @elseif($role == 'branch_manager') bg-purple-100 text-purple-800
                                @elseif($role == 'agent') bg-green-100 text-green-800
                                @elseif($role == 'auditor') bg-cyan-100 text-cyan-800
                                @elseif($role == 'trainee') bg-gray-100 text-gray-800
                                @else bg-green-100 text-green-800 @endif">
                                    @if ($role == 'admin')
                                        مدير
                                    @elseif($role == 'general_supervisor')
                                        مشرف عام
                                    @elseif($role == 'branch_manager')
                                        مدير فرع
                                    @elseif($role == 'agent')
                                        موظف
                                    @elseif($role == 'auditor')
                                        مراجع
                                    @elseif($role == 'trainee')
                                        متدرب
                                    @else
                                        موظف
                                    @endif
                                </span>
                            </div>
                        </div>
                        @can('update', $user)
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="mt-4 inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                تعديل المستخدم
                            </a>
                        @endcan
                    </div>

                    <!-- User Details -->
                    <div class="flex-1">
                        <h3
                            class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-indigo-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                            </svg>
                            المعلومات الشخصية
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-900">
                            <!-- Ignore Work Hours Status -->
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">تجاهل أوقات العمل</span>
                                    <span class="font-medium">
                                        @if ($user->ignore_work_hours)
                                            نعم (يمكنه العمل في أي وقت)
                                        @else
                                            لا (يخضع لأوقات العمل المحددة)
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">الاسم الكامل</span>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">البريد الإلكتروني</span>
                                    <span class="font-medium">{{ $user->email }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">رقم الهاتف</span>
                                    <span class="font-medium">{{ $user->phone_number ?? 'غير متوفر' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">الرقم القومي</span>
                                    <span class="font-medium">{{ $user->national_number ?? 'غير متوفر' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">الراتب</span>
                                    <span class="font-medium">
                                        @if ($user->salary)
                                            {{ number_format($user->salary, 2) }} ج.م
                                        @else
                                            غير محدد
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">العنوان</span>
                                    <span class="font-medium">{{ $user->address ?? 'غير متوفر' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">رقم الأرض</span>
                                    <span class="font-medium">{{ $user->land_number ?? 'غير متوفر' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">رقم هاتف القريب</span>
                                    <span class="font-medium">{{ $user->relative_phone_number ?? 'غير متوفر' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div>
                                    <span class="block text-sm text-gray-500">الفرع</span>
                                    <span class="font-medium">{{ $branch ? $branch->name : 'غير متوفر' }}</span>
                                </div>
                            </div>
                        </div>

                        @if ($user->notes)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    ملاحظات
                                </h4>
                                <div class="bg-gray-50 p-3 rounded-md text-gray-700">
                                    {{ $user->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Working Hours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="border-b border-gray-200 px-8 py-4 bg-gray-50 rounded-t-xl">
                <h4 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 text-amber-600 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ساعات العمل
                </h4>
            </div>
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    اليوم</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    وقت البدء</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    وقت الانتهاء</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($workingHours as $workingHour)
                                <tr class="hover:bg-gray-50">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                        {{ __(
                                            match ($workingHour->day_of_week) {
                                                'saturday' => 'السبت',
                                                'sunday' => 'الأحد',
                                                'monday' => 'الاثنين',
                                                'tuesday' => 'الثلاثاء',
                                                'wednesday' => 'الأربعاء',
                                                'thursday' => 'الخميس',
                                                'friday' => 'الجمعة',
                                                default => $workingHour->day_of_week,
                                            },
                                        ) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ \Carbon\Carbon::parse($workingHour->start_time)->format('h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ \Carbon\Carbon::parse($workingHour->end_time)->format('h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @if ($workingHour->is_enabled)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                مفعل
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                غير مفعل
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد
                                        ساعات عمل محددة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="border-b border-gray-200 px-8 py-4 bg-gray-50 rounded-t-xl">
                <h4 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    سجل المعاملات
                </h4>
            </div>
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    رقم المرجع</th>
                                {{-- <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    النوع</th> --}}
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    النوع العملية</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    المبلغ</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    الحالة</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $displayedTransactions = $transactions->forPage(1, 20); @endphp
                            @foreach ($displayedTransactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                        {{ $transaction->reference_number ?? '#' . $transaction->id }}</td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $transaction->type ?? '-' }}</td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $transaction->descriptive_transaction_name ?? $transaction->transaction_type ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $transaction->amount ? number_format($transaction->amount, 2) . ' ج.م' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @if (strtolower($transaction->status) === 'completed')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">مكتمل</span>
                                        @elseif(strtolower($transaction->status) === 'pending')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">قيد
                                                الانتظار</span>
                                        @elseif(strtolower($transaction->status) === 'rejected')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">مرفوض</span>
                                        @elseif(strtolower($transaction->status) === 'failed')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">فشل</span>
                                        @else
                                            {{ $transaction->status ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $transaction->created_at ? $transaction->created_at->format('d/m/y h:i A') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            @if ($transactions->count() === 0)
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد
                                        معاملات.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if ($transactions->count() > 20)
                        <div class="flex justify-center mt-4">
                            <button id="loadMoreBtn"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-md font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                تحميل المزيد
                            </button>
                        </div>
                        <script>
                            let currentPage = 1;
                            const perPage = 20;
                            const allRows = Array.from(document.querySelectorAll('tbody tr'));

                            function showRows(page) {
                                allRows.forEach((row, idx) => {
                                    row.style.display = (idx < page * perPage) ? '' : 'none';
                                });
                            }
                            showRows(currentPage);
                            document.getElementById('loadMoreBtn').addEventListener('click', function() {
                                currentPage++;
                                showRows(currentPage);
                                if (currentPage * perPage >= allRows.length) {
                                    this.style.display = 'none';
                                }
                            });
                        </script>
                    @endif
                    </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Login History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-8 py-4 bg-gray-50 rounded-t-xl">
                <h4 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                    سجل الدخول
                </h4>
            </div>
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    وقت الدخول</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    وقت الخروج</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    مدة الجلسة</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($loginHistories as $history)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $history->login_at ? \Carbon\Carbon::parse($history->login_at)->format('d/m/y h:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        {{ $history->logout_at ? \Carbon\Carbon::parse($history->logout_at)->format('d/m/y h:i A') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                        @if ($history->session_duration)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ gmdate('i\m\ s\s', $history->session_duration) }}
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                نشط
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">لا يوجد سجل
                                        دخول.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
