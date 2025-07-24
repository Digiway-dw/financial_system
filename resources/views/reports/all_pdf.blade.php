<!DOCTYPE html>
<html>
<head>
    <title>تقرير النظام الكامل</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #333; }
        h1, h2, h3 { color: #222; }
        .section { margin-bottom: 24px; }
        .summary-table, .balances-table, .transactions-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .summary-table th, .summary-table td, .balances-table th, .balances-table td, .transactions-table th, .transactions-table td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        .summary-table th, .balances-table th, .transactions-table th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h1>تقرير النظام الكامل</h1>
    <div class="section">
        <h2>الفترة</h2>
        <p><strong>من:</strong> {{ $startDate }} <strong>إلى:</strong> {{ $endDate }}</p>
    </div>
    <div class="section">
        <h2>ملخص مالي</h2>
        <table class="summary-table">
            <tr><th>المبلغ المستلم</th><td>{{ number_format($financialSummary['total_transfer'] ?? 0, 2) }} EGP</td></tr>
            <tr><th>العمولة</th><td>{{ number_format($financialSummary['commission_earned'] ?? 0, 2) }} EGP</td></tr>
            <tr><th>الخصم</th><td>{{ number_format($financialSummary['total_discounts'] ?? 0, 2) }} EGP</td></tr>
            <tr><th>الربح الصافي</th><td>{{ number_format($financialSummary['net_profit'] ?? 0, 2) }} EGP</td></tr>
        </table>
    </div>
    <div class="section">
        <h2>الرصيد المحفظة العميل</h2>
        <table class="balances-table">
            <thead><tr><th>اسم العميل</th><th>الرصيد</th></tr></thead>
            <tbody>
            @foreach($customerBalances as $customer)
                <tr><td>{{ $customer['customer'] }}</td><td>{{ number_format($customer['balance'], 2) }} EGP</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="section">
        <h2>الرصيد الخزني بالفروع</h2>
        <table class="balances-table">
            <thead><tr><th>الفرع</th><th>الرصيد</th></tr></thead>
            <tbody>
            @foreach($safeBalances as $safe)
                <tr><td>{{ $safe['branch'] }}</td><td>{{ number_format($safe['balance'], 2) }} EGP</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="section">
        <h2>الرصيد الخطي بالفروع</h2>
        <table class="balances-table">
            <thead><tr><th>الفرع</th><th>الرصيد</th></tr></thead>
            <tbody>
            @foreach($lineBalances as $line)
                <tr><td>{{ $line['branch'] }}</td><td>{{ number_format($line['balance'], 2) }} EGP</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="section">
        <h2>تفاصيل المعاملات</h2>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>اسم العميل</th>
                    <th>المبلغ</th>
                    <th>العمولة</th>
                    <th>الخصم</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                    <th>المسؤول</th>
                    <th>الفرع</th>
                    <th>التاريخ والوقت</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction['id'] }}</td>
                    <td>{{ $transaction['customer_name'] }}</td>
                    <td>{{ number_format($transaction['amount'], 2) }} EGP</td>
                    <td>{{ number_format($transaction['commission'], 2) }} EGP</td>
                    <td>{{ number_format($transaction['deduction'], 2) }} EGP</td>
                    <td>{{ $transaction['transaction_type'] }}</td>
                    <td>{{ $transaction['status'] }}</td>
                    <td>{{ $transaction['agent_name'] ?? '' }}</td>
                    <td>{{ $transaction['branch_name'] ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction['transaction_date_time'])->format('d/m/y h:i A') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</body>
</html> 