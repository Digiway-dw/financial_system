<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إرسال</h1>
                    <p class="text-gray-600"> معاملة إرسال جديدة</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">الموظف: {{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500">الفرع: {{ Auth::user()->branch->name ?? 'N/A' }}</p>
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
                        معلومات العميل
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Client Mobile Number -->
                        <div class="relative">
                            <label for="clientMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                رقم الموبايل <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input wire:model="clientMobile" wire:input="searchClient" id="clientMobile"
                                    type="text"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('clientMobile') border-red-500 @enderror"
                                    placeholder="ادخل رقم الموبايل، اسم العميل، أو رمز العميل" autocomplete="off">
                                @if ($clientId)
                                    <button type="button" wire:click="clearClientSelection"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @error('clientMobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Enhanced Client Suggestions Dropdown -->
                            @if (!empty($clientSuggestions) && !$clientId && strlen($clientMobile) >= 2)
                                <div
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                                    @foreach ($clientSuggestions as $suggestion)
                                        <div wire:click="selectClient({{ $suggestion['id'] }})"
                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ $suggestion['name'] }}
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        <span class="inline-flex items-center mr-4">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                            </svg>
                                                            {{ $suggestion['mobile_number'] }}
                                                        </span>
                                                        @if ($suggestion['customer_code'])
                                                            <span class="inline-flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
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
                                                        {{ format_int($suggestion['balance']) }} EGP
                                                    </div>
                                                    <div class="text-xs text-gray-500">Balance</div>
                                                </div>
                                            </div>
                                            @if ($suggestion['gender'])
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Gender: {{ ucfirst($suggestion['gender']) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Selected Client Info Display -->
                            @if ($clientId && $clientName)
                                <div class="mt-3 bg-emerald-50 rounded-lg border border-emerald-200 p-3 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-semibold text-emerald-800 text-sm flex items-center">
                                            <svg class="w-4 h-4 text-emerald-600 mr-1" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            العميل المختار
                                        </h5>
                                        <button type="button" wire:click="clearClientSelection"
                                            class="text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="font-medium text-gray-600">الرصيد:</span>
                                            <div
                                                class="font-semibold {{ $clientBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ format_int($clientBalance) }} EGP
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-600">الكود:</span>
                                            <div class="text-gray-900">{{ $clientCode }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Client Code -->
                        <div>
                            <label for="clientCode" class="block text-sm font-medium text-gray-700 mb-2">
                                الكود
                                <span class="text-gray-500 text-xs">(مولد تلقائي إذا كان عميل جديد)</span>
                            </label>
                            <input wire:model="clientCode" id="clientCode" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700"
                                placeholder="Auto-generated" readonly
                                @if ($clientId) disabled @endif>
                        </div>

                        <!-- Client Name -->
                        <div>
                            <label for="clientName" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم العميل <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="clientName" id="clientName" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('clientName') border-red-500 @enderror"
                                placeholder="ادخل اسم العميل" @if ($clientId) disabled @endif>
                            @error('clientName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Client Gender -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                جنس العميل <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input wire:model="clientGender" type="radio" value="male"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        @if ($clientId) disabled @endif>
                                    <span class="ml-2 text-sm text-gray-700">ذكر</span>
                                </label>
                                <label class="flex items-center">
                                    <input wire:model="clientGender" type="radio" value="female"
                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        @if ($clientId) disabled @endif>
                                    <span class="ml-2 text-sm text-gray-700">أنثى</span>
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
                                    الرصيد: {{ format_int((float) $clientBalance) }} EGP
                                </span>
                                <span class="ml-2 text-xs text-blue-700">
                                    (متاح لكل من الأمان والمحفظة)
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
                                رقم الموبايل المستلم <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="receiverMobile" id="receiverMobile" type="text" maxlength="11"
                                minlength="11"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('receiverMobile') border-red-500 @enderror"
                                placeholder="ادخل رقم الموبايل المستلم (11 رقم)" pattern="\d{11}"
                                autocomplete="off"
                                oninput="this.value = this.value.replace(/[^\d]/g, '').slice(0,11);" onblur="">
                            <p class="mt-1 text-xs text-gray-500">يجب أن يكون رقم الموبايل المستلم 11 رقم.</p>
                            @if (strlen($receiverMobile) > 0 && strlen($receiverMobile) < 11)
                                <p class="mt-1 text-sm text-red-600">يجب أن يكون رقم الموبايل المستلم 11 رقم.
                                </p>
                            @endif
                            @error('receiverMobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                المبلغ (EGP) <span class="text-red-500">*</span>
                            </label>
                            <input wire:model.live="amount" id="amount" type="text"
                                min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('amount') border-red-500 @enderror"
                                placeholder="ادخل المبلغ (أرقام فقط)" autocomplete="off"
                                oninput="this.value = this.value.replace(/[^\d]/g, '');">
                            <p class="mt-1 text-xs text-gray-500">يجب أن يكون المبلغ عدد صحيح (لا يسمح بالكسور)
                            </p>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission (Auto-calculated) -->
                        <div>
                            <label for="commission" class="block text-sm font-medium text-gray-700 mb-2">
                                العمولة (محسوب تلقائيا)
                            </label>
                            <input wire:model="commission" id="commission" type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('commission') border-red-500 @enderror"
                                placeholder="0.00" autocomplete="off" value="{{ old('commission', '') }}"
                                readonly />
                            <span class="text-xs text-gray-500">5 EGP لكل 500 EGP (لا يسمح بالكسور)</span>
                            @error('commission')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Discount (Optional) -->
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                                الخصم (اختياري)
                            </label>
                            <input wire:model.live="discount" id="discount" type="text"
                                min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('discount') border-red-500 @enderror"
                                placeholder="ادخل الخصم (أرقام فقط)" autocomplete="off"
                                value="{{ old('discount', '') }}"
                                onfocus="if(this.value=='0'||this.value=='0.00'){this.value='';}" />
                            <p class="mt-1 text-xs text-gray-500">يجب أن يكون الخصم عدد صحيح (لا يسمح بالكسور)
                            </p>
                            @error('discount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Discount Notes (appears only if discount > 0) -->
                    @if ($discount > 0)
                        <div class="mt-6">
                            <label for="discountNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                ملاحظات الخصم <span class="text-red-500">*</span>
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
                        اختيار الخط والدفع
                    </h2>

                    <!-- Branch Selection (for admin/supervisor only) -->
                    @if ($canSelectBranch)
                        <div class="mb-6">
                            <label for="selectedBranchId" class="block text-sm font-medium text-gray-700 mb-2">
                                اختيار الفرع <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedBranchId" id="selectedBranchId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                <option value="">اختر فرع...</option>
                                @foreach ($availableBranches as $branch)
                                    <option value="{{ $branch['id'] }}">{{ $branch['name'] }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">اختر الفرع من الذي سيتم إجراء هذه المعاملة.
                                performed.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Available Lines -->
                        <div>
                            <label for="selectedLineId" class="block text-sm font-medium text-gray-700 mb-2">
                                الخطوط المتاحة <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedLineId" id="selectedLineId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('selectedLineId') border-red-500 @enderror">
                                <option value="">اختر خط...</option>
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
                            <label class="block text-sm font-medium text-gray-700 mb-3">خيارات الدفع</label>

                            <div class="space-y-3">
                                <!-- Collect From Customer Wallet -->
                                @if ($clientBalance > 0)
                                    <label
                                        class="flex items-center p-3 bg-purple-50 rounded-xl cursor-pointer hover:bg-purple-100 transition-colors">
                                        <input wire:model.live="collectFromCustomerWallet" type="checkbox"
                                            class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-medium text-purple-900">
                                            تحصيل من محفظة العميل + رصيد الخط
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
                                        تحصيل من رصيد الخط فقط {{ $clientBalance > 0 ? '(افتراضي)' : '' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Summary -->
                @if ($amount > 0)
                    <div class="mb-8 p-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">ملخص المعاملة</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ format_int($amount) }}
                                </div>
                                <div class="text-sm text-gray-600">المبلغ</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ format_int($commission) }}
                                </div>
                                <div class="text-sm text-gray-600">العمولة</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ format_int($discount) }}
                                </div>
                                <div class="text-sm text-gray-600">الخصم</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ format_int($amount + $commission - abs($discount)) }}
                                </div>
                                <div class="text-sm text-gray-600">المجموع</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Inline Validation Warnings -->
                @if ($lowBalanceWarning)
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">لا يمكن معالجة المعاملة</p>
                            <p class="text-red-700 text-sm">{{ $lowBalanceWarning }}</p>
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
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">خطأ في المعاملة</p>
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
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">تم رفض المعاملة</p>
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
                            <p class="text-green-800 font-medium">تم إنشاء عميل جديد</p>
                            <p class="text-green-700 text-sm">كود العميل: <span
                                    class="font-mono font-bold">{{ $generatedCustomerCode }}</span></p>
                            <p class="text-green-600 text-xs">يرجى إبرام عملية الدفع للعميل للمرة القادمة.</p>
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
                            إعادة تعيين النموذج
                        </button>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $lowBalanceWarning ? 'disabled' : '' }}>
                            <span wire:loading.remove>إنشاء المعاملة</span>
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
