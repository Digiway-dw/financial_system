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

    <!-- Select Customer (Live Database Search Dropdown) -->
    <div x-data="{ open: false }" class="relative">
        <x-input-label for="searchCustomer" :value="__('Select Customer (type name or number)')" />
        <input wire:model.debounce.400ms="searchCustomer" id="searchCustomer" name="searchCustomer" type="text" placeholder="Type to search customers..." class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" @focus="open = true" @blur="setTimeout(() => open = false, 200)" autocomplete="off" />
        <input type="hidden" wire:model="selectedDestinationNumber" id="selectedDestinationNumber" name="selectedDestinationNumber" />
        @if(!empty($customerSearchResults))
            <ul x-show="open" class="absolute z-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 w-full mt-1 rounded shadow max-h-48 overflow-y-auto">
                @foreach ($customerSearchResults as $customer)
                    @if(isset($customer['mobile_number']) && isset($customer['name']))
                        <li class="px-4 py-2 hover:bg-indigo-100 dark:hover:bg-indigo-700 cursor-pointer" @mousedown.prevent="open = false" wire:click="selectCustomer('{{ $customer['id'] }}')">
                            {{ $customer['name'] }} - {{ $customer['mobile_number'] ?? '' }}
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif
        <div class="bg-yellow-100 text-yellow-800 p-2 text-xs rounded mb-2">
            <strong>Debug:</strong> {{ json_encode($customerSearchResults) }}
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('selectedDestinationNumber')" />
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

    <!-- Gender -->
    <div>
        <x-input-label for="gender" :value="__('Gender')" />
        <select wire:model="gender" id="gender" name="gender" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
    </div>

    <!-- Is Client -->
    <div>
        <label for="isClient" class="inline-flex items-center">
            <input wire:model.live="isClient" id="isClient" name="isClient" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Is Client?') }}</span>
        </label>
        <x-input-error class="mt-2" :messages="$errors->get('isClient')" />
    </div>

    <!-- Amount -->
    <div>
        <x-input-label for="amount" :value="__('Amount (EGP)')" />
        <x-text-input wire:model.live="amount" id="amount" name="amount" type="number" class="mt-1 block w-full" required />
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
    @if($deduction > 0)
        <div>
            <x-input-label for="notes" :value="__('Deduction Notes (Required)')" />
            <x-text-input wire:model="notes" id="notes" name="notes" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
        </div>
    @endif

    <!-- Payment Method -->
    <div>
        <x-input-label for="paymentMethod" :value="__('Payment Method')" />
        <select wire:model="paymentMethod" id="paymentMethod" name="paymentMethod" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
            <option value="branch safe">Branch Safe</option>
            <option value="client wallet">Client Wallet</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('paymentMethod')" />
    </div>

    @can('perform-unrestricted-withdrawal')
        <div x-data="{ transactionType: '{{ $transactionType }}' }" x-show="transactionType === 'Withdrawal'">
            <label for="isAbsoluteWithdrawal" class="inline-flex items-center">
                <input wire:model.live="isAbsoluteWithdrawal" id="isAbsoluteWithdrawal" name="isAbsoluteWithdrawal" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Absolute Withdrawal (No re-deposit to another safe)') }}</span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('isAbsoluteWithdrawal')" />
        </div>
    @endcan

    <!-- Branch -->
    <div>
        <x-input-label for="branchId" :value="__('Branch')" />
        <select wire:model="branchId" id="branchId" name="branchId" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
            <option value="">Select Branch</option>
            @foreach (($branches ?? []) as $branch)
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
            @foreach (($lines ?? []) as $line)
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
            @foreach (($safes ?? []) as $safe)
                <option value="{{ $safe->id }}">{{ $safe->name }} (Balance: {{ $safe->current_balance }} EGP)</option>
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

    <x-modal name="transaction-receipt" :show="$showReceiptModal" focusable>
        @if ($completedTransaction)
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Transaction Receipt') }}
                </h2>

                <div class="mt-6 text-gray-700 dark:text-gray-300 space-y-2">
                    <p><strong>{{ __('Transaction ID') }}:</strong> {{ $completedTransaction->id }}</p>
                    <p><strong>{{ __('Customer Name') }}:</strong> {{ $completedTransaction->customer_name }}</p>
                    <p><strong>{{ __('Customer Mobile') }}:</strong> {{ $completedTransaction->customer_mobile_number }}</p>
                    <p><strong>{{ __('Line Mobile Used') }}:</strong> {{ $completedTransaction->line_mobile_number }}</p>
                    <p><strong>{{ __('Amount') }}:</strong> {{ number_format($completedTransaction->amount, 2) }} EGP</p>
                    <p><strong>{{ __('Commission') }}:</strong> {{ number_format($completedTransaction->commission, 2) }} EGP</p>
                    <p><strong>{{ __('Deduction') }}:</strong> {{ number_format($completedTransaction->deduction, 2) }} EGP</p>
                    <p><strong>{{ __('Transaction Type') }}:</strong> {{ $completedTransaction->transaction_type }}</p>
                    <p><strong>{{ __('Payment Method') }}:</strong> {{ $completedTransaction->payment_method }}</p>
                    <p><strong>{{ __('Agent') }}:</strong> {{ $completedTransaction->agent->name ?? '-' }}</p>
                    <p><strong>{{ __('Branch') }}:</strong> {{ $completedTransaction->branch->name ?? '-' }}</p>
                    <p><strong>{{ __('Safe') }}:</strong> {{ $completedTransaction->safe->name ?? '-' }}</p>
                    <p><strong>{{ __('Status') }}:</strong> {{ $completedTransaction->status }}</p>
                    <p><strong>{{ __('Date & Time') }}:</strong> {{ $completedTransaction->transaction_date_time }}</p>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="closeReceiptModal">
                        {{ __('Close') }}
                    </x-secondary-button>
                </div>
            </div>
        @endif
    </x-modal> 