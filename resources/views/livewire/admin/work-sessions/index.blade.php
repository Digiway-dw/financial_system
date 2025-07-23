<div x-data="{ showRefreshMsg: false }">
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
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">جلسات العمل</h1>
                        <div class="flex space-x-3">
                            <button wire:click="updateSessionStatuses"
                                class="flex items-center px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>تحديث الحالة</span>
                            </button>

                            <button wire:click="$refresh"
                                class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                @click="showRefreshMsg = true; setTimeout(() => showRefreshMsg = false, 3000)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>تحديث</span>
                            </button>

                            <!-- Session Lifetime Settings Button -->
                            <button wire:click="openSessionLifetimeModal"
                                class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>إعدادات الجلسة</span>
                            </button>
                        </div>
                    </div>

                    @if (session()->has('message'))
                        <div
                            class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ session('message') }}
                        </div>
                    @endif

                    <div x-show="showRefreshMsg" x-transition
                        class="mb-4 p-4 bg-green-50 text-green-700 rounded-md border border-green-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        تم تحديث البيانات بنجاح!
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-5 shadow-sm border border-blue-100 flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-blue-800 text-sm font-semibold">إجمالي الجلسات</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ $totalSessions }}</p>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-5 shadow-sm border border-green-100 flex items-center">
                            <div class="bg-green-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-green-800 text-sm font-semibold">إجمالي الساعات</h3>
                                <p class="text-3xl font-bold text-green-600">{{ number_format($totalHours, 1) }}</p>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-5 shadow-sm border border-purple-100 flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-purple-800 text-sm font-semibold">متوسط الجلسة</h3>
                                <p class="text-3xl font-bold text-purple-600">
                                    {{ number_format($averageSessionLength, 0) }} <span class="text-sm">min</span></p>
                            </div>
                        </div>

                        <div class="bg-amber-50 rounded-lg p-5 shadow-sm border border-amber-100 flex items-center">
                            <div class="bg-amber-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-amber-800 text-sm font-semibold">الجلسات النشطة</h3>
                                <p class="text-3xl font-bold text-amber-600">
                                    {{ $sessions->where('logout_at', null)->count() }}</p>
                            </div>
                        </div>

                        <!-- Session Lifetime Card -->
                        <div class="bg-purple-50 rounded-lg p-5 shadow-sm border border-purple-100 flex items-center cursor-pointer" wire:click="openSessionLifetimeModal">
                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-purple-800 text-sm font-semibold">وقت انتهاء الجلسة</h3>
                                <p class="text-3xl font-bold text-purple-600">
                                    {{ $sessionLifetime }} <span class="text-sm">min</span>
                                </p>
                                <p class="text-xs text-purple-600 mt-1">(Click to change)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 p-5 rounded-lg shadow-sm mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-700">المرشحات</h2>
                            <div class="flex space-x-2">
                                <button wire:click="$set('dateFrom', '{{ date('Y-m-d') }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    اليوم
                                </button>
                                <button wire:click="$set('dateFrom', '{{ date('Y-m-d', strtotime('-1 day')) }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    اليوم السابق
                                </button>
                                <button
                                    wire:click="$set('dateFrom', '{{ date('Y-m-d', strtotime('monday this week')) }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    هذا الأسبوع
                                </button>
                                <button wire:click="$set('dateFrom', '{{ date('Y-m-01') }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    هذا الشهر
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Branch Filter -->
                            <div>
                                <label for="branch"
                                    class="block text-sm font-medium text-gray-700 mb-1">الفرع</label>
                                <div class="relative">
                                    <select id="branch" wire:model.live="selectedBranch"
                                        class="appearance-none mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">جميع الفروع</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- User Filter -->
                            <div>
                                <label for="user"
                                    class="block text-sm font-medium text-gray-700 mb-1">المستخدم</label>
                                <div class="relative">
                                    <select id="user" wire:model.live="selectedUser"
                                        class="appearance-none mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">جميع المستخدمين</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Date From Filter -->
                            <div>
                                <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-1">Date
                                    من</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="date" id="dateFrom" wire:model.live="dateFrom"
                                        class="mt-1 pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <!-- Date To Filter -->
                            <div>
                                <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-1">Date
                                    إلى</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="date" id="dateTo" wire:model.live="dateTo"
                                        class="mt-1 pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <!-- Export Buttons -->
                        <div class="mt-6 flex justify-between">
                            <button wire:click="resetFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                إعادة تعيين المرشحات
                            </button>

                            <div class="flex space-x-3">
                                <button wire:click="exportCsv"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export to CSV
                                </button>

                                <button wire:click="exportExcel"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Export to Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sessions Table -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('status')">
                                            Status
                                            @if ($sortField === 'status')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('user_id')">
                                            User
                                            @if ($sortField === 'user_id')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('branch_id')">
                                            Branch
                                            @if ($sortField === 'branch_id')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('login_at')">
                                            Login At
                                            @if ($sortField === 'login_at')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('logout_at')">
                                            Logout At
                                            @if ($sortField === 'logout_at')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('duration_minutes')">
                                            Duration
                                            @if ($sortField === 'duration_minutes')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('ip_address')">
                                            IP Address
                                            @if ($sortField === 'ip_address')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable-header"
                                            wire:click="sortBy('user_agent')">
                                            Browser
                                            @if ($sortField === 'user_agent')
                                                <span
                                                    class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($sessions as $session)
                                        <tr class="{{ $session->logout_at === null ? 'bg-green-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($session->logout_at === null)
                                                    <span class="flex items-center">
                                                        <span class="relative flex h-3 w-3 mr-2">
                                                            <span
                                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                            <span
                                                                class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                                        </span>
                                                        <span class="text-xs font-medium text-green-800">مفعل</span>
                                                    </span>
                                                @else
                                                    <span class="flex items-center">
                                                        <span class="h-3 w-3 mr-2 rounded-full bg-gray-300"></span>
                                                        <span class="text-xs font-medium text-gray-600">مغلق</span>
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <span
                                                            class="text-gray-500 font-medium">{{ substr($session->user->name ?? 'U', 0, 1) }}</span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $session->user->name ?? 'Unknown User' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $session->user->email ?? 'No Email' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $session->user->branch->name ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $session->login_at->format('d/m/y') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $session->login_at->format('h:i A') }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $session->login_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($session->logout_at)
                                                    <div class="text-sm text-gray-900">
                                                        {{ $session->logout_at->format('d/m/y') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $session->logout_at->format('h:i A') }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $session->logout_at->diffForHumans() }}</div>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        مفعل حاليا
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($session->duration_minutes !== null)
                                                    <div class="text-sm text-gray-900">
                                                        @if ($session->duration_minutes >= 60)
                                                            <span
                                                                class="font-medium">{{ floor($session->duration_minutes / 60) }}</span>h
                                                            <span
                                                                class="font-medium">{{ $session->duration_minutes % 60 }}</span>m
                                                        @else
                                                            <span
                                                                class="font-medium">{{ $session->duration_minutes }}</span>
                                                            min
                                                        @endif
                                                    </div>
                                                @elseif ($session->logout_at === null && $session->login_at)
                                                    <div class="text-sm text-green-600">
                                                        <span
                                                            class="font-medium">{{ $session->login_at->diffInMinutes(now()) }}</span>
                                                        دقيقة مضت
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $session->ip_address }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @php
                                                        $userAgent = $session->user_agent ?? '';
                                                        $browser = 'Unknown';

                                                        if (strpos($userAgent, 'Chrome') !== false) {
                                                            $browser = 'Chrome';
                                                        } elseif (strpos($userAgent, 'Firefox') !== false) {
                                                            $browser = 'Firefox';
                                                        } elseif (strpos($userAgent, 'Safari') !== false) {
                                                            $browser = 'Safari';
                                                        } elseif (strpos($userAgent, 'Edge') !== false) {
                                                            $browser = 'Edge';
                                                        } elseif (
                                                            strpos($userAgent, 'MSIE') !== false ||
                                                            strpos($userAgent, 'Trident') !== false
                                                        ) {
                                                            $browser = 'Internet Explorer';
                                                        }
                                                    @endphp
                                                    {{ $browser }}
                                                </div>
                                                <div class="text-xs text-gray-500 truncate max-w-xs"
                                                    title="{{ $session->user_agent }}">
                                                    {{ Str::limit($session->user_agent, 30) }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8"
                                                class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="h-10 w-10 text-gray-400 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-gray-500 font-medium">لم يتم العثور على جلسات عمل
                                                    </span>
                                                    <p class="text-gray-400 text-xs mt-1">حاول ضبط المرشحات
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $sessions->links() }}
                    </div>

                    <!-- Timeline Visualization -->
                    @if ($selectedUser && $sessions->count() > 0)
                        <div class="mt-8 bg-white rounded-lg shadow-md p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Session Timeline for
                                {{ $sessions->first()->user->name ?? 'User' }}</h3>

                            <div class="overflow-x-auto">
                                <div class="relative" style="min-height: 100px; min-width: 800px;">
                                    <!-- Today marker line -->
                                    <div class="absolute top-0 bottom-0 border-l-2 border-red-400"
                                        style="left: calc(100% - 100px); z-index: 10;">
                                        <div class="bg-red-400 text-white text-xs rounded px-2 py-1 absolute -top-6">
                                            اليوم
                                        </div>
                                    </div>

                                    <!-- Timeline bar -->
                                    <div class="w-full h-16 bg-gray-100 rounded-lg relative mt-8">
                                        @php
                                            // Get date range
                                            $minDate = $sessions->min('login_at');
                                            $maxDate = now();
                                            $totalDays = $minDate ? $minDate->diffInDays($maxDate) + 1 : 1;

                                            // Ensure we have at least 1 day
                                            $totalDays = max(1, $totalDays);
                                        @endphp

                                        @foreach ($sessions as $index => $session)
                                            @php
                                                // Calculate position
                                                $daysFromStart = $session->login_at->diffInDays($minDate);
                                                $positionPercent = ($daysFromStart / $totalDays) * 100;

                                                // Calculate width (duration)
                                                $durationDays = $session->logout_at
                                                    ? $session->login_at->diffInMinutes($session->logout_at) / (60 * 24)
                                                    : $session->login_at->diffInMinutes(now()) / (60 * 24);
                                                $widthPercent = max(0.5, ($durationDays / $totalDays) * 100);

                                                // Ensure minimum width
                                                $widthPercent = min($widthPercent, 100 - $positionPercent);
                                            @endphp

                                            <div class="absolute h-8 top-4 rounded-md {{ $session->logout_at ? 'bg-blue-500' : 'bg-green-500' }}"
                                                style="left: {{ $positionPercent }}%; width: {{ $widthPercent }}%; min-width: 10px;">
                                                <div class="absolute bottom-full mb-1 text-xs whitespace-nowrap">
                                                    {{ $session->login_at->format('d/m/y h:i A') }}
                                                </div>
                                                <div class="text-white text-xs px-1 truncate">
                                                    {{ $session->duration_minutes ?? '?' }}m
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Timeline dates -->
                                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                                        <div>{{ $minDate ? $minDate->format('d/m/y') : now()->format('d/m/y') }}
                                        </div>
                                        <div>{{ now()->format('d/m/y') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center mt-4">
                                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                <span class="text-sm text-gray-600 mr-4">جلسة منتهية</span>

                                <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                <span class="text-sm text-gray-600">جلسة نشطة</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Session Lifetime Modal -->
    <div x-data="{ show: @entangle('showSessionLifetimeModal') }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div x-show="show" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                إعدادات وقت انتهاء الجلسة
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    قم بتعيين الوقت (بالدقائق) الذي يمكن أن يكون المستخدم غير نشط قبل أن يتم تسجيله
                                    تلقائيا.
                                </p>

                                <div class="mt-4">
                                    <label for="sessionLifetime"
                                        class="block text-sm font-medium text-gray-700">وقت انتهاء الجلسة
                                        (دقائق)</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" wire:model="sessionLifetime" id="sessionLifetime"
                                            min="1" max="1440"
                                            class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-4 pr-12 sm:text-sm border-gray-300 rounded-md"
                                            placeholder="120">
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">min</span>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        موصى به: 120 دقيقة (2 ساعة). الحد الأقصى: 1440 دقيقة (24 ساعة).
                                    </div>
                                    @error('sessionLifetime')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-4 bg-yellow-50 p-3 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">ملاحظة أهمية</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>
                                                    هذا الإعداد يؤثر على جميع المستخدمين. المستخدمين الذين يكونون غير
                                                    نشطين لمدة أطول من الوقت المحدد سيتم تسجيلهم تلقائيا.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="saveSessionLifetime" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        حفظ الإعدادات
                    </button>
                    <button wire:click="closeSessionLifetimeModal" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
