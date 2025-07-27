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
                    <h1 class="text-3xl font-bold text-gray-900">تعديل بيانات المستخدم</h1>
                    <p class="mt-2 text-sm text-gray-600">يمكنك تعديل بيانات المستخدم من خلال النموذج التالي</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة لقائمة المستخدمين
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 rounded-t-xl">
                <h2 class="text-xl font-semibold text-gray-900">معلومات المستخدم</h2>
                <p class="text-sm text-gray-600 mt-1">يرجى تعبئة جميع الحقول المطلوبة *</p>
            </div>
            <form wire:submit.prevent="updateUser" class="p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Personal Information Section -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">المعلومات الشخصية</h3>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            الاسم الكامل <span class="text-red-500 ml-1">*</span>
                        </x-input-label>
                        <input id="name"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            type="text" wire:model="name" required autofocus autocomplete="name"
                            placeholder="ادخل اسم المستخدم" />
                        <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('name')" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                            البريد الإلكتروني <span class="text-red-500 ml-1">*</span>
                        </x-input-label>
                        <input id="email"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            type="email" wire:model="email" required autocomplete="username"
                            placeholder="ادخل البريد الإلكتروني" />
                        <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('email')" />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <x-input-label for="phone_number"
                            class="flex items-center text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            رقم الهاتف
                        </x-input-label>
                        <input id="phone_number"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            type="text" wire:model="phone_number" placeholder="ادخل رقم الهاتف" />
                        <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('phone_number')" />
                    </div>

                    <!-- National Number -->
                    <div>
                        <x-input-label for="national_number"
                            class="flex items-center text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            الرقم القومي <span class="text-xs text-gray-500 ml-1">(١٤ رقم)</span>
                        </x-input-label>
                        <input id="national_number"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                            type="text" wire:model="national_number" minlength="14" maxlength="14"
                            pattern="[0-9]{14}" required placeholder="ادخل الرقم القومي المكون من ١٤ رقمًا" />
                        <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('national_number')" />
                    </div>

                    <!-- Salary -->
                    <div>
                        <x-input-label for="salary"
                            class="flex items-center text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            الراتب الشهري
                        </x-input-label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">ج.م</span>
                            <input id="salary"
                                class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                type="text" wire:model="salary"
                                placeholder="ادخل الراتب بالجنيه المصري" />
                        </div>
                        <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('salary')" />
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="mt-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 616 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">معلومات التواصل</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Address -->
                        <div class="lg:col-span-2">
                            <x-input-label for="address"
                                class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                </svg>
                                العنوان
                            </x-input-label>
                            <input id="address"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                type="text" wire:model="address" placeholder="ادخل العنوان الكامل" />
                            <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('address')" />
                        </div>

                        <!-- Land Number -->
                        <div>
                            <x-input-label for="land_number"
                                class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                رقم التليفون الأرضي
                            </x-input-label>
                            <input id="land_number"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                type="text" wire:model="land_number" placeholder="ادخل رقم التليفون الأرضي" />
                            <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('land_number')" />
                        </div>

                        <!-- Relative's Phone Number -->
                        <div>
                            <x-input-label for="relative_phone_number"
                                class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                رقم هاتف قريب للطوارئ
                            </x-input-label>
                            <input id="relative_phone_number"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                type="text" wire:model="relative_phone_number"
                                placeholder="ادخل رقم هاتف للطوارئ" />
                            <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('relative_phone_number')" />
                        </div>
                    </div>
                </div>

                <!-- Role & Permissions Section - Only visible to Admin and Supervisor -->
                @if ($this->canEditRoles())
                    <div class="mt-8">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">الدور والصلاحيات</h3>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Role Selection -->
                            <div>
                                <x-input-label for="selectedRole"
                                    class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 616 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    الدور الوظيفي <span class="text-red-500 ml-1">*</span>
                                </x-input-label>
                                <select wire:model="selectedRole" id="selectedRole"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                    <option value="">اختر الدور</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('selectedRole')" />
                            </div>

                            <!-- Branch Selection -->
                            @if (!in_array($selectedRole, ['admin', 'general_supervisor']))
                                <div>
                                    <x-input-label for="branchId"
                                        class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        تعيين الفرع <span class="text-red-500 ml-1">*</span>
                                    </x-input-label>
                                    <select wire:model="branchId" id="branchId"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">اختر الفرع</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('branchId')" />
                                </div>
                            @else
                                <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-800">صلاحية كاملة</p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            هذا الدور لديه صلاحية الوصول إلى جميع الفروع.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Notes Section -->
                <div class="mt-8">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">ملاحظات إضافية</h3>
                    </div>

                    <x-input-label for="notes" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 text-gray-400 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                        ملاحظات داخلية
                    </x-input-label>
                    <textarea id="notes"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                        wire:model="notes" rows="4" placeholder="أضف أي ملاحظات أو تعليمات خاصة بهذا المستخدم..."></textarea>
                    <x-input-error class="mt-2 text-red-600 text-sm" :messages="$errors->get('notes')" />
                    <p class="mt-2 text-xs text-gray-500">هذه الملاحظات للاستخدام الداخلي فقط ولن تظهر للمستخدم.</p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-8 mt-8 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600">سيتم حفظ جميع التغييرات فور الإرسال</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            إلغاء
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm"
                            wire:loading.attr="disabled">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                wire:loading.remove>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg class="animate-spin w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" wire:loading>
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading.remove>حفظ التغييرات</span>
                            <span wire:loading>يتم الحفظ...</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Working Hours Section - Separate from main form -->
            <div class="border-t border-gray-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">ساعات العمل</h3>
                </div>

                <!-- Status Messages -->
                @if (session()->has('workingHourMessage'))
                    <div class="p-3 bg-green-100 border border-green-200 rounded-lg mb-6">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 mr-2"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-green-700 text-sm">{{ session('workingHourMessage') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('workingHourError'))
                    <div class="p-3 bg-red-100 border border-red-200 rounded-lg mb-6">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-red-700 text-sm">{{ session('workingHourError') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Working Hours Form -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">
                        {{ $editingWorkingHourId ? 'تعديل ساعات العمل' : 'إضافة ساعات العمل' }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Day of Week -->
                        <div>
                            <x-input-label for="dayOfWeek" class="text-sm font-medium text-gray-700 mb-1">
                                اليوم <span class="text-red-500">*</span>
                            </x-input-label>
                            <select id="dayOfWeek" wire:model="dayOfWeek"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- اختر اليوم --</option>
                                @foreach ($days as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1 text-red-600 text-xs" :messages="$errors->get('dayOfWeek')" />
                        </div>

                        <!-- Start Time -->
                        <div>
                            <x-input-label for="startTime" class="text-sm font-medium text-gray-700 mb-1">
                                وقت البدء <span class="text-red-500">*</span>
                            </x-input-label>
                            <input type="time" id="startTime" wire:model="startTime"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <x-input-error class="mt-1 text-red-600 text-xs" :messages="$errors->get('startTime')" />
                        </div>

                        <!-- End Time -->
                        <div>
                            <x-input-label for="endTime" class="text-sm font-medium text-gray-700 mb-1">
                                وقت الانتهاء <span class="text-red-500">*</span>
                            </x-input-label>
                            <input type="time" id="endTime" wire:model="endTime"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <x-input-error class="mt-1 text-red-600 text-xs" :messages="$errors->get('endTime')" />
                        </div>

                        <!-- Enabled Status -->
                        <div class="flex items-center mt-6">
                            <input type="checkbox" id="isEnabled" wire:model="isEnabled"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="isEnabled" class="ml-2 text-sm text-gray-700">مفعل</label>
                            <x-input-error class="mt-1 text-red-600 text-xs" :messages="$errors->get('isEnabled')" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4 space-x-3">
                        @if ($editingWorkingHourId)
                            <button type="button" wire:click="resetWorkingHourForm"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                إلغاء
                            </button>
                        @endif
                        <button type="button" wire:click="saveWorkingHour"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ $editingWorkingHourId ? 'تحديث' : 'إضافة' }} ساعات العمل
                        </button>
                    </div>
                </div>

                <!-- Working Hours List -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-medium text-gray-900">ساعات العمل الحالية</h4>
                    </div>

                    @if (count($workingHours) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            اليوم
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            وقت البدء
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            وقت الانتهاء
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الحالة
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الإجراءات
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($workingHours as $workingHour)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                                {{ ucfirst($workingHour->day_of_week) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                {{ \Carbon\Carbon::parse($workingHour->start_time)->format('h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                {{ \Carbon\Carbon::parse($workingHour->end_time)->format('h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                @if ($workingHour->is_enabled)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        مفعل
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        غير مفعل
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex justify-center gap-4">
                                                    <button
                                                        wire:click="toggleWorkingHourStatus({{ $workingHour->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $workingHour->is_enabled ? 'تعطيل' : 'تفعيل' }}
                                                    </button>
                                                    <span class="text-gray-300">|</span>
                                                    <button wire:click="editWorkingHour({{ $workingHour->id }})"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        تعديل
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            لا توجد ساعات عمل معرفة لهذا المستخدم. استخدم النموذج أعلاه لإضافة ساعات العمل.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Glassmorphism Delete Confirmation Dialog -->
@if ($deleteConfirmId)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="backdrop-filter: blur(10px); background: rgba(0, 0, 0, 0.3);" wire:click.self="cancelDelete"
        x-data="{ shown: false }" x-init="shown = true;" x-show="shown"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">
        <div class="relative bg-white/30 backdrop-blur-xl border border-white/30 rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4"
            style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.35), rgba(255, 255, 255, 0.15)); box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);"
            wire:click.stop>
            <!-- Close button -->
            <button wire:click="cancelDelete"
                class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                wire:loading.attr="disabled">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-red-100/50 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- Content -->
            <div class="text-center mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">حذف ساعات العمل</h3>
                <p class="text-gray-700 leading-relaxed">
                    هل أنت متأكد أنك تريد حذف ساعات العمل ليوم
                    <span class="font-semibold text-gray-900">
                        @if ($deleteConfirmId)
                            {{ ucfirst($workingHours->where('id', $deleteConfirmId)->first()?->day_of_week ?? 'هذا اليوم') }}
                        @endif
                    </span>؟
                    <br><span class="text-sm text-gray-600 mt-2 block">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>
            </div>
            <!-- Actions -->
            <div class="flex space-x-3">
                <button wire:click="cancelDelete"
                    class="flex-1 px-4 py-3 bg-white/40 backdrop-blur-sm border border-white/40 rounded-xl text-gray-700 font-medium hover:bg-white/60 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/50"
                    wire:loading.attr="disabled">
                    إلغاء
                </button>
                <button wire:click="confirmDeleteAction"
                    class="flex-1 px-4 py-3 bg-red-500/80 backdrop-blur-sm border border-red-400/50 rounded-xl text-white font-medium hover:bg-red-600/80 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400/50 shadow-lg"
                    wire:loading.attr="disabled" wire:loading.class="opacity-50">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        wire:loading.remove>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    <span wire:loading.remove>حذف</span>
                    <span wire:loading>يتم الحذف...</span>
                </button>
            </div>
        </div>
    </div>
@endif
</div>
