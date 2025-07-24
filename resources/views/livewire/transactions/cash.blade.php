<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-800 mb-6">إدارة المعاملات النقدية</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($canDeposit)
                            <div
                                class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg shadow-md border border-blue-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-medium text-blue-800">ايداع الاموال</h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <p class="text-blue-600 mb-4">إضافة الأموال إلى الخزينة أو إلى محفظة العميل أو لأغراض إدارية.</p>
                                <a href="{{ route('transactions.cash.deposit') }}"
                                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                    الانتقال للايداع
                                </a>
                            </div>
                        @endif

                        @if ($canWithdraw)
                            <div
                                class="bg-gradient-to-r from-red-50 to-red-100 p-6 rounded-lg shadow-md border border-red-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-medium text-red-800">سحب الاموال</h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                </div>
                                <p class="text-red-600 mb-4">سحب الأموال من الخزينة أو محفظة العميل أو لأغراض إدارية.</p>
                                <a href="{{ route('transactions.cash.withdrawal') }}"
                                    class="inline-block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                                    الانتقال للسحب
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-10">
                        {{-- Removed recent cash transactions table as per request --}}
                    </div>

                    <div class="mt-6 text-right">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800">
                            &larr; العودة لجميع المعاملات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
