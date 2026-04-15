<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin-top: 30px;
            margin-right: 30px;
            margin-bottom: 45px;
            /* space for footer */
            margin-left: 30px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
        
        }
        .company-info {
            line-height: 1.6;
            text-align:left;
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
        .divider {
            border: none;
            border-top: 2px solid #333;
            margin: 8px 0 14px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        thead tr {
            background-color: #2c2c2c;
            color: #ffffff;
        }

        thead th {
            padding: 7px 6px;
            text-align: center;
            border: 1px solid #444;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        tbody tr:nth-child(even) {
            /* background-color: #f5f5f5; */
        }

        tbody tr:nth-child(odd) {
            /* background-color: #ffffff; */
        }

        tbody td {
            padding: 6px 6px;
            /* border: 1px solid #ddd; */
            text-align: center;
            vertical-align: middle;
        }

        tbody td.text-left {
            text-align: left;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
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

    <div class="report-title"> Sample Report</div>

    {{-- ── Table ── --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Company</th>
                <th>Buyer</th>
                <th>Transport</th>
                <th>Garden</th>
                <th>Invoice No</th>
                <th>Grade</th>
                <th>Bags</th>
                <th>Net KG</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($brokerpurchase as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $item['company_name']   ?? '-' }}</td>
                    <td class="text-left">{{ $item['buyer_name']     ?? '-' }}</td>
                    <td class="text-left">{{ $item['transport_name'] ?? '-' }}</td>
                    <td class="text-left">{{ $item['garden_name']    ?? '-' }}</td>
                    <td>{{ $item['invoice_no']  ?? '-' }}</td>
                    <td>{{ $item['grade_name']  ?? '-' }}</td>
                    <td>{{ $item['bags']        ?? 0   }}</td>
                    <td>{{ $item['net_kg']      ?? 0   }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:14px; color:#999;">
                        No records found.
                    </td>
                </tr>
            @endforelse

            {{-- ── Totals Row ── --}}
            @if($brokerpurchase->count())
                <tr class="total-row">
                    <td colspan="7" style="text-align:right;">Total</td>
                    <td>{{ $brokerpurchase->sum('bags') }}</td>
                    <td>{{ number_format($brokerpurchase->sum('net_kg'), 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ── Footer ── --}}
    <footer>
        <table width="100%">
            <tr>
                <td align="left">
                    This is a computer-generated document. No signature is required.
                </td>
                <td align="right">
                    Printed on: {{ date('d-M-Y, h:i A') }}
                </td>
            </tr>
        </table>
    </footer>

</body>
</html>