<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-gradient-to-r from-purple-600 to-blue-600 p-4 rounded-lg shadow-lg">
            <div class="text-white">
                <h2 class="font-bold text-2xl tracking-wide">
                    {{ __('Welcome Back,') }} {{ Auth::user()->name }}!
                </h2>
                <p class="text-sm opacity-90 mt-1">
                    {{ __("You are logged in as a") }} {{ Auth::user()->roles->first()->name ?? 'User' }}
                </p>
            </div>
            <div class="text-white text-right">
                <p class="text-lg font-semibold">{{ \Carbon\Carbon::now()->format('l, F j, Y') }}</p>
                <p class="text-sm opacity-90" x-data="{ time: '' }" x-init="setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }); }, 1000)">
                    <span x-text="time"></span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @livewire('dashboard')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
