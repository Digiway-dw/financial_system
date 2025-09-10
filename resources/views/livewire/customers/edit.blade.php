<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex items-center space-x-4">
                <div
                    class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1
                        class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                        تعديل العميل
                    </h1>
                    <p class="text-slate-600 mt-2">تعديل معلومات العميل وتفضيلاته</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <form wire:submit.prevent="updateCustomer" class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">الاسم</label>
                        <input type="text" wire:model="name" id="name" name="name" required autofocus
                            class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                            placeholder="أدخل الاسم الكامل للعميل">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile Numbers -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">رقم الهاتف</label>
                        <div class="space-y-3">
                            @foreach ($mobileNumbers as $i => $number)
                                <div class="flex gap-3 items-center">
                                    <div class="flex-1">
                                        <input type="text" wire:model="mobileNumbers.{{ $i }}" required
                                            class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                                            placeholder="أدخل رقم الهاتف">
                                        @error('mobileNumbers.' . $i)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    @if (count($mobileNumbers) > 1)
                                        <button type="button" wire:click="removeMobileNumber({{ $i }})"
                                            class="flex items-center justify-center w-10 h-10 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition-colors duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                            <button type="button" wire:click="addMobileNumber"
                                class="inline-flex items-center px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors duration-150 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                إضافة رقم
                            </button>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-semibold text-slate-700 mb-2">الجنس</label>
                        <select wire:model="gender" id="gender" name="gender" required
                            class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200">
                            <option value="">اختر الجنس</option>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Balance - Only visible to Admin -->
                    @if ($this->canEditBalance())
                        <div>
                            <label for="balance" class="block text-sm font-semibold text-slate-700 mb-2">الرصيد</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">EGP</span>
                                <input type="text" wire:model="balance" id="balance" name="balance"
                                    min="0" required
                                    class="w-full pl-14 pr-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                                    placeholder="أدخل الرصيد (أرقام فقط)"
                                    @if (!$is_client) disabled @endif
                                    oninput="this.value = this.value.replace(/[^\d]/g, '');">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">الرصيد يجب أن يكون رقما فقط (لا يسمح بالكسور)</p>
                            @error('balance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Is Client - Only visible to Admin and Supervisor -->
                    @if ($this->canToggleWallet())
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">حالة المحفظة</label>
                            @if ($is_client)
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        مفعل
                                    </span>
                                    <button type="button" wire:click="deactivateWallet"
                                        class="px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-150 text-xs font-medium">إلغاء التفعيل</button>
                                </div>
                            @else
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        لا يوجد محفظة
                                    </span>
                                    <button type="button" wire:click="activateWallet"
                                        class="px-4 py-2 bg-green-100 text-green-700 rounded-xl hover:bg-green-200 transition-colors duration-150 text-xs font-medium">تفعيل المحفظة</button>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Agent ID - Hidden since no one can edit it -->
                    <!-- The agent who created the customer is preserved automatically -->

                    <!-- Branch ID -->
                    <div>
                        <label for="branch_id" class="block text-sm font-semibold text-slate-700 mb-2">الفرع</label>
                        <select wire:model="branch_id" id="branch_id" name="branch_id" required
                            class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200">
                            <option value="">اختر الفرع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Debt Mode - Only visible to Admin and Supervisor -->
                    @if ($this->canToggleWallet())
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4" 
                             wire:key="debt-mode-section"
                             x-data="{ allowDebt: @entangle('allow_debt') }">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-3">إعدادات الدين</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" 
                                               wire:model.live="allow_debt" 
                                               x-model="allowDebt"
                                               id="allow_debt" name="allow_debt"
                                               class="w-5 h-5 text-indigo-600 border border-slate-300 rounded focus:ring-indigo-500 focus:ring-2"
                                               wire:key="allow-debt-checkbox">
                                        <label for="allow_debt" class="text-sm font-medium text-slate-700">السماح بالدين</label>
                                    </div>
                                    @error('allow_debt')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Debt Limit - Always visible but disabled when debt not allowed -->
                                <div x-bind:class="allowDebt ? '' : 'opacity-50'" wire:key="debt-limit-field">
                                    <label for="max_debt_limit" class="block text-sm font-semibold text-slate-700 mb-2">
                                        الحد الأقصى للدين (أدخل رقم موجب)
                                        <span x-show="!allowDebt" class="text-xs text-gray-400 mr-2">- يجب تفعيل السماح بالدين أولاً</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">EGP</span>
                                        <input type="number" 
                                               wire:model.live="max_debt_limit" 
                                               id="max_debt_limit" name="max_debt_limit"
                                               min="1" step="0.01" placeholder="1000"
                                               x-bind:readonly="!allowDebt"
                                               x-bind:class="allowDebt ? 'bg-white/80' : 'bg-gray-100 text-gray-500'"
                                               class="w-full pl-14 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                                               wire:key="max-debt-limit-input">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        حد الدين يجب أن يكون قيمة موجبة (مثال: 1000)
                                        <span x-show="!allowDebt">
                                            <br><strong>ملاحظة: قم بتفعيل "السماح بالدين" لتمكين هذا الحقل</strong>
                                        </span>
                                    </p>
                                    @error('max_debt_limit')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-8 border-t border-slate-200 mt-8">
                <div class="flex space-x-4">
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-purple-700 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        تعديل العميل
                    </button>
                    <a href="{{ route('customers.index') }}"
                        class="px-6 py-3 bg-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-300 transition-colors duration-150">
                        إلغاء
                    </a>
                </div>

                <!-- Status Messages -->
                <div class="flex space-x-4">
                    @if (session('message'))
                        <div x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                            class="inline-flex items-center px-4 py-2 rounded-xl bg-green-100 border border-green-200 text-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('message') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                            class="inline-flex items-center px-4 py-2 rounded-xl bg-red-100 border border-red-200 text-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
