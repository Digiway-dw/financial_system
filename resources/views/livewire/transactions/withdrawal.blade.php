<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Cash Withdrawal</h1>

                    @if (session()->has('message'))
                        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-200 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <div class="flex flex-wrap gap-4">
                            <button wire:click="$set('withdrawalType', 'direct')"
                                class="px-4 py-2 rounded-md {{ $withdrawalType === 'direct' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Direct Withdrawal
                            </button>
                            <button wire:click="$set('withdrawalType', 'client_wallet')"
                                class="px-4 py-2 rounded-md {{ $withdrawalType === 'client_wallet' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Client Wallet
                            </button>
                            <button wire:click="$set('withdrawalType', 'user')"
                                class="px-4 py-2 rounded-md {{ $withdrawalType === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                User Withdrawal
                            </button>
                            <button wire:click="$set('withdrawalType', 'admin')"
                                class="px-4 py-2 rounded-md {{ $withdrawalType === 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Administrative
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="submitWithdrawal">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Safe Selection -->
                            <div>
                                <label for="safeId" class="block text-sm font-medium text-gray-700 mb-1">Select
                                    Safe</label>
                                <select id="safeId" wire:model="safeId"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    @foreach ($safes as $safe)
                                        <option value="{{ $safe->id }}">{{ $safe->name }} - Balance:
                                            {{ number_format($safe->current_balance, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Withdrawal
                                    Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" wire:model="amount"
                                        id="amount"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">EGP</span>
                                    </div>
                                </div>
                            </div>

                            @if ($withdrawalType === 'direct')
                                <!-- Direct Withdrawal - Customer Name -->
                                <div>
                                    <label for="customerName"
                                        class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                                    <input type="text" wire:model="customerName" id="customerName"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Enter recipient name">
                                </div>
                            @endif

                            @if ($withdrawalType === 'client_wallet')
                                <!-- Client Code -->
                                <div>
                                    <label for="clientCode" class="block text-sm font-medium text-gray-700 mb-1">Client
                                        Code</label>
                                    <input type="text" wire:model="clientCode" id="clientCode"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Enter client code">
                                </div>

                                <!-- Client Mobile -->
                                <div>
                                    <label for="clientNumber"
                                        class="block text-sm font-medium text-gray-700 mb-1">Client Mobile</label>
                                    <input type="text" wire:model="clientNumber" id="clientNumber"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Enter client mobile number">
                                </div>
                            @endif

                            @if ($withdrawalType === 'user')
                                <!-- User Selection -->
                                <div>
                                    <label for="userId" class="block text-sm font-medium text-gray-700 mb-1">Select
                                        User</label>
                                    <select id="userId" wire:model="userId"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Select a user</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes" wire:model="notes" rows="3"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Add any relevant notes about this withdrawal"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('transactions.cash') }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition mr-2">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Process Withdrawal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
