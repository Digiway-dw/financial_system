<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">User Management</h3>
    </div>
    @if (auth()->user() && auth()->user()->hasRole('admin'))
        <div class="mb-4">
            <a href="{{ route('users.create') }}">
                <x-primary-button>
                    {{ __('Add User') }}
                </x-primary-button>
            </a>
        </div>
    @endif

    @if (session('message'))
        <div class="mt-4 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-900 rounded shadow flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <!-- Each filter input: add w-full md:w-40 or md:w-36 as appropriate -->
        <div class="w-full md:w-40">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" wire:model.defer="name" class="w-full" />
        </div>
        <!-- Repeat for other filters, adjusting widths as needed -->
        <div class="w-full md:w-auto">
            <x-primary-button wire:click="filter" class="w-full md:w-auto">{{ __('Filter') }}</x-primary-button>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs md:text-sm">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Current Role</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($editingUserId === $user->id)
                                    <select wire:model="selectedRole" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    {{ $user->getRoleNames()->first() ?? 'No Role' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($editingUserId === $user->id)
                                    <x-primary-button wire:click="saveRole({{ $user->id }})">{{ __('Save') }}</x-primary-button>
                                    <x-secondary-button wire:click="cancelEdit">{{ __('Cancel') }}</x-secondary-button>
                                @else
                                    <x-secondary-button wire:click="editRole({{ $user->id }})">{{ __('Edit Role') }}</x-secondary-button>
                                    @if (auth()->user() && auth()->user()->hasRole('admin'))
                                        @if (!$user->hasRole('admin'))
                                            <a href="{{ route('users.edit', $user->id) }}" class="inline-block ml-2">
                                                <x-primary-button>{{ __('Edit') }}</x-primary-button>
                                            </a>
                                            <a href="{{ route('users.view', $user->id) }}" class="inline-block ml-2">
                                                <x-secondary-button>{{ __('View') }}</x-secondary-button>
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
