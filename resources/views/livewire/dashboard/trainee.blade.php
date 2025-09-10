<div>
    <!-- Trainee Summary Table: Safe Name, Safe Balance, Startup Balance, Today's Transactions -->


<!-- Add Customer Quick Action for Trainee -->
@can('manage-customers')
<a href="{{ route('customers.create') }}"
    class="group flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-xl transition-all duration-200 mb-6">
    <div
        class="w-12 h-12 bg-indigo-100 group-hover:bg-indigo-200 rounded-lg flex items-center justify-center mr-4">
        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </div>
    <div>
        <h3 class="font-semibold text-indigo-900">اضافة عميل</h3>
        <p class="text-sm text-indigo-700">اضافة عميل جديد</p>
    </div>
</a>
@endcan

<!-- Transaction Search (same as agent dashboard) -->
<div class="mb-6 bg-white rounded-xl shadow p-6 flex flex-col items-center">
    <form method="GET" action="{{ route('dashboard') }}" class="w-full max-w-md flex flex-col gap-4">
        <input type="hidden" name="search_transaction" value="1">
        <label for="reference_number" class="block text-sm font-medium text-gray-700">بحث المعاملة برقم المرجع</label>
        <div class="flex gap-2">
            <input type="text" name="reference_number" id="reference_number" value="{{ request('reference_number') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" placeholder="Enter reference number...">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
        </div>
    </form>
    @if(isset($searchedTransaction))
        <div class="mt-6 w-full max-w-md bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-bold text-blue-800 mb-2">تفاصيل المعاملة</h4>
            <div class="text-sm text-gray-700">
                <div><span class="font-semibold">الرقم المرجعي:</span> {{ $searchedTransaction->reference_number }}</div>
                <div><span class="font-semibold">المبلغ:</span> {{ format_int($searchedTransaction->amount) }}</div>
                <div><span class="font-semibold">الحالة:</span> {{ $searchedTransaction->status }}</div>
                @if (isset($searchedTransaction->transaction_date_time) && $searchedTransaction->transaction_date_time)
                    <div><span class="font-semibold">تاريخ و وقت المعاملة:</span> {{ \Carbon\Carbon::parse($searchedTransaction->transaction_date_time)->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</div>
                @endif
                <div><span class="font-semibold">تاريخ الانشاء:</span> {{ \Carbon\Carbon::parse($searchedTransaction->created_at)->setTimezone('Africa/Cairo')->format('d/m/y h:i A') }}</div>
            </div>
            <div class="mt-4 text-right">
                <a href="{{ route('transactions.print', $searchedTransaction->reference_number) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2V7a2 2 0 012-2h16a2 2 0 012 2v9a2 2 0 01-2 2h-2" />
                        <rect width="12" height="8" x="6" y="14" rx="2" />
                    </svg>
                    طباعة
                </a>
            </div>
        </div>
    @elseif(request('reference_number'))
        <div class="mt-4 text-red-600 font-semibold">لا يوجد معاملة بهذا الرقم المرجعي.</div>
    @endif
</div>
    <!-- Quick Actions (copied from agent dashboard) -->
    <div class="mb-8 bg-white rounded-2xl shadow border border-gray-200 p-6">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                Quick Actions
            </h2>
            <p class="text-sm text-gray-500 mt-1">إنشاء معاملات جديدة أو الوصول إلى أدوات المعاملات</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('transactions.send') }}"
                class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded-xl transition-all duration-200">
                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900">إرسال المال</h3>
                    <p class="text-sm text-blue-700">إنشاء معاملة صادرة</p>
                </div>
            </a>
                <!-- Line Transfer Quick Action -->
                <a href="{{ route('transactions.line-transfer') }}" class="group flex items-center p-4 bg-purple-50 hover:bg-purple-100 border border-purple-100 rounded-xl transition-all duration-200">
                    <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-purple-900">تحويل رصيد خط</h3>
                        <p class="text-sm text-purple-700">إنشاء معاملة تحويل رصيد خط</p>
                    </div>
                </a>
            <a href="{{ route('transactions.receive') }}"
                class="group flex items-center p-4 bg-green-50 hover:bg-green-100 border border-green-100 rounded-xl transition-all duration-200">
                <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-900">استقبال المال</h3>
                    <p class="text-sm text-green-700">معالجة المعاملة الواردة</p>
                </div>
            </a>
            @can('create-cash-transactions')
                <a href="{{ route('transactions.cash') }}"
                    class="group flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 border border-yellow-100 rounded-xl transition-all duration-200">
                    <div class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-yellow-900">المعاملة النقدية</h3>
                        <p class="text-sm text-yellow-700">معالجة العمليات النقدية</p>
                    </div>
                </a>
            @endcan
            @can('manage-customers')
                <a href="{{ route('customers.create') }}"
                    class="group flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 rounded-xl transition-all duration-200">
                    <div class="w-12 h-12 bg-indigo-100 group-hover:bg-indigo-200 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-indigo-900">إضافة العميل</h3>
                        <p class="text-sm text-indigo-700">تسجيل عميل جديد</p>
                    </div>
                </a>
            @endcan
        </div>
    </div>
