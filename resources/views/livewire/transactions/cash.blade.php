<!-- Cash Transaction Page with Modern Light Theme -->
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50">
    <!-- Header Section -->
    <div class="bg-white/70 backdrop-blur-sm border-b border-amber-200/20 shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h3
                        class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                        معاملة نقدية</h3>
                    <p class="text-gray-600 text-sm">إدارة المعاملات النقدية</p>
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
