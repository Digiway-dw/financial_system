<div>
    <!-- Auditor Name -->
    <div class="flex items-center justify-end mb-2">
        <span class="text-sm font-medium text-gray-700">المدقق:</span>
        <span class="ml-2 text-base font-bold text-gray-900">{{ $auditorName }}</span>
    </div>
    <!-- Branch Selector as HTML form -->
    <form method="GET" action="{{ url()->current() }}" class="flex items-center justify-end mb-4">
        <label for="branch" class="ml-2 text-sm font-medium text-gray-700">فرع:</label>
        <select name="branch" id="branch" class="form-select rounded-md shadow-sm block w-auto px-2 py-1 border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" onchange="this.form.submit()">
            <option value="all" {{ request('branch', 'all') == 'all' ? 'selected' : '' }}>كل الفروع</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
    </form>
    <!-- Branch Details -->
    @if($selectedBranchDetails)
        <div class="mb-4 text-right">
            <div class="text-base font-bold text-gray-800">{{ $selectedBranchDetails['name'] }}</div>
        </div>
    @endif
    <table class="min-w-max w-full table-auto border border-gray-300 mb-6">
        <thead>
            <tr class="bg-gray-100 text-center">
                <th class="px-4 py-2 border">رصيد افتتاحي</th>
                <th class="px-4 py-2 border">عدد المعاملات</th>
                <th class="px-4 py-2 border">الخزينة</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td class="px-4 py-2 border text-blue-700 font-bold">{{ number_format($startupSafeBalance, 2) }}</td>
                <td class="px-4 py-2 border text-purple-700 font-bold">{{ $totalTransactionsCount }}</td>
                <td class="px-4 py-2 border font-bold">{{ number_format($safesBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mb-8">

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