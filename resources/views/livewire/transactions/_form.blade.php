<!-- Modern Transaction Form -->
<form wire:submit.prevent="createTransaction" class="space-y-8">
    @if (empty($hideTransactionType))
        <!-- Transaction Type -->
        <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-white/20 shadow-lg">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">نوع المعاملة</h3>
            </div>
            <select wire:model="transactionType" id="transactionType" name="transactionType"
                class="w-full px-4 py-3 bg-white/70 border border-gray-200/50 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200 text-gray-900 placeholder-gray-500"
                required>
                <option value="Transfer">إرسال</option>
                <option value="Receive">استقبال</option>
                <option value="Deposit">إيداع</option>
                <option value="Withdrawal">سحب</option>
                <option value="Adjustment">تعديل</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('transactionType')" />
        </div>
    @endif

    @include('livewire.transactions.create-fields')

    <!-- Submit Button -->
    <div class="flex justify-end pt-6 border-t border-gray-200/30">
        <button type="submit"
            class="px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-cyan-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                حفظ المعاملة
            </div>
        </button>
    </div>
</form>
