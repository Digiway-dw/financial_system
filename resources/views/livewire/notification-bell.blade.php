<div class="relative">
    <a href="{{ route('notifications.index') }}"
        class="flex items-center p-2 rounded-xl hover:bg-gray-100 transition-all duration-200 group"
        title="Notifications">
        <!-- Enhanced Bell Icon -->
        <div class="relative">
            <svg class="w-6 h-6 text-gray-600 group-hover:text-amber-600 transition-colors duration-200" fill="none"
                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9" />
            </svg>

            @if ($unreadCount > 0)
                <!-- Enhanced notification badge -->
                <span
                    class="absolute -top-2 -right-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full px-1.5 py-0.5 font-bold animate-pulse shadow-lg ring-2 ring-white">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>

                <!-- Pulsing ring effect for urgent notifications -->
                @if ($unreadCount > 5)
                    <span
                        class="absolute -top-2 -right-2 bg-red-500 rounded-full px-1.5 py-0.5 animate-ping opacity-75"></span>
                @endif
            @endif
        </div>

        <!-- Tooltip for desktop -->
        <div
            class="hidden lg:block absolute left-1/2 transform -translate-x-1/2 -top-12 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
            @if ($unreadCount > 0)
                {{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}
            @else
                No new notifications
            @endif
            <div
                class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900">
            </div>
        </div>
    </a>
</div>
