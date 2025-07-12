<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Fido Dido</title>
    <style>
        body { width: 58mm; margin: 0 auto; font-family: 'Tahoma', monospace; text-align: center; }
        .receipt { padding: 8px; }
        .line { border-top: 1px dashed #000; margin: 16px 0; }
        .row { display: flex; justify-content: space-between; align-items: center; margin: 12px 0; flex-direction: row-reverse; }
        .label-ar { font-weight: bold; text-align: left; direction: rtl; flex: 1; }
        .value { text-align: right; direction: ltr; flex: 1; font-weight: normal; }
        .branch-bold { font-weight: bold; }
        h2 { font-weight: bold; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
<div class="receipt">
    <h2>Fido Dido</h2>
    <div class="line"></div>
    <div class="row"><span class="value">{{ $transaction->reference_number ?? 'N/A' }}</span><span class="label-ar">ref_no</span></div>
    <div class="row"><span class="value">{{ $transaction->transaction_date_time }}</span><span class="label-ar">Date</span></div>
    <div class="row"><span class="value branch-bold">{{ __($transaction->transaction_type) }}</span><span class="label-ar">Type</span></div>
    <div class="row"><span class="value branch-bold">{{ __($transaction->status) }}</span><span class="label-ar">Status</span></div>
    <div class="row"><span class="value">{{ $transaction->line->mobile_number ?? 'N/A' }}</span><span class="label-ar">Line</span></div>
    <div class="row"><span class="value">{{ $transaction->agent->name ?? $transaction->agent_id ?? 'N/A' }}</span><span class="label-ar">User</span></div>
    <div class="row"><span class="value">{{ $transaction->customer_name ?? 'N/A' }}</span><span class="label-ar">Customer</span></div>
    <div class="row"><span class="value">{{ $transaction->customer_mobile_number ?? 'N/A' }}</span><span class="label-ar">Mobile</span></div>
    <div class="row"><span class="value">{{ number_format($transaction->amount, 2) }}</span><span class="label-ar">Amount</span></div>
    <div class="row"><span class="value">{{ number_format($transaction->commission, 2) }}</span><span class="label-ar">Commission</span></div>
    <div class="row"><span class="value branch-bold">{{ $transaction->branch->name ?? 'N/A' }}</span><span class="label-ar branch-bold">Branch</span></div>
    <div class="line"></div>
    <button onclick="window.print()">Print</button>
</div>
</body>
</html> 