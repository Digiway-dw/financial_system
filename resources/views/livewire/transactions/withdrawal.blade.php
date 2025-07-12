<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="p-6 border-b border-gray-200/30">
                    <h2 class="text-2xl font-bold text-gray-900">سحب الأموال</h2>
                    <p class="text-gray-600 mt-2">اختر نوع السحب وأدخل التفاصيل المطلوبة</p>
                </div>

                <!-- Error Messages -->
                @if (session('error'))
                    <div class="p-4 bg-red-50/70 border border-red-200/50 rounded-xl backdrop-blur-sm mx-6 mt-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-red-800 font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Success Messages -->
                @if (session('message'))
                    <div class="p-4 bg-green-50/70 border border-green-200/50 rounded-xl backdrop-blur-sm mx-6 mt-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-green-800 font-medium">{{ session('message') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="p-4 bg-red-50/70 border border-red-200/50 rounded-xl backdrop-blur-sm mx-6 mt-6">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-red-800 font-medium">يرجى تصحيح الأخطاء التالية:</span>
                        </div>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="p-6">
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
                            <button wire:click="$set('withdrawalType', 'branch')"
                                class="px-4 py-2 rounded-md {{ $withdrawalType === 'branch' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Branch Withdrawal
                            </button>
                            <button wire:click="$set('withdrawalType', 'expense')"
                                class="px-4 py-2 rounded-md {{ $withdrawalType === 'expense' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                Expense Withdrawal
                            </button>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="submitWithdrawal">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Safe Selection and Amount for other types -->
                            @if($withdrawalType !== 'client_wallet' && $withdrawalType !== 'branch' && $withdrawalType !== 'expense')
                                <div>
                                    <label for="safeId" class="block text-sm font-medium text-gray-700 mb-1">Select Safe</label>
                                    <select id="safeId" wire:model="safeId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        @foreach (($this->safes ?? []) as $safe)
                                            <option value="{{ $safe['id'] }}">{{ $safe['name'] }} - Balance: {{ number_format($safe['current_balance'], 2) }}</option>
                                        @endforeach
                                    </select>
                                    @error('safeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Withdrawal Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" wire:model="amount" id="amount" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">EGP</span>
                                        </div>
                                    </div>
                                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            @if($withdrawalType === 'direct')
                                <!-- Direct Withdrawal - Customer Name -->
                                <div>
                                    <label for="customerName" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                                    <input type="text" wire:model="customerName" id="customerName" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Enter recipient name">
                                    @error('customerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <!-- National ID -->
                                <div>
                                    <label for="nationalId" class="block text-sm font-medium text-gray-700 mb-1">National ID Number</label>
                                    <input type="text" wire:model="nationalId" id="nationalId" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Enter national ID number">
                                    @error('nationalId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            @if($withdrawalType === 'client_wallet')
                                <!-- Client Wallet fields -->
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
                                    <label class="block text-gray-700 font-medium mb-1">Withdrawal To (Name)</label>
                                    <input type="text" wire:model="withdrawalToName" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" placeholder="Enter name of person withdrawing" />
                                    @error('withdrawalToName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Amount</label>
                                    <input type="number" wire:model.defer="amount" min="1" step="0.01" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                                    @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Withdrawal National ID</label>
                                    <input type="text" wire:model.defer="withdrawalNationalId" minlength="14" maxlength="14" pattern="[0-9]{14}" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" required />
                                    @error('withdrawalNationalId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Safe</label>
                                    <select wire:model="safeId" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200">
                                        @foreach (($this->safes ?? []) as $safe)
                                            <option value="{{ $safe['id'] }}">{{ $safe['name'] ?? 'Safe #' . $safe['id'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Notes</label>
                                    <textarea wire:model.defer="notes" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring focus:ring-blue-200" rows="2"></textarea>
                                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <!-- end client_wallet fields -->
                            @endif
                            @if($withdrawalType === 'user')
                                <!-- User Selection -->
                                <div>
                                    <label for="userId" class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                                    <select id="userId" wire:model="userId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Select a user</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('userId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            @if($withdrawalType === 'branch')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="selectedBranchId" class="block text-sm font-medium text-gray-700 mb-1">Select Branch</label>
                                        <select id="selectedBranchId" wire:model="selectedBranchId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                            <option value="">اختر الفرع</option>
                                            @foreach($branches as $branch)
                                                @if(Auth::user()->hasRole(['admin', 'general_supervisor']) || Auth::user()->branch_id == $branch['id'])
                                                    <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('selectedBranchId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">المبلغ</label>
                                        <input type="number" id="amount" wire:model="amount" step="0.01" min="0.01" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="أدخل المبلغ">
                                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                        <textarea id="notes" wire:model="notes" rows="3" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="أدخل ملاحظات إضافية"></textarea>
                                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif
                            @if($withdrawalType === 'expense')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="selectedBranchId" class="block text-sm font-medium text-gray-700 mb-1">Select Branch</label>
                                        <select id="selectedBranchId" wire:model="selectedBranchId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                            <option value="">اختر الفرع</option>
                                            @foreach($branches as $branch)
                                                @if(Auth::user()->hasRole(['admin', 'general_supervisor']) || Auth::user()->branch_id == $branch['id'])
                                                    <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('selectedBranchId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">المبلغ</label>
                                        <input type="number" id="amount" wire:model="amount" step="0.01" min="0.01" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="أدخل المبلغ">
                                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="selectedExpenseItem" class="block text-sm font-medium text-gray-700 mb-1">نوع المصروف</label>
                                        <select id="selectedExpenseItem" wire:model="selectedExpenseItem" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                            <option value="">اختر نوع المصروف</option>
                                            @foreach($expenseItems as $item)
                                                <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedExpenseItem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                            @if($selectedExpenseItem === 'other')
                                            <div>
                                                <label for="customExpenseItem" class="block text-sm font-medium text-gray-700 mb-1">نوع المصروف المخصص</label>
                                                <input type="text" id="customExpenseItem" wire:model="customExpenseItem" 
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    placeholder="أدخل نوع المصروف">
                                                @error('customExpenseItem') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>
                                            @endif

                                    <div class="md:col-span-2">
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                        <textarea id="notes" wire:model="notes" rows="3" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            placeholder="أدخل ملاحظات إضافية"></textarea>
                                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif
                            <!-- Notes -->
                            @if($withdrawalType !== 'client_wallet' && $withdrawalType !== 'branch' && $withdrawalType !== 'expense')
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea id="notes" wire:model="notes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Add any relevant notes about this withdrawal"></textarea>
                                    @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('transactions.cash') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition mr-2">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Process Withdrawal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
