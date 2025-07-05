<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create New Branch</h3>

    <form wire:submit.prevent="createBranch" class="mt-6 space-y-6">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Branch Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Description -->
        <div>
            <x-input-label for="description" :value="__('Description (Optional)')" />
            <textarea wire:model="description" id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <!-- Location -->
        <div>
            <x-input-label for="location" :value="__('Location')" />
            <x-text-input wire:model="location" id="location" name="location" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('location')" />
        </div>

        <!-- Branch Code -->
        <div>
            <x-input-label for="branch_code" :value="__('Branch Code')" />
            <x-text-input wire:model="branch_code" id="branch_code" name="branch_code" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('branch_code')" />
        </div>

        <!-- Safe Initial Balance -->
        <div>
            <x-input-label for="safe_initial_balance" :value="__('Safe Initial Balance (EGP)')" />
            <x-text-input wire:model="safe_initial_balance" id="safe_initial_balance" name="safe_initial_balance" type="number" step="0.01" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('safe_initial_balance')" />
        </div>

        <!-- Safe Description -->
        <div>
            <x-input-label for="safe_description" :value="__('Safe Description (Optional)')" />
            <textarea wire:model="safe_description" id="safe_description" name="safe_description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('safe_description')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create Branch') }}</x-primary-button>

            @if (session('message'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ session('message') }}</p>
            @endif

            @if (session('error'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-red-600 dark:text-red-400"
                >{{ session('error') }}</p>
            @endif
        </div>
    </form>
</div> 