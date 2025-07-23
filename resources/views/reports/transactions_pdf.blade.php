<!DOCTYPE html>
<html>
<head>
    <title>تقرير المعاملات</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            color: #555;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
            table-layout: auto;
            word-break: break-word;
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            color: #333;
        }
        .summary {
            margin-top: 20px;
            border: 1px solid #eee;
            padding: 10px;
            background-color: #f9f9f9;
            font-size: 13px;
        }
        .summary h2 {
            margin-top: 0;
            color: #333;
        }
        .table-container {
            overflow-x: auto;
        }
        .rtl {
            direction: rtl;
            unicode-bidi: embed;
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    <h1>تقرير المعاملات المالية</h1>

    <div class="summary">
        <h2>ملخص التقرير</h2>
        <p><strong>تاريخ البداية:</strong> {{ $startDate }}</p>
        <p><strong>تاريخ النهاية:</strong> {{ $endDate }}</p>
        <p><strong>المبلغ المحول:</strong> {{ number_format($totalTransferred, 2) }} EGP</p>
        <p><strong>العمولة:</strong> {{ number_format($totalCommission, 2) }} EGP</p>
        <p><strong>الخصم:</strong> {{ number_format($totalDeductions, 2) }} EGP</p>
        <p><strong>الأرباح:</strong> {{ number_format($netProfits, 2) }} EGP</p>
    </div>

    <h2>تفاصيل المعاملات</h2>
    <div class="table-container">
    <table>
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
                <td class="{{ preg_match('/[\x{0600}-\x{06FF}]/u', $transaction['customer_name']) ? 'rtl' : '' }}">
                    {{ $transaction['customer_name'] }}
                </td>
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