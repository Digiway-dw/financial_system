<x-app-layout>
    <x-slot name="header">
        <div
            class="flex justify-between items-center bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-xl shadow-md">
            <div>
                <h2 class="font-bold text-2xl text-white tracking-wide flex items-center">
                    <span class="bg-white text-blue-600 p-2 rounded-lg mr-3 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </span>
                    {{ __('Welcome Back,') }} <span
                        class="text-blue-100 ml-2 font-extrabold">{{ Auth::user()->name }}</span>!
                </h2>
                <p class="text-sm text-blue-100 mt-2 flex items-center">
                    <span class="inline-block h-2 w-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                    {{ __('You are logged in as') }}
                    <span class="font-medium text-white ml-1 bg-blue-700/30 px-3 py-0.5 rounded-full">
                        {{ Auth::user()->roles->first()->name ?? 'User' }}
                    </span>
                </p>
            </div>
            <div class="text-right">
                <div class="bg-white p-4 rounded-lg shadow-md border border-blue-100">
                    <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::now()->format('l, F j, Y') }}</p>
                    <p class="text-sm text-blue-600 font-medium" x-data="{ time: '' }" x-init="setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); }, 1000)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="time"></span>
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()->hasRole('admin'))
                <div class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                <div class="bg-blue-500 text-white p-2 rounded-lg mr-3 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                Admin Quick Access
                            </h3>
                            <span
                                class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full border border-blue-100">
                                Administrator Panel
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <a href="{{ route('users.index') }}"
                                class="bg-gradient-to-br from-indigo-500/10 to-indigo-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-indigo-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-indigo-100 group-hover:border-indigo-200">
                                    <x-heroicon-o-users class="w-8 h-8 text-indigo-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Users</span>
                            </a>
                            <a href="{{ route('lines.index') }}"
                                class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-yellow-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-yellow-100 group-hover:border-yellow-200">
                                    <x-heroicon-o-phone class="w-8 h-8 text-yellow-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Lines</span>
                            </a>
                            <a href="{{ route('customers.index') }}"
                                class="bg-gradient-to-br from-blue-500/10 to-blue-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-blue-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-blue-100 group-hover:border-blue-200">
                                    <x-heroicon-o-identification class="w-8 h-8 text-blue-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Customers</span>
                            </a>
                            <a href="{{ route('branches.index') }}"
                                class="bg-gradient-to-br from-green-500/10 to-green-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-green-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-green-100 group-hover:border-green-200">
                                    <x-heroicon-o-building-office-2 class="w-8 h-8 text-green-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Branches</span>
                            </a>
                            <a href="{{ route('safes.index') }}"
                                class="bg-gradient-to-br from-red-500/10 to-red-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-red-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-red-100 group-hover:border-red-200">
                                    <x-heroicon-o-banknotes class="w-8 h-8 text-red-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Safes</span>
                            </a>
                            <a href="{{ route('transactions.index') }}"
                                class="bg-gradient-to-br from-purple-500/10 to-purple-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-purple-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-purple-100 group-hover:border-purple-200">
                                    <x-heroicon-o-receipt-percent class="w-8 h-8 text-purple-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Transactions</span>
                            </a>
                            <a href="{{ route('reports.index') }}"
                                class="bg-gradient-to-br from-emerald-500/10 to-emerald-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-emerald-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-emerald-100 group-hover:border-emerald-200">
                                    <x-heroicon-o-chart-bar class="w-8 h-8 text-emerald-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Reports</span>
                            </a>
                            <a href="{{ route('audit-log.index') }}"
                                class="bg-gradient-to-br from-amber-500/10 to-amber-600/20 p-5 rounded-xl text-center transition-all duration-300 border border-amber-100 group hover:shadow-lg hover:scale-[1.02] transform">
                                <div
                                    class="bg-white rounded-full p-3 mx-auto w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg mb-3 border border-amber-100 group-hover:border-amber-200">
                                    <x-heroicon-o-document-magnifying-glass class="w-8 h-8 text-amber-600" />
                                </div>
                                <span class="block font-medium text-gray-800">Audit Log</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="{{ route('transactions.cash') }}"
                            class="px-6 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 transform hover:translate-y-[-2px]">
                            <x-heroicon-o-currency-dollar class="w-5 h-5" /> كاش
                        </a>
                        <a href="{{ route('transactions.receive') }}"
                            class="px-6 py-3 rounded-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 transform hover:translate-y-[-2px]">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5" /> استقبال
                        </a>
                        <a href="{{ route('transactions.send') }}"
                            class="px-6 py-3 rounded-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-300 transform hover:translate-y-[-2px]">
                            <x-heroicon-o-paper-airplane class="w-5 h-5" /> إرسال
                        </a>
                    </div>

                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-b from-blue-100 to-transparent h-20 rounded-t-xl -mx-6 -mt-6">
                        </div>
                        <div class="relative">
                            @livewire('dashboard')
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gradient-to-br from-green-400 to-green-500 p-3 mr-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">System Status</h4>
                            <p class="text-sm text-gray-500">All systems operational</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gradient-to-br from-blue-400 to-blue-500 p-3 mr-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">Need Help?</h4>
                            <p class="text-sm text-gray-500">Contact support team</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01]">
                    <div class="flex items-center">
                        <div class="rounded-full bg-gradient-to-br from-purple-400 to-purple-500 p-3 mr-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-lg">Documentation</h4>
                            <p class="text-sm text-gray-500">View user guides & manuals</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
