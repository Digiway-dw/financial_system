
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">User Management</h1>
            <p class="mt-1 text-sm text-gray-600">Manage users and their roles</p>
        </div>
        
        @can('create', App\Domain\Entities\User::class)
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </a>
        @endcan
    </div>

    @if (session('message'))
        <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 border-l-4 border-green-500 rounded-lg" role="alert">
            <div class="flex items-center">
                <x-heroicon-o-check-circle class="w-5 h-5 mr-2"/>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters Panel -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-5 border border-gray-200">
        <div class="p-6 bg-white">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 md:space-x-4">
                <!-- Search -->
                <div class="w-full md:w-1/3">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="name" type="text" id="search" class="pl-10 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Search users...">
                    </div>
                </div>

                <!-- Roles Filter -->
                <div class="w-full md:w-1/3">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select wire:model.live="role" id="role" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Branch Filter -->
                <div class="w-full md:w-1/3">
                    <label for="branchId" class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                    <select wire:model.live="branchId" id="branchId" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Toggle for Trashed Users & Reset Filters -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6">
                <div class="flex items-center mb-4 sm:mb-0">
                    <input wire:model.live="showTrashed" type="checkbox" id="showTrashed" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="showTrashed" class="ml-2 block text-sm font-medium text-gray-700">Show Deleted Users</label>
                </div>

                <button wire:click="resetFilters" type="button" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-8 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-8 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Branch
                        </th>
                        <th scope="col" class="px-8 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col" class="px-8 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @php
                                            $name = $user->name ?? 'User';
                                            $initials = implode('', array_map(fn($part) => strtoupper(substr($part, 0, 1)), explode(' ', $name)));
                                            $bgColors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                                            $randomColor = $bgColors[crc32($name) % count($bgColors)];
                                        @endphp
                                        <div class="{{ $randomColor }} rounded-full flex items-center justify-center h-10 w-10 text-white font-bold shadow-md">
                                            {{ substr($initials, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                @if(isset($user->branch) && $user->branch)
                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                    <x-heroicon-o-building-office-2 class="w-4 h-4 mr-1" />
                                    {{ $user->branch->name }}
                                </div>
                                @endif
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                @if ($editingUserId === $user->id)
                                    <select wire:model="selectedRole" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-purple-600 hover:bg-purple-700',
                                            'general_supervisor' => 'bg-blue-600 hover:bg-blue-700',
                                            'branch_manager' => 'bg-emerald-600 hover:bg-emerald-700',
                                            'agent' => 'bg-amber-600 hover:bg-amber-700',
                                            'trainee' => 'bg-gray-600 hover:bg-gray-700',
                                        ];
                                        $roleName = $user->getRoleNames()->first() ?? 'No Role';
                                        $roleColor = $roleColors[strtolower($roleName)] ?? 'bg-slate-600 hover:bg-slate-700';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleColor }} text-white shadow-sm transition-all duration-200">
                                        {{ $roleName }}
                                    </span>
                                @endif
                            </td>
                            <!-- Action Buttons -->
                            <td class="px-8 py-5 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($user->trashed())
                                        @can('restore', $user)
                                            <button wire:click="confirmRestore({{ $user->id }})" type="button" 
                                                class="inline-flex items-center justify-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Restore
                                            </button>
                                        @endcan
                                    @else
                                        @if ($editingUserId === $user->id)
                                            <button wire:click="saveRole" type="button" 
                                                class="inline-flex items-center justify-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                                Save
                                            </button>
                                            <button wire:click="cancelEdit" type="button" 
                                                class="inline-flex items-center justify-center px-3 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Cancel
                                            </button>
                                        @else
                                            @can('update', $user)
                                                <button wire:click="editRole({{ $user->id }})" type="button" 
                                                    class="inline-flex items-center justify-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                    Edit Role
                                                </button>
                                            @endcan
                                            
                                            <a href="{{ route('users.view', $user->id) }}" 
                                                class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                View
                                            </a>
                                            
                                            @can('update', $user)
                                                <a href="{{ route('users.edit', $user->id) }}" 
                                                    class="inline-flex items-center justify-center px-3 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                    Edit
                                                </a>
                                            @endcan
                                            
                                            @can('delete', $user)
                                                <button wire:click="confirmUserDeletion({{ $user->id }})" type="button" 
                                                    class="inline-flex items-center justify-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            @endcan
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mb-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <p class="font-medium">No users found</p>
                                    <p class="mt-1">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Delete User Confirmation Modal -->
    <div
        x-data="{ show: @entangle('confirmingUserDeletion').live }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            <div class="flex items-center text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                Delete User
                            </div>
                        </h3>

                        <div class="mt-4 text-sm text-gray-600">
                            <p class="mb-4">Are you sure you want to delete this user? This action cannot be undone.</p>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <div class="font-medium">{{ $userBeingDeleted?->name }}</div>
                                <div class="text-sm text-gray-600">{{ $userBeingDeleted?->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteUser" type="button" class="inline-flex items-center justify-center px-4 py-2 ml-3 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" wire:loading.attr="disabled">
                        Delete
                    </button>
                    <button wire:click="$set('confirmingUserDeletion', false)" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" wire:loading.attr="disabled">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore User Confirmation Modal -->
    <div
        x-data="{ show: @entangle('confirmingUserRestore').live }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            <div class="flex items-center text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Restore User
                            </div>
                        </h3>

                        <div class="mt-4 text-sm text-gray-600">
                            <p class="mb-4">Are you sure you want to restore this user?</p>
                            <div class="bg-gray-100 p-3 rounded-lg">
                                <div class="font-medium">{{ $userBeingRestored?->name }}</div>
                                <div class="text-sm text-gray-600">{{ $userBeingRestored?->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="restoreUser" type="button" class="inline-flex items-center justify-center px-4 py-2 ml-3 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" wire:loading.attr="disabled">
                        Restore
                    </button>
                    <button wire:click="$set('confirmingUserRestore', false)" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" wire:loading.attr="disabled">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
