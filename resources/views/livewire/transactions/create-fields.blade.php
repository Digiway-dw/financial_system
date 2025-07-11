<!-- Customer Information Section -->
<div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900">معلومات العميل</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Customer Name -->
        <div>
            <label for="customerName" class="block text-sm font-medium text-gray-700 mb-2">اسم العميل</label>
            <input wire:model="customerName" id="customerName" name="customerName" type="text"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('customerName')" />
        </div>

        <!-- Customer Mobile Number -->
        <div>
            <label for="customerMobileNumber" class="block text-sm font-medium text-gray-700 mb-2">رقم هاتف
                العميل</label>
            <input wire:model="customerMobileNumber" id="customerMobileNumber" name="customerMobileNumber"
                type="text"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('customerMobileNumber')" />
        </div>

        <!-- Customer Search -->
        <div x-data="{ open: false }" class="relative md:col-span-2">
            <label for="searchCustomer" class="block text-sm font-medium text-gray-700 mb-2">البحث عن عميل (اكتب الاسم
                أو الرقم)</label>
            <input wire:model.debounce.400ms="searchCustomer" id="searchCustomer" name="searchCustomer" type="text"
                placeholder="ابحث عن العملاء..."
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                @focus="open = true" @blur="setTimeout(() => open = false, 200)" autocomplete="off" />
            <input type="hidden" wire:model="selectedDestinationNumber" id="selectedDestinationNumber"
                name="selectedDestinationNumber" />
            @if (!empty($customerSearchResults))
                <ul x-show="open"
                    class="absolute z-10 bg-white/90 backdrop-blur-sm border border-gray-200 w-full mt-1 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                    @foreach ($customerSearchResults as $customer)
                        @if (isset($customer['mobile_number']) && isset($customer['name']))
                            <li class="px-4 py-3 hover:bg-cyan-50 cursor-pointer transition-colors duration-200"
                                @mousedown.prevent="open = false" wire:click="selectCustomer('{{ $customer['id'] }}')">
                                <div class="font-medium text-gray-900">{{ $customer['name'] }}</div>
                                <div class="text-sm text-gray-600">{{ $customer['mobile_number'] ?? '' }}</div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('selectedDestinationNumber')" />
        </div>

        <!-- Line Mobile Number removed as it doesn't exist in the transactions table -->

        <!-- Customer Code -->
        <div>
            <label for="customerCode" class="block text-sm font-medium text-gray-700 mb-2">كود العميل (اختياري)</label>
            <input wire:model="customerCode" id="customerCode" name="customerCode" type="text"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500" />
            <x-input-error class="mt-2" :messages="$errors->get('customerCode')" />
        </div>

        <!-- Gender -->
        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">الجنس</label>
            <select wire:model="gender" id="gender" name="gender"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200 text-gray-900"
                required>
                <option value="Male">ذكر</option>
                <option value="Female">أنثى</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
        </div>

        <!-- Is Client Checkbox -->
        <div>
            <div class="flex items-center space-x-3 mt-6">
                <input wire:model.live="isClient" id="isClient" name="isClient" type="checkbox"
                    class="w-5 h-5 text-cyan-600 bg-white/70 border-gray-300 rounded focus:ring-cyan-500 focus:ring-2" />
                <label for="isClient" class="text-sm font-medium text-gray-700">هل هو عميل؟</label>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('isClient')" />
        </div>
    </div>
</div>

