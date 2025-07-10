<div class="min-h-screen bg-gradient-to-br from-cyan-50 via-white to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white/70 backdrop-blur-sm border border-gray-200/50 rounded-xl shadow-sm p-6 mb-8">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Safe-to-Safe Transfer</h1>
                    <p class="text-sm text-gray-600">Transfer funds between safes securely</p>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Important Notice for Non-Admin Users -->
        @cannot('unrestricted-cash-withdrawal')
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>
                                Safe-to-safe transfers require approval from an Admin, General Supervisor, or the
                                destination Branch Manager.
                                Funds will be deducted from the source safe immediately, but will only be credited to the
                                destination
                                safe after approval.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endcannot

        <!-- Transfer Form -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-gray-200/50 overflow-hidden">
            <div class="p-6">
                <form wire:submit.prevent="transfer">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Source Safe Selection -->
                        <div class="sm:col-span-1">
                            <label for="sourceSafeId" class="block text-sm font-medium text-gray-700">Source
                                Safe</label>
                            <div class="mt-1">
                                <select id="sourceSafeId" wire:model.defer="sourceSafeId"
                                    wire:change="updatedSourceSafeId"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Source Safe</option>
                                    @foreach ($safes as $safe)
                                        <option value="{{ $safe['id'] }}">{{ $safe['name'] }} -
                                            {{ $safe['branch']['name'] ?? 'Unknown Branch' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('sourceSafeId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($sourceSafe)
                                <div class="mt-2 px-3 py-2 bg-gray-50 rounded-md text-sm">
                                    <span class="font-medium text-gray-700">Current Balance:</span>
                                    <span
                                        class="font-bold text-blue-600">{{ number_format($sourceSafe['current_balance'], 2) }}
                                        EGP</span>
                                </div>
                            @endif
                        </div>

                        <!-- Destination Safe Selection -->
                        <div class="sm:col-span-1">
                            <label for="destinationSafeId" class="block text-sm font-medium text-gray-700">Destination
                                Safe</label>
                            <div class="mt-1">
                                <select id="destinationSafeId" wire:model.defer="destinationSafeId"
                                    wire:change="updatedDestinationSafeId"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Destination Safe</option>
                                    @foreach ($safes as $safe)
                                        <option value="{{ $safe['id'] }}">{{ $safe['name'] }} -
                                            {{ $safe['branch']['name'] ?? 'Unknown Branch' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('destinationSafeId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($destinationSafe)
                                <div class="mt-2 px-3 py-2 bg-gray-50 rounded-md text-sm">
                                    <span class="font-medium text-gray-700">Current Balance:</span>
                                    <span
                                        class="font-bold text-green-600">{{ number_format($destinationSafe['current_balance'], 2) }}
                                        EGP</span>
                                </div>
                            @endif
                        </div>

                        <!-- Amount -->
                        <div class="sm:col-span-1">
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EGP</span>
                                </div>
                                <input type="number" id="amount" wire:model.defer="amount" step="0.01"
                                    min="1"
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md"
                                    placeholder="0.00">
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <div class="mt-1">
                                <textarea id="notes" wire:model.defer="notes" rows="3"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Add any notes about this transfer..."></textarea>
                            </div>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('safes.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Initiate Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Safe-to-Safe Transfer Explanation -->
        <div class="mt-8 bg-indigo-50 rounded-xl shadow-sm border border-indigo-100 p-6">
            <h2 class="text-lg font-medium text-indigo-800 mb-4">About Safe-to-Safe Transfers</h2>
            <div class="prose prose-indigo text-sm text-indigo-700">
                <p>Safe-to-safe transfers allow for the movement of funds between safes within the system. This feature
                    helps in:</p>
                <ul class="mt-2 space-y-1">
                    <li>Managing cash distribution across branches</li>
                    <li>Replenishing safes that are running low on funds</li>
                    <li>Moving excess cash to more secure safes</li>
                    <li>Balancing the cash flow between different operational units</li>
                </ul>

                <h3 class="mt-4 text-base font-medium text-indigo-800">Process Flow:</h3>
                <ol class="mt-2 space-y-1">
                    <li>1. Select source and destination safes</li>
                    <li>2. Enter the amount to transfer</li>
                    <li>3. Add any relevant notes</li>
                    <li>4. Submit the transfer request</li>
                    <li>5. Wait for approval (if required)</li>
                </ol>

                @cannot('unrestricted-cash-withdrawal')
                    <div class="mt-4 p-3 bg-white rounded-md">
                        <p class="font-medium text-indigo-900">Note for Non-Admin Users:</p>
                        <p class="mt-1">Your transfers will require approval. Funds will be deducted from the source safe
                            immediately, but will only be credited to the destination safe after approval by an authorized
                            user.</p>
                    </div>
                @endcannot
            </div>
        </div>
    </div>
</div>
