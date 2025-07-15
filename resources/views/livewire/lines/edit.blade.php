<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Line</h3>

    <form wire:submit.prevent="updateLine" class="mt-6 space-y-6">
        <!-- Mobile Number -->
        <div>
            <x-input-label for="mobileNumber" :value="__('Mobile Number')" />
            <x-text-input wire:model="mobileNumber" id="mobileNumber" name="mobileNumber" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('mobileNumber')" />
        </div>

        <!-- Current Balance -->
        <div>
            <x-input-label for="currentBalance" :value="__('Current Balance (EGP)')" />
            <x-text-input wire:model="currentBalance" id="currentBalance" name="currentBalance" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('currentBalance')" />
        </div>

        <!-- Daily Limit -->
        <div>
            <x-input-label for="dailyLimit" :value="__('Daily Limit (EGP)')" />
            <x-text-input wire:model="dailyLimit" id="dailyLimit" name="dailyLimit" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('dailyLimit')" />
        </div>

        <!-- Monthly Limit -->
        <div>
            <x-input-label for="monthlyLimit" :value="__('Monthly Limit (EGP)')" />
            <x-text-input wire:model="monthlyLimit" id="monthlyLimit" name="monthlyLimit" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('monthlyLimit')" />
        </div>

        <!-- Network -->
        <div>
            <x-input-label for="network" :value="__('Network')" />
            <select wire:model="network" id="network" name="network" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="Vodafone">Vodafone</option>
                <option value="Orange">Orange</option>
                <option value="Etisalat">Etisalat</option>
                <option value="We">We</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('network')" />
        </div>

        <!-- Status -->
        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select wire:model="status" id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>

        <!-- Assigned Branch -->
        <div>
            <x-input-label for="branchId" :value="__('Assigned Branch')" />
            <select wire:model="branchId" id="branchId" name="branchId" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">Select Branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Update Line') }}</x-primary-button>

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
