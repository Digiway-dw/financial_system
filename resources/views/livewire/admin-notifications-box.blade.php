<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50">
    <!-- Header Section -->
    <div class="bg-white/70 backdrop-blur-sm border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifications Center</h1>
                    <p class="text-sm text-gray-600">Stay updated with system alerts and important messages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Notification Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Unread</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->unreadNotifications()->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Read</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->readNotifications()->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m3 6V8a1 1 0 00-1-1H5a1 1 0 00-1 1v2m14 0v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8m14 0H4m5 4h6">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Total</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->notifications()->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600">Today</h3>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ auth()->user()->notifications()->whereDate('created_at', today())->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Management Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 overflow-hidden"
            wire:poll.10s>
            <!-- Header with Tabs and Actions -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Message Center</h3>
                            <p class="text-sm text-gray-600">{{ $notifications->count() }} notifications displayed</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Filter Tabs -->
                        <div class="flex space-x-1 bg-gray-100 rounded-xl p-1">
                            <button wire:click="setTab('all')"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'all' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                All
                            </button>
                            <button wire:click="setTab('unread')"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center {{ $tab === 'unread' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                Unread
                                @if (auth()->user()->unreadNotifications()->count() > 0)
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">
                                        {{ auth()->user()->unreadNotifications()->count() }}
                                    </span>
                                @endif
                            </button>
                            <button wire:click="setTab('read')"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $tab === 'read' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                Read
                            </button>
                        </div>

                        <!-- Action Button -->
                        <button wire:click="markAllAsRead"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark All Read
                        </button>
                        <button wire:click="clearAllRead"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 flex items-center ml-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear All Read
                        </button>
                    </div>
                </div>

                <!-- Custom Filters: Type and Date -->
                <div class="mt-4 flex flex-wrap gap-2 items-center">
                    <span class="font-semibold text-gray-700 mr-2">Type:</span>
                    <button wire:click="setTypeFilter('all')" class="px-3 py-1 rounded-lg text-sm font-medium border {{ $typeFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">All</button>
                    <button wire:click="setTypeFilter('send')" class="px-3 py-1 rounded-lg text-sm font-medium border {{ $typeFilter === 'send' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">Send</button>
                    <button wire:click="setTypeFilter('receive')" class="px-3 py-1 rounded-lg text-sm font-medium border {{ $typeFilter === 'receive' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">Receive</button>
                    <button wire:click="setTypeFilter('cash')" class="px-3 py-1 rounded-lg text-sm font-medium border {{ $typeFilter === 'cash' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">Cash</button>
                    <button wire:click="setTypeFilter('others')" class="px-3 py-1 rounded-lg text-sm font-medium border {{ $typeFilter === 'others' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">Others</button>

                    <span class="ml-6 font-semibold text-gray-700">From:</span>
                    <input type="date" wire:model.lazy="fromDate" wire:change="setFromDate($event.target.value)" class="border border-gray-300 rounded-lg px-2 py-1 text-sm">
                    <span class="font-semibold text-gray-700">To:</span>
                    <input type="date" wire:model.lazy="toDate" wire:change="setToDate($event.target.value)" class="border border-gray-300 rounded-lg px-2 py-1 text-sm">
                </div>
            </div>

            <!-- Notifications List -->
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @if ($notifications->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications found</h3>
                        <p class="text-sm text-gray-500">
                            @if ($tab === 'unread')
                                You're all caught up! No unread notifications.
                            @elseif($tab === 'read')
                                No read notifications available.
                            @else
                                No notifications have been received yet.
                            @endif
                        </p>
                    </div>
                @else
                    @foreach ($notifications as $notification)
                        <div
                            class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50/30' }}">
                            <div class="flex items-start space-x-4">
                                <!-- Notification Icon -->
                                <div class="flex-shrink-0">
                                    @if ($notification->read_at)
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="w-10 h-10 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full flex items-center justify-center animate-pulse">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <!-- Notification Title -->
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">
                                                {{ $notification->data['title'] ?? 'System Notification' }}
                                            </h4>

                                            <!-- Notification Message -->
                                            <p class="text-sm text-gray-700 mb-2 leading-relaxed">
                                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                            </p>

                                            <!-- Notification Meta -->
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>

                                                @if ($notification->data['type'] ?? null)
                                                    @php
                                                        $typeColors = [
                                                            'info' => 'bg-blue-100 text-blue-800',
                                                            'success' => 'bg-green-100 text-green-800',
                                                            'warning' => 'bg-yellow-100 text-yellow-800',
                                                            'error' => 'bg-red-100 text-red-800',
                                                        ];
                                                        $typeClass =
                                                            $typeColors[$notification->data['type']] ??
                                                            'bg-gray-100 text-gray-800';
                                                    @endphp
                                                    <span
                                                        class="px-2 py-0.5 rounded-full text-xs font-medium {{ $typeClass }}">
                                                        {{ ucfirst($notification->data['type']) }}
                                                    </span>
                                                @endif

                                                @if ($notification->read_at)
                                                    <span class="flex items-center text-green-600">
                                                        <svg class="w-3 h-3 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Read {{ $notification->read_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-col space-y-2 ml-4">
                                            @if (($notification->data['type'] ?? null) === 'withdrawal')
                                                <div class="flex flex-col items-end space-y-1">
                                                    <button wire:click="approveNotification('{{ $notification->id }}')"
                                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg shadow transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Accept Withdrawal
                                                    </button>
                                                    <button wire:click="rejectNotification('{{ $notification->id }}')"
                                                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Reject Withdrawal
                                                    </button>
                                                    <a href="{{ route('transactions.cash.waiting-approval', ['cashTransaction' => $notification->data['transaction_id'] ?? 0]) }}"
                                                        wire:click.prevent="viewNotification('{{ $notification->id }}')"
                                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        View
                                                    </a>
                                                </div>
                                            @elseif (($notification->data['type'] ?? null) === 'pending_transaction')
                                                <button wire:click="approveNotification('{{ $notification->id }}')"
                                                    class="inline-flex items-center px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Approve
                                                </button>
                                                <button wire:click="rejectNotification('{{ $notification->id }}')"
                                                    class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-lg transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Reject
                                                </button>
                                            @else
                                                <a href="{{ route('notifications.show', $notification->id) }}"
                                                    class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                        </path>
                                                    </svg>
                                                    View
                                                </a>
                                            @endif
                                            @if ($notification->read_at === null && !in_array(($notification->data['type'] ?? null), ['pending_transaction', 'withdrawal']))
                                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                                    class="inline-flex items-center px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Mark Read
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Footer with Auto-refresh Info -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Auto-refreshes every 10 seconds</span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Live updates
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
