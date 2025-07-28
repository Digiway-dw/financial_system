<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1
                                class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                سحب الأموال
                            </h1>
                            <p class="text-slate-600 mt-2 text-lg">اختر نوع السحب وأدخل التفاصيل المطلوبة</p>
                        </div>
                        <div class="hidden sm:block">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 rounded-r-xl shadow-lg backdrop-blur-sm">
                    <div class="flex items-center p-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-semibold">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('message'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-400 rounded-r-xl shadow-lg backdrop-blur-sm">
                    <div class="flex items-center p-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-emerald-800 font-semibold">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl shadow-lg backdrop-blur-sm">
                    <div class="p-4">
                        <div class="flex items-center mb-3">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <h3 class="ml-3 text-amber-800 font-semibold">يرجى تصحيح الأخطاء التالية:</h3>
                        </div>
                        <ul class="list-disc list-inside text-amber-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <!-- Withdrawal Type Selector -->
                <div class="p-6 border-b border-slate-200/50">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">نوع السحب</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <button wire:click="$set('withdrawalType', 'direct')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $withdrawalType === 'direct' ? 'border-blue-500 bg-blue-50 shadow-lg shadow-blue-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $withdrawalType === 'direct' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $withdrawalType === 'direct' ? 'text-blue-700' : 'text-slate-700' }}">Direct</span>
                            </div>
                        </button>

                        <button wire:click="$set('withdrawalType', 'client_wallet')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $withdrawalType === 'client_wallet' ? 'border-emerald-500 bg-emerald-50 shadow-lg shadow-emerald-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $withdrawalType === 'client_wallet' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $withdrawalType === 'client_wallet' ? 'text-emerald-700' : 'text-slate-700' }}">Client
                                    Wallet</span>
                            </div>
                        </button>

                        <button wire:click="$set('withdrawalType', 'user')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $withdrawalType === 'user' ? 'border-purple-500 bg-purple-50 shadow-lg shadow-purple-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $withdrawalType === 'user' ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $withdrawalType === 'user' ? 'text-purple-700' : 'text-slate-700' }}">User</span>
                            </div>
                        </button>

                        <button wire:click="$set('withdrawalType', 'admin')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $withdrawalType === 'admin' ? 'border-orange-500 bg-orange-50 shadow-lg shadow-orange-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $withdrawalType === 'admin' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $withdrawalType === 'admin' ? 'text-orange-700' : 'text-slate-700' }}">Admin</span>
                            </div>
                        </button>

                        <button wire:click="$set('withdrawalType', 'branch')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $withdrawalType === 'branch' ? 'border-indigo-500 bg-indigo-50 shadow-lg shadow-indigo-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $withdrawalType === 'branch' ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $withdrawalType === 'branch' ? 'text-indigo-700' : 'text-slate-700' }}">Branch</span>
                            </div>
                        </button>

                        @if($showExpenseWithdrawal)
                        <button wire:click="$set('withdrawalType', 'expense')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $withdrawalType === 'expense' ? 'border-amber-500 bg-amber-50 shadow-lg shadow-amber-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $withdrawalType === 'expense' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 4h6a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $withdrawalType === 'expense' ? 'text-amber-700' : 'text-slate-700' }}">Expense</span>
                            </div>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Form Section -->
                <form wire:submit.prevent="submitWithdrawal" class="p-6 space-y-6">

                    <!-- Common Fields for most withdrawal types -->
                    @if ($withdrawalType !== 'client_wallet' && $withdrawalType !== 'branch' && $withdrawalType !== 'expense')
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Safe Selection -->
                            <div class="space-y-2">
                                <label for="safeId" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    اختيار الخزنة
                                </label>
                                <select id="safeId" wire:model="safeId"
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 bg-white text-slate-900">
                                    @foreach ($this->safes ?? [] as $safe)
                                        <option value="{{ $safe['id'] }}">{{ $safe['name'] }} - الرصيد:
                                            {{ format_int($safe['current_balance']) }} ج.م</option>
                                    @endforeach
                                </select>
                                @error('safeId')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount Field -->
                            <div class="space-y-2">
                                <label for="amount" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    مبلغ السحب
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 text-sm font-medium">ج.م</span>
                                    </div>
                                    <input type="text" wire:model="amount"
                                        id="amount"
                                        class="w-full pl-12 pr-4 py-3 rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 bg-white text-slate-900"
                                        placeholder="0.00">
                                </div>
                                @error('amount')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Direct Withdrawal Specific Fields -->
                    @if ($withdrawalType === 'direct')
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="customerName"
                                    class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    اسم المستلم
                                </label>
                                <input type="text" wire:model="customerName" id="customerName"
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 bg-white text-slate-900"
                                    placeholder="أدخل اسم مستلم المبلغ">
                                @error('customerName')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="nationalId"
                                    class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    الرقم القومي
                                </label>
                                <input type="text" wire:model="nationalId" id="nationalId"
                                    minlength="14" maxlength="14" pattern="[0-9]{14}" required
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 bg-white text-slate-900"
                                    placeholder="أدخل الرقم القومي">
                                @error('nationalId')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Client Wallet Specific Fields -->
                    @if ($withdrawalType === 'client_wallet')
                        <div class="bg-emerald-50/50 rounded-xl p-6 border border-emerald-200">
                            <h4 class="text-lg font-semibold text-emerald-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                سحب من محفظة العميل
                            </h4>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Client Search -->
                                <div class="space-y-2 relative">
                                    <label class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        البحث عن العميل (رقم الجوال أو كود العميل)
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input type="text" wire:model.live.debounce.300ms="clientSearch"
                                            class="w-full pl-10 pr-10 py-3 rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-200 bg-white text-slate-900"
                                            placeholder="ابحث برقم الجوال أو كود العميل..." autocomplete="off" />
                                        @if ($clientSearch && !$clientId)
                                            <button type="button" wire:click="clearClientSelection"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    @if (!empty($clientSuggestions) && !$clientId && strlen($clientSearch) >= 2)
                                        <div
                                            class="absolute z-50 w-full bg-white border border-slate-200 rounded-lg shadow-xl mt-1 max-h-64 overflow-y-auto">
                                            <div class="p-2 border-b border-slate-100 bg-slate-50">
                                                <p class="text-xs text-slate-600 font-medium">
                                                    {{ count($clientSuggestions) }} عميل موجود</p>
                                            </div>
                                            @foreach ($clientSuggestions as $suggestion)
                                                <div class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-slate-100 last:border-b-0 transition-colors duration-150 group"
                                                    wire:click="selectClient({{ $suggestion['id'] }})">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex-1">
                                                            <div
                                                                class="font-semibold text-slate-900 group-hover:text-emerald-700">
                                                                {{ $suggestion['name'] }}
                                                            </div>
                                                            <div class="text-sm text-slate-600 mt-1 space-y-1">
                                                                <div class="flex items-center gap-4">
                                                                    <span class="flex items-center gap-1 font-medium">
                                                                        <svg class="w-3 h-3 text-emerald-500"
                                                                            fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                        </svg>
                                                                        {{ $suggestion['mobile_number'] }}
                                                                    </span>
                                                                    <span class="flex items-center gap-1 font-medium">
                                                                        <svg class="w-3 h-3 text-blue-500"
                                                                            fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                        </svg>
                                                                        {{ $suggestion['customer_code'] }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="text-sm font-bold text-emerald-600">
                                                                {{ format_int($suggestion['balance']) }} ج.م
                                                            </div>
                                                            <div class="text-xs text-slate-500">الرصيد المتاح</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @error('clientSearch')
                                        <p class="text-red-600 text-sm mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Selected Client Info -->
                                @if ($clientName)
                                    <div class="bg-white rounded-lg border-2 border-emerald-200 p-4 shadow-sm">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="font-semibold text-emerald-800 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                العميل المحدد
                                            </h5>
                                            <button type="button" wire:click="clearClientSelection"
                                                class="text-slate-400 hover:text-red-500 transition-colors duration-200 p-1 rounded-full hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="space-y-3 text-sm">
                                            <div
                                                class="flex items-center justify-between py-2 px-3 bg-slate-50 rounded-lg">
                                                <span class="text-slate-600 font-medium">الاسم:</span>
                                                <span class="font-semibold text-slate-900">{{ $clientName }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between py-2 px-3 bg-slate-50 rounded-lg">
                                                <span class="text-slate-600 font-medium">الجوال:</span>
                                                <span class="font-semibold text-slate-900">{{ $clientMobile }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between py-2 px-3 bg-slate-50 rounded-lg">
                                                <span class="text-slate-600 font-medium">الكود:</span>
                                                <span class="font-semibold text-slate-900">{{ $clientCode }}</span>
                                            </div>
                                            <div
                                                class="flex items-center justify-between py-2 px-3 bg-emerald-50 rounded-lg border border-emerald-200">
                                                <span class="text-emerald-700 font-medium">الرصيد المتاح:</span>
                                                <span
                                                    class="font-bold text-emerald-600 text-lg">{{ format_int($clientBalance) }}
                                                    ج.م</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Withdrawal Details -->
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        اسم مستلم المبلغ
                                    </label>
                                    <input type="text" wire:model="withdrawalToName"
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-200 bg-white text-slate-900"
                                        placeholder="أدخل اسم الشخص الذي سيستلم المبلغ" />
                                    @error('withdrawalToName')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        المبلغ
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-500 text-sm font-medium">ج.م</span>
                                        </div>
                                        <input type="text" wire:model.defer="amount"
                                            step="0.01"
                                            class="w-full pl-12 pr-4 py-3 rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-200 bg-white text-slate-900"
                                            placeholder="0.00" required />
                                    </div>
                                    @error('amount')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        الرقم القومي للمستلم
                                    </label>
                                    <input type="text" wire:model.defer="withdrawalNationalId" minlength="14"
                                        maxlength="14" pattern="[0-9]{14}" required
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-200 bg-white text-slate-900"
                                        placeholder="أدخل الرقم القومي (14 رقم)" />
                                    @error('withdrawalNationalId')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        الخزنة
                                    </label>
                                    <select wire:model="safeId"
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-200 bg-white text-slate-900">
                                        @foreach ($this->safes ?? [] as $safe)
                                            <option value="{{ $safe['id'] }}">
                                                {{ $safe['name'] ?? 'خزنة رقم ' . $safe['id'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="lg:col-span-2 space-y-2">
                                    <label class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        ملاحظات
                                    </label>
                                    <textarea wire:model.defer="notes" rows="3"
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 transition-all duration-200 bg-white text-slate-900"
                                        placeholder="أدخل أي ملاحظات إضافية"></textarea>
                                    @error('notes')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- User Selection -->
                    @if ($withdrawalType === 'user')
                        <div class="bg-purple-50/50 rounded-xl p-6 border border-purple-200">
                            <h4 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                سحب المستخدم
                            </h4>
                            <div class="space-y-2">
                                <label for="userId" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    اختر المستخدم
                                </label>
                                <select id="userId" wire:model="userId"
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 transition-all duration-200 bg-white text-slate-900">
                                    <option value="">اختر مستخدم</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('userId')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Branch Withdrawal Specific Fields -->
                    @if ($withdrawalType === 'branch')
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="selectedBranchId" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    اختر الفرع (المصدر)
                                </label>
                                <select id="selectedBranchId" wire:model="selectedBranchId" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-white text-slate-900">
                                    <option value="">اختر الفرع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('selectedBranchId')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('general_supervisor'))
                            <div class="space-y-2">
                                <label for="destinationBranchId" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    اختر الفرع (الوجهة)
                                </label>
                                <select id="destinationBranchId" wire:model="destinationBranchId" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-white text-slate-900">
                                    <option value="">اختر الفرع الوجهة</option>
                                    @foreach ($destinationBranches as $branch)
                                        <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('destinationBranchId')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                            <div class="space-y-2">
                                <label for="amount" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    المبلغ
                                </label>
                                <input type="text" wire:model="amount" id="amount" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-white text-slate-900" placeholder="أدخل المبلغ ج.م">
                                @error('amount')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="lg:col-span-2 space-y-2">
                                <label for="notes" class="flex items-center text-sm font-semibold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    ملاحظات
                                </label>
                                <textarea id="notes" wire:model="notes" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all duration-200 bg-white text-slate-900" placeholder="أدخل ملاحظات إضافية"></textarea>
                                @error('notes')
                                    <p class="text-red-600 text-sm mt-1 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Expense Withdrawal -->
                    @if ($withdrawalType === 'expense')
                        <div class="bg-red-50/50 rounded-xl p-6 border border-red-200">
                            <h4 class="text-lg font-semibold text-red-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                سحب مصروفات
                            </h4>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="selectedBranchId"
                                        class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        اختر الفرع
                                    </label>
                                    <select id="selectedBranchId" wire:model="selectedBranchId"
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition-all duration-200 bg-white text-slate-900">
                                        <option value="">اختر الفرع</option>
                                        @foreach ($branches as $branch)
                                            @if (Auth::user()->hasRole(['admin', 'general_supervisor']) || Auth::user()->branch_id == $branch['id'])
                                                <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('selectedBranchId')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="amount"
                                        class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        المبلغ
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-500 text-sm font-medium">ج.م</span>
                                        </div>
                                        <input type="text" id="amount" wire:model="amount"
                                            class="w-full pl-12 pr-4 py-3 rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition-all duration-200 bg-white text-slate-900"
                                            placeholder="أدخل المبلغ">
                                    </div>
                                    @error('amount')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="selectedExpenseItem"
                                        class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        نوع المصروف
                                    </label>
                                    <select id="selectedExpenseItem" wire:model="selectedExpenseItem"
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition-all duration-200 bg-white text-slate-900">
                                        <option value="">اختر نوع المصروف</option>
                                        @foreach ($expenseItems as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedExpenseItem')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($selectedExpenseItem === 'other')
                                    <div class="space-y-2">
                                        <label for="customExpenseItem"
                                            class="flex items-center text-sm font-semibold text-slate-700">
                                            <svg class="w-4 h-4 mr-2 text-slate-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            نوع المصروف المخصص
                                        </label>
                                        <input type="text" id="customExpenseItem" wire:model="customExpenseItem"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition-all duration-200 bg-white text-slate-900"
                                            placeholder="أدخل نوع المصروف">
                                        @error('customExpenseItem')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <div class="lg:col-span-2 space-y-2">
                                    <label for="notes"
                                        class="flex items-center text-sm font-semibold text-slate-700">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        ملاحظات
                                    </label>
                                    <textarea id="notes" wire:model="notes" rows="3"
                                        class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition-all duration-200 bg-white text-slate-900"
                                        placeholder="أدخل ملاحظات إضافية"></textarea>
                                    @error('notes')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- General Notes for other types -->
                    @if ($withdrawalType !== 'client_wallet' && $withdrawalType !== 'branch' && $withdrawalType !== 'expense')
                        <div class="space-y-2">
                            <label for="notes" class="flex items-center text-sm font-semibold text-slate-700">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                ملاحظات
                            </label>
                            <textarea id="notes" wire:model="notes" rows="4"
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200 bg-white text-slate-900"
                                placeholder="أضف أي ملاحظات متعلقة بهذا السحب"></textarea>
                            @error('notes')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-slate-200">
                        <a href="{{ route('transactions.cash') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-4 focus:ring-slate-200 transition-all duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            إلغاء
                        </a>
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl font-semibold text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200 active:from-blue-800 active:to-blue-900 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:from-gray-400 disabled:to-gray-500"
                            @if ($withdrawalType === 'client_wallet' && !$clientId) disabled @endif>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            تنفيذ عملية السحب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
