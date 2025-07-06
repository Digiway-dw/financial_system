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
            @if (Auth::user()->hasRole('admin'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4">Admin Quick Access</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="{{ route('users.index') }}"
                                class="bg-indigo-100 hover:bg-indigo-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-users class="w-8 h-8 mx-auto text-indigo-600" />
                                <span class="block mt-2 font-medium">Users</span>
                            </a>
                            <a href="{{ route('lines.index') }}"
                                class="bg-yellow-100 hover:bg-yellow-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-phone class="w-8 h-8 mx-auto text-yellow-600" />
                                <span class="block mt-2 font-medium">Lines</span>
                            </a>
                            <a href="{{ route('customers.index') }}"
                                class="bg-blue-100 hover:bg-blue-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-identification class="w-8 h-8 mx-auto text-blue-600" />
                                <span class="block mt-2 font-medium">Customers</span>
                            </a>
                            <a href="{{ route('branches.index') }}"
                                class="bg-green-100 hover:bg-green-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-building-office-2 class="w-8 h-8 mx-auto text-green-600" />
                                <span class="block mt-2 font-medium">Branches</span>
                            </a>
                            <a href="{{ route('safes.index') }}"
                                class="bg-red-100 hover:bg-red-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-banknotes class="w-8 h-8 mx-auto text-red-600" />
                                <span class="block mt-2 font-medium">Safes</span>
                            </a>
                            <a href="{{ route('transactions.index') }}"
                                class="bg-purple-100 hover:bg-purple-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-receipt-percent class="w-8 h-8 mx-auto text-purple-600" />
                                <span class="block mt-2 font-medium">Transactions</span>
                            </a>
                            <a href="{{ route('reports.index') }}"
                                class="bg-emerald-100 hover:bg-emerald-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-chart-bar class="w-8 h-8 mx-auto text-emerald-600" />
                                <span class="block mt-2 font-medium">Reports</span>
                            </a>
                            <a href="{{ route('audit-log.index') }}"
                                class="bg-amber-100 hover:bg-amber-200 p-4 rounded-lg text-center transition">
                                <x-heroicon-o-document-magnifying-glass class="w-8 h-8 mx-auto text-amber-600" />
                                <span class="block mt-2 font-medium">Audit Log</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

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
