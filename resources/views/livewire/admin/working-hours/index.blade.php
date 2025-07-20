<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50">
    <!-- Header Section -->
    <div class="bg-white/70 backdrop-blur-sm border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Working Hours Management</h1>
                    <p class="text-sm text-gray-600">Define when users are allowed to access the system</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Messages -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 border border-green-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-700 text-sm">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-red-700 text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- User Selection -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Select User</h2>
                <div class="w-full md:w-1/2">
                    <select wire:model="selectedUser"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                        <option value="">-- Select a user --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if ($selectedUser)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Working Hours Form -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingId ? 'Edit Working Hours' : 'Add Working Hours' }}
                        </h2>
                        <form wire:submit.prevent="save">
                            <div class="mb-4">
                                <label for="dayOfWeek" class="block text-sm font-medium text-gray-700 mb-1">Day of
                                    Week</label>
                                <select id="dayOfWeek" wire:model="dayOfWeek"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    <option value="">-- Select day --</option>
                                    @foreach ($days as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('dayOfWeek')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="startTime" class="block text-sm font-medium text-gray-700 mb-1">Start
                                        Time</label>
                                    <input type="time" id="startTime" wire:model="startTime"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    @error('startTime')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="endTime" class="block text-sm font-medium text-gray-700 mb-1">End
                                        Time</label>
                                    <input type="time" id="endTime" wire:model="endTime"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    @error('endTime')
                                        <span class="text-red-600 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="isEnabled"
                                        class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Enabled</span>
                                </label>
                                @error('isEnabled')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end space-x-3">
                                @if ($editingId)
                                    <button type="button" wire:click="resetForm"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                        Cancel
                                    </button>
                                @endif
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-orange-600 border border-transparent rounded-lg hover:from-amber-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                    {{ $editingId ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Working Hours List -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 lg:col-span-2">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Current Working Hours</h2>

                        @if (count($workingHours) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Day</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Start Time</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                End Time</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($workingHours as $workingHour)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ ucfirst($workingHour->day_of_week) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($workingHour->start_time)->format('H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($workingHour->end_time)->format('H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if ($workingHour->is_enabled)
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Enabled
                                                        </span>
                                                    @else
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Disabled
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex justify-end space-x-2">
                                                        <button wire:click="toggleStatus({{ $workingHour->id }})"
                                                            class="text-amber-600 hover:text-amber-900">
                                                            {{ $workingHour->is_enabled ? 'Disable' : 'Enable' }}
                                                        </button>
                                                        <button wire:click="edit({{ $workingHour->id }})"
                                                            class="text-indigo-600 hover:text-indigo-900">
                                                            Edit
                                                        </button>
                                                        <button wire:click="delete({{ $workingHour->id }})"
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this working hours entry?')">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500">No working hours defined for this user.</p>
                                <p class="text-sm text-gray-400 mt-1">Use the form to add working hours.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-8 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-amber-500 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Select a User</h3>
                    <p class="text-gray-500">Please select a user from the dropdown above to manage their working
                        hours.</p>
                </div>
            </div>
        @endif
    </div>
</div>
