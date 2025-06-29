<!DOCTYPE html>
<html>
<head>
    <title>Transactions Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
            padding: 15px;
            background-color: #f9f9f9;
        }
        .summary h2 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Financial Transactions Report</h1>

    <div class="summary">
        <h2>Report Summary</h2>
        <p><strong>Start Date:</strong> {{ $startDate }}</p>
        <p><strong>End Date:</strong> {{ $endDate }}</p>
        <p><strong>Total Transferred:</strong> {{ number_format($totalTransferred, 2) }} EGP</p>
        <p><strong>Total Commission:</strong> {{ number_format($totalCommission, 2) }} EGP</p>
        <p><strong>Total Deductions:</strong> {{ number_format($totalDeductions, 2) }} EGP</p>
        <p><strong>Net Profits:</strong> {{ number_format($netProfits, 2) }} EGP</p>
    </div>

    <h2>Transactions Details</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Amount</th>
                <th>Commission</th>
                <th>Deduction</th>
                <th>Type</th>
                <th>Status</th>
                <th>Agent</th>
                <th>Branch</th>
                <th>Date/Time</th>
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
                <td>{{ $transaction['agent_name'] }}</td>
                <td>{{ $transaction['branch_name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($transaction['transaction_date_time'])->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 