<div x-data="{ showRefreshMsg: false }">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">User Work Sessions</h1>
                        <div class="flex space-x-3">
                            <button wire:click="updateSessionStatuses"
                                class="flex items-center px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Update Status</span>
                            </button>

                            <button wire:click="$refresh"
                                class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                @click="showRefreshMsg = true; setTimeout(() => showRefreshMsg = false, 3000)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Refresh</span>
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
                        Data refreshed successfully!
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
                                <h3 class="text-blue-800 text-sm font-semibold">Total Sessions</h3>
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
                                <h3 class="text-green-800 text-sm font-semibold">Total Hours</h3>
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
                                <h3 class="text-purple-800 text-sm font-semibold">Avg Session</h3>
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
                                <h3 class="text-amber-800 text-sm font-semibold">Active Sessions</h3>
                                <p class="text-3xl font-bold text-amber-600">
                                    {{ $sessions->where('logout_at', null)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 p-5 rounded-lg shadow-sm mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-700">Filters</h2>
                            <div class="flex space-x-2">
                                <button wire:click="$set('dateFrom', '{{ date('Y-m-d') }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    Today
                                </button>
                                <button wire:click="$set('dateFrom', '{{ date('Y-m-d', strtotime('-1 day')) }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    Yesterday
                                </button>
                                <button
                                    wire:click="$set('dateFrom', '{{ date('Y-m-d', strtotime('monday this week')) }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    This Week
                                </button>
                                <button wire:click="$set('dateFrom', '{{ date('Y-m-01') }}')"
                                    wire:click="$set('dateTo', '{{ date('Y-m-d') }}')"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    This Month
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Branch Filter -->
                            <div>
                                <label for="branch"
                                    class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                                <div class="relative">
                                    <select id="branch" wire:model.live="selectedBranch"
                                        class="appearance-none mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">All Branches</option>
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
                                    class="block text-sm font-medium text-gray-700 mb-1">User</label>
                                <div class="relative">
                                    <select id="user" wire:model.live="selectedUser"
                                        class="appearance-none mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">All Users</option>
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
                                    From</label>
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
                                    To</label>
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
                                Reset Filters
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
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Branch</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Login At</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Logout At</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Duration</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            IP Address</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Browser</th>
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
                                                        <span class="text-xs font-medium text-green-800">Active</span>
                                                    </span>
                                                @else
                                                    <span class="flex items-center">
                                                        <span class="h-3 w-3 mr-2 rounded-full bg-gray-300"></span>
                                                        <span class="text-xs font-medium text-gray-600">Closed</span>
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
                                                    {{ $session->login_at->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $session->login_at->format('h:i:s A') }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $session->login_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($session->logout_at)
                                                    <div class="text-sm text-gray-900">
                                                        {{ $session->logout_at->format('M d, Y') }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $session->logout_at->format('h:i:s A') }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $session->logout_at->diffForHumans() }}</div>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Currently Active
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
                                                        min so far
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
                                                    <span class="text-gray-500 font-medium">No work sessions
                                                        found</span>
                                                    <p class="text-gray-400 text-xs mt-1">Try adjusting your filters
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
                                            Today
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
                                                    {{ $session->login_at->format('M d, h:i A') }}
                                                </div>
                                                <div class="text-white text-xs px-1 truncate">
                                                    {{ $session->duration_minutes ?? '?' }}m
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Timeline dates -->
                                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                                        <div>{{ $minDate ? $minDate->format('M d, Y') : now()->format('M d, Y') }}
                                        </div>
                                        <div>{{ now()->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center mt-4">
                                <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                <span class="text-sm text-gray-600 mr-4">Completed Session</span>

                                <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                <span class="text-sm text-gray-600">Active Session</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
