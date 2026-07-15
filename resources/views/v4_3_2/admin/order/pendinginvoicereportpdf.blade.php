<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Pending Invoice Report</title>
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
        .status-completed {
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
        .child-label
        {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="report-title">Pending Invoice Report</div>
        <table class="report-meta-table" style="border: none;">
            <tr>
                <td class="report-meta-left">
                    <div class="report-meta report-meta-border">
                        Generated on: {{ date('d-m-Y H:i:s') }}
                    </div>
                </td>
                <td class="report-meta-right">
                    <div class="report-meta report-meta-border">
                        Total Records: {{ $data->count() }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if(request()->filter_order_date_from || request()->filter_order_date_to || request()->filter_invoice_status || request()->filter_company || request()->filter_buyer || request()->filter_sample_date_from || request()->filter_sample_date_to)
    <div class="filters-section">
        <strong>Filters Applied:</strong>
        @if(request()->filter_order_date_from) Order Date From: {{ request()->filter_order_date_from }} @endif
        @if(request()->filter_order_date_to) Order Date To: {{ request()->filter_order_date_to }} @endif
        @if(request()->filter_invoice_status) | Invoice Status: {{ request()->filter_invoice_status }} @endif
        @if(request()->filter_company) | Company: {{ $filterNames['company'] ?? request()->filter_company }} @endif
        @if(request()->filter_buyer) | Buyer: {{ $filterNames['buyer'] ?? request()->filter_buyer }} @endif
        @if(request()->filter_sample_date_from) | Sample Date From: {{ request()->filter_sample_date_from }} @endif
        @if(request()->filter_sample_date_to) | Sample Date To: {{ request()->filter_sample_date_to }} @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Company Name</th>
                <th>Gardens</th>
                <th>Buyer</th>
                <th>Reference</th>
                <th>Invoice/Lot No</th>
                <th>Grades</th>
                <th>Invoice Status</th>
                <th>Sample Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                {{-- Main row: 10 columns --}}
                <tr>
                    <td>{{ $item->order_id ?? '-' }}</td>
                    <td>{{ $item->order_date ?? '-' }}</td>
                    <td>{{ $item->company_name ?? '-' }}</td>
                    <td>{{ $item->garden_name ?? '-' }}</td>
                    <td>{{ $item->buyer_name ?? '-' }}</td>
                    <td>{{ $item->reference_name ?? '-' }}</td>
                    <td>{{ $item->invoice_no ?? '-' }}</td>
                    <td>{{ $item->grade ?? '-' }}</td>
                    <td>{{ $item->invoice_status ?? '-' }}</td>
                    <td>{{ $item->sample_status ?? '-' }}</td>
                </tr>

                {{-- Child / detail row: 6 fields shown as label:value grid --}}
                <tr class="child-row">
                    <td colspan="10" style="padding: 0;">
                        <div class="child-detail-wrap">
                            <table class="child-grid" style="border:none;">
                                <tr>
                                    <td>
                                        <span class="child-label">Dispatch Status</span>
                                        <span class="child-value">
                                            @if($item->dispatch_status == 'Completed')
                                                <span class="status-completed">{{ $item->dispatch_status }}</span>
                                            @elseif($item->dispatch_status == 'Pending')
                                                <span class="status-pending">{{ $item->dispatch_status }}</span>
                                            @else
                                                {{ $item->dispatch_status ?? '-' }}
                                            @endif
                                        </span>
                                    </td>
</tr>
<tr>
                                    <td>
                                        <span class="child-label">Expected Dispatch Date</span>
                                        <span class="child-value">{{ $item->expected_dispatch_date ?? '-' }}</span>
                                    </td>
</tr>
<tr>
                                    <td>
                                        <span class="child-label">Total Net Kg</span>
                                        <span class="child-value">{{ $item->net_kg ?? '-' }}</span>
                                    </td>
</tr>
<tr>
                                    <td>
                                        <span class="child-label">Rate</span>
                                        <span class="child-value">{{ $item->rate ?? '-' }}</span>
                                    </td>
</tr>
<tr>
                                    <td>
                                        <span class="child-label">Final Amount</span>
                                        <span class="child-value">{{ $item->amount ?? '-' }}</span>
                                    </td>
</tr>
<tr>
                                    <td>
                                        <span class="child-label">Credit Days</span>
                                        <span class="child-value">{{ $item->credit_days ?? '-' }}</span>
                                    </td>
</tr>
                            </table>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 15px; color: #999;">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totals Table --}}
    <table style="margin-top: 20px; border: 1px solid #002060;">
        <thead>
            <tr>
                <th style="background-color: #002060; color: #fff; padding: 8px;">Total Records</th>
                <th style="background-color: #002060; color: #fff; padding: 8px;">Total Net Kg</th>
                <th style="background-color: #002060; color: #fff; padding: 8px;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px; font-weight: bold;">{{ $totalRecords ?? 0 }}</td>
                <td style="padding: 8px; font-weight: bold;">{{ $totalNetKg ?? 0 }}</td>
                <td style="padding: 8px; font-weight: bold;">{{ number_format($totalAmount ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>