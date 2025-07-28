<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    @php
        $forbiddenRoles = ['agent', 'trainee'];
    @endphp
    @if (auth()->user()->hasAnyRole($forbiddenRoles))
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white p-8 rounded shadow text-center">
                <h2 class="text-2xl font-bold text-red-600 mb-4">403 Forbidden</h2>
                <p class="text-gray-700">You do not have permission to access the Customers screen.</p>
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <!-- Header Section -->
            <div class="bg-white shadow-sm border-b border-gray-200 mb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">إدارة العملاء</h1>
                            <p class="text-slate-600 mt-2">إدارة قاعدة العملاء بأدوات وأفكار موثوقة</p>
                        </div>
                        @php
                            $canAddCustomer =
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('branch_manager') ||
                                auth()->user()->hasRole('general_supervisor');
                        @endphp
                        @if ($canAddCustomer)
                            <div class="mt-4 lg:mt-0">
                                <a href="{{ route('customers.create') }}"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 border border-blue-500/20">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    اضافة عميل جديد
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="mb-8">
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-slate-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        تصفية العملاء
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 items-end">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">الاسم</label>
                            <input type="text" id="name" wire:model.debounce.400ms="name"
                                class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm"
                                placeholder="Search by name...">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">رقم
                                الهاتف</label>
                            <input type="text" id="phone" wire:model.defer="phone"
                                class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm"
                                placeholder="Search by phone...">
                        </div>
                        <div>
                            <label for="code" class="block text-sm font-medium text-slate-700 mb-2">كود
                                العميل</label>
                            <input type="text" id="code" wire:model.defer="code"
                                class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm"
                                placeholder="Search by code...">
                        </div>
                        <div>
                            <label for="region" class="block text-sm font-medium text-slate-700 mb-2">المنطقة</label>
                            <input type="text" id="region" wire:model.defer="region"
                                class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm"
                                placeholder="Search by region...">
                        </div>
                        <div>
                            <label for="date_added_start" class="block text-sm font-medium text-slate-700 mb-2">تاريخ
                                الإضافة
                                (بداية)</label>
                            <input type="date" id="date_added_start" wire:model.defer="date_added_start"
                                class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm">
                        </div>
                        <div>
                            <label for="date_added_end" class="block text-sm font-medium text-slate-700 mb-2">تاريخ
                                الإضافة
                                (نهاية)</label>
                            <input type="date" id="date_added_end" wire:model.defer="date_added_end"
                                class="w-full px-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm">
                        </div>
                        <div class="md:col-span-2 lg:col-span-4 xl:col-span-6">
                            <button wire:click="filter"
                                class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-slate-600 to-slate-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl hover:from-slate-700 hover:to-slate-800 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                تطبيق التصفية
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                <div class="overflow-x-auto">
                    <!-- Lazy Load Info and Load More Button -->
                    <div class="flex justify-between items-center px-4 py-2">
                        <div>
                            عرض
                            <span class="font-bold">{{ count($customers) }}</span>
                            من أصل
                            <span class="font-bold">{{ $totalCustomers }}</span>
                            عميل
                        </div>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 sortable-header"
                                    wire:click="sortBy('name')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>الاسم</span>
                                        @if ($sortField === 'name')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 sortable-header"
                                    wire:click="sortBy('mobile_number')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span>رقم الهاتف</span>
                                        @if ($sortField === 'mobile_number')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 sortable-header"
                                    wire:click="sortBy('customer_code')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        <span>كود العميل</span>
                                        @if ($sortField === 'customer_code')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 sortable-header"
                                    wire:click="sortBy('balance')">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span>حالة المحفظة</span>
                                        @if ($sortField === 'balance')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-slate-700">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($customers as $customer)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ strtoupper(substr($customer['name'], 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $customer['name'] }}
                                                </div>
                                                <div class="text-xs text-slate-500">ID: {{ $customer['id'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-700 font-mono">{{ $customer['mobile_number'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $customer['customer_code'] ? 'bg-slate-100 text-slate-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $customer['customer_code'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (!empty($customer['is_client']) && $customer['is_client'])
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ format_int($customer['balance'] ?? 0) }} EGP
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                لا يوجد محفظة
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('customers.view', $customer['id']) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors duration-150">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                عرض
                                            </a>
                                            @php
                                                $cannotEditRoles = ['agent', 'trainee', 'auditor'];
                                            @endphp
                                            @if (!auth()->user()->hasAnyRole($cannotEditRoles))
                                                <a href="{{ route('customers.edit', $customer['id']) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-lg hover:bg-indigo-200 transition-colors duration-150">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M11 5h2m-1 0v14m-7-7h14" />
                                                    </svg>
                                                    تعديل
                                                </a>
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors duration-150"
                                                    onclick="if(confirm('Are you sure you want to delete this customer?')) { @this.deleteCustomer('{{ $customer['id'] }}') }">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    حذف
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <h3 class="text-sm font-medium text-slate-900 mb-1">لا يوجد عملاء</h3>
                                            <p class="text-sm text-slate-500">حاول تعديل معايير البحث أو إضافة عميل
                                                جديد.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if (count($customers) < $totalCustomers)
                        <div class="flex justify-center py-4">
                            <button wire:click="loadMore"
                                class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-base font-bold shadow">تحميل
                                المزيد</button>
                        </div>
                    @elseif($totalCustomers > 0)
                        <div class="flex justify-center py-4">
                            <span class="text-xs text-gray-400">تم عرض جميع النتائج</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Confirmation Modal --}}
            @if ($customerToDelete)
                <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40">
                    <div class="bg-white rounded-xl shadow-lg p-8 max-w-sm w-full">
                        <h2 class="text-lg font-bold mb-4">تأكيد الحذف</h2>
                        <p class="mb-6">هل أنت متأكد من حذف هذا العميل؟ لا يمكن التراجع عن هذا الإجراء.
                        </p>
                        <div class="flex justify-end gap-4">
                            <button type="button" wire:click="$set('customerToDelete', null)"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">إلغاء</button>
                            <button type="button" wire:click="deleteCustomer('{{ $customerToDelete }}')"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">حذف</button>
                        </div>
                    </div>
                </div>
            @endif
    @endif
</div>
