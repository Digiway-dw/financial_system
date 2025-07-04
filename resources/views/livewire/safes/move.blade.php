<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Move Safe Cash</h3>

    <form wire:submit.prevent="moveCash" class="mt-6 space-y-6">
        <!-- From Safe -->
        <div>
            <x-input-label for="fromSafeId" :value="__('From Safe')" />
            <select wire:model="fromSafeId" id="fromSafeId" name="fromSafeId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Source Safe</option>
                @foreach ($safes as $safe)
                    <option value="{{ $safe['id'] }}">{{ $safe['name'] }} (Balance: {{ number_format($safe['current_balance'], 2) }} EGP)</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('fromSafeId')" />
        </div>

        <!-- To Safe -->
        <div>
            <x-input-label for="toSafeId" :value="__('To Safe')" />
            <select wire:model="toSafeId" id="toSafeId" name="toSafeId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Destination Safe</option>
                @foreach ($safes as $safe)
                    <option value="{{ $safe['id'] }}">{{ $safe['name'] }} (Balance: {{ number_format($safe['current_balance'], 2) }} EGP)</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('toSafeId')" />
        </div>

        <!-- Amount -->
        <div>
            <x-input-label for="amount" :value="__('Amount (EGP)')" />
            <x-text-input wire:model="amount" id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Move Cash') }}</x-primary-button>

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
