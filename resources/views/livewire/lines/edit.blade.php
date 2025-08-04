<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100" dir="rtl" style="direction: rtl;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">تعديل الخط المالي</h3>
            <form wire:submit.prevent="updateLine" class="space-y-6">
                <!-- رقم الجوال -->
                <div>
                    <x-input-label for="mobileNumber" :value="'رقم الجوال'" />
                    <x-text-input wire:model="mobileNumber" id="mobileNumber" name="mobileNumber" type="text"
                        class="mt-1 block w-full" required autofocus placeholder="أدخل رقم الجوال (11 رقمًا)"
                        maxlength="11" minlength="11" pattern="\d{11}"
                        x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '').slice(0,11)" />
                    <div class="text-xs text-gray-500 mt-1">يجب أن يكون رقم الجوال 11 رقمًا بالضبط.</div>
                    @if (strlen($mobileNumber ?? '') > 0 && strlen($mobileNumber ?? '') != 11)
                        <div class="text-sm text-red-600 mt-1">يرجى إدخال جميع الأرقام الـ 11 لرقم الجوال.</div>
                    @endif
                    <x-input-error class="mt-2" :messages="$errors->get('mobileNumber')" />
                </div>

                <!-- الرصيد الحالي - Only visible to Admin -->
                @if ($this->canEditBalance())
                    <div>
                        <x-input-label for="currentBalance" :value="'الرصيد الحالي'" />
                        <div class="relative">
                            <x-text-input wire:model="currentBalance" id="currentBalance" name="currentBalance"
                                type="text" class="mt-1 block w-full pl-16" required
                                x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-base">ج.م</span>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('currentBalance')" />
                        <p class="text-xs text-gray-500 mt-1">أرقام صحيحة فقط (بدون كسور عشرية)</p>
                    </div>
                @endif

                <!-- الحد اليومي -->
                <div>
                    <x-input-label for="dailyLimit" :value="'الحد اليومي'" />
                    <div class="relative">
                        <x-text-input wire:model="dailyLimit" id="dailyLimit" name="dailyLimit" type="text"
                            class="mt-1 block w-full pl-16" required
                            x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-base">ج.م</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('dailyLimit')" />
                    <p class="text-xs text-gray-500 mt-1">أرقام صحيحة فقط (بدون كسور عشرية)</p>
                </div>

                <!-- الحد الشهري -->
                <div>
                    <x-input-label for="monthlyLimit" :value="'الحد الشهري'" />
                    <div class="relative">
                        <x-text-input wire:model="monthlyLimit" id="monthlyLimit" name="monthlyLimit" type="text"
                            class="mt-1 block w-full pl-16" required
                            x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-base">ج.م</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('monthlyLimit')" />
                    <p class="text-xs text-gray-500 mt-1">أرقام صحيحة فقط (بدون كسور عشرية)</p>
                </div>

                <!-- المتبقي اليومي -->
                <div>
                    <x-input-label for="dailyRemaining" :value="'المتبقي اليومي'" />
                    <div class="relative">
                        <x-text-input wire:model="dailyRemaining" id="dailyRemaining" name="dailyRemaining" type="text"
                            class="mt-1 block w-full pl-16" required
                            x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-base">ج.م</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('dailyRemaining')" />
                    <p class="text-xs text-gray-500 mt-1">أرقام صحيحة فقط (بدون كسور عشرية)</p>
                </div>

                <!-- المتبقي الشهري -->
                <div>
                    <x-input-label for="monthlyRemaining" :value="'المتبقي الشهري'" />
                    <div class="relative">
                        <x-text-input wire:model="monthlyRemaining" id="monthlyRemaining" name="monthlyRemaining" type="text"
                            class="mt-1 block w-full pl-16" required
                            x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-base">ج.م</span>
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('monthlyRemaining')" />
                    <p class="text-xs text-gray-500 mt-1">أرقام صحيحة فقط (بدون كسور عشرية)</p>
                </div>

                <!-- مزود الشبكة -->
                <div>
                    <x-input-label for="network" :value="'مزود الشبكة'" />
                    <select wire:model="network" id="network" name="network"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required>
                        <option value="vodafone">فودافون</option>
                        <option value="orange">أورانج</option>
                        <option value="etisalat">اتصالات</option>
                        <option value="we">وي</option>
                        <option value="fawry">فوري</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('network')" />
                </div>

                <!-- الحالة -->
                <div>
                    <x-input-label for="status" :value="'الحالة'" />
                    <select wire:model="status" id="status" name="status"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>

                <!-- الفرع المخصص -->
                <div>
                    <x-input-label for="branchId" :value="'الفرع المخصص'" />
                    <select wire:model="branchId" id="branchId" name="branchId"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required>
                        <option value="">اختر الفرع</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
                </div>


                <div class="flex items-center gap-4 mt-8">
                    <x-primary-button class="px-8 py-3 text-base">تعديل الخط</x-primary-button>

                    @if (session('message'))
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 dark:text-gray-400">{{ session('message') }}</p>
                    @endif

                    @if (session('error'))
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
