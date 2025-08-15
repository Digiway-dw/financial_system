<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 md:p-6" dir="rtl"
    style="direction: rtl;">
    <!-- Header Section Container for Consistent Padding -->
    <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="bg-transparent p-0 rounded-none shadow-none border-l-4 border-indigo-500 w-full md:w-auto">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2 text-indigo-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        إدارة المستخدمين
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">إدارة مستخدمي النظام وصلاحياتهم</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>إجمالي المستخدمين: <span class="font-bold">{{ $users->total() }}</span></span>
                    </div>
                    @can('create', App\Domain\Entities\User::class)
                        <a href="{{ route('users.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white shadow-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة مستخدم جديد
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container for Consistent Padding -->
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


        <!-- لوحة الفلاتر -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 mb-8">
            <div class="p-6">
                <div class="flex items-center mb-6 text-indigo-700 border-b border-gray-200 pb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="font-semibold">خيارات البحث والتصفية</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- البحث -->
                    <div class="w-full">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث بالاسم</label>
                        <div class="relative rounded-md">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="name" type="text" id="search"
                                class="pr-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all duration-200 text-right"
                                placeholder="ابحث بالاسم...">
                        </div>
                    </div>

                    <!-- تصفية الدور -->
                    <div class="w-full">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">تصفية حسب
                            الدور</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <select wire:model.live="role" id="role"
                                class="pr-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all duration-200 text-right">
                                <option value="">كل الأدوار</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ __($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- تصفية الفرع -->
                    <div class="w-full">
                        <label for="branchId" class="block text-sm font-medium text-gray-700 mb-1">تصفية حسب
                            الفرع</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <select wire:model.live="branchId" id="branchId"
                                class="pr-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all duration-200 text-right">
                                <option value="">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- تبديل عرض المحذوفين وإعادة تعيين الفلاتر -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-gray-100 gap-4">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <div class="relative inline-block w-10 ml-2 align-middle select-none">
                            <input wire:model.live="showTrashed" type="checkbox" id="showTrashed"
                                class="checked:bg-indigo-500 outline-none focus:outline-none left-4 checked:left-0 duration-200 ease-in absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                            <label for="showTrashed"
                                class="block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                        <label for="showTrashed" class="text-sm font-medium text-gray-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            عرض المستخدمين المحذوفين
                        </label>
                    </div>

                    <button wire:click="resetFilters" type="button"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        إعادة تعيين الفلاتر
                    </button>
                </div>
            </div>
        </div>

        <!-- جدول المستخدمين -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0 z-10 text-center">
                        <tr>
                            <th scope="col"
                                class="px-8 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider sortable-header text-center"
                                wire:click="sortBy('name')">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="flex flex-row-reverse items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-indigo-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        بيانات المستخدم
                                        @if ($sortField === 'name')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th scope="col"
                                class="px-8 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider sortable-header text-center"
                                wire:click="sortBy('branch_id')">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="flex flex-row-reverse items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-indigo-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        الفرع
                                        @if ($sortField === 'branch_id')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th scope="col"
                                class="px-8 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider sortable-header text-center"
                                wire:click="sortBy('role')">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="flex flex-row-reverse items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-indigo-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        الدور
                                        @if ($sortField === 'role')
                                            <span
                                                class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th scope="col"
                                class="px-8 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                تجاهل أوقات العمل
                            </th>
                            <th scope="col"
                                class="px-8 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider sortable-header text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="flex flex-row-reverse items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-indigo-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                        </svg>
                                        الإجراءات
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr
                                class="hover:bg-blue-50/40 transition-all duration-200 {{ $user->trashed() ? 'bg-red-50/30' : '' }}">
                                <td class="px-8 py-6 whitespace-nowrap align-middle">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 relative">
                                            @php
                                                $name = $user->name ?? 'User';
                                                $initials = implode(
                                                    '',
                                                    array_map(
                                                        fn($part) => strtoupper(substr($part, 0, 1)),
                                                        explode(' ', $name),
                                                    ),
                                                );
                                                $bgColors = [
                                                    'bg-gradient-to-br from-blue-400 to-blue-600',
                                                    'bg-gradient-to-br from-green-400 to-green-600',
                                                    'bg-gradient-to-br from-yellow-400 to-yellow-600',
                                                    'bg-gradient-to-br from-red-400 to-red-600',
                                                    'bg-gradient-to-br from-purple-400 to-purple-600',
                                                    'bg-gradient-to-br from-pink-400 to-pink-600',
                                                    'bg-gradient-to-br from-indigo-400 to-indigo-600',
                                                ];
                                                $randomColor = $bgColors[crc32($name) % count($bgColors)];
                                            @endphp
                                            <div
                                                class="{{ $randomColor }} rounded-full flex items-center justify-center h-12 w-12 text-white text-lg font-bold shadow-lg">
                                                {{ substr($initials, 0, 2) }}
                                            </div>
                                            @if ($user->trashed())
                                                <div
                                                    class="absolute -bottom-1 -right-1 h-5 w-5 bg-red-500 rounded-full flex items-center justify-center shadow-md border-2 border-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                {{ $user->name }}
                                                @if ($user->email_verified_at)
                                                    <span class="ml-1 text-green-500" title="Verified Email">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $user->email }}
                                            </div>
                                            @if ($user->created_at)
                                                <div class="text-xs text-gray-400 mt-1">
                                                    تاريخ الانضمام: {{ $user->created_at->format('Y/m/d') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap align-middle">
                                    @if (isset($user->branch) && $user->branch)
                                        <div class="text-sm text-gray-900">{{ $user->branch->name }}</div>
                                    @else
                                        <div class="text-sm text-gray-500">غير متوفر</div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap align-middle">
                                    @if ($editingUserId === $user->id)
                                        <div class="relative">
                                            <select wire:model="selectedRole"
                                                class="block w-full pl-10 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg shadow-sm transition-all duration-200">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $roleColors = [
                                                'admin' => 'bg-purple-500 border-purple-300',
                                                'general_supervisor' => 'bg-blue-500 border-blue-300',
                                                'branch_manager' => 'bg-emerald-500 border-emerald-300',
                                                'agent' => 'bg-green-500 border-green-300',
                                                'trainee' => 'bg-gray-500 border-gray-300',
                                                'auditor' => 'bg-cyan-500 border-cyan-300',
                                            ];

                                            $roleIcons = [
                                                'admin' =>
                                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
                                                'general_supervisor' =>
                                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                                                'branch_manager' =>
                                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                                                'agent' =>
                                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
                                            ];
                                            $roleName = $user->getRoleNames()->first() ?? 'No Role';
                                            $roleColor =
                                                $roleColors[strtolower($roleName)] ??
                                                'bg-gradient-to-r from-slate-500 to-slate-700 border-slate-300';
                                            $roleIcon =
                                                $roleIcons[strtolower($roleName)] ??
                                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />';
                                        @endphp
                                        <div class="flex items-center">
                                            <span
                                                class="px-3 py-1.5 inline-flex items-center text-xs leading-5 font-medium rounded-full {{ $roleColor }} text-white shadow-sm border transition-all duration-200 transform hover:scale-105">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    {!! $roleIcon !!}
                                                </svg>
                                                {{ ucfirst($roleName) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-center align-middle">
                                    @if (app()->make('livewire')->current()->canToggleIgnoreWorkHours($user))
                                        <button wire:click="toggleIgnoreWorkHours({{ $user->id }})"
                                            wire:loading.attr="disabled" class="focus:outline-none">
                                            @if ($user->ignore_work_hours)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">نعم</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">لا</span>
                                            @endif
                                            <span wire:loading
                                                wire:target="toggleIgnoreWorkHours({{ $user->id }})"
                                                class="ml-2 inline-block align-middle">
                                                <svg class="animate-spin h-4 w-4 text-indigo-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v8z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    @else
                                        @if ($user->ignore_work_hours)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">نعم</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">لا</span>
                                        @endif
                                    @endif
                                </td>
                                <!-- Action Buttons -->
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-medium align-middle">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if ($user->trashed())
                                            @can('restore', $user)
                                                <button wire:click="confirmRestore({{ $user->id }})" type="button"
                                                    class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-green-400 via-green-500 to-green-600 border border-green-300 rounded-lg font-semibold text-xs text-white tracking-wide shadow-md hover:scale-105 hover:from-green-500 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    استعادة
                                                </button>
                                            @endcan
                                        @else
                                            @if ($editingUserId === $user->id)
                                                <div class="flex gap-1.5">
                                                    <button wire:click="saveRole" type="button"
                                                        class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-green-500 to-green-600 border border-green-300 rounded-lg font-medium text-xs text-white tracking-wide hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="w-4 h-4 mr-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M4.5 12.75l6 6 9-13.5" />
                                                        </svg>
                                                        حفظ
                                                    </button>
                                                    <button wire:click="cancelEdit" type="button"
                                                        class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-gray-400 to-gray-500 border border-gray-300 rounded-lg font-medium text-xs text-white tracking-wide hover:from-gray-500 hover:to-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="w-4 h-4 mr-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        إلغاء
                                                    </button>
                                                </div>
                                            @else
                                                <div class="flex flex-wrap gap-1.5">
                                                    @if ($this->canEditUserRole($user))
                                                        <button wire:click="editRole({{ $user->id }})"
                                                            type="button"
                                                            class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-indigo-400 via-indigo-500 to-indigo-600 border border-indigo-300 rounded-lg font-semibold text-xs text-white tracking-wide shadow-md hover:scale-105 hover:from-indigo-500 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-4 h-4 mr-1">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                            تعديل الدور
                                                        </button>
                                                    @endif

                                                    @php $currentUser = auth()->user(); $roleName = $user->getRoleNames()->first(); @endphp
                                                    @if (
                                                        // Show edit button for admin@financial.system to edit their own account
                                                        ($currentUser && $currentUser->hasRole('admin') && ($user->email !== 'admin@financial.system' || $currentUser->id === $user->id))
                                                        // Supervisor can edit their own profile
                                                        || ($currentUser && $currentUser->hasRole('general_supervisor') && $currentUser->id === $user->id)
                                                    )
                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                            class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 border border-yellow-300 rounded-lg font-semibold text-xs text-gray-900 tracking-wide shadow-md hover:scale-105 hover:from-yellow-500 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-4 h-4 mr-1">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                            تعديل
                                                        </a>
                                                    @endif

                                                    @if ($this->canViewUser($user))
                                                        <a href="{{ route('users.view', $user->id) }}"
                                                            class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-blue-400 via-blue-500 to-blue-600 border border-blue-300 rounded-lg font-semibold text-xs text-white tracking-wide shadow-md hover:scale-105 hover:from-blue-500 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-4 h-4 mr-1">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            عرض
                                                        </a>
                                                    @elseif (auth()->user() && auth()->user()->id === $user->id)
                                                        <a href="{{ route('users.view', $user->id) }}"
                                                            class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-blue-400 via-blue-500 to-blue-600 border border-blue-300 rounded-lg font-semibold text-xs text-white tracking-wide shadow-md hover:scale-105 hover:from-blue-500 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-4 h-4 mr-1">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            عرض
                                                        </a>
                                                    @endif

                                                    @can('delete', $user)
                                                        <button wire:click="confirmUserDeletion({{ $user->id }})"
                                                            type="button"
                                                            class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gradient-to-r from-red-400 via-red-500 to-red-600 border border-red-300 rounded-lg font-semibold text-xs text-white tracking-wide shadow-md hover:scale-105 hover:from-red-500 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="w-4 h-4 mr-1">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                            حذف
                                                        </button>
                                                    @endcan
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-100 p-5 rounded-full mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-16 h-16 text-gray-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-700 mb-1">No users found</h3>
                                        <p class="text-gray-500 mb-4 max-w-sm">We couldn't find any users matching your
                                            current filters. Try adjusting your search criteria.</p>
                                        <button wire:click="resetFilters" type="button"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg font-medium text-sm hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-4 h-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            Reset All Filters
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-8 py-6 border-t border-gray-200 rounded-b-2xl">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-500 mb-4 sm:mb-0">
                        Showing <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span> to <span
                            class="font-medium">{{ $users->lastItem() ?? 0 }}</span> of <span
                            class="font-medium">{{ $users->total() }}</span> users
                    </div>
                    <div class="w-full sm:w-auto">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete User Confirmation Modal -->
        <div x-data="{ show: @entangle('confirmingUserDeletion').live }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 backdrop-blur-sm"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl border border-red-100 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-14 sm:w-14">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">
                                حذف المستخدم
                            </h3>

                            <div class="mt-4 text-sm text-gray-600">
                                <p class="mb-4">هل أنت متأكد أنك تريد حذف هذا المستخدم؟ لا يمكن التراجع عن هذا
                                    الإجراء.
                                </p>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex items-center">
                                        @php
                                            $name = $userBeingDeleted?->name ?? 'User';
                                            $initials = implode(
                                                '',
                                                array_map(
                                                    fn($part) => strtoupper(substr($part, 0, 1)),
                                                    explode(' ', $name),
                                                ),
                                            );
                                        @endphp
                                        <div
                                            class="bg-red-100 rounded-full flex items-center justify-center h-10 w-10 text-red-600 font-bold shadow-sm">
                                            {{ substr($initials, 0, 2) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium">{{ $userBeingDeleted?->name }}</div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $userBeingDeleted?->email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                        <button wire:click="deleteUser" type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 border border-red-300 rounded-lg font-medium text-sm text-white tracking-wide hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                            wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            حذف المستخدم
                        </button>
                        <button wire:click="$set('confirmingUserDeletion', false)" type="button"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                            wire:loading.attr="disabled">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore User Confirmation Modal -->
        <div x-data="{ show: @entangle('confirmingUserRestore').live }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 backdrop-blur-sm"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl border border-green-100 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-14 sm:w-14">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-bold leading-6 text-gray-900">
                                استعادة المستخدم
                            </h3>

                            <div class="mt-4 text-sm text-gray-600">
                                <p class="mb-4">هل أنت متأكد أنك تريد استعادة هذا المستخدم؟ سيتمكن من الدخول للنظام
                                    مرة أخرى.</p>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex items-center">
                                        @php
                                            $name = $userBeingRestored?->name ?? 'User';
                                            $initials = implode(
                                                '',
                                                array_map(
                                                    fn($part) => strtoupper(substr($part, 0, 1)),
                                                    explode(' ', $name),
                                                ),
                                            );
                                        @endphp
                                        <div
                                            class="bg-green-100 rounded-full flex items-center justify-center h-10 w-10 text-green-600 font-bold shadow-sm">
                                            {{ substr($initials, 0, 2) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium">{{ $userBeingRestored?->name }}</div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $userBeingRestored?->email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                        <button wire:click="restoreUser" type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 border border-green-300 rounded-lg font-medium text-sm text-white tracking-wide hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 shadow-sm"
                            wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            استعادة المستخدم
                        </button>
                        <button wire:click="$set('confirmingUserRestore', false)" type="button"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200"
                            wire:loading.attr="disabled">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
