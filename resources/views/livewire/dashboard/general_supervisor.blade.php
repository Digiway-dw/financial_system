<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">General Supervisor Dashboard Overview</h2>
    @if(isset($totalSafeBalance))
        <div class="text-lg text-gray-700 mb-1">Total Safe Balance: <span class="font-bold">{{ number_format($totalSafeBalance, 2) }} EGP</span></div>
    @endif
    @if(isset($branchSafeBalance))
        <div class="text-lg text-gray-700 mb-4">Branch Safe Balance: <span class="font-bold">{{ number_format($branchSafeBalance, 2) }} EGP</span></div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mb-8">
        <!-- Send/Receive -->
        <a href="{{ route('transactions.index') }}" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center hover:shadow-lg transition border-b-4 border-blue-400">
            <x-heroicon-o-arrow-path class="h-8 w-8 text-blue-500 mb-2" />
            <span class="text-md font-semibold text-gray-800">التحويل - ارسال واستقبال</span>
            <span class="text-xs text-gray-500 mt-1">ادارة التحويلات</span>
        </a>
        <!-- Branch Report -->
        <a href="{{ route('reports.index') }}" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center hover:shadow-lg transition border-b-4 border-cyan-400">
            <x-heroicon-o-chart-bar class="h-8 w-8 text-cyan-500 mb-2" />
            <span class="text-md font-semibold text-gray-800">تقرير الفروع</span>
            <span class="text-xs text-gray-500 mt-1">تقارير ومعاملات الفروع</span>
        </a>
        <!-- Transaction Report -->
        <a href="{{ route('reports.index') }}" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center hover:shadow-lg transition border-b-4 border-blue-500">
            <x-heroicon-o-document-text class="h-8 w-8 text-blue-500 mb-2" />
            <span class="text-md font-semibold text-gray-800">تقرير المعاملات</span>
            <span class="text-xs text-gray-500 mt-1">تقارير الارسال والاستقبال</span>
        </a>
        <!-- Lines -->
        <a href="{{ route('lines.index') }}" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center hover:shadow-lg transition border-b-4 border-indigo-400">
            <x-heroicon-o-device-phone-mobile class="h-8 w-8 text-indigo-500 mb-2" />
            <span class="text-md font-semibold text-gray-800">الخطوط</span>
            <span class="text-xs text-gray-500 mt-1">ادارة واعدادات الخطوط</span>
        </a>
        <!-- Transaction History -->
        <a href="{{ route('transactions.index') }}" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center hover:shadow-lg transition border-b-4 border-gray-400">
            <x-heroicon-o-users class="h-8 w-8 text-gray-500 mb-2" />
            <span class="text-md font-semibold text-gray-800">Transaction History</span>
        </a>
        <!-- Customers -->
        <a href="{{ route('customers.index') }}" class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center hover:shadow-lg transition border-b-4 border-blue-400">
            <x-heroicon-o-user class="h-8 w-8 text-blue-500 mb-2" />
            <span class="text-md font-semibold text-gray-800">العملاء</span>
            <span class="text-xs text-gray-500 mt-1">ادارة العملاء</span>
        </a>
    </div>
</div> 