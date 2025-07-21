<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Branch Safe Balance Editor</h1>
                    <p class="text-gray-600">Admin-only tool to adjust branch safe balances with full audit logging</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Admin: {{ Auth::user()->name }}</p>
                    <p class="text-sm text-red-500 font-medium">⚠️ Admin Access Required</p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if ($successMessage)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-green-800 font-medium">{{ $successMessage }}</p>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errorMessage)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-red-800 font-medium">{{ $errorMessage }}</p>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form wire:submit.prevent="prepareBalanceUpdate">
                <!-- Safe Selection -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Select Safe to Edit
                    </h2>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="selectedSafeId" class="block text-sm font-medium text-gray-700 mb-2">
                                Branch Safe <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedSafeId" id="selectedSafeId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('selectedSafeId') border-red-500 @enderror">
                                <option value="">Select a safe to edit...</option>
                                @foreach ($safes as $safe)
                                    <option value="{{ $safe['id'] }}">
                                        {{ $safe['name'] }} ({{ $safe['branch_name'] }}) - Current Balance: {{ number_format($safe['current_balance']) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedSafeId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Safe Information -->
                @if ($selectedSafe)
                    <div class="mb-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">Current Safe Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg p-4">
                                <div class="text-sm text-gray-600">Safe Name</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $selectedSafe['name'] }}</div>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <div class="text-sm text-gray-600">Branch</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $selectedSafe['branch_name'] }}</div>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <div class="text-sm text-gray-600">Current Balance</div>
                                <div class="text-2xl font-bold text-blue-600">{{ number_format($selectedSafe['current_balance']) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Adjustment Form -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Balance Adjustment
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- New Balance -->
                            <div>
                                <label for="newBalance" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Balance <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="newBalance" id="newBalance" type="number" step="1" min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('newBalance') border-red-500 @enderror"
                                    placeholder="Enter new balance (whole numbers only)">
                                <p class="mt-1 text-xs text-gray-500">Balance must be a whole number (no decimals allowed)</p>
                                @error('newBalance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Balance Change Preview -->
                            @if ($newBalance !== '' && is_numeric($newBalance))
                                <div class="flex items-center justify-center">
                                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                                        <div class="text-sm text-gray-600 mb-2">Balance Change</div>
                                        @php
                                            $difference = (int)$newBalance - $selectedSafe['current_balance'];
                                        @endphp
                                        <div class="text-2xl font-bold {{ $difference > 0 ? 'text-green-600' : ($difference < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ $difference > 0 ? '+' : '' }}{{ number_format($difference) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $difference > 0 ? 'Increase' : ($difference < 0 ? 'Decrease' : 'No Change') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Adjustment Reason -->
                        <div class="mt-6">
                            <label for="adjustmentReason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Adjustment <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="adjustmentReason" id="adjustmentReason" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('adjustmentReason') border-red-500 @enderror"
                                placeholder="Provide a detailed reason for this balance adjustment (minimum 10 characters)"></textarea>
                            <p class="mt-1 text-xs text-gray-500">This reason will be logged in the transaction history for audit purposes</p>
                            @error('adjustmentReason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                @if ($selectedSafe && !$showConfirmation)
                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="resetForm"
                            class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors duration-200">
                            Reset Form
                        </button>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span wire:loading.remove>Review Changes</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                @endif
            </form>

            <!-- Confirmation Modal -->
            @if ($showConfirmation && $selectedSafe)
                <div class="mt-8 p-6 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Confirm Balance Adjustment
                    </h3>
                    
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-sm text-gray-600">Current Balance</div>
                                <div class="text-xl font-bold text-gray-900">{{ number_format($selectedSafe['current_balance']) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">New Balance</div>
                                <div class="text-xl font-bold text-blue-600">{{ number_format($newBalance) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Change</div>
                                @php
                                    $difference = (int)$newBalance - $selectedSafe['current_balance'];
                                @endphp
                                <div class="text-xl font-bold {{ $difference > 0 ? 'text-green-600' : ($difference < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ $difference > 0 ? '+' : '' }}{{ number_format($difference) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-1">Reason:</div>
                        <div class="text-gray-900 bg-white rounded-lg p-3 border">{{ $adjustmentReason }}</div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <p class="text-red-800 text-sm font-medium">
                            ⚠️ This action will permanently modify the safe balance and create a transaction log entry. This cannot be undone.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="cancelConfirmation"
                            class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" wire:click="confirmBalanceUpdate"
                            class="px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <span wire:loading.remove>Confirm & Apply Changes</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Applying Changes...
                            </span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Security Notice -->
        <div class="mt-8 bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-red-800 font-medium text-sm">
                        🔒 Security Notice: All balance adjustments are logged with full audit trails including admin identity, timestamps, and reasons. This feature is restricted to administrators only.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
