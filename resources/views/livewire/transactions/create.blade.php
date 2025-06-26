<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create New Transaction</h3>

    <form wire:submit.prevent="createTransaction" class="mt-6 space-y-6">
        <!-- Customer Name -->
        <div>
            <x-input-label for="customerName" :value="__('Customer Name')" />
            <x-text-input wire:model="customerName" id="customerName" name="customerName" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('customerName')" />
        </div>

        <!-- Customer Mobile Number -->
        <div>
            <x-input-label for="customerMobileNumber" :value="__('Customer Mobile Number')" />
            <x-text-input wire:model="customerMobileNumber" id="customerMobileNumber" name="customerMobileNumber" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('customerMobileNumber')" />
        </div>

        <!-- Line Mobile Number -->
        <div>
            <x-input-label for="lineMobileNumber" :value="__('Line Mobile Number Used')" />
            <x-text-input wire:model="lineMobileNumber" id="lineMobileNumber" name="lineMobileNumber" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('lineMobileNumber')" />
        </div>

        <!-- Customer Code -->
        <div>
            <x-input-label for="customerCode" :value="__('Customer Code (Optional)')" />
            <x-text-input wire:model="customerCode" id="customerCode" name="customerCode" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('customerCode')" />
        </div>

        <!-- Amount -->
        <div>
            <x-input-label for="amount" :value="__('Amount (EGP)')" />
            <x-text-input wire:model.live="amount" id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
        </div>

        <!-- Commission (Auto-calculated) -->
        <div>
            <x-input-label for="commission" :value="__('Commission (EGP)')" />
            <x-text-input wire:model="commission" id="commission" name="commission" type="number" step="0.01" class="mt-1 block w-full" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('commission')" />
        </div>

        <!-- Deduction -->
        <div>
            <x-input-label for="deduction" :value="__('Deduction (Manual Override from Commission)')" />
            <x-text-input wire:model="deduction" id="deduction" name="deduction" type="number" step="0.01" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('deduction')" />
        </div>

        <!-- Transaction Type -->
        <div>
            <x-input-label for="transactionType" :value="__('Transaction Type')" />
            <select wire:model="transactionType" id="transactionType" name="transactionType" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="Transfer">Transfer</option>
                <option value="Withdrawal">Withdrawal</option>
                <option value="Deposit">Deposit</option>
                <option value="Adjustment">Adjustment</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('transactionType')" />
        </div>

        <!-- Branch -->
        <div>
            <x-input-label for="branchId" :value="__('Branch')" />
            <select wire:model="branchId" id="branchId" name="branchId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
        </div>

        <!-- Line -->
        <div>
            <x-input-label for="lineId" :value="__('Line')" />
            <select wire:model="lineId" id="lineId" name="lineId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Line</option>
                @foreach ($lines as $line)
                    <option value="{{ $line->id }}">{{ $line->mobile_number }} (Balance: {{ $line->current_balance }} EGP)</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('lineId')" />
        </div>

        <!-- Safe -->
        <div>
            <x-input-label for="safeId" :value="__('Safe')" />
            <select wire:model="safeId" id="safeId" name="safeId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                <option value="">Select Safe</option>
                @foreach ($safes as $safe)
                    <option value="{{ $safe->id }}">{{ $safe->name }} (Balance: {{ $safe->balance }} EGP)</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('safeId')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create Transaction') }}</x-primary-button>

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
