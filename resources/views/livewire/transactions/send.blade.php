<!-- Send Transaction Page with Modern Light Theme -->
<div class="min-h-screen bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-50">
    <!-- Header Section -->
    <div class="bg-white/70 backdrop-blur-sm border-b border-cyan-200/20 shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </div>
                <div>
                    <h3
                        class="text-2xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                        إرسال حوالة</h3>
                    <p class="text-gray-600 text-sm">إرسال حوالة مالية جديدة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="p-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/60 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="p-8">
                    @include('livewire.transactions._form', ['hideTransactionType' => true])
                </div>
            </div>
        </div>
    </div>
</div>
