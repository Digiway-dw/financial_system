<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Role Permission Management</h1>

                @if ($successMessage)
                    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 relative" x-data="{ show: true }"
                        x-show="show">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ $successMessage }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" @click="show = false" wire:click="clearMessages"
                                        class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <span class="sr-only">Dismiss</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 relative" x-data="{ show: true }"
                        x-show="show">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" @click="show = false" wire:click="clearMessages"
                                        class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <span class="sr-only">Dismiss</span>
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Role Selection -->
                    <div class="w-full md:w-1/3">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Role</h3>

                            <div class="mb-6">
                                <label for="role-select"
                                    class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select id="role-select" wire:model.live="selectedRole"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <label for="group-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by
                                    Group</label>
                                <select id="group-filter" wire:model.live="selectedGroup"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="all">All Groups</option>
                                    @foreach ($availableGroups as $group)
                                        <option value="{{ $group }}">{{ ucwords(str_replace('_', ' ', $group)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search
                                    Permissions</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="search" wire:model.live.debounce.300ms="searchTerm"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Search permission name or description">
                                </div>
                            </div>

                            <button wire:click="savePermissions"
                                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Permissions
                            </button>
                        </div>
                    </div>

                    <!-- Permissions List -->
                    <div class="w-full md:w-2/3">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Assign Permissions</h3>

                            @if (count($permissionsByGroup) === 0)
                                <div class="py-4 text-center text-gray-500">
                                    No permissions found matching your criteria.
                                </div>
                            @else
                                <div class="space-y-6">
                                    @foreach ($permissionsByGroup as $group => $permissions)
                                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                <div class="flex items-center">
                                                    <div class="flex items-center h-5">
                                                        <input id="group-{{ $group }}" type="checkbox"
                                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                            wire:click="toggleGroupPermissions('{{ $group }}', $event.target.checked)"
                                                            @if ($this->isGroupSelected($group)) checked @endif>
                                                    </div>
                                                    <div class="ml-3 flex items-center justify-between w-full">
                                                        <label for="group-{{ $group }}"
                                                            class="font-medium text-gray-700 flex items-center">
                                                            @switch($group)
                                                                @case('user_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-blue-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                                    </svg>
                                                                @break

                                                                @case('financial_operations')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-green-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                @break

                                                                @case('approval_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-indigo-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                @break

                                                                @case('data_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-purple-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                                                    </svg>
                                                                @break

                                                                @case('branch_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-amber-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                    </svg>
                                                                @break

                                                                @case('line_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-yellow-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                    </svg>
                                                                @break

                                                                @case('safe_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-red-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                                    </svg>
                                                                @break

                                                                @case('customer_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-emerald-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    </svg>
                                                                @break

                                                                @case('reporting')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-sky-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                @break

                                                                @case('system_configuration')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-gray-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    </svg>
                                                                @break

                                                                @case('audit_compliance')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-orange-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                                    </svg>
                                                                @break

                                                                @case('communication')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-pink-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                                    </svg>
                                                                @break

                                                                @case('staff_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-cyan-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    </svg>
                                                                @break

                                                                @case('transaction_management')
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-rose-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                                    </svg>
                                                                @break

                                                                @default
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-5 w-5 mr-2 text-gray-600" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                                    </svg>
                                                            @endswitch
                                                            {{ ucwords(str_replace('_', ' ', $group)) }}
                                                        </label>
                                                        <span
                                                            class="text-xs font-medium bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full">
                                                            {{ count($permissions) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if ($this->isGroupPartiallySelected($group))
                                                    <div class="mt-1 ml-7 text-xs text-gray-500">
                                                        Some permissions in this group are selected
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="px-4 py-3 divide-y divide-gray-200">
                                                @foreach ($permissions as $permission)
                                                    <div class="py-3">
                                                        <div class="flex items-start">
                                                            <div class="flex items-center h-5">
                                                                <input id="permission-{{ $permission->id }}"
                                                                    type="checkbox"
                                                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                                                    wire:click="togglePermission({{ $permission->id }})"
                                                                    @if (in_array($permission->id, $selectedPermissions)) checked @endif>
                                                            </div>
                                                            <div class="ml-3 text-sm">
                                                                <label for="permission-{{ $permission->id }}"
                                                                    class="font-medium text-gray-700">{{ $permission->name }}</label>
                                                                @if ($permission->description)
                                                                    <p class="text-gray-500">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
