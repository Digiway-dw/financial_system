<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-12">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-2xl mb-6">
        <div class="flex justify-between items-center mb-2">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Deposit</h1>
                <p class="text-gray-600 text-base">Create a new deposit operation</p>
            </div>
            <div class="text-right text-sm text-gray-500">
                Agent: {{ auth()->user()->role ?? '' }}<br>
                Branch: {{ auth()->user()->branch->name ?? '' }}
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-2xl">
        <div class="flex space-x-8 border-b mb-6">
            <button wire:click="$set('depositType', 'direct')" class="pb-2 px-2 font-semibold focus:outline-none {{ $depositType === 'direct' ? 'border-b-4 border-blue-600 text-blue-700' : 'text-gray-700' }}">Direct Deposit</button>
            <button wire:click="$set('depositType', 'user')" class="pb-2 px-2 font-semibold focus:outline-none {{ $depositType === 'user' ? 'border-b-4 border-blue-600 text-blue-700' : 'text-gray-700' }}">User Deposit</button>
            <button wire:click="$set('depositType', 'client_wallet')" class="pb-2 px-2 font-semibold focus:outline-none {{ $depositType === 'client_wallet' ? 'border-b-4 border-blue-600 text-blue-700' : 'text-gray-700' }}">Client Wallet Deposit</button>
            <button wire:click="$set('depositType', 'admin')" class="pb-2 px-2 font-semibold focus:outline-none {{ $depositType === 'admin' ? 'border-b-4 border-blue-600 text-blue-700' : 'text-gray-700' }}">Admin Deposit</button>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-center">
                {{ session('message') }}
            </div>
        @endif

        @if ($depositType === 'direct')
            <form wire:submit.prevent="submitDeposit" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Name</label>
                    <input type="text" wire:model.defer="customerName" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('customerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                    <input type="number" wire:model.defer="amount" min="1" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Notes</label>
                    <textarea wire:model.defer="notes" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Safe</label>
                    <select wire:model="safeId" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @foreach ($branchSafes as $safe)
                            <option value="{{ $safe->id }}">{{ $safe->name ?? 'Safe #' . $safe->id }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit Direct Deposit</button>
            </form>
        @elseif ($depositType === 'user')
            <form wire:submit.prevent="submitDeposit" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">User</label>
                    <select wire:model="userId" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required>
                        <option value="">Select a user</option>
                        @foreach ($branchUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('userId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                    <input type="number" wire:model.defer="amount" min="1" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Notes</label>
                    <textarea wire:model.defer="notes" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Safe</label>
                    <select wire:model="safeId" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @foreach ($branchSafes as $safe)
                            <option value="{{ $safe->id }}">{{ $safe->name ?? 'Safe #' . $safe->id }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit User Deposit</button>
            </form>
        @elseif ($depositType === 'client_wallet')
            <form wire:submit.prevent="submitDeposit" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Customer Code or Mobile</label>
                    <input type="text" wire:model.debounce.300ms="clientSearch" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" placeholder="Enter customer code or mobile number" autocomplete="off" />
                    @if (!empty($clientSuggestions))
                        <ul class="bg-white border border-gray-200 rounded shadow mt-1 max-h-32 overflow-y-auto">
                            @foreach ($clientSuggestions as $suggestion)
                                <li class="px-3 py-2 hover:bg-blue-100 cursor-pointer" wire:click="selectClient({{ $suggestion['id'] }})">
                                    {{ $suggestion['name'] }} ({{ $suggestion['mobile_number'] }})
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @error('clientSearch') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @if ($clientName)
                    <div class="p-3 bg-blue-50 rounded border border-blue-100 mb-2">
                        <div><span class="font-semibold">Name:</span> {{ $clientName }}</div>
                        <div><span class="font-semibold">Mobile:</span> {{ $clientMobile }}</div>
                        <div><span class="font-semibold">Code:</span> {{ $clientCode }}</div>
                        <div><span class="font-semibold">Balance:</span> {{ number_format($clientBalance, 2) }} EGP</div>
                    </div>
                @endif
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Depositor National ID</label>
                    <input type="text" wire:model.defer="depositorNationalId" minlength="14" maxlength="14" pattern="[0-9]{14}" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('depositorNationalId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Depositor Mobile Number</label>
                    <input type="text" wire:model.defer="depositorMobileNumber" minlength="11" maxlength="15" pattern="[0-9]+" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('depositorMobileNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                    <input type="number" wire:model.defer="amount" min="1" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Notes</label>
                    <textarea wire:model.defer="notes" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Safe</label>
                    <select wire:model="safeId" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                        @foreach ($branchSafes as $safe)
                            <option value="{{ $safe->id }}">{{ $safe->name ?? 'Safe #' . $safe->id }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit Client Wallet Deposit</button>
            </form>
        @elseif ($depositType === 'admin')
            <form wire:submit.prevent="submitDeposit" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                    <input type="number" wire:model.defer="amount" min="1" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Notes</label>
                    <textarea wire:model.defer="notes" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2" required></textarea>
                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Submit Admin Deposit</button>
            </form>
        @endif
    </div>
</div> 