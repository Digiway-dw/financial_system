<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
</div>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">System Permissions</h1>

                    <a href="{{ route('permissions.roles') }}" wire:navigate
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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

                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Permissions by Category</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($permissionsByGroup as $group => $permissions)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                    @switch($group)
                                        @case('user_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('financial_operations')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('approval_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('data_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('branch_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-amber-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('line_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('safe_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('customer_management')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-emerald-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @case('reporting')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-sky-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                        @break

                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                            </svg>
                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                    @endswitch
                                </h3>

                                <ul class="space-y-2">
                                    @foreach ($permissions as $permission)
                                        <li class="text-sm text-gray-700 flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2 text-green-500 mt-0.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <span class="font-medium">{{ $permission->name }}</span>
                                                @if ($permission->description)
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ $permission->description }}</p>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-12">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Roles and Their Permissions</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($roles as $role)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ ucwords(str_replace('_', ' ', $role->name)) }}</h3>
                                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ $role->permissions->count() }} permissions
                                    </span>
                                </div>

                                @if ($role->description)
                                    <p class="text-sm text-gray-600 mb-4">{{ $role->description }}</p>
                                @endif

                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Assigned Permissions:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($role->permissions as $permission)
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-md">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
