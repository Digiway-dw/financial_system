<form wire:submit.prevent="createTransaction" class="mt-6 space-y-6">
    <!-- (Copy all form fields from create.blade.php except the outer <div> and <h3>) -->
    <!-- ... existing fields ... -->
    @if(empty($hideTransactionType))
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
    @endif
    <!-- (Rest of the form fields and submit button) -->
    @include('livewire.transactions.create-fields')
</form> 