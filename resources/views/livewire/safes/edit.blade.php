<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Safe</h3>

    <form wire:submit.prevent="updateSafe" class="mt-6 space-y-6">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Safe Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Balance -->
        <div>
            <x-input-label for="currentBalance" :value="__('Current Balance (EGP)')" />
            <x-text-input wire:model="currentBalance" id="currentBalance" name="currentBalance" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('currentBalance')" />
        </div>

        <!-- Branch (Read-only) -->
        <div>
            <x-input-label for="branchName" :value="__('Branch')" />
            <x-text-input wire:model="branchName" id="branchName" name="branchName" type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" readonly />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Branch cannot be changed. Each branch has exactly one safe.</p>
        </div>

        <!-- Type -->
        <!-- Removed Safe Type field as only branch safes are allowed -->

        <!-- Description -->
        <div>
            <x-input-label for="description" :value="__('Description (Optional)')" />
            <textarea wire:model="description" id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Update Safe') }}</x-primary-button>

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
