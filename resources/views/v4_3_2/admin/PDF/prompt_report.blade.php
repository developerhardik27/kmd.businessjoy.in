<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Prompt Report</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .report-header {
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #002060;
            text-align: center;
            margin-bottom: 8px;
        }

        .report-meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .report-meta-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .report-meta-left {
            text-align: left;
        }

        .report-meta-right {
            text-align: right;
        }

        .report-meta {
            font-size: 11px;
            color: #666;
        }

        .report-meta-border {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .filters-section {
            margin-bottom: 10px;
            padding: 6px 8px;
            background-color: #f0f4f8;
            border-left: 3px solid #002060;
            font-size: 11px;
            color: #444;
        }

        .filters-section strong {
            color: #002060;
        }

        /* Main table */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        th {
            background-color: #002060;
            color: #fff;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
            padding: 6px 8px;
            border: 1px solid #001a4d;
        }

        td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 5px 8px;
            color: #333;
        }

        tr:nth-child(odd) td {
            background-color: #ffffff;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 500;
        }

        .status-part_payment {
            background-color: #cce5ff;
            color: #004085;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 500;
        }

        .status-cancel {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 500;
        }

        .status-due {
            background-color: #e2e3e5;
            color: #383d41;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 500;
        }

        .child-label {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="report-title">Prompt Report</div>
        <table class="report-meta-table" style="border: none;">
            <tr>
                <td class="report-meta-left">
                    <div class="report-meta report-meta-border">
                        Generated on: {{ date('d-m-Y H:i:s') }}
                    </div>
                </td>
                <td class="report-meta-right">
                    <div class="report-meta report-meta-border">
                        Total Records: {{ $invoices->count() }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if(request()->filter_company || request()->filter_buyer || request()->filter_payment_status || request()->filter_credit_days)
    <div class="filters-section">
        <strong>Filters Applied:</strong>
        @if(request()->filter_company) Company: {{ request()->filter_company }} @endif
        @if(request()->filter_buyer) | Buyer: {{ request()->filter_buyer }} @endif
        @if(request()->filter_payment_status) | Status: {{ request()->filter_payment_status }} @endif
        @if(request()->filter_credit_days) | Credit Days: {{ request()->filter_credit_days }} @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Company Name</th>
                <th>Buyer Name</th>
                <th>Amount</th>
                <th>Credit Days</th>
                <th>Expected Payment Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $item)
                <tr>
                    <td>{{ $item->id ?? '-' }}</td>
                    <td>{{ $item->inv_no ?? '-' }}</td>
                    <td>{{ $item->inv_date_formatted ?? '-' }}</td>
                    <td>{{ $item->garden_company_name ?? '-' }}</td>
                    <td>{{ $item->customer ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->grand_total ?? 0, 2) }}</td>
                    <td>{{ $item->credit_days ?? '-' }}</td>
                    <td>{{ $item->expected_payment_date ?? '-' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            switch(strtolower($item->status ?? '')) {
                                case 'paid': $statusClass = 'status-paid'; break;
                                case 'pending': $statusClass = 'status-pending'; break;
                                case 'part_payment': $statusClass = 'status-part_payment'; break;
                                case 'cancel': $statusClass = 'status-cancel'; break;
                                case 'due': $statusClass = 'status-due'; break;
                            }
                        @endphp
                        <span class="{{ $statusClass }}">{{ ucfirst($item->status ?? '-') }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 15px; color: #999;">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals Table --}}
    <table style="margin-top: 20px; border: 1px solid #002060;">
        <thead>
            <tr>
                <th style="background-color: #002060; color: #fff; padding: 8px;">Total Records</th>
                <th style="background-color: #002060; color: #fff; padding: 8px;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px; font-weight: bold;">{{ $totalRecords ?? 0 }}</td>
                <td style="padding: 8px; font-weight: bold;">{{ number_format($totalAmount ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
