{{-- Universal Filters Component --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z" />
        </svg>
        فلاتر البحث
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        {{-- Transaction Type Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">نوع العملية</label>
            <select wire:model.live="filterTransactionType"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">كل الأنواع</option>
                <option value="receive">استلام</option>
                <option value="transfer">تحويل</option>
                <option value="Deposit">إيداع</option>
                <option value="Withdrawal">سحب</option>
                <!-- adjustment removed -->
            </select>
        </div>
        {{-- Line Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                اختيار الخط
            </label>
            <select wire:model.live="selectedLine"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">اختر خط</option>
                @foreach ($lines as $lineId => $lineName)
                    <option value="{{ $lineId }}">{{ $lineName }}</option>
                @endforeach
            </select>
        </div>

        {{-- Reference Number Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الرقم المرجعي</label>
            <input wire:model.live.debounce.500ms="referenceNumber" type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="أدخل الرقم المرجعي">
        </div>

        {{-- Amount Range --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من مبلغ</label>
            <input wire:model.live.debounce.500ms="amountFrom" type="number" step="0.01" min="0"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="الحد الأدنى">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى مبلغ</label>
            <input wire:model.live.debounce.500ms="amountTo" type="number" step="0.01" min="0"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="الحد الأقصى">
        </div>

        {{-- Date Range --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
            <input wire:model.live="startDate" type="date"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
            <input wire:model.live="endDate" type="date"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        {{-- Branch Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الفروع</label>
            <select wire:model.live="selectedBranches" multiple
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all">جميع الفروع</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch['id'] ?? $branch->id }}">{{ $branch['name'] ?? $branch->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">اضغط Ctrl للاختيار المتعدد</p>
        </div>

        {{-- Employee Filter --}}
        @if (isset($showEmployeeFilter) && $showEmployeeFilter)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الموظف</label>
                <select wire:model.live="selectedEmployee"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">جميع الموظفين</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee['id'] ?? $employee->id }}">
                            {{ $employee['name'] ?? $employee->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Customer Filter --}}
        @if (isset($showCustomerFilter) && $showCustomerFilter && $reportType === 'customer')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">العميل</label>
                <input wire:model.live.debounce.500ms="customerSearch" type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="اسم أو كود أو رقم العميل">
            </div>
        @endif

        {{-- Mobile Number Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الجوال (إرسال أو استقبال أو رقم العميل)</label>
            <input wire:model.live.debounce.500ms="filterMobileNumber" type="text"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="أدخل رقم الجوال للبحث في الإرسال أو الاستقبال أو رقم العميل">
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3 mt-6">
        <button wire:click="generateReport"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow-md">
            تطبيق الفلاتر
        </button>

        <button wire:click="resetFilters"
            class="px-6 py-3 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700 transition shadow-md">
            إعادة تعيين
        </button>
    </div>
</div>
