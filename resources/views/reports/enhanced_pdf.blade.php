<!DOCTYPE html>
<html dir="rtl">

<head>
    <title>{{ ucfirst($reportType) }} Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #333;
            direction: rtl;
            text-align: right;
        }

        h1,
        h2,
        h3 {
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .section {
            margin-bottom: 24px;
        }

        .summary-table,
        .balances-table,
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .summary-table th,
        .summary-table td,
        .balances-table th,
        .balances-table td,
        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: right;
        }

        .summary-table th,
        .balances-table th,
        .transactions-table th {
            background: #f2f2f2;
            font-weight: bold;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
        }

        .totals-section {
            background: #f8f9fa;
            padding: 15px;
            border: 2px solid #007bff;
            border-radius: 5px;
            margin: 20px 0;
        }

        .page-break {
            page-break-before: always;
        }

        .status-completed {
            color: #28a745;
        }

        .status-pending {
            color: #ffc107;
        }

        .status-rejected {
            color: #dc3545;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>
            @switch($reportType)
                @case('employee')
                    تقرير الموظف
                @break

                @case('customer')
                    تقرير العميل
                @break

                @case('branch')
                    تقرير الفرع
                @break

                @default
                    تقرير المعاملات المالية
            @endswitch
        </h1>
        <p><strong>الفترة:</strong> {{ $startDate }} إلى {{ $endDate }}</p>
        <p><strong>تاريخ التقرير:</strong> {{ now()->format('Y/m/d H:i') }}</p>
    </div>

    {{-- Employee Details --}}
    @if ($reportType === 'employee' && isset($employeeDetails))
        <div class="section">
            <h2>معلومات الموظف</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell"><strong>الاسم:</strong> {{ $employeeDetails['name'] }}</div>
                    <div class="info-cell"><strong>الهاتف:</strong> {{ $employeeDetails['phone'] }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell"><strong>الفرع:</strong> {{ $employeeDetails['branch'] }}</div>
                    <div class="info-cell"><strong>تاريخ التوظيف:</strong>
                        {{ $employeeDetails['employment_start_date'] ?? 'غير محدد' }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Customer Details --}}
    @if ($reportType === 'customer' && isset($customerDetails))
        <div class="section">
            <h2>معلومات العميل</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell"><strong>اسم العميل:</strong> {{ $customerDetails['name'] }}</div>
                    <div class="info-cell"><strong>كود العميل:</strong> {{ $customerDetails['customer_code'] }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell"><strong>رقم الجوال:</strong> {{ $customerDetails['mobile_number'] }}</div>
                    <div class="info-cell"><strong>رصيد المحفظة:</strong>
                        @if ($customerDetails['is_client'])
                            {{ number_format($customerDetails['balance'], 2) }} EGP
                        @else
                            غير متاح
                        @endif
                    </div>
                </div>
                @if (isset($customerDetails['safe_balance']) && $customerDetails['safe_balance'] !== null)
                    <div class="info-row">
                        <div class="info-cell" colspan="2"><strong>رصيد الخزينة المربوطة:</strong>
                            {{ number_format($customerDetails['safe_balance'], 2) }} EGP</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Branch Balances --}}
    @if ($reportType === 'branch' && isset($branchDetails))
        <div class="section">
            <h2>أرصدة الخزائن بالفروع</h2>
            <table class="balances-table">
                <thead>
                    <tr>
                        <th>الفرع</th>
                        <th>الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branchDetails['safe_balances'] as $safe)
                        <tr>
                            <td>{{ $safe['branch'] }}</td>
                            <td>{{ number_format($safe['balance'], 2) }} EGP</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>أرصدة الخطوط بالفروع</h2>
            <table class="balances-table">
                <thead>
                    <tr>
                        <th>الفرع</th>
                        <th>الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branchDetails['line_balances'] as $line)
                        <tr>
                            <td>{{ $line['branch'] }}</td>
                            <td>{{ number_format($line['balance'], 2) }} EGP</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Financial Summary --}}
    @if (isset($totals) && !empty($totals))
        <div class="totals-section">
            <h2>الملخص المالي</h2>
            <table class="summary-table">
                <tr>
                    <th>إجمالي التداول</th>
                    <td>{{ number_format($totals['total_turnover'] ?? 0, 2) }} EGP</td>
                </tr>
                <tr>
                    <th>إجمالي العمولات</th>
                    <td>{{ number_format($totals['total_commissions'] ?? 0, 2) }} EGP</td>
                </tr>
                <tr>
                    <th>إجمالي الخصومات</th>
                    <td>{{ number_format($totals['total_deductions'] ?? 0, 2) }} EGP</td>
                </tr>
                @if ($reportType === 'branch' && isset($totals['total_expenses']))
                    <tr>
                        <th>مصاريف الفروع</th>
                        <td>{{ number_format($totals['total_expenses'], 2) }} EGP</td>
                    </tr>
                    <tr>
                        <th>صافي الربح (بعد المصاريف)</th>
                        <td>{{ number_format(($totals['net_profit'] ?? 0) - ($totals['total_expenses'] ?? 0), 2) }} EGP
                        </td>
                    </tr>
                @else
                    <tr>
                        <th>صافي الربح</th>
                        <td>{{ number_format($totals['net_profit'] ?? 0, 2) }} EGP</td>
                    </tr>
                @endif
                <tr>
                    <th>عدد المعاملات</th>
                    <td>{{ number_format($totals['transactions_count'] ?? 0) }}</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Transactions Table --}}
    @if (isset($transactions) && !empty($transactions))
        <div class="page-break">
            <div class="section">
                <h2>تفاصيل المعاملات</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الرقم المرجعي</th>
                            <th>اسم العميل</th>
                            <th>المبلغ</th>
                            <th>العمولة</th>
                            <th>الخصم</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الموظف</th>
                            <th>الفرع</th>
                            <th>التاريخ والوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $index => $transaction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $transaction['reference_number'] ?? 'N/A' }}</td>
                                <td>{{ $transaction['customer_name'] ?? 'N/A' }}</td>
                                <td>{{ number_format($transaction['amount'] ?? 0, 2) }} EGP</td>
                                <td>{{ number_format($transaction['commission'] ?? 0, 2) }} EGP</td>
                                <td>{{ number_format($transaction['deduction'] ?? 0, 2) }} EGP</td>
                                <td>{{ $transaction['transaction_type'] ?? 'N/A' }}</td>
                                <td class="status-{{ $transaction['status'] ?? 'unknown' }}">
                                    {{ $transaction['status'] ?? 'N/A' }}
                                </td>
                                <td>{{ $transaction['agent_name'] ?? 'N/A' }}</td>
                                <td>{{ $transaction['branch_name'] ?? 'N/A' }}</td>
                                <td>
                                    @if (isset($transaction['transaction_date_time']))
                                        {{ \Carbon\Carbon::parse($transaction['transaction_date_time'])->format('d/m/Y H:i') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div style="position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 10px; color: #666;">
        تم إنشاء هذا التقرير بواسطة النظام المالي - {{ now()->format('Y/m/d H:i:s') }}
    </div>
</body>

</html>
