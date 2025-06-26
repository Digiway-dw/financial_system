<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create New Customer</h3>

    <form wire:submit.prevent="createCustomer" class="mt-6 space-y-6">
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

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create Customer') }}</x-primary-button>

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
