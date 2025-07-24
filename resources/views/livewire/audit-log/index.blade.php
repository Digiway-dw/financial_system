<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
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
    <div class="bg-white/70 backdrop-blur-sm border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">تتبع النشاط</h1>
                    <p class="text-sm text-gray-600">مراقبة نشاط النظام وتتبع الامتثال</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Activity Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">النشاطات الكلية</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $activities->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">الأحداث اليومية</h3>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $activities->where('created_at', '>=', today())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">المستخدمون الفريدون</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $activities->groupBy('causer_id')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">أنواع الأحداث</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $eventTypes->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filter Section -->
        <div
            class="mb-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-purple-200/70 p-0 overflow-hidden">
            <button type="button" onclick="toggleFilters()"
                class="w-full flex items-center justify-between px-8 py-5 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100 focus:outline-none">
                <div class="flex items-center">
                    <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-900">المرشحات المتقدمة</span>
                </div>
                <svg id="filtersChevron" class="w-6 h-6 text-purple-500 transition-transform duration-200"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="filtersPanel" class="px-8 py-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Search -->
                    <div class="space-y-2 relative">
                        <label for="search" class="block text-sm font-medium text-gray-700">البحث</label>
                        <div class="relative">
                            <input wire:model.defer="search" id="search" type="text"
                                placeholder="البحث عن الوصفات، الأحداث..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-purple-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Removed 'فئة السجل' filter -->

                    <!-- Event Type Filter -->
                    <div class="space-y-2">
                        <label for="eventType" class="block text-sm font-medium text-gray-700">نوع الحدث</label>
                        <select wire:model.defer="eventType" id="eventType"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                            <option value="">جميع الأحداث</option>
                            @foreach ($eventTypes as $event)
                                <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Type Filter -->
                    <div class="space-y-2">
                        <label for="causerType" class="block text-sm font-medium text-gray-700">نوع المستخدم</label>
                        <select wire:model.defer="causerType" id="causerType"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                            <option value="">جميع أنواع المستخدمين</option>
                            @foreach ($causerTypes as $type)
                                <option value="{{ $type }}">{{ class_basename($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div class="space-y-2">
                        <label for="startDate" class="block text-sm font-medium text-gray-700">من تاريخ</label>
                        <input wire:model.defer="startDate" id="startDate" type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                    </div>

                    <!-- End Date -->
                    <div class="space-y-2">
                        <label for="endDate" class="block text-sm font-medium text-gray-700">إلى تاريخ</label>
                        <input wire:model.defer="endDate" id="endDate" type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                    </div>

                    <!-- Subject Type Filter -->
                    <div class="space-y-2">
                        <label for="subjectType" class="block text-sm font-medium text-gray-700">نوع الموضوع</label>
                        <select wire:model.defer="subjectType" id="subjectType"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                            <option value="">جميع الموضوعات</option>
                            @foreach ($subjectTypes as $type)
                                <option value="{{ $type }}">{{ class_basename($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="flex flex-row-reverse items-center gap-3 mt-8">
                    <button wire:click="$refresh"
                        class="flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-sm">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                        </svg>
                        تطبيق الفلاتر
                    </button>
                    <button wire:click="resetFilters"
                        class="flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200 transform hover:scale-105">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        إعادة تعيين
                    </button>
                </div>
            </div>
        </div>
        <script>
            function toggleFilters() {
                const panel = document.getElementById('filtersPanel');
                const chevron = document.getElementById('filtersChevron');
                if (panel.style.display === 'none') {
                    panel.style.display = '';
                    chevron.style.transform = 'rotate(0deg)';
                } else {
                    panel.style.display = 'none';
                    chevron.style.transform = 'rotate(180deg)';
                }
            }
            // Start collapsed on mobile
            document.addEventListener('DOMContentLoaded', function() {
                if (window.innerWidth < 768) {
                    document.getElementById('filtersPanel').style.display = 'none';
                    document.getElementById('filtersChevron').style.transform = 'rotate(180deg)';
                }
            });
        </script>

        <!-- Enhanced Audit Log Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">سجلات النشاط</h3>
                            <p class="text-sm text-gray-600">{{ $activities->total() }} إدخالات</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        الصفحة {{ $activities->currentPage() }} من {{ $activities->lastPage() }}
                    </div>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors duration-200 sortable-header"
                                wire:click="sortBy('created_at')">
                                <div class="flex items-center space-x-1">
                                    <span>الوقت</span>
                                    @if ($sortField === 'created_at')
                                        <div class="text-purple-600">
                                            @if ($sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </th>
                            <!-- Removed 'الفئة' column header -->
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors duration-200 sortable-header"
                                wire:click="sortBy('event')">
                                <div class="flex items-center space-x-1">
                                    <span>نوع الحدث</span>
                                    @if ($sortField === 'event')
                                        <div class="text-purple-600">
                                            @if ($sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                الوصف</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                المستخدم</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                الموضوع</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                الخصائص</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($activities as $activity)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <!-- Timestamp -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/y') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->format('h:i A') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Removed 'الفئة' column cell -->

                                <!-- Event Type -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $eventColors = [
                                            'created' => 'bg-green-100 text-green-800',
                                            'updated' => 'bg-blue-100 text-blue-800',
                                            'deleted' => 'bg-red-100 text-red-800',
                                            'login' => 'bg-indigo-100 text-indigo-800',
                                            'logout' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $eventClass = $eventColors[$activity->event] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $eventClass }}">
                                        {{ ucfirst($activity->event) }}
                                    </span>
                                </td>

                                <!-- Description -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate"
                                        title="{{ $activity->description }}">
                                        {{ $activity->description }}
                                    </div>
                                </td>

                                <!-- User/Causer -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($activity->causer)
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-xs font-semibold text-white">
                                                    {{ substr($activity->causer->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $activity->causer->name ?? 'Unknown' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    ID: {{ $activity->causer_id }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <span class="text-sm text-gray-500">النظام</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Subject -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($activity->subject)
                                        <div class="text-sm text-gray-900">
                                            <span
                                                class="font-medium">{{ class_basename($activity->subject_type) }}</span>
                                            <div class="text-xs text-gray-500">ID: {{ $activity->subject_id }}</div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">غير متوفر</span>
                                    @endif
                                </td>

                                <!-- Properties -->
                                <td class="px-6 py-4">
                                    @if (!empty($activity->properties))
                                        <button
                                            onclick="showProperties({{ json_encode($activity->properties->toArray()) }})"
                                            class="inline-flex items-center px-3 py-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            عرض التفاصيل
                                        </button>
                                    @else
                                        <span class="text-sm text-gray-500">لا يوجد بيانات</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12">
                                    <div class="text-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">لم يتم العثور على سجلات
                                            التدقيق</h3>
                                        <p class="text-sm text-gray-500">حاول تعديل معايير البحث أو التحقق مرة أخرى
                                            لاحقًا
                                            للنشاط الجديد.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            @if ($activities->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            عرض {{ $activities->firstItem() }} إلى {{ $activities->lastItem() }} من
                            {{ $activities->total() }} نتائج
                        </div>
                        <div>
                            {{ $activities->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Properties Modal -->
    <div id="propertiesModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePropertiesModal()">
            </div>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">خصائص النشاط</h3>
                        </div>
                        <button onclick="closePropertiesModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 max-h-96 overflow-y-auto">
                        <div id="propertiesContent" class="text-sm text-gray-800"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3">
                    <button onclick="closePropertiesModal()"
                        class="w-full inline-flex justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors duration-200">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showProperties(properties) {
            const container = document.getElementById('propertiesContent');
            container.innerHTML = '';
            // If properties has 'attributes', use it, else use as is
            const data = properties.attributes || properties;
            if (typeof data === 'object' && data !== null) {
                let html = '<table class="min-w-full divide-y divide-gray-200"><tbody>';
                for (const [key, value] of Object.entries(data)) {
                    html +=
                        `<tr><td class='py-2 pr-4 font-semibold text-gray-700'>${key}</td><td class='py-2'>${value}</td></tr>`;
                }
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.textContent = data;
            }
            document.getElementById('propertiesModal').classList.remove('hidden');
        }

        function closePropertiesModal() {
            document.getElementById('propertiesModal').classList.add('hidden');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePropertiesModal();
            }
        });
    </script>
</div>
