<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Receive Transaction</h1>
                    <p class="text-gray-600">Record money received from a client for agents</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Agent: {{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500">Branch: {{ Auth::user()->branch->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if ($successMessage)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-green-800 font-medium">{{ $successMessage }}</p>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-red-800 font-medium">{{ $errorMessage }}</p>
                </div>
            </div>
        @endif

        <!-- Safe Balance Warning -->
        @if ($safeBalanceWarning)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-yellow-800 font-medium">{{ $safeBalanceWarning }}</p>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form wire:submit.prevent="submitTransaction">
                <!-- Client Information Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Client Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Client Mobile Number -->
                        <div class="relative">
                            <label for="clientMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live.debounce.300ms="clientMobile" id="clientMobile" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('clientMobile') border-red-500 @enderror"
                                placeholder="Enter mobile number" autocomplete="off">
                            @error('clientMobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Client Suggestions Dropdown -->
                            @if (!empty($clientSuggestions) && $clientMobile)
                                <div
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    @foreach ($clientSuggestions as $suggestion)
                                        <div wire:click="selectClient({{ $suggestion['id'] }})"
                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                            <div class="font-medium text-gray-900">{{ $suggestion['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $suggestion['mobile_number'] }} •
                                                Code: {{ $suggestion['customer_code'] ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">Balance:
                                                {{ format_int((float) $suggestion['balance']) }} EGP</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Client Code (Display Only) -->
                        <div>
                            <label for="clientCode" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Code
                            </label>
                            <input wire:model="clientCode" id="clientCode" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700"
                                placeholder="Auto-generated" readonly @if ($clientId) disabled @endif>
                        </div>

                        <!-- Client Name -->
                        <div>
                            <label for="clientName" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Name <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="clientName" id="clientName" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('clientName') border-red-500 @enderror"
                                placeholder="Enter client name" @if ($clientId) disabled @endif>
                            @error('clientName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Gender (Bubble) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gender
                            </label>
                            <div class="flex space-x-6 mt-2">
                                <label class="inline-flex items-center">
                                    <input wire:model="clientGender" type="radio" value="male"
                                        class="form-radio h-5 w-5 text-blue-600"
                                        @if ($clientId) disabled @endif>
                                    <span class="ml-2 text-gray-700">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input wire:model="clientGender" type="radio" value="female"
                                        class="form-radio h-5 w-5 text-pink-600"
                                        @if ($clientId) disabled @endif>
                                    <span class="ml-2 text-gray-700">Female</span>
                                </label>
                            </div>
                            @error('clientGender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sender Information Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                        Sender Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sender Mobile Number -->
                        <div class="relative">
                            <label for="senderMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                Sender Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live.debounce.300ms="senderMobile" id="senderMobile" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('senderMobile') border-red-500 @enderror"
                                placeholder="Enter sender mobile number" maxlength="11" minlength="11"
                                pattern="\d{11}" autocomplete="off"
                                oninput="this.value=this.value.replace(/[^\d]/g,'').slice(0,11)">
                            @error('senderMobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if (strlen($senderMobile ?? '') > 0 && strlen($senderMobile ?? '') != 11)
                                <p class="mt-1 text-sm text-red-600">Mobile number must be exactly 11 digits.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Transaction Details Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        Transaction Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Amount (EGP) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live="amount" id="amount" type="number" step="1"
                                min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('amount') border-red-500 @enderror"
                                placeholder="Enter amount" oninput="this.value=this.value.replace(/[^\d]/g,'')">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission (Display Only) -->
                        <div>
                            <label for="commission" class="block text-sm font-medium text-gray-700 mb-2">
                                Commission (EGP)
                            </label>
                            <input wire:model="commission" id="commission" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 cursor-not-allowed"
                                placeholder="Auto-calculated" readonly>
                            <p class="mt-1 text-xs text-gray-500">Calculated: 5 EGP per 500 EGP</p>
                        </div>

                        <!-- Discount -->
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                                Discount (EGP)
                            </label>
                            <input wire:model.live="discount" id="discount" type="number" step="1"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('discount') border-red-500 @enderror"
                                placeholder="Enter discount amount"
                                oninput="this.value=this.value.replace(/[^\d]/g,'')">
                            @error('discount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Notes -->
                    @if ($discount > 0)
                        <div class="mt-6">
                            <label for="discountNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                Discount Notes <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="discountNotes" id="discountNotes" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('discountNotes') border-red-500 @enderror"
                                placeholder="Please provide reason for discount..."></textarea>
                            @error('discountNotes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- Line Selection Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        Line Selection
                    </h2>

                    <!-- Branch Selection (for admin/supervisor only) -->
                    @if ($canSelectBranch)
                        <div class="mb-6">
                            <label for="selectedBranchId" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Branch <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedBranchId" id="selectedBranchId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Select a branch...</option>
                                @foreach ($availableBranches as $branch)
                                    <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Select the branch from which this transaction will be
                                performed.</p>
                        </div>
                    @endif

                    <div>
                        <label for="selectedLineId" class="block text-sm font-medium text-gray-700 mb-2">
                            Available Lines <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="selectedLineId" id="selectedLineId"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('selectedLineId') border-red-500 @enderror">
                            <option value="">Select a line</option>
                            @foreach ($availableLines as $line)
                                <option value="{{ $line['id'] }}">{{ $line['display'] }}</option>
                            @endforeach
                        </select>
                        @error('selectedLineId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Lines from your branch only</p>
                    </div>
                </div>

                <!-- Transaction Summary -->
                @if ($amount > 0 && $commission >= 0)
                    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">Transaction Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">Amount Received</p>
                                <p class="text-lg font-bold text-blue-700">{{ format_int($amount) }} EGP
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">Commission</p>
                                <p class="text-lg font-bold text-green-700">
                                    {{ format_int($commission) }} EGP</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">Discount</p>
                                <p class="text-lg font-bold text-red-700">{{ format_int($discount) }} EGP
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">From Safe</p>
                                <p class="text-lg font-bold text-purple-700">
                                    {{ format_int($amount - ($commission - abs($discount))) }} EGP
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">Total</p>
                                <p class="text-lg font-bold text-purple-700">
                                    {{ format_int($amount - ($commission - abs($discount))) }} EGP
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 text-xs text-gray-600">
                            <p>• Line balance will increase by {{ format_int($amount) }} EGP</p>
                            <p>• Safe balance will decrease by
                                {{ format_int($amount - $commission) }} EGP</p>
                            <p>• Commission earnings: {{ format_int($commission) }} EGP</p>
                        </div>
                    </div>
                @endif

                <!-- Inline Validation Warnings -->
                @if ($safeBalanceWarning)
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">Transaction cannot be processed</p>
                            <p class="text-red-700 text-sm">{{ $safeBalanceWarning }}</p>
                        </div>
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">Transaction Error</p>
                            <p class="text-red-700 text-sm">{{ $errorMessage }}</p>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">Transaction Rejected</p>
                            <p class="text-red-700 text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Auto-Generated Customer Code Display -->
                @if (isset($generatedCustomerCode) && $generatedCustomerCode)
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-green-800 font-medium">New Customer Created</p>
                            <p class="text-green-700 text-sm">Customer Code: <span
                                    class="font-mono font-bold">{{ $generatedCustomerCode }}</span></p>
                            <p class="text-green-600 text-xs">Please inform the customer of this code for future
                                transactions.</p>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex flex-col items-end space-y-2">
                    @if ($errorMessage)
                        <div class="mb-2 w-full flex justify-end">
                            <div
                                class="px-4 py-2 bg-red-100 border border-red-300 rounded-lg text-red-700 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                                </svg>
                                <span>{{ $errorMessage }}</span>
                            </div>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-2 w-full flex justify-end">
                            <div
                                class="px-4 py-2 bg-red-100 border border-red-300 rounded-lg text-red-700 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                                </svg>
                                <span>
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                </span>
                            </div>
                        </div>
                    @endif
                    <div class="flex justify-end space-x-4 w-full">
                        <button type="button" wire:click="resetTransactionForm"
                            class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors duration-200">
                            Reset Form
                        </button>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $safeBalanceWarning ? 'disabled' : '' }}>
                            <span wire:loading.remove>Receive Transaction</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
