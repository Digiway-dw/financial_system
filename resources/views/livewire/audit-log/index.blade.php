<div class="p-4">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Audit Log</h3>

    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-900 rounded shadow flex flex-col md:flex-row flex-wrap gap-4 items-end">
        <!-- Each filter input: add w-full md:w-40 or md:w-36 as appropriate -->
        <div class="w-full md:w-40">
            <x-input-label for="subject" :value="__('Subject')" />
            <x-text-input id="subject" type="text" wire:model.defer="subject" class="w-full" />
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
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">Timestamp
                        @if ($sortField === 'created_at')
                            <span>{!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider cursor-pointer" wire:click="sortBy('log_name')">Log Name
                        @if ($sortField === 'log_name')
                            <span>{!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider cursor-pointer" wire:click="sortBy('event')">Event Type
                        @if ($sortField === 'event')
                            <span>{!! $sortDirection === 'asc' ? '&uarr;' : '&darr;' !!}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Causer</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Properties</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                @forelse ($activities as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $activity->log_name }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $activity->event }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $activity->description }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">
                            @if ($activity->causer)
                                {{ $activity->causer->name ?? 'N/A' }} ({{ class_basename($activity->causer_type) }}: {{ $activity->causer_id }})
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">
                            @if ($activity->subject)
                                {{ class_basename($activity->subject_type) }} ({{ $activity->subject_id }})
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400">
                            @if (!empty($activity->properties))
                                <pre class="overflow-auto text-xs">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 dark:text-gray-400 text-center">No audit log entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </div>
</div> 