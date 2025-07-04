<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Customer List</h3>

    @can('manage-customers')
        <div class="mt-4 mb-4">
            <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Add New Customer</a>
        </div>
    @endcan

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-900 rounded shadow flex flex-col md:flex-row md:flex-nowrap flex-wrap gap-2 items-end">
        <div class="w-full md:w-32">
            <x-input-label for="name" :value="__('Name')" class="text-xs" />
            <x-text-input id="name" type="text" wire:model.debounce.400ms="name" class="w-full h-8 text-xs px-2" />
        </div>
        <div class="w-full md:w-32">
            <x-input-label for="phone" :value="__('Phone Number')" class="text-xs" />
            <x-text-input id="phone" type="text" wire:model.defer="phone" class="w-full h-8 text-xs px-2" />
        </div>
        <div class="w-full md:w-32">
            <x-input-label for="code" :value="__('Customer Code')" class="text-xs" />
            <x-text-input id="code" type="text" wire:model.defer="code" class="w-full h-8 text-xs px-2" />
        </div>
        <div class="w-full md:w-32">
            <x-input-label for="region" :value="__('Region/Area')" class="text-xs" />
            <x-text-input id="region" type="text" wire:model.defer="region" class="w-full h-8 text-xs px-2" />
        </div>
        <div class="w-full md:w-36">
            <x-input-label for="date_added_start" :value="__('Date Added (Start)')" class="text-xs" />
            <x-text-input id="date_added_start" type="date" wire:model.defer="date_added_start" class="w-full h-8 text-xs px-2" />
        </div>
        <div class="w-full md:w-36">
            <x-input-label for="date_added_end" :value="__('Date Added (End)')" class="text-xs" />
            <x-text-input id="date_added_end" type="date" wire:model.defer="date_added_end" class="w-full h-8 text-xs px-2" />
        </div>
        <div class="w-full md:w-auto">
            <x-primary-button wire:click="filter" class="w-full md:w-auto h-8 text-xs px-2">{{ __('Filter') }}</x-primary-button>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs md:text-sm">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Mobile Number</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Customer Code</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Wallet</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ $customer['name'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $customer['mobile_number'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $customer['customer_code'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">
                            @if(!empty($customer['is_client']) && $customer['is_client'])
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-500 text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-red-500 text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                            <a href="{{ route('customers.view', $customer['id']) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">View</a>
                            <a href="{{ route('customers.edit', $customer['id']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">Edit</a>
                            <button wire:click="deleteCustomer('{{ $customer['id'] }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
