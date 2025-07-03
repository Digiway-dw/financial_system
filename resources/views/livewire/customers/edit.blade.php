<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Customer</h3>

    <form wire:submit.prevent="updateCustomer" class="mt-6 space-y-6">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Customer Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Mobile Number -->
        <div>
            <x-input-label for="mobileNumber" :value="__('Mobile Number')" />
            <x-text-input wire:model="mobileNumber" id="mobileNumber" name="mobileNumber" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('mobileNumber')" />
        </div>

        <!-- Customer Code -->
        <div>
            <x-input-label for="customerCode" :value="__('Customer Code (Optional)')" />
            <x-text-input wire:model="customerCode" id="customerCode" name="customerCode" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('customerCode')" />
        </div>

        <!-- Gender -->
        <div>
            <x-input-label for="gender" :value="__('Gender')" />
            <select wire:model="gender" id="gender" name="gender" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
        </div>

        <!-- Balance -->
        <div>
            <x-input-label for="balance" :value="__('Balance')" />
            <x-text-input wire:model="balance" id="balance" name="balance" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('balance')" />
        </div>

        <!-- Is Client -->
        <div>
            <label for="is_client" class="inline-flex items-center">
                <input type="checkbox" wire:model="is_client" id="is_client" name="is_client" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Is Client') }}</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('is_client')" />
        </div>

        <!-- Agent ID -->
        <div>
            <x-input-label for="agent_id" :value="__('Agent')" />
            <select wire:model="agent_id" id="agent_id" name="agent_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">Select Agent</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('agent_id')" />
        </div>

        <!-- Branch ID -->
        <div>
            <x-input-label for="branch_id" :value="__('Branch')" />
            <select wire:model="branch_id" id="branch_id" name="branch_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('branch_id')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Update Customer') }}</x-primary-button>

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
