<div class="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white">Create New User</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new user to the system with appropriate access rights</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
        <form wire:submit.prevent="createUser">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information Section -->
                <div class="col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Personal Information</h3>
                </div>
                
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Full Name')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="name" class="pl-10 block w-full" type="text" wire:model="name" required autofocus autocomplete="name" placeholder="Enter full name" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-envelope class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="email" class="pl-10 block w-full" type="email" wire:model="email" required autocomplete="username" placeholder="user@example.com" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="password" class="pl-10 block w-full" type="password" wire:model="password" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="password_confirmation" class="pl-10 block w-full" type="password" wire:model="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                </div>
                
                <!-- Contact Information Section -->
                <div class="col-span-2 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Contact Information</h3>
                </div>

                <!-- Phone Number -->
                <div>
                    <x-input-label for="phone_number" :value="__('Phone Number')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-device-phone-mobile class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="phone_number" class="pl-10 block w-full" type="text" wire:model="phone_number" placeholder="+20 123 456 7890" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                </div>

                <!-- Relative's Phone Number -->
                <div>
                    <x-input-label for="relative_phone_number" :value="__('Relative\'s Phone Number')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-phone class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="relative_phone_number" class="pl-10 block w-full" type="text" wire:model="relative_phone_number" placeholder="+20 123 456 7890" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('relative_phone_number')" />
                </div>

                <!-- Land Number -->
                <div>
                    <x-input-label for="land_number" :value="__('Land Number')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-phone class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="land_number" class="pl-10 block w-full" type="text" wire:model="land_number" placeholder="02 XXXX XXXX" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('land_number')" />
                </div>

                <!-- Address -->
                <div>
                    <x-input-label for="address" :value="__('Address')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="address" class="pl-10 block w-full" type="text" wire:model="address" placeholder="Street address" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>

                <!-- Employment Information Section -->
                <div class="col-span-2 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Employment Information</h3>
                </div>

                <!-- National Number -->
                <div>
                    <x-input-label for="national_number" :value="__('National Number (14 digits)')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-identification class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="national_number" class="pl-10 block w-full" type="text" wire:model="national_number" maxlength="14" placeholder="XXXXXXXXXXXX" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('national_number')" />
                </div>

                <!-- Salary -->
                <div>
                    <x-input-label for="salary" :value="__('Salary')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-banknotes class="w-5 h-5 text-gray-400" />
                        </div>
                        <x-text-input id="salary" class="pl-10 block w-full" type="number" step="0.01" wire:model="salary" placeholder="0.00" />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('salary')" />
                </div>

                <!-- Role Selection -->
                <div>
                    <x-input-label for="selectedRole" :value="__('Role')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-user-circle class="w-5 h-5 text-gray-400" />
                        </div>
                        <select wire:model="selectedRole" id="selectedRole" class="pl-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('selectedRole')" />
                </div>

                <!-- Branch Selection (only for non-admin/general_supervisor) -->
                @if (!in_array($selectedRole, ['admin', 'general_supervisor']))
                <div>
                    <x-input-label for="branchId" :value="__('Branch')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-400" />
                        </div>
                        <select wire:model="branchId" id="branchId" class="pl-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
                </div>
                @endif

                <!-- Notes -->
                <div class="col-span-2">
                    <x-input-label for="notes" :value="__('Additional Notes')" class="text-gray-700 dark:text-gray-300" />
                    <div class="relative mt-1">
                        <textarea id="notes" rows="4" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model="notes" placeholder="Any additional information about this user..."></textarea>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-300 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-3">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                    {{ __('Cancel') }}
                </a>
                <x-primary-button class="ml-3 py-3 px-6">
                    <x-heroicon-o-user-plus class="w-5 h-5 mr-1" />
                    {{ __('Create User') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>