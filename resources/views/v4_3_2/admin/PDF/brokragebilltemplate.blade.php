@php
    // convert number to spelling:
    //dd($data);
    // $words = Number::spell($data['mainCompanyData']['grand_total']);

    $total;
    $roundof;
    $sign = '';
    $withgst = false;

    if (
        $data['mainCompanyData']['gst'] > 0 ||
        $data['mainCompanyData']['sgst'] > 0 ||
        $data['mainCompanyData']['cgst'] > 0
    ) {
        $withgst = true;
    }

    if ($data['mainCompanyData']['gst'] != 0) {
        $total = $data['mainCompanyData']['total'] + $data['mainCompanyData']['gst'];
    } else {
        $total =
            $data['mainCompanyData']['total'] + $data['mainCompanyData']['sgst'] + $data['mainCompanyData']['cgst'];
    }

    if ($data['mainCompanyData']['grand_total'] > $total) {
        $value = $data['mainCompanyData']['grand_total'] - $total;
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $sign = '+';
        }
    } else {
        $value = $total - $data['mainCompanyData']['grand_total'];
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $sign = '-';
        }
    }

    // $othersettings = json_decode($othersettings['gstsettings'], true);

    $blankrows = 0;
    $showgodname = 0;
    $loopnumber = 0; // array for alignment column type text or longtext

    $fixedFirstCols = ['#']; // manual column for serial number with 4% width
    $fixedWidths = 4; // % width for #
    $amountColumnWidth = 20; // amount column width (fixed)
    $totalWidth = $fixedWidths + $amountColumnWidth;
    $firstRowCols = []; // columns to show in main row
    $wrappedCols = []; // columns to wrap as separate rows
    $productscolumn = [
        [
            'column_name' => 'Buyer',
            'column_type' => 'text',
            'column_width' => '10',
        ],
        [
            'column_name' => 'Bags',
            'column_type' => 'text',
            'column_width' => '10',
        ],
        [
            'column_name' => 'Kgs',
            'column_type' => 'text',
            'column_width' => '10',
        ],
        [
            'column_name' => 'Brokerage',
            'column_type' => 'text',
            'column_width' => '10',
        ],
    ];

    // We assume $productscolumn includes all columns except #.
    // amount is last column, include it separately always.
    foreach ($productscolumn as $col) {
        if ($col['column_name'] === 'amount') {
            // Always include amount at last, skip here
            continue;
        }

        // Check if adding this column exceeds 100%
        if ($totalWidth + intval($col['column_width']) <= 100) {
            $totalWidth += intval($col['column_width']);
            $firstRowCols[] = $col;
        } else {
            $wrappedCols[] = $col;
        }
    }
    // Always push amount column at the end of first row columns
    $amountCol = collect($productscolumn)->first(fn($c) => $c['column_name'] === 'amount');
    if ($amountCol) {
        $firstRowCols[] = $amountCol;
    }

    $colspan = count($firstRowCols);

    $isVerticalAlign = false;
    $products = $data['usedInvoices'];

    // if ($companydetails['img'] != '') {
    //     $isVerticalAlign = true;
    // }
    function numberToWords($number)
    {
        $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
        return 'Rupees ' . ucfirst($formatter->format($number));
    }

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Brokrage Bill</title>
    <!-- Favicon -->
    <link rel="stylesheet" href="{{ public_path('admin/css/bootstrap.min.css') }}">
    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .bgblue {
            background-color: #002060 !important;
            /* background-color: rgb(10 8 108 / 99%); */
            color: rgb(255, 253, 253);
            text-transform: uppercase !important;
            font-weight: bold;
            font-size: 13px;
            border-right: none !important;
            border-bottom: none !important;
        }

        .bglightblue {
            background-color: rgb(32, 55, 100, 1);
            color: rgb(255, 253, 253);
            border: none !important;
        }

        .bgsilver {
            background-color: rgb(239, 235, 235);
        }

        .textblue {
            color: #092d75;
            font: bolder
        }

        td img {
            display: block;
            margin: 0 auto;
        }

        #cname {
            font-size: 20px;
            font-weight: bolder;
        }

        .horizontal-border td {
            border-bottom: 1px solid black;
        }

        .data td {
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        .data td:last-child {
            border-right: none;
        }

        /*.data tbody tr:last-child td {
            border-bottom: none;
        } */

        .data .removeborder {
            border-right: none;
        }

        .horizontal-border {
            width: 100%;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }

        .border {
            border: 1px solid !important;
        }

        .border-left-right {
            border-left: 1px solid !important;
            border-right: 1px solid !important;
        }

        .full-height {
            height: '100%' !important;
        }

        .bgspecial {
            background: rgba(48, 84, 150, 1);
        }

        .firstrow span {
            line-height: 20px !important;
        }

        table {
            width: 100%;
            border-spacing: 10px;
            page-break-inside: auto;
            table-layout: auto;
            /* Prevent table from breaking across pages */
            font-size: 12px;
        }

        .data td,
        th {
            white-space: normal;
            word-wrap: break-word;
        }

        .data td {
            line-break: anywhere !important;
        }

        td {
            padding: 0px 5px;
            word-wrap: break-word;
        }

        .currencysymbol {
            font-family: DejaVu Sans, sans-serif;
        }

        #footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
        }

        #tcspan * {
            margin: 0;
            padding: 0,
        }

        .vertical-align-custom {
            vertical-align: start !important;
        }
    </style>
