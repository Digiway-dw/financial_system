<!-- Receive Transaction Page with Modern Light Theme -->
<div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50">
    <!-- Header Section -->
    <div class="bg-white/70 backdrop-blur-sm border-b border-green-200/20 shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                </div>
                <div>
                    <h3
                        class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        استقبال حوالة</h3>
                    <p class="text-gray-600 text-sm">استقبال حوالة مالية جديدة</p>
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
