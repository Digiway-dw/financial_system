<div class="min-h-screen bg-gray-50" dir="rtl" style="direction: rtl;">
    <style>
        .sortable-header {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .sortable-header:hover {
            background-color: #f3f4f6 !important;
            transform: translateY(-1px);
        }

        .sortable-header:active {
            transform: translateY(0);
        }
    </style>
    <!-- قسم العنوان -->
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
                    <h1 class="text-2xl font-bold text-gray-900">إدارة المعاملات المالية</h1>
                    <p class="text-sm text-gray-500">مراقبة ومعالجة وإدارة جميع المعاملات المالية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php
            use App\Constants\Roles;
            $user = Auth::user();
            $hideEmployeeFilter =
                $user->hasRole(Roles::BRANCH_MANAGER) || $user->hasRole(Roles::AGENT) || $user->hasRole(Roles::TRAINEE);
            $hideBranchFilter =
                $user->hasRole(Roles::BRANCH_MANAGER) || $user->hasRole(Roles::AGENT) || $user->hasRole(Roles::TRAINEE);
        @endphp
        @if (!auth()->user() || !auth()->user()->hasRole(Roles::AUDITOR))
            <!-- Transaction Actions Section -->
            <div class="mb-10 bg-white rounded-2xl shadow border border-gray-200 px-8 py-8 md:px-12 md:py-10">
                <div class="border-b border-gray-100 pb-4 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        إجراءات سريعة
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">إنشاء معاملة جديدة أو الوصول إلى أدوات المعاملات</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                    <a href="{{ route('transactions.send') }}"
                        class="group flex items-center px-8 py-7 md:px-10 md:py-8 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-2xl transition-all duration-200 shadow-sm mb-2 md:mb-0">
                        <div
                            class="w-16 h-16 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center mr-8">
                            <svg class="w-6 h-6 text-blue-600 " fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                        <div class="space-y-1 p-2">
                            <h3 class="font-semibold text-blue-900">إرسال أموال</h3>
                            <p class="text-sm text-blue-700">إنشاء تحويل صادر</p>
                        </div>
                    </a>
                    <a href="{{ route('transactions.receive') }}"
                        class="group flex items-center px-8 py-7 md:px-10 md:py-8 bg-green-50 hover:bg-green-100 border border-green-100 rounded-2xl transition-all duration-200 shadow-sm mb-2 md:mb-0">
                        <div
                            class="w-16 h-16 bg-green-100 group-hover:bg-green-200 rounded-xl flex items-center justify-center mr-8">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                        </div>
                        <div class="space-y-1 p-2">
                            <h3 class="font-semibold text-green-900">استلام أموال</h3>
                            <p class="text-sm text-green-700">معالجة تحويل وارد</p>
                        </div>
                    </a>
                    @can('create-cash-transactions')
                        <a href="{{ route('transactions.cash') }}"
                            class="group flex items-center px-8 py-7 md:px-10 md:py-8 bg-yellow-50 hover:bg-yellow-100 border border-yellow-100 rounded-2xl transition-all duration-200 shadow-sm mb-2 md:mb-0">
                            <div
                                class="w-16 h-16 bg-yellow-100 group-hover:bg-yellow-200 rounded-xl flex items-center justify-center mr-8">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="space-y-1 p-2">
                                <h3 class="font-semibold text-yellow-900">معاملة نقدية</h3>
                                <p class="text-sm text-yellow-700">إدارة العمليات النقدية</p>
                            </div>
                        </a>
                    @endcan
                </div>
            </div>
        @endif
        <!-- Transaction Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-2 mb-5">
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-8 md:p-10 mb-4 md:mb-0">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mr-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">إجمالي المعاملات</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ count($transactions) }}</p>
                        @if (auth()->user()->hasRole('agent'))
                            <p class="text-xs text-gray-400">معاملاتك فقط</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-8 md:p-10 mb-4 md:mb-0">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mr-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">إجمالي المبالغ</h3>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ format_int(array_sum(array_column($transactions, 'amount'))) }} ج.م</p>
                        @if (auth()->user()->hasRole('agent'))
                            <p class="text-xs text-gray-400">مبالغك فقط</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow border border-gray-200 p-8 md:p-10 mb-4 md:mb-0">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center mr-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">قيد الانتظار</h3>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($transactions, fn($t) => $t['status'] === 'pending')) }}</p>
                        @if (auth()->user()->hasRole('agent'))
                            <p class="text-xs text-gray-400">معاملاتك المعلقة فقط</p>
                        @endif
                    </div>
                </div>
            </div>
            @can('view-commission-data')
                <div class="bg-white rounded-2xl shadow border border-gray-200 p-8 md:p-10 mb-4 md:mb-0">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mr-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">إجمالي العمولات</h3>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ format_int(array_sum(array_column($transactions, 'commission'))) }} ج.م</p>
                            @if (auth()->user()->hasRole('agent'))
                                <p class="text-xs text-gray-400">عمولاتك فقط</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan
        </div>
        <!-- Advanced Filter Section -->
        <div class="mb-10 bg-white rounded-2xl shadow border-2 border-blue-200 p-3 md:p-4">
            <div class="border-b-2 border-blue-100 pb-3 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                            </path>
                        </svg>
                    </div>
                    فلاتر متقدمة
                </h2>
                <p class="text-sm text-gray-500 mt-1">تصفية المعاملات حسب معايير متعددة لتحليل مفصل</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5">
                <!-- عدد الصفوف لكل صفحة -->
                <div class="space-y-2">
                    <label for="perPage" class="block text-sm font-medium text-gray-700">عدد الصفوف في الجدول</label>
                    <select wire:model="perPage" id="perPage"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-white">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <!-- كود العميل -->
                <div class="space-y-3">
                    <label for="customer_code" class="block text-sm font-medium text-gray-700">كود العميل</label>
                    <input wire:model.defer="customer_code" id="customer_code" type="text"
                        placeholder="ادخل كود العميل..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-white" />
                </div>
                <!-- جوال المستلم -->
                <div class="space-y-2">
                    <label for="receiver_mobile_number" class="block text-sm font-medium text-gray-700">جوال
                        المستلم</label>
                    <input wire:model.defer="receiver_mobile_number" id="receiver_mobile_number" type="text"
                        placeholder="ادخل رقم الجوال..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- خط التحويل -->
                <div class="space-y-2">
                    <label for="transfer_line" class="block text-sm font-medium text-gray-700">خط التحويل</label>
                    <input wire:model.defer="transfer_line" id="transfer_line" type="text"
                        placeholder="ادخل رقم الخط..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- نطاق المبلغ -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">نطاق المبلغ</label>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <input wire:model.defer="amount_from" id="amount_from" type="text" placeholder="من"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                        </div>
                        <div>
                            <input wire:model.defer="amount_to" id="amount_to" type="text" placeholder="إلى"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                        </div>
                    </div>
                </div>

                <!-- العمولة -->
                <div class="space-y-2">
                    <label for="commission" class="block text-sm font-medium text-gray-700">العمولة</label>
                    <input wire:model.defer="commission" id="commission" type="text" placeholder="0.00"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- نوع المعاملة -->
                <div class="space-y-2">
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700">نوع المعاملة</label>
                    <select wire:model.defer="transaction_type" id="transaction_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                        <option value="">كل الأنواع</option>
                        <option value="Transfer">تحويل</option>
                        <option value="Deposit">إيداع</option>
                        <option value="Withdrawal">سحب</option>
                    </select>
                </div>

                <!-- تاريخ البدء -->
                <div class="space-y-2">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">تاريخ البدء</label>
                    <input wire:model.defer="start_date" id="start_date" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- تاريخ الانتهاء -->
                <div class="space-y-2">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">تاريخ الانتهاء</label>
                    <input wire:model.defer="end_date" id="end_date" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                </div>

                <!-- فلتر الفروع -->
                @if (!$hideBranchFilter)
                    <div class="space-y-2">
                        <label for="branch_ids" class="block text-sm font-medium text-gray-700">الفروع</label>
                        <select wire:model.defer="branch_ids" id="branch_ids" multiple
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white/50 backdrop-blur-sm"
                            @cannot('view-all-branches-data') disabled @endcannot>
                            @foreach (\App\Models\Domain\Entities\Branch::all() as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @cannot('view-all-branches-data')
                            <p class="text-xs text-gray-500 mt-1">يمكنك فقط عرض بيانات الفرع المخصص لك.</p>
                        @endcannot
                    </div>
                @else
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">الفرع</label>
                        <div class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-700">
                            {{ $user->branch->name ?? 'غير متوفر' }}</div>
                        <p class="text-xs text-gray-500 mt-1">يمكنك فقط عرض بيانات الفرع المخصص لك.</p>
                    </div>
                @endif

                <!-- فلتر الموظفين (يظهر أو يختفي حسب الصلاحية) -->
                @if (!$hideEmployeeFilter)
                    <div class="space-y-2">
                        <label for="employee_ids" class="block text-sm font-medium text-gray-700">الموظفون</label>
                        <select wire:model.defer="employee_ids" id="employee_ids" multiple
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-white"
                            @cannot('view-other-employees-data') disabled @endcannot>
                            <option value="" @if (empty($employee_ids) || in_array('', (array) $employee_ids)) selected @endif>كل الموظفين
                            </option>
                            @foreach (\App\Domain\Entities\User::all() as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @cannot('view-other-employees-data')
                            <p class="text-xs text-gray-500 mt-1">يمكنك فقط عرض معاملاتك الخاصة.</p>
                        @endcannot
                    </div>
                @endif

                <!-- رقم المرجع -->
                <div class="space-y-2">
                    <label for="reference_number" class="block text-sm font-medium text-gray-700">رقم المرجع</label>
                    <input wire:model.defer="reference_number" id="reference_number" type="text"
                        placeholder="ادخل رقم المرجع..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200 bg-white" />
                </div>

                <!-- إجراءات الفلترة -->
                <div class="space-y-3 flex flex-col justify-end lg:col-span-2">
                    <div class="flex flex-col md:flex-row gap-3">
                        <button wire:click="filter" type="button"
                            class="flex-1 px-4 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                                </path>
                            </svg>
                            تطبيق الفلاتر
                        </button>
                        <button wire:click="resetFilters" type="button"
                            class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl transition-all duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582M20 20v-5h-.581M5 9A7 7 0 0119 15M19 15a7 7 0 01-14 0"></path>
                            </svg>
                            إعادة تعيين الفلاتر
                        </button>
                        @if ($user->hasRole('admin') || $user->hasRole('general_supervisor'))
                            <a href="{{ route('transactions.pending') }}"
                                class="px-4 py-3 bg-orange-100 hover:bg-orange-200 text-orange-700 font-medium rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                قيد الانتظار
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 overflow-x-auto">
            <div class="bg-white rounded-2xl shadow-lg p-0 md:p-0 transition-all duration-300"
                style="box-shadow: none;">
                <!-- Pagination Controls -->
                <div class="flex justify-between items-center px-4 py-2">
                    <div>
                        عرض
                        <span class="font-bold">{{ count($transactions) }}</span>
                        من أصل
                        <span class="font-bold">{{ $totalTransactions }}</span>
                        معاملة
                    </div>
                </div>
                <table class="min-w-full divide-y divide-blue-100 text-xs rtl text-right border border-blue-200" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <!-- Reference Number (Most Important for Identification) -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[100px] sortable-header border-b border-blue-200 border-l border-blue-200 rounded-tr-2xl"
                                wire:click="sortBy('reference_number')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>رقم المرجع</span>
                                    @if ($sortField === 'reference_number')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Date (Chronological Order) -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[90px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('created_at')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>التاريخ</span>
                                    @if ($sortField === 'created_at')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Transaction Type -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[80px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('transaction_type')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>النوع</span>
                                    @if ($sortField === 'transaction_type')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Customer Name -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[100px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('customer_name')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>اسم العميل</span>
                                    @if ($sortField === 'customer_name')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Amount -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[80px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('amount')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>المبلغ</span>
                                    @if ($sortField === 'amount')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Commission -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[70px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('commission')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>العمولة</span>
                                    @if ($sortField === 'commission')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Customer Mobile -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[100px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('customer_mobile_number')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>رقم العميل</span>
                                    @if ($sortField === 'customer_mobile_number')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Recipient Mobile -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[100px] border-b border-blue-200 border-l border-blue-200">
                                <span>رقم المستلم</span>
                            </th>
                            <!-- Agent -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[90px] sortable-header border-b border-blue-200 border-l border-blue-200"
                                wire:click="sortBy('agent_name')">
                                <div class="flex items-center gap-1 justify-end">
                                    <span>الوكيل</span>
                                    @if ($sortField === 'agent_name')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <!-- Actions -->
                            <th class="px-2 py-2 bg-blue-50 text-right text-xs font-bold text-blue-900 tracking-wider min-w-[120px] border-b border-blue-200 border-l border-blue-200 rounded-tl-2xl whitespace-nowrap">
                                إجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-blue-50 text-xs" style="font-size: 12px;">
                        @forelse ($transactions as $transaction)
                            <tr
                                class="hover:bg-blue-50 transition-all duration-150 @if (strtolower($transaction['status']) === 'pending') bg-yellow-100 @endif border-b border-blue-100 last:border-b-0">
                                <!-- Reference Number -->
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700 font-bold border-l border-blue-200">
                                    {{ $transaction['reference_number'] ?? '' }}</td>
                                <!-- Date -->
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700 border-l border-blue-200">
                                    {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d/m/y h:i A') }}</td>
                                <!-- Transaction Type -->
                                <td class="px-2 py-2 whitespace-nowrap text-blue-700 font-bold border-l border-blue-200">
                                    {{ $transaction['transaction_type'] }}</td>
                                <!-- Customer Name -->
                                <td class="px-2 py-2 whitespace-nowrap font-bold text-gray-900 border-l border-blue-200">
                                    {{ $transaction['customer_name'] }}</td>
                                <!-- Amount -->
                                <td class="px-2 py-2 whitespace-nowrap text-green-700 font-bold border-l border-blue-200">
                                    {{ format_int($transaction['amount']) }} ج.م</td>
                                <!-- Commission -->
                                <td class="px-2 py-2 whitespace-nowrap text-purple-700 font-bold border-l border-blue-200">
                                    {{ format_int($transaction['commission']) }} ج.م</td>
                                <!-- Customer Mobile -->
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700 border-l border-blue-200">
                                    {{ $transaction['customer_mobile_number'] }}</td>
                                <!-- Recipient Mobile -->
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700 border-l border-blue-200">
                                    {{ $transaction['receiver_mobile_number'] ?? '' }}</td>
                                <!-- Agent Name -->
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700 border-l border-blue-200">{{ $transaction['agent_name'] }}</td>
                                <td
                                    class="px-1 py-2 whitespace-nowrap text-right text-xs font-bold flex flex-row flex-nowrap gap-1 justify-end items-center border-l border-blue-200">
                                    <a href="{{ route('transactions.details', $transaction['id']) }}"
                                        class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-200 transition-colors duration-150 mb-1">
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        عرض
                                    </a>
                                    @can('edit-all-transactions')
                                        @if (isset($transaction['source_table']) && $transaction['source_table'] === 'cash_transactions')
                                            <a href="{{ route('cash-transactions.edit', $transaction['id']) }}"
                                                class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-200 transition-colors duration-150 mb-1">
                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5h2m-1 0v14m-7-7h14" />
                                                </svg>
                                                تعديل
                                            </a>
                                        @elseif(empty($transaction['source_table']) || $transaction['source_table'] === 'transactions')
                                            <a href="{{ route('transactions.edit', $transaction['id']) }}"
                                                class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-200 transition-colors duration-150 mb-1">
                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5h2m-1 0v14m-7-7h14" />
                                                </svg>
                                                تعديل
                                            </a>
                                        @endif
                                    @endcan
                                    <a href="{{ route('transactions.receipt', $transaction['id']) }}"
                                        class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 text-xs font-bold rounded-lg hover:bg-green-200 transition-colors duration-150 mb-1"
                                        title="طباعة الإيصال">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2m-6 0v4m0 0h4m-4 0H8" />
                                        </svg>
                                        طباعة
                                    </a>
                                    @can('delete-transactions')
                                        <button x-data
                                            @click="if(confirm('هل أنت متأكد أنك تريد حذف هذه المعاملة؟')) { $wire.deleteTransaction('{{ $transaction['id'] }}') }"
                                            class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-xs font-bold rounded-lg hover:bg-red-200 transition-colors duration-150 mb-1">حذف</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-8 py-12 text-blue-400 text-center text-lg font-bold">لا
                                    توجد معاملات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Load More Button at Bottom -->
                @if (count($transactions) < $totalTransactions)
                    <div class="flex justify-center py-4">
                        <button wire:click="loadMore"
                            class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-base font-bold shadow">تحميل
                            المزيد</button>
                    </div>
                @elseif($totalTransactions > 0)
                    <div class="flex justify-center py-4">
                        <span class="text-xs text-gray-400">تم عرض جميع النتائج</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