<!-- Transaction Details Section -->
<div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900">تفاصيل المعاملة</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Amount -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ (جنيه مصري)</label>
            <input wire:model.live="amount" id="amount" name="amount" type="number"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
        </div>

        <!-- Commission -->
        <div>
            <label for="commission" class="block text-sm font-medium text-gray-700 mb-2">العمولة (جنيه مصري)</label>
            <input wire:model="commission" id="commission" name="commission" type="number" step="0.01"
                class="w-full px-4 py-3 bg-gray-100/70 border border-gray-200/50 rounded-xl text-gray-900 cursor-not-allowed"
                readonly />
            <x-input-error class="mt-2" :messages="$errors->get('commission')" />
        </div>

        <!-- Deduction -->
        <div>
            <label for="deduction" class="block text-sm font-medium text-gray-700 mb-2">الخصم (تعديل يدوي من
                العمولة)</label>
            <input wire:model="deduction" id="deduction" name="deduction" type="number" step="0.01"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500" />
            <x-input-error class="mt-2" :messages="$errors->get('deduction')" />
        </div>

        @if ($deduction > 0)
            <!-- Deduction Notes -->
            <div class="md:col-span-2 lg:col-span-3">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات الخصم
                    (مطلوب)</label>
                <input wire:model="notes" id="notes" name="notes" type="text"
                    class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                    required />
                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
            </div>
        @endif

        <!-- Payment Method -->
        <div>
            <label for="paymentMethod" class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
            <select wire:model="paymentMethod" id="paymentMethod" name="paymentMethod"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900"
                required>
                <option value="branch safe">خزنة الفرع</option>
                <option value="client wallet">محفظة العميل</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('paymentMethod')" />
        </div>
    </div>

    @can('perform-unrestricted-withdrawal')
        <div x-data="{ transactionType: '{{ $transactionType }}' }" x-show="transactionType === 'Withdrawal'" class="mt-6">
            <div class="flex items-center space-x-3 p-4 bg-amber-50/70 border border-amber-200/50 rounded-xl">
                <input wire:model.live="isAbsoluteWithdrawal" id="isAbsoluteWithdrawal" name="isAbsoluteWithdrawal"
                    type="checkbox"
                    class="w-5 h-5 text-amber-600 bg-white/70 border-amber-300 rounded focus:ring-amber-500 focus:ring-2" />
                <label for="isAbsoluteWithdrawal" class="text-sm font-medium text-amber-800">سحب مطلق (عدم إعادة إيداع في
                    خزنة أخرى)</label>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('isAbsoluteWithdrawal')" />
        </div>
    @endcan
</div>

<!-- Location & Configuration Section -->
<div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900">الموقع والإعدادات</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Branch -->
        <div>
            <label for="branchId" class="block text-sm font-medium text-gray-700 mb-2">الفرع</label>
            <select wire:model="branchId" id="branchId" name="branchId"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900"
                required>
                <option value="">اختر الفرع</option>
                @foreach ($branches ?? [] as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
        </div>

        <!-- Line -->
        <div>
            <label for="lineId" class="block text-sm font-medium text-gray-700 mb-2">الخط</label>
            <select wire:model="lineId" id="lineId" name="lineId"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900"
                required>
                <option value="">اختر الخط</option>
                @foreach ($lines ?? [] as $line)
                    <option value="{{ $line->id }}">{{ $line->mobile_number }} (الرصيد:
                        {{ $line->current_balance }} ج.م)</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('lineId')" />
        </div>

        <!-- Safe -->
        <div>
            <label for="safeId" class="block text-sm font-medium text-gray-700 mb-2">الخزنة</label>
            <select wire:model="safeId" id="safeId" name="safeId"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900"
                required>
                <option value="">اختر الخزنة</option>
                @foreach ($safes ?? [] as $safe)
                    <option value="{{ $safe->id }}">{{ $safe->name }} (الرصيد: {{ $safe->current_balance }}
                        ج.م)</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('safeId')" />
        </div>
    </div>
</div>

<!-- Messages Section -->
@if (session('message') || session('error'))
    <div class="bg-white/50 backdrop-blur-sm rounded-xl p-4 border border-white/20 shadow-lg">
        @if (session('message'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                class="p-4 bg-green-50/70 border border-green-200/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                class="p-4 bg-red-50/70 border border-red-200/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>
@endif

<!-- Transaction Receipt Modal -->
@if ($showReceiptModal && $completedTransaction)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
        x-data="{ show: true }" x-show="show" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">إيصال المعاملة</h2>
                    <button wire:click="closeReceiptModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">رقم المعاملة:</span>
                            <span class="text-gray-900">{{ $completedTransaction->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">اسم العميل:</span>
                            <span class="text-gray-900">{{ $completedTransaction->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">هاتف العميل:</span>
                            <span class="text-gray-900">{{ $completedTransaction->customer_mobile_number }}</span>
                        </div>
                        <!-- Line mobile number field removed as it doesn't exist in the database -->
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">المبلغ:</span>
                            <span
                                class="text-gray-900 font-bold">{{ number_format($completedTransaction->amount, 2) }}
                                ج.م</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">العمولة:</span>
                            <span class="text-gray-900">{{ number_format($completedTransaction->commission, 2) }}
                                ج.م</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">الخصم:</span>
                            <span class="text-gray-900">{{ number_format($completedTransaction->deduction, 2) }}
                                ج.م</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">نوع المعاملة:</span>
                            <span class="text-gray-900">{{ $completedTransaction->transaction_type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">طريقة الدفع:</span>
                            <span class="text-gray-900">{{ $completedTransaction->payment_method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-600">الحالة:</span>
                            <span
                                class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $completedTransaction->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
