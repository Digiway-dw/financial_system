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
        @if(isset($errorMessage) && $errorMessage)
            <div class="alert alert-danger" style="color: #b91c1c; background: #fee2e2; border: 1px solid #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ $errorMessage }}
            </div>
        @endif
        @if(isset($successMessage) && $successMessage)
            <div class="alert alert-success" style="color: #166534; background: #dcfce7; border: 1px solid #86efac; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ $successMessage }}
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
                                                {{ number_format((float) $suggestion['balance'], 2) }} EGP</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Client Name -->
                        <div>
                            <label for="clientName" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Name <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="clientName" id="clientName" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('clientName') border-red-500 @enderror"
                                placeholder="Enter client name">
                            @error('clientName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Gender -->
                        <div>
                            <label for="clientGender" class="block text-sm font-medium text-gray-700 mb-2">
                                Gender
                            </label>
                            <select wire:model="clientGender" id="clientGender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('clientGender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Code (Display Only) -->
                        <div>
                            <label for="clientCode" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Code
                            </label>
                            <input wire:model="clientCode" id="clientCode" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 cursor-not-allowed"
                                placeholder="Auto-generated" readonly>
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
                        <div>
                            <label for="senderMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                Sender Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="senderMobile" id="senderMobile" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('senderMobile') border-red-500 @enderror"
                                placeholder="Enter sender mobile number">
                            @error('senderMobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                            <input wire:model.live="amount" id="amount" type="number" step="any"
                                min="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('amount') border-red-500 @enderror"
                                placeholder="Enter amount">
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
                            <input wire:model.live="discount" id="discount" type="number" step="0.01"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('discount') border-red-500 @enderror"
                                placeholder="Enter discount amount">
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
                                <p class="text-lg font-bold text-blue-700">{{ number_format((float) $amount, 2) }} EGP
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">Commission</p>
                                <p class="text-lg font-bold text-green-700">{{ number_format((float) $commission, 2) }}
                                    EGP</p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">Discount</p>
                                <p class="text-lg font-bold text-red-700">{{ number_format((float) $discount, 2) }} EGP
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-gray-600">From Safe</p>
                                <p class="text-lg font-bold text-purple-700">
                                    {{ number_format((float) $amount - (float) $commission, 2) }} EGP</p>
                            </div>
                        </div>
                        <div class="mt-4 text-xs text-gray-600">
                            <p>• Line balance will increase by {{ number_format((float) $amount, 2) }} EGP</p>
                            <p>• Safe balance will decrease by
                                {{ number_format((float) $amount - (float) $commission, 2) }} EGP</p>
                            <p>• Commission earnings: {{ number_format((float) $commission, 2) }} EGP</p>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('transactions.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Transactions
                    </a>

                    <button type="submit"
                        class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                        <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <svg wire:loading class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        <span wire:loading.remove>Create Receive Transaction</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
