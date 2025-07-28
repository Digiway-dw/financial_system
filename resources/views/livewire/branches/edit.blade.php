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
                    <h1 class="text-3xl font-bold text-gray-900">تعديل بيانات الفرع</h1>
                    <p class="mt-2 text-sm text-gray-600">يمكنك تعديل بيانات الفرع من خلال النموذج التالي</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('branches.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة لقائمة الفروع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form wire:submit.prevent="updateBranch" class="space-y-8">
                <!-- Basic Information Section -->
                <div class="space-y-6">
                    <div class="border-b border-gray-200 pb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            معلومات الفرع
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">قم بتحديث بيانات الفرع أدناه</p>
                    </div>

                    <!-- Branch Name -->
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            اسم الفرع <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input wire:model="name" id="name" name="name" type="text" required autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/50 backdrop-blur-sm"
                                placeholder="ادخل اسم الفرع" />
                        </div>
                        @if ($errors->has('name'))
                            <p class="text-sm text-red-600 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $errors->first('name') }}
                            </p>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            الوصف
                        </label>
                        <textarea wire:model="description" id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/50 backdrop-blur-sm resize-none"
                            placeholder="وصف اختياري للفرع"></textarea>
                        @if ($errors->has('description'))
                            <p class="text-sm text-red-600 flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $errors->first('description') }}
                            </p>
                        @endif
                    </div>

                    <!-- Branch Status -->
                    <div class="space-y-2">
                        <label for="is_active" class="block text-sm font-medium text-gray-700">
                            حالة الفرع
                        </label>
                        <select wire:model="is_active" id="is_active" name="is_active"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                            <option value="1" {{ $is_active == true || $is_active == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ $is_active == false || $is_active == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">الحالة الحالية: {{ $is_active ? 'نشط' : 'غير نشط' }} (قيمة: {{ $is_active }})</p>
                    </div>
                </div>

                <!-- Safe Information Section -->
                <div class="space-y-6 border-t border-gray-200 pt-8 mt-8">
                    <div class="border-b border-gray-200 pb-4">
                        <h2 class="text-lg font-semibold text-amber-900 flex items-center">
                            <div class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            معلومات الخزينة
                        </h2>
                        <p class="text-sm text-amber-700 mt-1">قم بتعديل بيانات الخزينة المرتبطة بهذا الفرع</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="safe_name" class="block text-sm font-medium text-gray-700">اسم الخزينة</label>
                            <input wire:model="safe_name" id="safe_name" name="safe_name" type="text" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-white/50 backdrop-blur-sm"
                                placeholder="ادخل اسم الخزينة" />
                        </div>
                        <div>
                            <label for="safe_current_balance" class="block text-sm font-medium text-gray-700">الرصيد
                                (ج.م)</label>
                            <input wire:model="safe_current_balance" id="safe_current_balance"
                                name="safe_current_balance" type="text" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-white/50 backdrop-blur-sm"
                                placeholder="٠.٠٠" />
                        </div>
                        <div>
                            <label for="safe_description" class="block text-sm font-medium text-gray-700">وصف
                                الخزينة</label>
                            <input wire:model="safe_description" id="safe_description" name="safe_description"
                                type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-white/50 backdrop-blur-sm"
                                placeholder="وصف اختياري للخزينة" />
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        @if (session('message'))
                            <div x-data="{ show: true }" x-show="show"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-90"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 5000)"
                                class="flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-xl">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-green-700">{{ session('message') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div x-data="{ show: true }" x-show="show"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-90"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => show = false, 5000)"
                                class="flex items-center px-4 py-2 bg-red-50 border border-red-200 rounded-xl">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-red-700">{{ session('error') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('branches.index') }}"
                            class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            إلغاء
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            حفظ التعديلات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
