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
                                إيداع الأموال
                            </h1>
                            <p class="text-slate-600 mt-2 text-lg">اختر نوع الإيداع وأدخل التفاصيل المطلوبة</p>
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
                <!-- Deposit Type Selector -->
                <div class="p-6 border-b border-slate-200/50">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">نوع الإيداع</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <button wire:click="$set('depositType', 'direct')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $depositType === 'direct' ? 'border-blue-500 bg-blue-50 shadow-lg shadow-blue-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $depositType === 'direct' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $depositType === 'direct' ? 'text-blue-700' : 'text-slate-700' }}">Direct</span>
                            </div>
                        </button>
                        <button wire:click="$set('depositType', 'client_wallet')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $depositType === 'client_wallet' ? 'border-emerald-500 bg-emerald-50 shadow-lg shadow-emerald-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $depositType === 'client_wallet' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $depositType === 'client_wallet' ? 'text-emerald-700' : 'text-slate-700' }}">Client
                                    Wallet</span>
                            </div>
                        </button>
                        <button wire:click="$set('depositType', 'user')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $depositType === 'user' ? 'border-purple-500 bg-purple-50 shadow-lg shadow-purple-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $depositType === 'user' ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $depositType === 'user' ? 'text-purple-700' : 'text-slate-700' }}">User</span>
                            </div>
                        </button>
                        <button wire:click="$set('depositType', 'admin')"
                            class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 {{ $depositType === 'admin' ? 'border-orange-500 bg-orange-50 shadow-lg shadow-orange-100' : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-md' }}">
                            <div class="p-4 text-center">
                                <div
                                    class="mx-auto w-10 h-10 rounded-lg flex items-center justify-center mb-2 {{ $depositType === 'admin' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $depositType === 'admin' ? 'text-orange-700' : 'text-slate-700' }}">Admin</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Deposit Forms -->
                <div class="p-6">
                    @if ($depositType === 'direct')
                        <form wire:submit.prevent="submitDeposit" class="space-y-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Name</label>
                                <input type="text" wire:model.defer="customerName"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                    required />
                                @error('customerName')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Amount</label>
                                <input type="number" wire:model.defer="amount" min="1" step="0.01"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                    required />
                                @error('amount')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Notes</label>
                                <textarea wire:model.defer="notes"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                                @error('notes')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Safe</label>
                                <select wire:model="safeId"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                                    @foreach ($branchSafes as $safe)
                                        <option value="{{ $safe->id }}">{{ $safe->name ?? 'Safe #' . $safe->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit
                                Direct Deposit</button>
                        </form>
                    @elseif ($depositType === 'client_wallet')
                        <form wire:submit.prevent="submitDeposit" class="space-y-6">
                            <!-- Customer Search Section -->
                            <div
                                class="bg-gradient-to-r from-emerald-50 to-blue-50 rounded-xl p-6 border border-emerald-100">
                                <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Customer Selection
                                </h4>

                                <!-- Customer Search Field -->
                                <div class="relative mb-4">
                                    <label class="block text-gray-700 font-medium mb-2">Search Customer</label>
                                    <div class="relative">
                                        <input type="text" wire:model.live.debounce.300ms="clientSearch"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            placeholder="Enter name, mobile, or customer code" autocomplete="off" />
                                        @if ($clientId)
                                            <button type="button" wire:click="clearClientSelection"
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Enhanced Customer Suggestions Dropdown -->
                                    @if (!empty($clientSuggestions) && !$clientId && strlen($clientSearch) >= 2)
                                        <div
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                                            @foreach ($clientSuggestions as $suggestion)
                                                <div wire:click="selectClient({{ $suggestion['id'] }})"
                                                    class="px-4 py-3 hover:bg-emerald-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $suggestion['name'] }}</div>
                                                            <div class="text-sm text-gray-600">
                                                                <span class="inline-flex items-center mr-4">
                                                                    <svg class="w-4 h-4 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                    </svg>
                                                                    {{ $suggestion['mobile_number'] }}
                                                                </span>
                                                                @if ($suggestion['customer_code'])
                                                                    <span class="inline-flex items-center">
                                                                        <svg class="w-4 h-4 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                                        </svg>
                                                                        {{ $suggestion['customer_code'] }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div
                                                                class="text-sm font-semibold {{ $suggestion['balance'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                                {{ number_format($suggestion['balance'], 2) }} EGP
                                                            </div>
                                                            <div class="text-xs text-gray-500">Balance</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @error('clientSearch')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Selected Customer Info -->
                                @if ($clientId && $clientName)
                                    <div class="bg-white rounded-lg border border-emerald-200 p-4 shadow-sm">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="font-semibold text-emerald-800 flex items-center">
                                                <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Selected Customer
                                            </h5>
                                            <button type="button" wire:click="clearClientSelection"
                                                class="text-gray-400 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-600">Name:</span>
                                                <div class="text-gray-900">{{ $clientName }}</div>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600">Mobile:</span>
                                                <div class="text-gray-900">{{ $clientMobile }}</div>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600">Code:</span>
                                                <div class="text-gray-900">{{ $clientCode }}</div>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-600">Balance:</span>
                                                <div
                                                    class="font-semibold {{ $clientBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                    {{ number_format($clientBalance, 2) }} EGP
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Depositor Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Depositor National ID</label>
                                    <input type="text" wire:model.defer="depositorNationalId" minlength="14"
                                        maxlength="14" pattern="[0-9]{14}" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        placeholder="14-digit national ID" />
                                    @error('depositorNationalId')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Depositor Mobile Number</label>
                                    <input type="text" wire:model.defer="depositorMobileNumber" minlength="11"
                                        maxlength="15" pattern="[0-9]+"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        placeholder="Mobile number" required />
                                    @error('depositorMobileNumber')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Transaction Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Amount</label>
                                    <input type="number" wire:model.defer="amount" min="1" step="0.01"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        placeholder="Enter amount" required />
                                    @error('amount')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Safe</label>
                                    <select wire:model="safeId"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                        @foreach ($branchSafes as $safe)
                                            <option value="{{ $safe->id }}">
                                                {{ $safe->name ?? 'Safe #' . $safe->id }}
                                                @if ($safe->branch)
                                                    - {{ $safe->branch->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Notes</label>
                                <textarea wire:model.defer="notes"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                    rows="3" placeholder="Enter transaction notes" required></textarea>
                                @error('notes')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                @if (!$clientId) disabled @endif>
                                <span class="flex items-center justify-center text-white">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                    Submit Client Wallet Deposit
                                </span>
                            </button>
                        </form>
                    @elseif ($depositType === 'user')
                        <form wire:submit.prevent="submitDeposit" class="space-y-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">User</label>
                                <select wire:model="userId"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                    required>
                                    <option value="">Select a user</option>
                                    @foreach ($branchUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('userId')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Amount</label>
                                <input type="number" wire:model.defer="amount" min="1" step="0.01"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                    required />
                                @error('amount')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Notes</label>
                                <textarea wire:model.defer="notes"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                                @error('notes')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Safe</label>
                                <select wire:model="safeId"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                                    @foreach ($branchSafes as $safe)
                                        <option value="{{ $safe->id }}">{{ $safe->name ?? 'Safe #' . $safe->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit
                                User Deposit</button>
                        </form>
                    @elseif ($depositType === 'admin')
                        <form wire:submit.prevent="submitDeposit" class="space-y-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Amount</label>
                                <input type="number" wire:model.defer="amount" min="1" step="0.01"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200"
                                    required />
                                @error('amount')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Notes</label>
                                <textarea wire:model.defer="notes"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                                @error('notes')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Safe</label>
                                <select wire:model="safeId"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                                    @foreach ($branchSafes as $safe)
                                        <option value="{{ $safe->id }}">
                                            {{ $safe->name ?? 'Safe #' . $safe->id }}
                                            @if ($safe->branch)
                                                - {{ $safe->branch->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit
                                Admin Deposit</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
