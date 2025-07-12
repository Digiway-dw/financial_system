<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fido Dido</title>
    <style>
        body { width: 58mm; margin: 0 auto; font-family: 'Arial', sans-serif; text-align: center; font-size: 11px; }
        .receipt { padding: 4px; font-size: 11px; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; align-items: center; margin: 4px 0; flex-direction: row-reverse; font-size: 11px; }
        .label-ar { font-weight: bold; text-align: left; direction: rtl; flex: 1; }
        .value { text-align: right; direction: ltr; flex: 1; font-weight: normal; }
        .branch-bold { font-weight: bold; }
        h2 { font-family: 'Times New Roman', 'Georgia', serif; font-weight: bold; font-style: italic; font-size: 15px; margin: 4px 0; }
        h3, h4, h5 { margin: 2px 0; font-size: 12px; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
<div class="receipt">
    <h2>Fido Dido</h2>
    <div class="row"><span class="value">{{ \Carbon\Carbon::parse($cashTransaction->transaction_date_time)->format('d/m/y - h:i A') }}</span><span class="label-en"></span>ref:  {{ $cashTransaction->reference_number ?? $cashTransaction->id }}</div>
    <div class="line" style="margin: 4px 0;"></div>
    <div class="row"><span class="value">{{ $cashTransaction->safe->name ?? $cashTransaction->safe_id ?? 'N/A' }}</span><span class="label-en">Safe</span></div>
    <div class="row"><span class="value">{{ $cashTransaction->agent->name ?? $cashTransaction->agent_id ?? 'N/A' }}</span><span class="label-en">User</span></div>
    @if($cashTransaction->customer_name)
        <div class="row"><span class="value">{{ $cashTransaction->customer_name }}</span><span class="label-en">Customer</span></div>
    @endif
    @if($cashTransaction->customer_code)
        <div class="row"><span class="value">{{ $cashTransaction->customer_code }}</span><span class="label-en">Customer Code</span></div>
    @endif
    @if($cashTransaction->depositor_mobile_number)
        <div class="row"><span class="value">{{ $cashTransaction->depositor_mobile_number }}</span><span class="label-en">Mobile</span></div>
    @endif
    @php
        // For cash transactions, total is amount + commission for deposits, amount - commission for withdrawals
        $isWithdrawal = strtolower($cashTransaction->transaction_type) === 'withdrawal';
        $finalTotal = $isWithdrawal
            ? $cashTransaction->amount - ($cashTransaction->commission ?? 0)
            : $cashTransaction->amount + ($cashTransaction->commission ?? 0);
    @endphp
    <div class="row"><span class="value branch-bold">{{ number_format($finalTotal, 2) }}</span><span class="label-ar branch-bold">Total</span></div>
    <div class="line" style="margin: 4px 0;" ></div>
    <h5>01278120303  للشكاوى و المقترحات</h5>
    <button onclick="window.print()">Print</button>
</div>
</body>
</html> 