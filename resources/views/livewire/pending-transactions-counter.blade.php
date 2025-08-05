<a href="{{ route('transactions.pending') }}" class="relative ml-4 group" title="Pending Transactions" wire:poll.10s>
    <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl flex items-center justify-center hover:scale-105 transition-transform">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @if($pendingCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow">{{ $pendingCount }}</span>
        @endif
    </div>
</a> 