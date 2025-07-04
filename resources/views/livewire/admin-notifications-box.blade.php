<div wire:poll.10s class="mb-6">
    <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-2">
            <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100">Notifications</h4>
            <div class="flex items-center gap-2">
                <button wire:click="setTab('all')" class="px-3 py-1 rounded text-sm font-semibold focus:outline-none {{ $tab === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">All</button>
                <button wire:click="setTab('unread')" class="px-3 py-1 rounded text-sm font-semibold focus:outline-none {{ $tab === 'unread' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">Unread</button>
                <button wire:click="setTab('read')" class="px-3 py-1 rounded text-sm font-semibold focus:outline-none {{ $tab === 'read' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200' }}">Read</button>
                <button wire:click="markAllAsRead" class="ml-4 px-3 py-1 rounded text-sm font-semibold bg-green-600 text-white hover:bg-green-700 focus:outline-none">Mark all as Read</button>
            </div>
        </div>
        @if($notifications->isEmpty())
            <div class="text-gray-500 dark:text-gray-400">No notifications found.</div>
        @else
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <li class="py-3 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <div>
                            <span class="inline-block w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-700 flex items-center justify-center">
                                <x-heroicon-o-bell class="w-5 h-5 text-indigo-600 dark:text-indigo-200" />
                            </span>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $notification->data['message'] ?? 'Notification' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 items-end">
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-bold">View</a>
                            @endif
                            @if($notification->read_at === null)
                                <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-green-600 dark:text-green-400 hover:underline font-bold">Mark as Read</button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
