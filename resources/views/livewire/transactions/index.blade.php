<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Transaction List</h3>

    <div class="mt-4 mb-4">
        <!-- Updated: Use new transaction type buttons -->
        <a href="{{ route('transactions.send') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase mr-2">Send</a>
        <a href="{{ route('transactions.receive') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase mr-2">Receive</a>
        <a href="{{ route('transactions.cash') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase">Cash</a>
    </div>

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-900 rounded shadow flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <div class="w-full md:w-40">
            <x-input-label for="customer_code" :value="__('Customer Code')" />
            <x-text-input id="customer_code" type="text" wire:model.defer="customer_code" class="w-full" />
        </div>
        <div class="w-full md:w-40">
            <x-input-label for="receiver_mobile" :value="__('Receiver Mobile')" />
            <x-text-input id="receiver_mobile" type="text" wire:model.defer="receiver_mobile" class="w-full" />
        </div>
        <div class="w-full md:w-40">
            <x-input-label for="transfer_line" :value="__('Transfer Line Number')" />
            <x-text-input id="transfer_line" type="text" wire:model.defer="transfer_line" class="w-full" />
        </div>
        <div class="w-full md:w-32">
            <x-input-label for="amount" :value="__('Amount')" />
            <x-text-input id="amount" type="number" step="0.01" wire:model.defer="amount" class="w-full" />
        </div>
        <div class="w-full md:w-32">
            <x-input-label for="commission" :value="__('Commission')" />
            <x-text-input id="commission" type="number" step="0.01" wire:model.defer="commission" class="w-full" />
        </div>
        <div class="w-full md:w-40">
            <x-input-label for="transaction_type" :value="__('Transaction Type')" />
            <select id="transaction_type" wire:model.defer="transaction_type" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                <option value="">All</option>
                <option value="Transfer">Transfer</option>
                <option value="Deposit">Deposit</option>
                <option value="Withdrawal">Withdrawal</option>
                <!-- Add more types as needed -->
            </select>
        </div>
        <div class="w-full md:w-36">
            <x-input-label for="start_date" :value="__('Start Date')" />
            <x-text-input id="start_date" type="date" wire:model.defer="start_date" class="w-full" />
        </div>
        <div class="w-full md:w-36">
            <x-input-label for="end_date" :value="__('End Date')" />
            <x-text-input id="end_date" type="date" wire:model.defer="end_date" class="w-full" />
        </div>
        <div class="w-full md:w-40">
            <x-input-label for="employee_ids" :value="__('Employee(s)')" />
            <select id="employee_ids" wire:model.defer="employee_ids" multiple class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                @foreach (\App\Domain\Entities\User::all() as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-40">
            <x-input-label for="branch_ids" :value="__('Branch(es)')" />
            <select id="branch_ids" wire:model.defer="branch_ids" multiple class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                @foreach (\App\Models\Domain\Entities\Branch::all() as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-auto">
            <x-primary-button wire:click="filter" class="w-full md:w-auto">{{ __('Filter') }}</x-primary-button>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs md:text-sm">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Customer Name</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Mobile Number</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Commission</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Agent</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $transaction['customer_name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['customer_mobile_number'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($transaction['amount'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($transaction['commission'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['transaction_type'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['agent_name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $transaction['status'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                            <a href="{{ route('transactions.edit', $transaction['id']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">Edit</a>
                            <button wire:click="deleteTransaction('{{ $transaction['id'] }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
