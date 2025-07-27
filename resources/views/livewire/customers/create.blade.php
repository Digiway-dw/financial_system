<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 md:p-6">

    @if ($showStatusPage)
        <!-- Status Page -->
        <div class="max-w-2xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 {{ $creationStatus === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-500' : 'bg-gradient-to-r from-red-500 to-pink-500' }} rounded-xl flex items-center justify-center">
                            @if ($creationStatus === 'success')
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h1
                                class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                                {{ $creationStatus === 'success' ? 'Customer Created Successfully!' : 'Creation Failed' }}
                            </h1>
                            <p class="text-slate-600 mt-2">{{ $statusMessage }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Details -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                <div class="p-8">
                    @if ($creationStatus === 'success' && $createdCustomer)
                        <!-- Success Details -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-slate-800">تفاصيل العميل</h3>

                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">الاسم:</span>
                                            <span class="font-medium text-slate-800">{{ $createdCustomer->name }}</span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-slate-600">كود العميل:</span>
                                            <span
                                                class="font-medium text-slate-800">{{ $createdCustomer->customer_code }}</span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-slate-600">الجنس:</span>
                                            <span
                                                class="font-medium text-slate-800 capitalize">{{ $createdCustomer->gender }}</span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-slate-600">الرصيد:</span>
                                            <span
                                                class="font-medium text-slate-800">${{ format_int($createdCustomer->balance) }}</span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-slate-600">نوع الحساب:</span>
                                            <span
                                                class="font-medium text-slate-800">{{ $createdCustomer->is_client ? 'Client (Has Wallet)' : 'Regular Customer' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-slate-800">معلومات الاتصال</h3>

                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-slate-600">رقم الهاتف:</span>
                                            <div class="mt-1 space-y-1">
                                                @foreach ($createdCustomer->mobileNumbers as $mobile)
                                                    <div class="font-medium text-slate-800">{{ $mobile->mobile_number }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if ($createdCustomer->branch)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">الفرع:</span>
                                                <span
                                                    class="font-medium text-slate-800">{{ $createdCustomer->branch->name }}</span>
                                            </div>
                                        @endif

                                        @if ($createdCustomer->agent)
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">الموزع:</span>
                                                <span
                                                    class="font-medium text-slate-800">{{ $createdCustomer->agent->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Error Details -->
                        <div class="text-center py-8">
                            <div
                                class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-slate-600">{{ $statusMessage }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-slate-200">
                        @if ($creationStatus === 'success')
                            <button wire:click="backToForm"
                                class="flex-1 px-6 py-3 bg-white border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                إنشاء عميل آخر
                            </button>
                        @else
                            <button wire:click="backToForm"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                إعادة إنشاء
                            </button>
                            <button wire:click="goToCustomersList"
                                class="flex-1 px-6 py-3 bg-white border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                العودة إلى العملاء
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Original Create Form -->
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                            إنشاء عميل جديد
                        </h1>
                        <p class="text-slate-600 mt-2">إضافة عميل جديد إلى النظام بمعلوماته وتفضيلاته
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">

            <form wire:submit.prevent="createCustomer" class="p-8">
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
                            @error('mobileNumbers')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="space-y-3">
                                @foreach ($mobileNumbers as $i => $number)
                                    <div class="flex gap-3 items-center">
                                        <div class="flex-1">
                                            <input type="text" wire:model="mobileNumbers.{{ $i }}"
                                                required maxlength="11" minlength="11" pattern="\d{11}"
                                                class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                                                placeholder="أدخل رقم الهاتف"
                                                oninput="this.value = this.value.replace(/[^\d]/g, '').slice(0,11);">
                                            <p class="mt-1 text-xs text-gray-500">يجب أن يكون رقم الهاتف 11 رقما</p>
                                            @error('mobileNumbers.' . $i)
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        @if (count($mobileNumbers) > 1)
                                            <button type="button"
                                                wire:click="removeMobileNumber({{ $i }})"
                                                class="flex items-center justify-center w-10 h-10 bg-red-100 text-red-600 rounded-xl hover:bg-red-200 transition-colors duration-150">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addMobileNumber"
                                    class="inline-flex items-center px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-colors duration-150 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    إضافة رقم
                                </button>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender"
                                class="block text-sm font-semibold text-slate-700 mb-2">الجنس</label>
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

                        <!-- Initial Balance Checkbox -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">الرصيد الابتدائي</label>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" wire:model="useInitialBalance" id="useInitialBalance"
                                    class="form-checkbox h-5 w-5 text-blue-600">
                                <label for="useInitialBalance" class="text-slate-700">تفعيل الرصيد الابتدائي</label>
                            </div>
                        </div>

                        <!-- Balance -->
                        <div>
                            <label for="balance"
                                class="block text-sm font-semibold text-slate-700 mb-2">الرصيد</label>
                            <input type="text" wire:model="balance" id="balance" name="balance"
                                step="0.01"
                                class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                                placeholder="أدخل الرصيد الابتدائي" :disabled="!useInitialBalance">
                            @error('balance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Wallet Status -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">حالة المحفظة</label>
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" wire:model="is_client" id="is_client"
                                    class="w-4 h-4 text-green-600 bg-white border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                                <span class="text-sm font-medium text-slate-700">تفعيل المحفظة</span>
                                <span class="ml-2 text-xs text-gray-500">(إذا لم تتحقق من إنشاء المحفظة، يمكنك تركها
                                    غير مفعلة)</span>
                            </div>
                        </div>

                        <!-- Agent ID -->
                        <div>
                            <label for="agent_id" class="block text-sm font-semibold text-slate-700 mb-2">موزع</label>
                            <select wire:model="agent_id" id="agent_id" name="agent_id"
                                class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200">
                                <option value="">اختر موزع (اختياري)</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                            @error('agent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Branch ID -->
                        <div>
                            <label for="branch_id"
                                class="block text-sm font-semibold text-slate-700 mb-2">الفرع</label>
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
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-8 border-t border-slate-200 mt-8">
                    <div class="flex space-x-4">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            إنشاء عميل
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
    @endif
</div>
