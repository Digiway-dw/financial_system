<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-lg">
            <div class="text-gray-900">
                <h2 class="font-bold text-2xl tracking-wide">
                    {{ __('Welcome Back,') }} {{ Auth::user()->name }}!
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('You are logged in as a') }} {{ Auth::user()->roles->first()->name ?? 'User' }}
                </p>
            </div>
            <div class="text-gray-900 text-right">
                <p class="text-lg font-semibold">{{ \Carbon\Carbon::now()->format('l, F j, Y') }}</p>
                <p class="text-sm text-gray-600" x-data="{ time: '' }" x-init="setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); }, 1000)">
                    <span x-text="time"></span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex gap-4 mb-8">
                        <a href="{{ route('transactions.cash') }}"
                            class="px-6 py-3 rounded bg-blue-500 text-white font-bold flex items-center gap-2">
                            <x-heroicon-o-currency-dollar class="w-5 h-5" /> كاش
                        </a>
                        <a href="{{ route('transactions.receive') }}"
                            class="px-6 py-3 rounded bg-green-600 text-white font-bold flex items-center gap-2">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5" /> استقبال
                        </a>
                        <a href="{{ route('transactions.send') }}"
                            class="px-6 py-3 rounded bg-red-500 text-white font-bold flex items-center gap-2">
                            <x-heroicon-o-paper-airplane class="w-5 h-5" /> إرسال
                        </a>
                    </div>
                    @livewire('dashboard')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