</head>

<body>
    <main>
        <div class=" table-wrapper">

            <div class=" table-wrapper">

                <table cellspacing=0 cellpadding=0 width="100%" class="border">
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center bgblue">BILL FOR COMMISSION CHARGES</td>
                        </tr>
                        <tr>
                            <td style="width: 50%;padding:0;vertical-align:top">
                                <table width="100%" min-height="200px">
                                    <tr class="textblue">
                                        <th id="cname" style="padding-left:10px">
                                            {{ $data['mainCompanyData']['name'] }}
                                        </th>
                                    </tr>
                                    @isset($data['mainCompanyData']['house_no_building_name'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $data['mainCompanyData']['house_no_building_name'] }}
                                            </td>
                                        </tr>
                                    @endisset
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $data['mainCompanyData']['city_name'] }},
                                            {{ $data['mainCompanyData']['state_name'] }},
                                            {{ $data['mainCompanyData']['pincode'] }}
                                        </td>
                                    </tr>
                                    @isset($data['mainCompanyData']['email'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $data['mainCompanyData']['email'] }}
                                            </td>
                                        </tr>
                                    @endisset
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $data['mainCompanyData']['contact_no'] }}

                                        </td>
                                    </tr>
                                    @if (isset($data['mainCompanyData']['gst_no']))
                                        <tr>

                                            <td style="padding-left:10px">
                                                <b>GSTIN No: @isset($data['mainCompanyData']['gst_no'])
                                                        {{ $data['mainCompanyData']['gst_no'] }}
                                                    @endisset </b>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (isset($data['mainCompanyData']['pan_number']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>Pan No: @isset($data['mainCompanyData']['pan_number'])
                                                        {{ $data['mainCompanyData']['pan_number'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <table width="100%">
                                    <tr class="bgblue">
                                        <th class="font-weight-bold  bgblue" style="padding-left:10px">
                                            Bill To
                                        </th>
                                    </tr>
                                    @if (isset($data['gardenCompanyData']['company_name']))
                                        <tr class="font-weight-bold">
                                            <td class="textblue" style="padding-left:10px">
                                                @isset($data['gardenCompanyData']['company_name'])
                                                    {{ $data['gardenCompanyData']['company_name'] }}
                                                @endisset
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($data['gardenCompanyData']['address'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $data['gardenCompanyData']['address'] }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="padding-left:10px">
                                            @isset($data['gardenCompanyData']['city_name'])
                                                {{ $data['gardenCompanyData']['city_name'] }},
                                            @endisset
                                            @isset($data['gardenCompanyData']['state_name'])
                                                {{ $data['gardenCompanyData']['state_name'] }},
                                            @endisset
                                            @isset($data['gardenCompanyData']['pincode'])
                                                {{ $data['gardenCompanyData']['pincode'] }}
                                            @endisset
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $data['gardenCompanyData']['email'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $data['gardenCompanyData']['mobile_1'] }} @if ($data['gardenCompanyData']['mobile_2'] != null && $data['gardenCompanyData']['mobile_2'] != '')
                                                / {{ $data['gardenCompanyData']['mobile_2'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (isset($data['gardenCompanyData']['gst_no']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>GSTIN No: @isset($data['gardenCompanyData']['gst_no'])
                                                        {{ $data['gardenCompanyData']['gst_no'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (isset($data['gardenCompanyData']['pan']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>Pan No: @isset($data['gardenCompanyData']['pan'])
                                                        {{ $data['gardenCompanyData']['pan'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif

                                </table>
                            </td>
                            <td style="width: 10%;"></td>
                            <td style="width: 40%;padding:0;text-align: center;vertical-align:top"
                                @class(['vertical-align-custom' => $isVerticalAlign])>
                                {{-- <table width="100%">
                                    <tr>
                                        <td class="font-weight-bold  text-center bgblue">Invoice ID</td>
                                        <td class="font-weight-bold text-center bgblue">date</td>
                                    </tr>
                                    <tr class="font-weight-bold">

                                        <td class="text-center">{{ $invdata['inv_no'] }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($invdata['inv_date'])->format('d-m-Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold text-center bgblue">currency</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td class="text-center">{{ $invdata['currency'] }}</td>
                                    </tr>

                                </table> --}}
                                <table width="100%" class="table-striped">
                                    <tr class="bgblue text-center">
                                        <th colspan="2">
                                            bankdetails
                                        </th>
                                    </tr>
                                    @if ($data['bank_details']['bank_name'] != null && $data['bank_details']['bank_name'] != '')
                                        <tr class="">
                                            <td>Bank Name</td>
                                            <td>{{ $data['bank_details']['bank_name'] }}</td>
                                        </tr>
                                    @endif
                                    <tr class="">
                                        <td>Holder Name</td>
                                        <td>{{ $data['bank_details']['holder_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>A/C No</td>
                                        <td>{{ $data['bank_details']['account_no'] }}</td>
                                    </tr>
                                    @if ($data['bank_details']['swift_code'] != null && $data['bank_details']['swift_code'] != '')
                                        <tr class="">
                                            <td>Swift Code</td>
                                            <td>{{ $data['bank_details']['swift_code'] }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>IFSC Code</td>
                                        <td>{{ $data['bank_details']['ifsc_code'] }}</td>
                                    </tr>
                                    @if ($data['bank_details']['branch_name'] != null && $data['bank_details']['branch_name'] != '')
                                        <tr class="">
                                            <td>Branch</td>
                                            <td>{{ $data['bank_details']['branch_name'] }}</td>
                                        </tr>
                                    @endif
                                </table>
                                <table width="100%" class="table-striped" style="">
                                    <tr class="bgblue text-center">
                                        <th colspan="2">
                                            Bill Details
                                        </th>
                                    </tr>

                                    <tr class="">
                                        <td>Bill No </td>
                                        <td>{{ $data['invoice']['invoice_no'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Bill Date</td>
                                        <td>{{ $data['invoice']['invoice_date'] }}</td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table cellspacing="0" cellpadding="0" width="100%" class="border-left-right">
                    <tr>
                        <td style="padding-left:10px; ">
                            <b>
                                Bill Of commission form
                                @isset($data['invoice']['from_date'])
                                    {{ $data['invoice']['from_date'] }}
                                @endisset
                                to
                                @isset($data['invoice']['to_date'])
                                    {{ $data['invoice']['to_date'] }}
                                @endisset
                            </b>

                        </td>
                    </tr>
                </table>
                <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                    width="100%">
                    <thead>
                        <tr class="bgblue">
                            <th style="width:4%;text-align:center;">ID</th>
                            <th style="width:12%;text-align:center;">Buyer</th>
                            <th style="width:10%;text-align:center;">Inv No</th>
                            <th style="width:10%;text-align:center;">Inv Date</th>
                            <th style="width:6%;text-align:center;">Pkgs</th>
                            <th style="width:8%;text-align:center;">Kgs</th>
                            <th style="width:10%;text-align:center;">Shortage</th>
                            <th style="width:10%;text-align:center;">Net Weight Kg</th>
                            <th style="width:10%;text-align:center;">Amount</th>
                            <th style="width:10%;text-align:center;">Comm</th>
                        </tr>
                    </thead>
                    @php
                        $usedInvoices = $data['usedInvoices'] ?? [];
                        $maxRows = 10;

                        $totalBags = 0;
                        $totalAmount = 0;
                        $totalCommission = 0;
                        $rowCount = count($usedInvoices);
                        foreach ($usedInvoices as $row) {
                            $amount = ($row->net_kg ?? 0) * ($row->rate ?? 0);
                            $commission = ($amount * ($row->brokerage ?? 0)) / 100;

                            $totalBags += $row->bags ?? 0;
                            $totalAmount += $amount;
                            $totalCommission += $commission;
                        }

                        $igst = ($totalCommission * 18) / 100;
                        $grandTotal = $totalCommission - $igst;

                        $roundedTotal = round($grandTotal);
                        $roundOff = $roundedTotal - $grandTotal;
                    @endphp

                    <tbody>

                        @forelse ($usedInvoices as $key => $row)
                            <tr>
                                <td style="text-align:center;">{{ $key + 1 }}</td>
                                <td style="text-align:center;">{{ $row->buyer_name ?? '-' }}</td>
                                <td style="text-align:center;">{{ $row->inv_no ?? '-' }}</td>
                                <td style="text-align:center;">
                                    {{ $row->inv_date ? \Carbon\Carbon::parse($row->inv_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td style="text-align:center;">{{ $row->bags ?? 0 }}</td>
                                <td style="text-align:center;">{{ number_format($row->net_kg ?? 0, 2) }}</td>
                                <td style="text-align:center;">{{ number_format($row->shortage ?? 0, 2) }}</td>
                                <td style="text-align:center;">{{ number_format($row->final_net_kg ?? 0, 2) }}</td>
                                <td style="text-align:center;">
                                    {{ number_format(($row->net_kg ?? 0) * ($row->rate ?? 0), 2) }}</td>
                                <td style="text-align:center;">
                                    {{ number_format((($row->net_kg ?? 0) * ($row->rate ?? 0) * ($row->brokerage ?? 0)) / 100, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;">No records found</td>
                            </tr>
                        @endforelse
                        @for ($i = $rowCount; $i < $maxRows; $i++)
                            {
                            <tr>
                                <td style="text-align:center; color: white;"> - </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                                <td style="text-align:center;"> </td>
                            </tr>
                            }
                        @endfor

                        @if (!empty($usedInvoices) && count($usedInvoices) > 0)
                            <tr style="font-weight:bold; background:#f2f2f2;">
                                <td colspan="4" style="text-align:right;">TOTAL</td>
                                <td style="text-align:center;">
                                    {{ $totalBags }}
                                </td>
                                <td colspan="3"></td>

                                <td style="text-align:center;">
                                    {{ number_format($totalAmount, 2) }}
                                </td>
                                <td style="text-align:center;">
                                    {{ number_format($totalCommission, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                    width="100%">
                    <tbody>
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="12" class="text-right left removeborder">
                                Subtotal
                            </td>
                            <td style="width:15%;" class="right removeborder currencysymbol text-right"
                                id="subtotal">
                                ₹{{ number_format($totalCommission, 2) }}
                            </td>
                        </tr>
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="12" style="text-align: right" class="left removeborder ">
                                IGST(18%)
                            </td>
                            <td style="text-align: right ;width:15%;" class="currencysymbol" id="sgst">
                                ₹{{ number_format($igst, 2) }}
                            </td>
                        </tr>
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="12" style="text-align: right" class="left removeborder ">
                                SGST(0%)
                            </td>
                            <td style="text-align: right ;width:15%;" class="currencysymbol" id="sgst">
                                ₹{{ number_format(0, 2) }}
                            </td>
                        </tr>
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="12" style="text-align: right" class="left removeborder ">
                                CGST(0%)
                            </td>
                            <td style="text-align: right;width: 20%;" class=" currencysymbol" id="cgst">
                                ₹{{ number_format(0, 2) }}
                            </td>
                        </tr>
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="12" class="text-right left removeborder">
                                Round of
                            </td>
                            <td style="width: 20%;" class="right currencysymbol text-right">
                                ₹ {{ number_format($roundOff, 2) }}
                            </td>
                        </tr>

                        <tr style="font-size:15px;text-align: right">
                            <td colspan="12" class="text-right left removeborder">
                                <b>Total</b>
                            </td>
                            <td style="width: %;" class="right currencysymbol text-right">
                                ₹{{ number_format($grandTotal, 2) }}
                            </td>
                        </tr>
                        <tr class="removeborder">
                            <td colspan="13" class="text-right"
                                style="width: %;vertical-align: middle; text-align: right;font-style:italic;border-bottom:transparent;text-transform:uppercase;">
                                <strong>{{ strtoupper(numberToWords($roundedTotal)) }} ONLY</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="horizontal-border border">
                    <tr>
                        <td colspan="3" class="bgblue  bgspecial"
                            style="vertical-align: middle; text-align: center;font-style:italic">
                            <strong>THANK YOU FOR YOUR BUSINESS!</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="vertical-align: top;border-bottom:1px solid black;">
                            @isset($invdata['notes'])
                                <span style="margin-top: 0"><b>Notes :- </b></span><br>
                                <div>{!! nl2br(e($invdata['notes'])) !!} </div>
                            @endisset
                            @isset($invdata['t_and_c'])
                                <span style="margin-top: 0;font-size:13px;"><b>Terms And Conditions :- </b></span>
                                <div id="tcspan"> {!! $invdata['t_and_c'] !!}</div>
                            @endisset
                        </td>
                    </tr>
                </table>

                <div class="mt-1" style="font-size: 12px" id="footer">
                    <span class="float-left">
                        <small>This is a computer-generated document. No signature is required.</small>
                    </span>
                    <span class="float-right">
                        <small>{{ date('d-M-Y, h:i A') }}</small>
                    </span>
                </div>
            </div>
    </main>
</body>

</html>
