<div class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-50">
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="bg-white/90 rounded-2xl shadow-xl p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Notification Details</h1>
                </div>
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
            </div>
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $notification->data['title'] ?? 'System Notification' }}</h2>
                <p class="text-gray-700 text-base mb-4">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                <div class="flex items-center space-x-4 text-xs text-gray-500 mb-2">
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $notification->created_at->format('Y-m-d H:i') }}
                    </span>
                    @if ($notification->read_at)
                        <span class="flex items-center text-green-600">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Read {{ $notification->read_at->diffForHumans() }}
                        </span>
                    @else
                        <span class="flex items-center text-blue-600">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Unread
                        </span>
                    @endif
                    @if ($notification->data['type'] ?? null)
                        @php
                            $typeColors = [
                                'info' => 'bg-blue-100 text-blue-800',
                                'success' => 'bg-green-100 text-green-800',
                                'warning' => 'bg-yellow-100 text-yellow-800',
                                'error' => 'bg-red-100 text-red-800',
                            ];
                            $typeClass = $typeColors[$notification->data['type']] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $typeClass }}">
                            {{ ucfirst($notification->data['type']) }}
                        </span>
                    @endif
                </div>
            </div>
            @if (!empty($notification->data['transaction_id']))
                <div class="mb-4">
                    <a href="{{ route('transactions.edit', $notification->data['transaction_id']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        View Transaction
                    </a>
                </div>
            @endif
        </div>
    </div>
</div> 