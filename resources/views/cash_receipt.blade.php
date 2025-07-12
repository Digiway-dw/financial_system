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
    <div class="row"><span class="value">{{ $cashTransaction->reference_number ?? $cashTransaction->id }}</span><span class="label-ar">ref_no</span></div>
    <div class="row"><span class="value">{{ $cashTransaction->transaction_date_time }}</span><span class="label-ar">Date</span></div>
    <div class="row"><span class="value branch-bold">{{ __($cashTransaction->transaction_type) }}</span><span class="label-ar">Type</span></div>
    <div class="row"><span class="value branch-bold">{{ __($cashTransaction->status) }}</span><span class="label-ar">Status</span></div>
    <div class="row"><span class="value">{{ $cashTransaction->safe->name ?? $cashTransaction->safe_id ?? 'N/A' }}</span><span class="label-ar">Safe</span></div>
    <div class="row"><span class="value">{{ $cashTransaction->agent->name ?? $cashTransaction->agent_id ?? 'N/A' }}</span><span class="label-ar">User</span></div>
    @if($cashTransaction->customer_name)
        <div class="row"><span class="value">{{ $cashTransaction->customer_name }}</span><span class="label-ar">Customer</span></div>
    @endif
    @if($cashTransaction->customer_code)
        <div class="row"><span class="value">{{ $cashTransaction->customer_code }}</span><span class="label-ar">Customer Code</span></div>
    @endif
    @if($cashTransaction->depositor_mobile_number)
        <div class="row"><span class="value">{{ $cashTransaction->depositor_mobile_number }}</span><span class="label-ar">Mobile</span></div>
    @endif
    <div class="row"><span class="value">{{ number_format($cashTransaction->amount, 2) }}</span><span class="label-ar">Amount</span></div>
    <div class="row"><span class="value">{{ number_format($cashTransaction->commission ?? 0, 2) }}</span><span class="label-ar">Commission</span></div>
    <div class="row"><span class="value branch-bold">{{ $cashTransaction->safe->branch->name ?? 'N/A' }}</span><span class="label-ar branch-bold">Branch</span></div>
    @if($cashTransaction->notes)
        <div class="row"><span class="value">{{ $cashTransaction->notes }}</span><span class="label-ar">Notes</span></div>
    @endif
    <div class="line"></div>
    <button onclick="window.print()">Print</button>
</div>
</body>
</html> 