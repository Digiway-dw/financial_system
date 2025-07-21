<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
    <!-- Header Section -->
    <div class="bg-white/70 backdrop-blur-sm border-b border-indigo-200/20 shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        تعديل معاملة نقدية
                    </h3>
                    <p class="text-gray-600 text-sm">تعديل بيانات المعاملة النقدية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="p-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="p-8 space-y-8">
                    <form wire:submit.prevent="updateCashTransaction">
                        <!-- Customer Information Section -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
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
                                        class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                        required />
                                    @error('customerName')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Details Section -->
                        <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">تفاصيل المعاملة</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Amount -->
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ (جنيه مصري)</label>
                                    <input wire:model="amount" id="amount" name="amount" type="number" step="0.01"
                                        class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                                        required />
                                    @error('amount')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                                    <input wire:model="notes" id="notes" name="notes" type="text"
                                        class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500" />
                                    @error('notes')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                                <!-- Transaction Type -->
                                <div>
                                    <label for="transactionType" class="block text-sm font-medium text-gray-700 mb-2">نوع المعاملة</label>
                                    <select wire:model="transactionType" id="transactionType" name="transactionType"
                                        class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900"
                                        required>
                                        <option value="Withdrawal">سحب</option>
                                        <option value="Deposit">إيداع</option>
                                    </select>
                                    @error('transactionType')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                                    <select wire:model="status" id="status" name="status"
                                        class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-gray-900"
                                        required>
                                        <option value="pending">معلقة</option>
                                        <option value="completed">مكتملة</option>
                                        <option value="rejected">مرفوضة</option>
                                    </select>
                                    @error('status')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                                <!-- Safe -->
                                <div>
                                    <label for="safeId" class="block text-sm font-medium text-gray-700 mb-2">الخزنة المستخدمة</label>
                                    <select wire:model="safeId" id="safeId" name="safeId"
                                        class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 text-gray-900"
                                        required>
                                        <option value="">اختر الخزنة</option>
                                        @foreach ($safes as $safe)
                                            <option value="{{ $safe->id }}">{{ $safe->name ?? ("Safe #" . $safe->id) }}</option>
                                        @endforeach
                                    </select>
                                    @error('safeId')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6 border-t border-gray-200/30">
                            <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    تحديث المعاملة
                                </div>
                            </button>
                        </div>
                        @if (session('message') || session('error'))
                            <div class="space-y-4 mt-4">
                                @if (session('message'))
                                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                                        class="p-4 bg-green-50/70 border border-green-200/50 rounded-xl backdrop-blur-sm">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-green-800 font-medium">{{ session('message') }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                                        class="p-4 bg-red-50/70 border border-red-200/50 rounded-xl backdrop-blur-sm">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-red-800 font-medium">{{ session('error') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 