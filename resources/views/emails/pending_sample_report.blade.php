<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width" name="viewport"/>
    <title>Pending Sample Report</title>
    <style type="text/css">
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: linear-gradient(135deg, #97271f 0%, #253566 100%); }
        * { box-sizing: border-box; }
        .wrapper { max-width: 640px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(90deg, #97271f 0%, #253566 100%); padding: 28px 40px 20px; text-align: center; }
        .logo-box { display: inline-block; background: rgba(255,255,255,0.12); border-radius: 6px; padding: 10px 20px; }
        .logo-box img { height: 36px; display: block; }
        .title-bar { background: #f7f4f2; border-bottom: 3px solid #97271f; padding: 20px 40px 16px; text-align: center; }
        .title-bar h1 { margin: 0; font-size: 22px; font-weight: 600; color: #253566; }
        .title-bar p { margin: 6px 0 0; font-size: 13px; color: #7a7a7a; }
        .body-pad { padding: 28px 40px 0; }
        .details-card { background: #f7f4f2; border-radius: 6px; border-left: 4px solid #97271f; padding: 16px 20px; margin-bottom: 24px; }
        .details-card .label { margin: 0 0 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #97271f; }
        .details-card table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .details-card td { padding: 4px 0; }
        .details-card .key { color: #666; width: 140px; }
        .details-card .val { color: #253566; font-weight: 500; }
        .section-pad { padding: 0 24px 24px; }
        .section-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #253566; margin: 0 0 10px 4px; }
        .table-scroll { overflow-x: auto; border-radius: 6px; border: 1px solid #e0dbd7; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 12px; min-width: 580px; }
        .data-table thead tr { background: linear-gradient(90deg, #97271f 0%, #253566 100%); }
        .data-table thead th { padding: 10px; text-align: left; color: #fff; font-weight: 600; white-space: nowrap; }
        .data-table thead th.right { text-align: right; }
        .data-table thead th.center { text-align: center; }
        .data-table tbody tr:nth-child(even) { background: #f9f7f6; }
        .data-table tbody tr:nth-child(odd) { background: #fff; }
        .data-table tbody td { padding: 9px 10px; color: #333; border-bottom: 1px solid #eee; }
        .data-table tbody td.right { text-align: right; }
        .data-table tbody td.center { text-align: center; }
        .badge { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; display: inline-block; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-half { background: #e3f2fd; color: #1565c0; }
        .badge-created { background: #e8f5e9; color: #2e7d32; }
        .footer-msg { padding: 16px 40px 28px; text-align: center; }
        .footer-msg p { font-size: 13px; color: #555; line-height: 1.7; margin: 0; }
        .divider { margin: 0 32px; border: none; border-top: 1px solid #e0dbd7; }
        .footer { background: linear-gradient(90deg, #97271f 0%, #253566 100%); padding: 14px 40px; text-align: center; }
        .footer p { margin: 0; font-size: 12px; color: rgba(255,255,255,0.75); }
        .footer a { color: #fff; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background: linear-gradient(135deg, #97271f 0%, #253566 100%); padding: 40px 20px;">
    <tr><td align="center">
    <div class="wrapper">

        {{-- HEADER --}}
        <div class="header">
            <div class="logo-box">
                <img src="{{ asset('admin/images/bjlogo2.png') }}" alt="BusinessJoy" />
            </div>
        </div>

        {{-- TITLE --}}
        <div class="title-bar">
            <h1>Pending Sample Report</h1>
            <p>Generated report for your records</p>
        </div>

        {{-- REPORT DETAILS --}}
        <div class="body-pad">
            <div class="details-card">
                <p class="label">Report Details</p>
                <table>
                    <tr>
                        <td class="key">Date Range</td>
                        <td class="val">{{ $dateFrom ?? 'All' }} — {{ $dateTo ?? 'All' }}</td>
                    </tr>
                    <tr>
                        <td class="key">Sample Status</td>
                        <td class="val">{{ $sampleStatus ?? 'All' }}</td>
                    </tr>
                    <tr>
                        <td class="key">Company</td>
                        <td class="val">{{ $companies[0]['name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="key">Email</td>
                        <td class="val">{{ $companies[0]['email'] ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- DATA TABLE --}}
        @if(!empty($reportData))
        <div class="section-pad">
            <p class="section-label">Pending Sample Data</p>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order Date</th>
                            <th>Company Name</th>
                            <th>Gardens</th>
                            <th>Buyer</th>
                            <th>Reference</th>
                            <th>Invoice/Lot No</th>
                            <th>Grades</th>
                            <th>Invoice Status</th>
                            <th class="center">Sample Status</th>
                            <th>Dispatch Status</th>
                            <th>Expected Dispatch Date</th>
                            <th class="right">Total Net Kg</th>
                            <th class="right">Rate</th>
                            <th class="right">Final Amount</th>
                            <th class="right">Credit Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $row)
                        <tr>
                            <td>{{ $row['id'] ?? '-' }}</td>
                            <td>{{ $row['order_date'] ?? '-' }}</td>
                            <td>{{ $row['company_names'] ?? '-' }}</td>
                            <td>{{ $row['garden_names'] ?? '-' }}</td>
                            <td>{{ $row['buyer_name'] ?? '-' }}</td>
                            <td>{{ $row['reference_name'] ?? '-' }}</td>
                            <td>{{ $row['invoice_nos'] ?? '-' }}</td>
                            <td>{{ $row['grades'] ?? '-' }}</td>
                            <td>{{ $row['invoice_status'] ?? '-' }}</td>
                            <td class="center">
                                @php $status = strtolower($row['sample_status'] ?? ''); @endphp
                                @if($status === 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($status === 'half sample')
                                    <span class="badge badge-half">Half Sample</span>
                                @else
                                    <span class="badge badge-created">Sample Created</span>
                                @endif
                            </td>
                            <td>{{ $row['dispatch_status'] ?? '-' }}</td>
                            <td>{{ $row['expected_dispatch_date'] ?? '-' }}</td>
                            <td class="right">{{ $row['net_kg'] ?? '-' }}</td>
                            <td class="right">{{ $row['rate'] ?? '-' }}</td>
                            <td class="right">{{ $row['amount'] ?? '-' }}</td>
                            <td class="right">{{ $row['credit_days'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- FOOTER MESSAGE --}}
        <div class="footer-msg">
            <p>Please find the pending sample details above.<br>
            <strong style="color: #253566;">Thank you for your business.</strong></p>
        </div>

        <hr class="divider" />

        {{-- FOOTER --}}
        <div class="footer">
            <p>© Copyright <a href="https://businessjoy.in">Businessjoy</a>. All rights reserved.</p>
        </div>

    </div>
    </td></tr>
</table>
</body>
</html>
