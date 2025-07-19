<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Deposit</h1>
                    <p class="text-gray-600">Create a new deposit operation</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Agent: {{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500">Branch: {{ Auth::user()->branch->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Deposit Type Tabs -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="mb-6 flex space-x-4">
                <button type="button" class="px-4 py-2 rounded-xl font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                    :class="depositType === 'direct' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'" @click="depositType = 'direct'">
                    Direct Deposit
                </button>
                <button type="button" class="px-4 py-2 rounded-xl font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                    :class="depositType === 'user' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'" @click="depositType = 'user'">
                    User Deposit
                </button>
                <button type="button" class="px-4 py-2 rounded-xl font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                    :class="depositType === 'client' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'" @click="depositType = 'client'">
                    Client Wallet Deposit
                </button>
                <button type="button" class="px-4 py-2 rounded-xl font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                    :class="depositType === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'" @click="depositType = 'admin'">
                    Admin Deposit
                </button>
            </div>

            <!-- Direct Deposit -->
            <div x-show="depositType === 'direct'">
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
                <form class="space-y-6" wire:submit.prevent="submitDirectDeposit">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" wire:model.defer="direct_name" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter name">
                        @error('direct_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" wire:model.defer="direct_amount" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter amount">
                        @error('direct_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                        <textarea wire:model.defer="direct_note" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter note"></textarea>
                        @error('direct_note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-all duration-200">Submit Direct Deposit</button>
                    </div>
                </form>
            </div>

            <!-- User Deposit -->
            <div x-show="depositType === 'user'">
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option>Select user</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter amount">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter note"></textarea>
                    </div>
                </form>
            </div>

            <!-- Client Wallet Deposit -->
            <div x-show="depositType === 'client'">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Code</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter customer code">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Mobile Number</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter mobile number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter customer name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">National ID</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" minlength="14" maxlength="14" pattern="[0-9]{14}" required placeholder="Enter national ID (14 digits)">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter amount">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter note"></textarea>
                    </div>
                </form>
            </div>

            <!-- Admin Deposit -->
            <div x-show="depositType === 'admin'">
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter amount">
                    </div>
<div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                        <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Enter note"></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
