<?php
function formatINR($amount)
{
    if (!$amount) {
        return '0.00';
    }
    $explrestunits = '';
    $amount = round($amount);
    $num = strlen($amount);
    if ($num > 3) {
        $lastthree = substr($amount, -3);
        $restunits = substr($amount, 0, -3);
        $restunits = strlen($restunits) % 2 == 1 ? '0' . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        foreach ($expunit as $key => $value) {
            if ($key == 0) {
                $explrestunits .= (int) $value . ',';
            } else {
                $explrestunits .= $value . ',';
            }
        }
        $formatted = $explrestunits . $lastthree;
    } else {
        $formatted = $amount;
    }
    return $formatted;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} - Ledger</title>
    <style>
        @page {
            margin: 40px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #444;
        }

        /* Grouping Styles */
        .customer-section {
            border: 1px solid #ccc;
            margin-bottom: 25px;

        }

        .customer-header {
            background: #f9f9f9;
            padding: 6px 10px;
            border-bottom: 1px solid #999;
            font-weight: bold;
            font-size: 13px;
        }

        .company-header {
            background: #f9f9f9;
            padding: 6px 10px;
            border-bottom: 1px solid #999;
            font-weight: bold;
            font-size: 13px;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #ffffff;
            padding: 8px;
            border-bottom: 1px solid #999;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }


        .debit {
            color: #d32f2f;
        }


        .credit {
            color: #2e7d32;
            background: #ffffff;
            font-weight: bold;
        }


        .pending-box {
            color: #ff0000;
            background: #ffffff;
            font-weight: bold;
        }

        footer {
            position: fixed;
            bottom: -20px;
            font-size: 12px;
            color: #777;
            width: 100%;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td width="60%" class="company-info">
                <h2>KMD TEA AND AGRO</h2>
                Room No. 316, 3rd Floor, 32 Ezra Street<br>
                Corp Address: Room No. 46, Nilhat House, 11 R, Kolkata<br>
                West Bengal, Code: 19<br>
                <strong>GSTIN:</strong> 19ABBFK5569Q1ZF | <strong>PAN:</strong> ABBFK5569Q
            </td>
            <td width="40%" align="right" style="vertical-align: top;">
                Date: {{ date('d-m-Y') }}
            </td>
        </tr>
    </table>


    <div class="report-title">Ledger</div>

    @foreach ($ledger as $customer)
        <div class="customer-section">
            <div class="customer-header">
                Buyer: {{ $customer['customer_name'] ?? 'N/A' }}
            </div>

            @foreach ($customer['companies'] as $company)
                <div class="company-header">Company: {{ $company['company_name'] ?? 'N/A' }}</div>

                @foreach ($company['invoices'] as $invoice)
                    <div style=" background: #f9f9f9; font-size:13px ;border-color: #999;">
                        <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                            <tr>
                                <td style="text-align: left;"><strong>Invoice No:</strong> {{ $invoice['inv_no'] }}</td>
                                <td style="text-align: center;"><strong>Date:</strong>
                                    {{ $invoice['inv_date_formatted'] }}</td>
                                @php
                                    $due = $invoice['grand_total'] ?? 0;
                                    foreach ($invoice['payment_details'] ?? [] as $payment) {
                                        $due -= $payment['paid_amount'] ?? 0;
                                    }
                                @endphp
                                <td style="text-align: right;  background: #f9f9f9;">
                                    <strong>Due:</strong>
                                    <span class="{{ $due > 0 ? 'pending-box' : 'credit' }}"  style="text-align: right;  background: #f9f9f9;">
                                        {{ formatINR($due) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th width="12%">Date</th>
                                <th width="43%">Details / Remarks</th>
                                <th width="15%" class="text-right">Debit</th>
                                <th width="15%" class="text-right">Credit</th>
                                <th width="15%" class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Invoice Row --}}
                            <tr>
                                <td>{{ $invoice['inv_date_formatted'] }}</td>
                                <td>Invoice Balance</td>
                                <td class="text-right font-bold">{{ formatINR($invoice['grand_total']) }}</td>
                                <td class="text-right"><b>0.00</b></td>
                                <td class="text-right "><b>{{ formatINR($invoice['grand_total']) }}</b></td>
                            </tr>

                            {{-- Payments --}}
                            @forelse ($invoice['payment_details'] as $payment)
                                <tr>
                                    <td>{{ isset($payment['datetime']) ? \Carbon\Carbon::parse($payment['datetime'])->format('d-m-Y') : '-' }}
                                    </td>
                                    <td>
                                        Payment Received ({{ $payment['paid_type'] ?? 'N/A' }})<br>
                                        <small>Ref: {{ $payment['receipt_number'] }} | By:
                                            {{ $payment['paid_by'] }}</small>
                                    </td>
                                    <td class="text-right"><b>0.00</b></td>
                                    <td class="text-right"><b>{{ formatINR($payment['paid_amount']) }}</b></td>
                                    <td class="text-right">
                                        <b>{{ formatINR($payment['pending_amount']) ?? 0.0 }}</b></td>
                                </tr>
                                @if ($payment['pending_amount'] == 0)
                                    <tr>
                                        <td colspan="5" style="text-align:center;  color: #2ecc71;"><b>All payments
                                                completed</b> </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center; color: #e74c3c;"><b>No payment
                                            transactions found for this invoice.</b></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endforeach
            @endforeach
        </div>
    @endforeach

    <footer>
        <table width="100%">
            <tr>
                <td>Generated by System | {{ config('app.name') }}</td>
                <td class="text-right">Printed: {{ date('d-M-Y H:i') }}</td>
            </tr>
        </table>
    </footer>
</body>

</html>
