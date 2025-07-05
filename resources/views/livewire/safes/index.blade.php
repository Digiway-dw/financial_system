<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Safe List</h3>

    <div class="mt-4 mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Safes are automatically created when branches are created. To add a new safe, create a new branch.</p>
    </div>

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-900 rounded shadow flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <!-- Each filter input: add w-full md:w-40 or md:w-36 as appropriate -->
        <div class="w-full md:w-40">
            <x-input-label for="name" :value="__('Safe Name')" />
            <x-text-input id="name" type="text" wire:model.defer="name" class="w-full" />
        </div>
        <!-- Repeat for other filters, adjusting widths as needed -->
        <div class="w-full md:w-auto">
            <x-primary-button wire:click="filter" class="w-full md:w-auto">{{ __('Filter') }}</x-primary-button>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs md:text-sm">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Branch</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($safes as $safe)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $safe['name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($safe['current_balance'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $safe['branch']['name'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $safe['description'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                            <a href="{{ route('safes.edit', $safe['id']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">Edit</a>
                            <button wire:click="deleteSafe('{{ $safe['id'] }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No safes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-8">Client Wallets</h3>

    <div class="mt-4">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Customer Name</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Mobile Number</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($clients as $client)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $client['name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $client['mobile_number'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($client['balance'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                            <!-- Add actions for client wallets here if needed -->
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">View Transactions</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No client wallets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
