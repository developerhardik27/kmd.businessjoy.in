<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Turnover Report</title>
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
    </style>
</head>

<body>
    <div class="report-header">
        <div class="report-title">Turnover Report</div>
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

    @if(request()->filter_order_date_from || request()->filter_order_date_to || request()->filter_company || request()->filter_buyer)
    <div class="filters-section">
        <strong>Filters Applied:</strong>
        @if(request()->filter_order_date_from) Order Date From: {{ request()->filter_order_date_from }} @endif
        @if(request()->filter_order_date_to) Order Date To: {{ request()->filter_order_date_to }} @endif
        @if(request()->filter_company) | Company: {{ $filterNames['company'] ?? request()->filter_company }} @endif
        @if(request()->filter_buyer) | Buyer: {{ $filterNames['buyer'] ?? request()->filter_buyer }} @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Buyer Name</th>
                <th>Total Net Kg</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $item['company_name'] ?? '-' }}</td>
                    <td>{{ $item['buyer_name'] ?? '-' }}</td>
                    <td>{{ $item['total_net_kg'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 15px; color: #999;">No data found</td>
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
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px; font-weight: bold;">{{ $totalRecords ?? 0 }}</td>
                <td style="padding: 8px; font-weight: bold;">{{ $totalNetKg ?? 0 }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
