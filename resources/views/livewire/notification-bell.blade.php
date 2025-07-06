<div class="relative">
    <a href="{{ route('notifications.index') }}" class="flex items-center" title="Notifications">
        <!-- Bell Icon -->
        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
            stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9" />
        </svg>
        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5 font-bold animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
</div>
