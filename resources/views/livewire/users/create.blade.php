<div class="p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Create New User</h3>

    <form wire:submit.prevent="createUser">
        <!-- Name -->
        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" wire:model="name" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" wire:model="email" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" wire:model="password" required autocomplete="new-password" />

            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" wire:model="password_confirmation" required autocomplete="new-password" />

            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Phone Number -->
        <div class="mb-4">
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" wire:model="phone_number" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <!-- National Number -->
        <div class="mb-4">
            <x-input-label for="national_number" :value="__('National Number (14 digits)')" />
            <x-text-input id="national_number" class="block mt-1 w-full" type="text" wire:model="national_number" maxlength="14" />
            <x-input-error class="mt-2" :messages="$errors->get('national_number')" />
        </div>

        <!-- Salary -->
        <div class="mb-4">
            <x-input-label for="salary" :value="__('Salary')" />
            <x-text-input id="salary" class="block mt-1 w-full" type="number" step="0.01" wire:model="salary" />
            <x-input-error class="mt-2" :messages="$errors->get('salary')" />
        </div>

        <!-- Address -->
        <div class="mb-4">
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" class="block mt-1 w-full" type="text" wire:model="address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <!-- Land Number -->
        <div class="mb-4">
            <x-input-label for="land_number" :value="__('Land Number')" />
            <x-text-input id="land_number" class="block mt-1 w-full" type="text" wire:model="land_number" />
            <x-input-error class="mt-2" :messages="$errors->get('land_number')" />
        </div>

        <!-- Relative's Phone Number -->
        <div class="mb-4">
            <x-input-label for="relative_phone_number" :value="__('Relative\'s Phone Number')" />
            <x-text-input id="relative_phone_number" class="block mt-1 w-full" type="text" wire:model="relative_phone_number" />
            <x-input-error class="mt-2" :messages="$errors->get('relative_phone_number')" />
        </div>

        <!-- Notes -->
        <div class="mb-4">
            <x-input-label for="notes" :value="__('Notes')" />
            <textarea id="notes" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model="notes"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <x-input-label for="selectedRole" :value="__('Role')" />
            <select wire:model="selectedRole" id="selectedRole" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('selectedRole')" />
        </div>

        <!-- Branch Selection (only for non-admin/general_supervisor) -->
        @if (!in_array($selectedRole, ['admin', 'general_supervisor']))
        <div class="mb-4">
            <x-input-label for="branchId" :value="__('Branch')" />
            <select wire:model="branchId" id="branchId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">Select Branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
        </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Create User') }}
            </x-primary-button>
        </div>
    </form>
</div> 