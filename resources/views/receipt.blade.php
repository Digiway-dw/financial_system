<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fido Dido</title>
    <style>
        body {
            width: 58mm;
            margin: 0 auto;
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 11px;
        }

        .receipt {
            padding: 4px;
            font-size: 11px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 4px 0;
            flex-direction: row-reverse;
            font-size: 11px;
        }

        .label-ar {
            font-weight: bold;
            text-align: left;
            direction: rtl;
            flex: 1;
        }

        .value {
            text-align: right;
            direction: ltr;
            flex: 1;
            font-weight: normal;
        }

        .branch-bold {
            font-weight: bold;
        }

        h2 {
            font-weight: bold;
            font-size: 15px;
            margin: 4px 0;
        }

        h3,
        h4,
        h5 {
            margin: 2px 0;
            font-size: 12px;
        }

        .circled {
            font-family: 'Times New Roman', 'Georgia', serif;
            font-weight: bold;
            font-style: italic;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <h2 class="circled">Fido Dido</h2>
        <div class="row"><span
                class="value">{{ \Carbon\Carbon::parse($transaction->transaction_date_time)->format('d/m/y - h:i A') }}</span><span
                class="label-en"></span>ref: {{ $transaction->reference_number ?? 'N/A' }}</div>
        <div class="line" style="margin: 4px 0;"></div>
        <div class="row"><span class="value">{{ $transaction->line->mobile_number ?? 'N/A' }}</span><span
                class="label-en">Line</span></div>
        <div class="row"><span class="value">{{ ucfirst(strtolower($transaction->transaction_type)) }}</span><span
                class="label-en">Type</span></div>
        <div class="row"><span
                class="value">{{ $transaction->agent->name ?? ($transaction->agent_id ?? 'N/A') }}</span><span
                class="label-en">User</span></div>
        <div class="row">
            <span class="value">{{ $transaction->customer_name ?? 'N/A' }}</span>
            <span class="label-en">Customer</span>
        </div>
        <div class="row">
            <span class="value">{{ $transaction->customer_code ?? 'N/A' }}</span>
            <span class="label-en">Customer Code</span>
        </div>
        @if (strtolower($transaction->transaction_type) === 'receive' && !empty($transaction->sender_mobile_number))
            <div class="row">
                <span class="value">{{ $transaction->sender_mobile_number }}</span>
                <span class="label-en">Sender Mobile</span>
            </div>
        @endif
        <div class="row">
            <span class="value">
                @if (strtolower($transaction->transaction_type) === 'transfer' && !empty($transaction->receiver_mobile_number))
                    {{ $transaction->receiver_mobile_number }}
                @else
                    {{ $transaction->customer_mobile_number ?? 'N/A' }}
                @endif
            </span>
            <span class="label-en">Mobile</span>
        </div>
        <div class="row"><span class="value">{{ number_format($transaction->amount, 2) }}</span><span
                class="label-en">Amount</span></div>
        <div class="row"><span class="value">{{ number_format($transaction->commission, 2) }}</span><span
                class="label-en">Commission</span></div>
        @if ($transaction->deduction > 0)
            <div class="row"><span class="value">{{ number_format(abs($transaction->deduction), 2) }}</span><span
                    class="label-en">Discount</span></div>
        @endif
        @php
            $isReceive = strtolower($transaction->transaction_type) === 'receive';
            $isWithdrawal = strtolower($transaction->transaction_type) === 'withdrawal';
            $amount = $transaction->amount;
            $commission = $transaction->commission;
            $deduction = abs($transaction->deduction ?? 0);
            $finalTotal = $isReceive
                ? $amount - ($commission - $deduction)
                : ($isWithdrawal
                    ? $amount - $commission
                    : $amount + $commission - $deduction);
        @endphp
        <div class="row"><span class="value branch-bold">{{ number_format($finalTotal, 2) }}</span><span
                class="label-ar branch-bold">Total</span></div>
        <div class="line" style="margin: 4px 0;"></div>
        <h5>01278120303 للشكاوى و المقترحات</h5>
        <button onclick="window.print()">Print</button>
        <button onclick="window.location.href='{{ route('dashboard') }}'" style="margin-left: 8px;">Home</button>
    </div>
</body>

</html>
