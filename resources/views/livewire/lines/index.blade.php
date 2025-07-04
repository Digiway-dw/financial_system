<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Line List</h3>

    <div class="mt-4 mb-4">
        <a href="{{ route('lines.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Add New Line</a>
    </div>

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-900 rounded shadow flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <!-- Each filter input: add w-full md:w-40 or md:w-36 as appropriate -->
        <div class="w-full md:w-40">
            <x-input-label for="number" :value="__('Line Number')" />
            <x-text-input id="number" type="text" wire:model.defer="number" class="w-full" />
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
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('mobile_number')" style="cursor:pointer">Mobile Number @if($sortField==='mobile_number')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('current_balance')" style="cursor:pointer">Current Balance @if($sortField==='current_balance')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('daily_limit')" style="cursor:pointer">Daily Limit @if($sortField==='daily_limit')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('monthly_limit')" style="cursor:pointer">Monthly Limit @if($sortField==='monthly_limit')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('daily_usage')" style="cursor:pointer">Daily Usage @if($sortField==='daily_usage')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('monthly_usage')" style="cursor:pointer">Monthly Usage @if($sortField==='monthly_usage')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('network')" style="cursor:pointer">Network @if($sortField==='network')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('status')" style="cursor:pointer">Status @if($sortField==='status')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider" wire:click="sortBy('branch_id')" style="cursor:pointer">Branch @if($sortField==='branch_id')<span>{{ $sortDirection==='asc'?'▲':'▼' }}</span>@endif</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($lines as $line)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100 flex items-center gap-1">
                            @if($line['status'] === 'active')
                                <span class="inline-flex items-center justify-center w-3 h-3 rounded-full bg-green-500 text-white">
                                    <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-3 h-3 rounded-full bg-red-500 text-white">
                                    <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </span>
                            @endif
                            {{ $line['mobile_number'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['current_balance'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['daily_limit'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['monthly_limit'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['daily_usage'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ number_format($line['monthly_usage'], 2) }} EGP</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $line['network'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $line['status'] }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $line['branch']['name'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-right">
                            <a href="{{ route('lines.edit', $line['id']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">Edit</a>
                            <a href="{{ route('lines.transfer', $line['id']) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 ms-3">Transfer</a>
                            <a href="{{ route('lines.change-provider', $line['id']) }}" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-600 ms-3">Change Provider</a>
                            <button wire:click="toggleStatus('{{ $line['id'] }}')" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600 ms-3">{{ $line['status'] === 'active' ? 'Deactivate' : 'Activate' }}</button>
                            <button wire:click="deleteLine('{{ $line['id'] }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600 ms-3">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No lines found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
