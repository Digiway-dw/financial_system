<!-- Cash Transaction Choice Page -->
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 flex flex-col items-center justify-center">
    <div class="bg-white/80 rounded-2xl shadow-xl p-10 flex flex-col items-center gap-8">
        <h2 class="text-3xl font-bold text-amber-700 mb-6">اختر نوع المعاملة النقدية</h2>
        <div class="flex gap-16">
            <!-- Withdrawal Icon -->
            <a href="{{ route('transactions.cash.withdrawal') }}" class="flex flex-col items-center group">
                <div class="bg-gradient-to-br from-orange-400 to-amber-600 p-6 rounded-full shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="mt-4 text-lg font-semibold text-amber-800">سحب</span>
            </a>
            <!-- Deposit Icon -->
            <a href="{{ route('transactions.cash.deposit') }}" class="flex flex-col items-center group">
                <div class="bg-gradient-to-br from-amber-400 to-orange-600 p-6 rounded-full shadow-lg group-hover:scale-110 transition-transform">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="mt-4 text-lg font-semibold text-amber-800">ايداع</span>
            </a>
        </div>
    </div>
</div>
