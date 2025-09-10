<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 md:p-6" dir="rtl">

    <!-- Header Section -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                        تحويل خط
                    </h1>
                    <p class="text-slate-600 mt-2">تحويل رصيد من خط إلى خط آخر</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
            
            <!-- Messages -->
            @if ($successMessage)
                <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 m-6 rounded-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $successMessage }}
                    </div>
                </div>
            @endif

            @if ($errorMessage)
                <div class="bg-red-100 border border-red-200 text-red-700 px-6 py-4 m-6 rounded-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ $errorMessage }}
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="submitTransfer" class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Left Column: Line Selection -->
                    <div class="space-y-6">
                        
                        <!-- From Line Selection -->
                        <div>
                            <label for="fromLineSearch" class="block text-sm font-semibold text-slate-700 mb-2">
                                الخط المرسل *
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live="fromLineSearch" 
                                       id="fromLineSearch"
                                       class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 transition-all duration-200"
                                       placeholder="ابحث برقم الخط...">
                                
                                @if (!empty($fromLineSuggestions))
                                    <ul class="absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                        @foreach ($fromLineSuggestions as $line)
                                            <li wire:click="selectFromLine({{ $line['id'] }})" 
                                                class="px-4 py-3 hover:bg-slate-50 cursor-pointer flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium">{{ $line['mobile_number'] }}</span>
                                                    <span class="text-sm text-slate-500 mr-2">
                                                        رصيد: {{ number_format($line['current_balance'], 2) }} ج.م
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            @error('fromLineId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- To Line Selection -->
                        <div>
                            <label for="toLineSearch" class="block text-sm font-semibold text-slate-700 mb-2">
                                الخط المستقبل *
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live="toLineSearch" 
                                       id="toLineSearch"
                                       class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 transition-all duration-200"
                                       placeholder="ابحث برقم الخط...">
                                
                                @if (!empty($toLineSuggestions))
                                    <ul class="absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-auto">
                                        @foreach ($toLineSuggestions as $line)
                                            <li wire:click="selectToLine({{ $line['id'] }})" 
                                                class="px-4 py-3 hover:bg-slate-50 cursor-pointer flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium">{{ $line['mobile_number'] }}</span>
                                                    <span class="text-sm text-slate-500 mr-2">
                                                        رصيد: {{ number_format($line['current_balance'], 2) }} ج.م
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            @error('toLineId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-semibold text-slate-700 mb-2">
                                المبلغ المراد تحويله *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">ج.م</span>
                                <input type="number" 
                                       wire:model.live="amount" 
                                       id="amount"
                                       step="0.01" 
                                       min="0.01"
                                       class="w-full pl-14 pr-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 transition-all duration-200"
                                       placeholder="0.00">
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Extra Fee -->
                        <div>
                            <label for="extraFee" class="block text-sm font-semibold text-slate-700 mb-2">
                                رسوم إضافية (اختياري)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">ج.م</span>
                                <input type="number" 
                                       wire:model.live="extraFee" 
                                       id="extraFee"
                                       step="0.01" 
                                       min="0"
                                       class="w-full pl-14 pr-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200"
                                       placeholder="">
                            </div>
                            @error('extraFee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <!-- Right Column: Summary & Notes -->
                    <div class="space-y-6">
                        
                        <!-- Transfer Summary -->
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">ملخص التحويل</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">المبلغ الأساسي:</span>
                                    <span class="font-medium">{{ number_format($amount ?: 0, 2) }} ج.م</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-slate-600">الرسوم الأساسية (1%):</span>
                                    <span class="font-medium">{{ number_format($baseFee, 2) }} ج.م</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-slate-600">الرسوم الإضافية:</span>
                                    <span class="font-medium">{{ number_format($extraFee ?: 0, 2) }} ج.م</span>
                                </div>
                                
                                <div class="border-t border-slate-300 pt-3">
                                    <div class="flex justify-between text-base font-semibold">
                                        <span class="text-red-600">إجمالي المخصوم:</span>
                                        <span class="text-red-600">{{ number_format($totalDeducted, 2) }} ج.م</span>
                                    </div>
                                    <div class="flex justify-between text-base font-semibold">
                                        <span class="text-green-600">المبلغ المستلم:</span>
                                        <span class="text-green-600">{{ number_format($finalAmount, 2) }} ج.م</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-slate-700 mb-2">
                                ملاحظات (اختياري)
                            </label>
                            <textarea wire:model="notes" 
                                      id="notes" 
                                      rows="4"
                                      class="w-full px-4 py-3 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-400 transition-all duration-200 resize-none"
                                      placeholder="أدخل أي ملاحظات إضافية..."></textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-8 border-t border-slate-200 mt-8">
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:bg-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            تحويل الآن
                        </button>
                        
                        <button type="button" 
                                wire:click="resetForm"
                                class="px-6 py-3 bg-slate-200 text-slate-700 font-medium rounded-xl hover:bg-slate-300 transition-colors duration-150">
                            إعادة تعيين
                        </button>
                    </div>

                    <a href="{{ route('transactions.index') }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors duration-150">
                        العودة للمعاملات
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
