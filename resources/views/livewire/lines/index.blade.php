<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Lines Management</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage your financial lines, balances, and limits</p>
                </div>
                @php
                    $canManageLines = auth()->user()->hasRole('admin') || auth()->user()->hasRole('general_supervisor');
                @endphp
                @if ($canManageLines)
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('lines.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Line
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Filter Options</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="number" :value="__('Line Number')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="number" type="text" wire:model.defer="number"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Search by number..." />
                </div>
                <div>
                    <x-primary-button wire:click="filter"
                        class="w-full bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 rounded-lg py-2.5 font-medium shadow-sm hover:shadow-md transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Apply Filter
                    </x-primary-button>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Lines Overview</h3>
                <p class="text-sm text-gray-600 mt-1">Track and manage all your financial lines</p>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[120px]" wire:click="sortBy('mobile_number')">
                                <div class="flex items-center space-x-1">
                                    <span>Mobile Number</span>
                                    @if ($sortField === 'mobile_number')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[90px]" wire:click="sortBy('current_balance')">
                                <div class="flex items-center space-x-1">
                                    <span>Current Balance</span>
                                    @if ($sortField === 'current_balance')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[80px]" wire:click="sortBy('daily_limit')">
                                <div class="flex items-center space-x-1">
                                    <span>Daily Limit</span>
                                    @if ($sortField === 'daily_limit')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[90px]" wire:click="sortBy('monthly_limit')">
                                <div class="flex items-center space-x-1">
                                    <span>Monthly Limit</span>
                                    @if ($sortField === 'monthly_limit')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[80px]" wire:click="sortBy('daily_usage')">
                                <div class="flex items-center space-x-1">
                                    <span>Daily Usage</span>
                                    @if ($sortField === 'daily_usage')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[90px]" wire:click="sortBy('monthly_usage')">
                                <div class="flex items-center space-x-1">
                                    <span>Monthly Usage</span>
                                    @if ($sortField === 'monthly_usage')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[70px]" wire:click="sortBy('network')">
                                <div class="flex items-center space-x-1">
                                    <span>Network</span>
                                    @if ($sortField === 'network')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[70px]" wire:click="sortBy('status')">
                                <div class="flex items-center space-x-1">
                                    <span>Status</span>
                                    @if ($sortField === 'status')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[70px]" wire:click="sortBy('branch_id')">
                                <div class="flex items-center space-x-1">
                                    <span>Branch</span>
                                    @if ($sortField === 'branch_id')
                                        <span class="text-blue-600">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th class="px-3 py-2 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider min-w-[70px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        @forelse ($lines as $line)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $line['monthly_limit_row_class'] }}">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($line['status'] === 'active')
                                            <div
                                                class="flex-shrink-0 w-3 h-3 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                                            </div>
                                        @else
                                            <div
                                                class="flex-shrink-0 w-3 h-3 bg-red-100 rounded-full flex items-center justify-center mr-2">
                                                <div class="w-1.5 h-1.5 bg-red-500 rounded-full"></div>
                                            </div>
                                        @endif
                                        <div class="text-xs font-medium text-gray-900">{{ $line['mobile_number'] }}</div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($line['current_balance'], 2) }}</div>
                                    <div class="text-xs text-gray-500">EGP</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($line['daily_limit'], 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">EGP</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($line['monthly_limit'], 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">EGP</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap {{ $line['daily_usage_class'] }}">
                                    <div class="text-sm text-gray-900">{{ number_format($line['daily_usage'], 2) }}</div>
                                    <div class="text-xs text-gray-500">EGP</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($line['monthly_usage'], 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">EGP</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $line['network'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if ($line['status'] === 'active')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ $line['branch']['name'] ?? 'N/A' }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('lines.view', $line['id']) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors duration-150">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        View
                                    </a>
                                    @php
                                        $canManageLines = auth()->user()->hasRole('admin') || auth()->user()->hasRole('general_supervisor');
                                    @endphp
                                    @if ($canManageLines)
                                        <a href="{{ route('lines.edit', $line['id']) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        <button wire:click="toggleStatus('{{ $line['id'] }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-medium rounded-md transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 9V3m4 6V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Toggle Status
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No lines found</h3>
                                        <p class="text-sm text-gray-500">Get started by creating your first line.</p>
                                        @if ($canManageLines)
                                            <a href="{{ route('lines.create') }}"
                                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-150">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Create Line
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