@if(isset($traineeLines) && count($traineeLines))
    <div class="mt-10 bg-white p-6 shadow-xl sm:rounded-lg">
        <h4 class="text-xl font-bold text-gray-900 mb-4">جميع الخطوط في فرعك</h4>
        <div class="mb-4">
            <span class="text-lg font-semibold text-gray-700">إجمالي رصيد الخطوط: </span>
            <span class="text-2xl font-bold text-blue-700">{{ format_int($traineeLinesTotalBalance ?? 0) }} EGP</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-3 text-center">
                            <input type="checkbox" id="select-all-trainee-lines" onclick="toggleAllTraineeLines(this)" @if(isset($selectedTraineeLineIds) && count($selectedTraineeLineIds) === count($traineeLines) && count($traineeLines) > 0) checked @endif />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">رقم الهاتف</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">الرصيد</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">المتبقي اليومي</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">المستلم اليومي</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">المتبقي الشهري</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">المستلم الشهري</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">الشبكة</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($traineeLines as $line)
                            @php
                                $dailyRemaining = $line->daily_remaining ?? 0;
                                $monthlyRemaining = $line->monthly_remaining ?? 0;
                                $dailyLimit = $line->daily_limit ?? 0;
                                $monthlyLimit = $line->monthly_limit ?? 0;
                                $currentBalance = $line->current_balance ?? 0;
                                $dailyStartingBalance = $line->daily_starting_balance ?? 0;
                                $monthlyStartingBalance = $line->starting_balance ?? 0;
                                $dailyUsage = $line->daily_usage ?? max(0, $currentBalance - $dailyStartingBalance);
                                $monthlyUsage = $line->monthly_usage ?? max(0, $currentBalance - $monthlyStartingBalance);
                                $circleColor = 'bg-green-400';
                                if ($dailyRemaining <= 240) {
                                    $circleColor = 'bg-red-500';
                                } elseif ($dailyRemaining <= 1800) {
                                    $circleColor = 'bg-yellow-400';
                                }
                            @endphp
                        <tr>
                            <td class="px-2 py-4 text-center">
                                <input type="checkbox" class="trainee-line-checkbox" value="{{ $line->id }}" onclick="updateTraineeLinesSum()" @if(isset($selectedTraineeLineIds) && in_array($line->id, $selectedTraineeLineIds)) checked @endif />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-3 h-3 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $circleColor }}"></div>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $line->mobile_number }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($line->current_balance) }} EGP</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($dailyRemaining) }} EGP</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($dailyUsage) }} EGP</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($monthlyRemaining) }} EGP</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ format_int($monthlyUsage) }} EGP</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $line->network }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($line->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td class="px-2 py-3 text-center" colspan="2">Selected Total</td>
                        <td class="px-6 py-3 text-sm text-blue-700" id="selected-trainee-current-balance">0 EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700" id="selected-trainee-daily-remaining">0 EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700" id="selected-trainee-daily-usage">0 EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700" id="selected-trainee-monthly-remaining">0 EGP</td>
                        <td class="px-6 py-3 text-sm text-blue-700" id="selected-trainee-monthly-usage">0 EGP</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            <script>
                function parseEGP(str) {
                    return parseInt((str || '').replace(/,/g, '').replace(/\s*EGP/, '')) || 0;
                }
                function updateTraineeLinesSum() {
                    let checkboxes = document.querySelectorAll('.trainee-line-checkbox');
                    let totalCurrent = 0,
                        totalDailyRem = 0,
                        totalDailyUsage = 0,
                        totalMonthlyRem = 0,
                        totalMonthlyUsage = 0;
                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            let row = cb.closest('tr');
                            let tds = row.querySelectorAll('td');
                            totalCurrent += parseEGP(tds[2].innerText);
                            totalDailyRem += parseEGP(tds[3].innerText);
                            totalDailyUsage += parseEGP(tds[4].innerText);
                            totalMonthlyRem += parseEGP(tds[5].innerText);
                            totalMonthlyUsage += parseEGP(tds[6].innerText);
                        }
                    });
                    document.getElementById('selected-trainee-current-balance').innerText = totalCurrent.toLocaleString() + ' EGP';
                    document.getElementById('selected-trainee-daily-remaining').innerText = totalDailyRem.toLocaleString() + ' EGP';
                    document.getElementById('selected-trainee-daily-usage').innerText = totalDailyUsage.toLocaleString() + ' EGP';
                    document.getElementById('selected-trainee-monthly-remaining').innerText = totalMonthlyRem.toLocaleString() + ' EGP';
                    document.getElementById('selected-trainee-monthly-usage').innerText = totalMonthlyUsage.toLocaleString() + ' EGP';
                }
                function toggleAllTraineeLines(source) {
                    let checkboxes = document.querySelectorAll('.trainee-line-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = source.checked;
                    });
                    updateTraineeLinesSum();
                }
            </script>
        </div>
    </div>
@endif
</div>
