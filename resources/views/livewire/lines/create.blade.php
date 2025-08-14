<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50" dir="rtl" style="direction: rtl;">
    <style>
        .sortable-header {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .sortable-header:hover {
            background-color: #f3f4f6 !important;
            transform: translateY(-1px);
        }

        .sortable-header:active {
            transform: translateY(0);
        }
    </style>
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إنشاء خط مالي جديد</h1>
                    <p class="mt-2 text-sm text-gray-600">أضف خطًا ماليًا جديدًا للنظام</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('lines.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة إلى الخطوط
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Form Header -->
            <div class="px-8 py-7 border-b border-gray-100 bg-white/80 rounded-t-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">معلومات الخط المالي</h2>
                        <p class="text-base text-gray-600">يرجى تعبئة بيانات الخط المالي الجديد</p>
                    </div>
                </div>
            </div>
            <!-- Form Content -->
            <form wire:submit.prevent="createLine" class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Mobile Number -->
                   
                    <div class="md:col-span-2">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <x-input-label for="mobileNumber" :value="'رقم الجوال'"
                                class="text-sm font-medium text-gray-700" />
                        </div>
                        <x-text-input wire:model="mobileNumber" id="mobileNumber" name="mobileNumber" type="text"
                            class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                            placeholder="أدخل رقم الجوال (11 رقمًا)" maxlength="11" minlength="11" pattern="\d{11}"
                            required autofocus
                            x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '').slice(0,11)" />
                        <div class="text-xs text-gray-500 mt-1">يجب أن يكون رقم الجوال 11 رقمًا بالضبط.</div>
                        @if (strlen($mobileNumber ?? '') > 0 && strlen($mobileNumber ?? '') != 11)
                            <div class="text-sm text-red-600 mt-1">يرجى إدخال جميع الأرقام الـ 11 لرقم الجوال.
                            </div>
                        @endif
                        <x-input-error class="mt-2" :messages="$errors->get('mobileNumber')" />
                    </div>
                     <!-- الرقم التسلسلي (Serial Number) -->
                    <div class="md:col-span-2">
                        <x-input-label for="serialNumber" :value="'الرقم التسلسلي (Serial Number)'" class="text-sm font-medium text-gray-700" />
                        <x-text-input wire:model="serialNumber" id="serialNumber" name="serialNumber" type="text"
                            class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                            maxlength="255" placeholder="أدخل الرقم التسلسلي (اختياري)" />
                        <x-input-error class="mt-2" :messages="$errors->get('serialNumber')" />
                    </div>

                    <!-- Current Balance -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            <x-input-label for="currentBalance" :value="'الرصيد الحالي'"
                                class="text-sm font-medium text-gray-700" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="currentBalance" id="currentBalance" name="currentBalance"
                                type="text"
                                class="block w-full pl-16 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                                required x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-base">ج.م</span>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('currentBalance')" />
                    </div>

                    <!-- Daily Limit -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <x-input-label for="dailyLimit" :value="'الحد اليومي'"
                                class="text-sm font-medium text-gray-700" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="dailyLimit" id="dailyLimit" name="dailyLimit" type="text"
                                class="block w-full pl-16 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                                required x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-base">ج.م</span>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('dailyLimit')" />
                    </div>

                    <!-- Monthly Limit -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <x-input-label for="monthlyLimit" :value="'الحد الشهري'"
                                class="text-sm font-medium text-gray-700" />
                        </div>
                        <div class="relative">
                            <x-text-input wire:model="monthlyLimit" id="monthlyLimit" name="monthlyLimit"
                                type="text"
                                class="block w-full pl-16 rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                                required x-on:input="event.target.value = event.target.value.replace(/[^\d]/g, '')" />
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-base">ج.م</span>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('monthlyLimit')" />
                    </div>

                    <!-- Network -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                            </svg>
                            <x-input-label for="network" :value="'مزود الشبكة'"
                                class="text-sm font-medium text-gray-700" />
                        </div>
                        <select wire:model="network" id="network" name="network"
                            class="block w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                            required>
                            <option value="">اختر مزود الشبكة</option>
                            <option value="vodafone">فودافون</option>
                            <option value="orange">أورانج</option>
                            <option value="etisalat">اتصالات</option>
                            <option value="we">وي</option>
                            <option value="fawry">فوري</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('network')" />
                    </div>



                    <!-- Assigned Branch -->
                    <div>
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <x-input-label for="branchId" :value="'الفرع المخصص'"
                                class="text-sm font-medium text-gray-700" />
                        </div>
                        <select wire:model="branchId" id="branchId" name="branchId"
                            class="block w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500 text-base py-3"
                            required>
                            <option value="">اختر الفرع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('branchId')" />
                    </div>

                    <!-- ملاحظة (Note) -->
                    <div class="col-span-2 mt-6">
                        <x-input-label for="note" :value="'ملاحظة (Note)'" />
                        <textarea wire:model="note" id="note" name="note" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="أدخل أي ملاحظات إضافية (اختياري)"></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('note')" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-4 pt-10 mt-10 border-t border-gray-100">
                    <div class="flex flex-col md:flex-row gap-4">
                        @if (session('message'))
                            <div x-data="{ show: true }" x-show="show"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-90"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 4000)"
                                class="inline-flex items-center px-4 py-2 rounded-lg bg-green-50 border border-green-200">
                                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm font-medium text-green-700">{{ session('message') }}</span>
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
                                class="inline-flex items-center px-4 py-2 rounded-lg bg-red-50 border border-red-200">
                                <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-sm font-medium text-red-700">{{ session('error') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col md:flex-row gap-4">
                        <a href="{{ route('lines.index') }}"
                            class="inline-flex items-center px-8 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-medium rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                            إلغاء
                        </a>
                        <x-primary-button
                            class="px-10 py-3 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                            {{-- <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg> --}}
                            إنشاء الخط
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
