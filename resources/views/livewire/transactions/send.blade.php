<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Send Transaction</h1>
                    <p class="text-gray-600">Create a new transfer operation for agents</p>
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

        <!-- Low Balance Warning -->
        @if ($lowBalanceWarning)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-yellow-800 font-medium">{{ $lowBalanceWarning }}</p>
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
                                            <div class="text-sm text-blue-600">Balance:
                                                {{ number_format($suggestion['balance'], 2) }} EGP</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Client Code -->
                        <div>
                            <label for="clientCode" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Code
                                <span class="text-gray-500 text-xs">(Auto-generated if new client)</span>
                            </label>
                            <input wire:model="clientCode" id="clientCode" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700"
                                placeholder="Auto-generated" readonly>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Client Gender <span class="text-gray-500 text-xs">(Optional)</span>
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input wire:model="clientGender" type="radio" value="male"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Male</span>
                                </label>
                                <label class="flex items-center">
                                    <input wire:model="clientGender" type="radio" value="female"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Female</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Client Balance Display -->
                    @if ($clientId)
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                                <span class="text-sm font-medium text-blue-900">
                                    Client Balance: {{ number_format((float) $clientBalance, 2) }} EGP
                                </span>
                                <span class="ml-2 text-xs text-blue-700">
                                    (Available for both Safe and Wallet payment methods)
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Transaction Details Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        Transaction Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Receiver Mobile -->
                        <div>
                            <label for="receiverMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                Receiver Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="receiverMobile" id="receiverMobile" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('receiverMobile') border-red-500 @enderror"
                                placeholder="Enter receiver mobile number">
                            @error('receiverMobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Amount (EGP) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live="amount" id="amount" type="number" step="any"
                                min="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('amount') border-red-500 @enderror"
                                placeholder="Enter amount">
                            <p class="mt-1 text-xs text-gray-500">Amount must be a multiple of 5 EGP</p>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission (Auto-calculated) -->
                        <div>
                            <label for="commission" class="block text-sm font-medium text-gray-700 mb-2">
                                Commission (Auto-calculated)
                            </label>
                            <input value="{{ number_format((float) $commission, 2) }}" id="commission"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700"
                                readonly>
                            <p class="mt-1 text-xs text-gray-500">5 EGP per 500 EGP (no fractions)</p>
                        </div>

                        <!-- Discount -->
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                                Discount (Optional)
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

                    <!-- Discount Notes (appears only if discount > 0) -->
                    @if ($discount > 0)
                        <div class="mt-6">
                            <label for="discountNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                Discount Notes <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="discountNotes" id="discountNotes" rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('discountNotes') border-red-500 @enderror"
                                placeholder="Please provide a reason for the discount..."></textarea>
                            @error('discountNotes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- Line Selection and Payment Options -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Line Selection & Payment
                    </h2>

                    <!-- Branch Selection (for admin/supervisor only) -->
                    @if ($canSelectBranch)
                        <div class="mb-6">
                            <label for="selectedBranchId" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Branch <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedBranchId" id="selectedBranchId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                <option value="">Select a branch...</option>
                                @foreach ($availableBranches as $branch)
                                    <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Select the branch from which this transaction will be
                                performed.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Available Lines -->
                        <div>
                            <label for="selectedLineId" class="block text-sm font-medium text-gray-700 mb-2">
                                Available Lines <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedLineId" id="selectedLineId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('selectedLineId') border-red-500 @enderror">
                                <option value="">Select a line...</option>
                                @foreach ($availableLines as $line)
                                    <option value="{{ $line['id'] }}">{{ $line['display'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedLineId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Payment Options</label>

                            <div class="space-y-3">
                                <!-- Collect From Client Safe -->
                                @if ($clientBalance > 0)
                                    <label
                                        class="flex items-center p-3 bg-blue-50 rounded-xl cursor-pointer hover:bg-blue-100 transition-colors">
                                        <input wire:model.live="collectFromClientSafe" type="checkbox"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-3 text-sm font-medium text-blue-900">
                                            Collect from Client Safe + Line Balance
                                        </span>
                                    </label>

                                    <!-- Collect From Customer Wallet -->
                                    <label
                                        class="flex items-center p-3 bg-purple-50 rounded-xl cursor-pointer hover:bg-purple-100 transition-colors">
                                        <input wire:model.live="collectFromCustomerWallet" type="checkbox"
                                            class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-medium text-purple-900">
                                            Collect from Customer Wallet + Line Balance
                                        </span>
                                    </label>
                                @endif

                                <!-- Deduct From Line Only -->
                                <label
                                    class="flex items-center p-3 bg-green-50 rounded-xl cursor-pointer hover:bg-green-100 transition-colors">
                                    <input wire:model="deductFromLineOnly" type="checkbox"
                                        class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                        {{ $deductFromLineOnly ? 'checked' : '' }} disabled>
                                    <span class="ml-3 text-sm font-medium text-green-900">
                                        Deduct from Line Balance Only {{ $clientBalance > 0 ? '(Default)' : '' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Summary -->
                @if ($amount > 0)
                    <div class="mb-8 p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ number_format((float) $amount, 2) }}
                                </div>
                                <div class="text-sm text-gray-600">Amount</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ number_format((float) $commission, 2) }}
                                </div>
                                <div class="text-sm text-gray-600">Commission</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ number_format((float) $discount, 2) }}
                                </div>
                                <div class="text-sm text-gray-600">Discount</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ number_format((float) $amount + (float) $commission, 2) }}
                                </div>
                                <div class="text-sm text-gray-600">Total</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="resetTransactionForm"
                        class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors duration-200">
                        Reset Form
                    </button>
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $lowBalanceWarning ? 'disabled' : '' }}>
                        <span wire:loading.remove>Create Transaction</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
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
            </form>
        </div>
    </div>
</div>
