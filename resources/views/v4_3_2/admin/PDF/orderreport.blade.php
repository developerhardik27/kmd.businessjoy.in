<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} - Payment Receipt</title>
    <style>
        /* Modern reset and base styles */
        @page {
            margin: 30px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Typography */
        h2 {
            font-size: 18px;
            margin: 0;
            color: #000;
        }

        strong {
            color: #000;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .company-info {
            line-height: 1.6;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 0;
            border-top: 1px solid #eee;

            margin: 15px 0;
            background-color: #f9f9f9;
        }

        /* Invoice Container */
        .invoice-box {
            margin-bottom: 35px;
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 4px;
        }

        .invoice-header {
            background: #f4f7f9;
            padding: 10px;
            border-bottom: 1px solid #d1d1d1;
            overflow: hidden;
        }

        .invoice-no {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            float: left;
        }

        .grand-total {
            float: right;
            font-size: 14px;
            font-weight: bold;
        }

        .invoice-meta {
            padding: 8px 10px;
            background: #fff;
            font-size: 10px;
            color: #666;
            border-bottom: 1px solid #eee;
        }

        /* Table Styling */
        table.payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.payment-table th {
            background-color: #fcfcfc;
            color: #555;
            font-weight: bold;
            text-align: left;
            padding: 8px 5px;
            border-bottom: 1px solid #444;
            text-transform: uppercase;
            font-size: 9px;
        }

        table.payment-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .blue-text {
            color: green;
            font-weight: bold;
        }

        .pending-text {
            color: #d9534f;
            font-weight: bold;
        }

        /* Footer */
        #footer {
            position: fixed;
            bottom: -10px;
            width: 100%;
            border-top: 1px solid #eee;
            padding-top: 5px;
            color: #999;
            font-size: 9px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
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

    <div class="report-title"> SAUDA REGISTER</div>

    @foreach ($order as $orders)
    <div class="invoice-box">
        <div class="invoice-header clearfix">
            <span class="invoice-no">Buyer Name #{{ $orders['buyer_name'] ?? '-' }}</span>
            <span class="grand-total">Final Amount {{ $orders['final_amount'] ?? '-' }}</span>
        </div>

        <div class="invoice-meta">
            <strong>Credit Days:</strong> {{ $orders['credit_days'] ?? '-' }} &nbsp;|&nbsp;
            <strong>Discount:</strong> {{ $orders['discount'] ?? '-' }} &nbsp;|&nbsp;
            <strong>Transport Name:</strong> <span style="text-transform: uppercase">{{ $orders['transport_name'] ?? '-'
                }}</span> &nbsp;|&nbsp;
            <strong>Total Net KG:</strong> {{ $orders['totalNetKg'] ?? '-' }} to {{ $invoice['to_date'] ?? '-' }}
        </div>

        <table class="payment-table">
            <thead>
                <tr>
                    <th>Garden Name</th>
                    <th>Grade Name</th>
                    <th>Invoice No</th>
                    <th>Bags</th>
                    <th>KG</th>
                    <th>Net KG</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders['details'] as $detail)
                <tr>
                    <td>{{ $detail['garden_name'] ?? '-' }}</td>
                    <td>{{ $detail['grade_name'] ?? '-' }}</td>
                    <td>{{ $detail['invoice_no'] ?? '-' }}</td>
                    <td>{{ $detail['bags'] ?? '-' }}</td>
                    <td>{{ $detail['kg'] ?? '-' }}</td>
                    <td>{{ $detail['net_kg'] ?? '-' }}</td>
                    <td>{{ $detail['rate'] ?? '-' }}</td>
                    <td>{{ $detail['amount'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div id="footer">
        <div style="float: left;">This is a computer-generated document. No signature is required.</div>
        <div style="float: right;">Printed on: {{ date('d-M-Y, h:i A') }}</div>
    </div>

</body>

</html>