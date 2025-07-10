<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white mr-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold text-white">System Permissions</h1>
                            <p class="text-indigo-100">Manage and view all system permission settings</p>
                        </div>
                    </div>

                    <a href="{{ route('permissions.roles') }}" wire:navigate
                        class="inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-50 focus:bg-indigo-100 active:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 transition ease-in-out duration-150 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Manage Role Permissions
                    </a>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-5 flex items-center">
                    <div class="rounded-full bg-blue-50 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Permissions</p>
                        <p class="text-xl font-bold text-gray-800">{{ $permissionsByGroup->flatten()->count() }}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-5 flex items-center">
                    <div class="rounded-full bg-green-50 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Roles</p>
                        <p class="text-xl font-bold text-gray-800">{{ $roles->count() }}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-5 flex items-center">
                    <div class="rounded-full bg-purple-50 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Categories</p>
                        <p class="text-xl font-bold text-gray-800">{{ $permissionsByGroup->count() }}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-5 flex items-center">
                    <div class="rounded-full bg-amber-50 p-3 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Average Per Role</p>
                        <p class="text-xl font-bold text-gray-800">
                            @php
                                $avgPerms = 0;
                                if ($roles->count() > 0) {
                                    $totalPerms = 0;
                                    foreach ($roles as $role) {
                                        $totalPerms += $role->permissions->count();
                                    }
                                    $avgPerms = round($totalPerms / $roles->count());
                                }
                            @endphp
                            {{ $avgPerms }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Permission Categories -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Permissions by Category</h2>
                    <div class="inline-flex shadow-sm rounded-md">
                        <button
                            class="px-4 py-2 text-sm font-medium text-indigo-700 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                            </svg>
                            Sort A-Z
                        </button>
                        <button
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-r border-gray-200 rounded-r-lg hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($permissionsByGroup as $group => $permissions)
                        <div
                            class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                            @php
                                $icon = 'question-mark-circle';
                                $colorClass = 'text-gray-600';
                                $bgColorClass = 'bg-gray-50';

                                switch ($group) {
                                    case 'user_management':
                                        $icon = 'user-group';
                                        $colorClass = 'text-blue-600';
                                        $bgColorClass = 'bg-blue-50';
                                        break;
                                    case 'financial_operations':
                                        $icon = 'currency-dollar';
                                        $colorClass = 'text-green-600';
                                        $bgColorClass = 'bg-green-50';
                                        break;
                                    case 'approval_management':
                                        $icon = 'check-circle';
                                        $colorClass = 'text-purple-600';
                                        $bgColorClass = 'bg-purple-50';
                                        break;
                                    case 'data_management':
                                        $icon = 'database';
                                        $colorClass = 'text-yellow-600';
                                        $bgColorClass = 'bg-yellow-50';
                                        break;
                                    case 'branch_management':
                                        $icon = 'office-building';
                                        $colorClass = 'text-indigo-600';
                                        $bgColorClass = 'bg-indigo-50';
                                        break;
                                    case 'line_management':
                                        $icon = 'phone';
                                        $colorClass = 'text-teal-600';
                                        $bgColorClass = 'bg-teal-50';
                                        break;
                                    case 'safe_management':
                                        $icon = 'lock-closed';
                                        $colorClass = 'text-red-600';
                                        $bgColorClass = 'bg-red-50';
                                        break;
                                    case 'customer_management':
                                        $icon = 'users';
                                        $colorClass = 'text-sky-600';
                                        $bgColorClass = 'bg-sky-50';
                                        break;
                                    case 'reporting':
                                        $icon = 'document-report';
                                        $colorClass = 'text-amber-600';
                                        $bgColorClass = 'bg-amber-50';
                                        break;
                                    case 'ungrouped':
                                        $icon = 'collection';
                                        $colorClass = 'text-gray-600';
                                        $bgColorClass = 'bg-gray-50';
                                        break;
                                }

                                $displayName =
                                    $group === 'ungrouped'
                                        ? 'Other Permissions'
                                        : ucwords(str_replace('_', ' ', $group));
                            @endphp

                            <div class="{{ $bgColorClass }} p-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold {{ $colorClass }} flex items-center">
                                    <!-- Using a simple SVG instead of x-heroicon component that might be causing issues -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ $displayName }}
                                    <span
                                        class="ml-auto bg-white rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colorClass }}">
                                        {{ $permissions->count() }}
                                    </span>
                                </h3>
                            </div>

                            <div class="divide-y divide-gray-100">
                                @foreach ($permissions as $permission)
                                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                                        <div class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <div>
                                                <span class="font-medium text-gray-800">{{ $permission->name }}</span>
                                                @if ($permission->description)
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        {{ $permission->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Roles Section -->
            <div class="mt-10">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    System Roles and Their Permissions
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($roles as $role)
                        @php
                            $bgColor = $roleColors[$role->name][0] ?? 'bg-gray-50';
                            $textColor = $roleColors[$role->name][1] ?? 'text-gray-800';
                            $pillBgColor = $roleColors[$role->name][2] ?? 'bg-gray-100';
                        @endphp

                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                            <div class="{{ $bgColor }} p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold {{ $textColor }}">
                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                    </h3>
                                    <span
                                        class="px-3 py-1 text-xs font-medium {{ $textColor }} {{ $pillBgColor }} rounded-full">
                                        {{ $role->permissions->count() }} permissions
                                    </span>
                                </div>
                                @if ($role->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $role->description }}</p>
                                @endif
                            </div>

                            <div class="p-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Key Permissions:</h4>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @php
                                        $shownPermissions = collect($role->permissions)->take(5);
                                    @endphp
                                    @foreach ($shownPermissions as $permission)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if ($role->permissions->count() > 5)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            +{{ $role->permissions->count() - 5 }} more
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('permissions.roles', ['role' => $role->id]) }}"
                                    class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    View all permissions
                                    <svg class="ml-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-10 text-center text-sm text-gray-500">
                <p>Last updated: {{ now()->format('F j, Y, g:i a') }}</p>
                <p class="mt-1">This information is used for system administrators only.</p>
            </div>
        </div>
    </div>
</div>
