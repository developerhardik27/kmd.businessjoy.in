@php
    // convert number to spelling:
    $words = Number::spell($invdata['grand_total']);

    $total;
    $roundof;
    $sign = '';
    $withgst = false;

    if ($invdata['gst'] > 0 || $invdata['sgst'] > 0 || $invdata['cgst'] > 0) {
        $withgst = true;
    }

    if ($invdata['gst'] != 0) {
        $total = $invdata['total'] + $invdata['gst'];
    } else {
        $total = $invdata['total'] + $invdata['sgst'] + $invdata['cgst'];
    }

    if ($invdata['grand_total'] > $total) {
        $value = $invdata['grand_total'] - $total;
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $sign = '+';
        }
    } else {
        $value = $total - $invdata['grand_total'];
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $sign = '-';
        }
    }

    $othersettings = json_decode($othersettings['gstsettings'], true);

    $blankrows = $invoiceothersettings['no_of_blank_row'];
    $showgodname = $invoiceothersettings['god_name_show/hide'];
    $loopnumber = 0; // array for alignment column type text or longtext

    $fixedFirstCols = ['#']; // manual column for serial number with 4% width
    $fixedWidths = 4; // % width for #
    $amountColumnWidth = 20; // amount column width (fixed)
    $totalWidth = $fixedWidths + $amountColumnWidth;
    $firstRowCols = []; // columns to show in main row
    $wrappedCols = []; // columns to wrap as separate rows

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

    // if ($companydetails['img'] != '') {
    //     $isVerticalAlign = true;
    // }

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - invoicePDf</title>
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
                            <td colspan="3" class="text-center bgblue">TAX Invoice</td>
                        </tr>
                        <tr>
                            <td style="width: 50%;padding:0;vertical-align:top">
                                <table width="100%" min-height="200px">
                                    <tr class="textblue">
                                        <th id="cname" style="padding-left:10px">
                                            {{ $companydetails['company_name'] }}
                                        </th>
                                    </tr>
                                    @isset($companydetails['address'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $companydetails['address'] }}
                                            </td>
                                        </tr>
                                    @endisset
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $companydetails['city_name'] }},
                                            {{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}
                                        </td>
                                    </tr>
                                    @isset($companydetails['email'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $companydetails['email'] }}
                                            </td>
                                        </tr>
                                    @endisset
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $companydetails['mobile_1'] }} @if ($companydetails['mobile_2'] != null && $companydetails['mobile_2'] != '')
                                                / {{ $companydetails['mobile_2'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (isset($companydetails['gst_no']))
                                        <tr>

                                            <td style="padding-left:10px">
                                                <b>GSTIN No: @isset($companydetails['gst_no'])
                                                        {{ $companydetails['gst_no'] }}
                                                    @endisset </b>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (isset($companydetails['pan']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>Pan No: @isset($companydetails['pan'])
                                                        {{ $companydetails['pan'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <table width="100%">
                                    <tr class="bgblue">
                                        <th class="font-weight-bold  bgblue" style="padding-left:10px">
                                            Buyer Details
                                        </th>
                                    </tr>
                                    @if (isset($invdata['name']))
                                        <tr class="font-weight-bold">
                                            <td class="textblue" style="padding-left:10px">
                                                @isset($invdata['name'])
                                                    {{ $invdata['name'] }}
                                                @endisset
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($invdata['address'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $invdata['address'] }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="padding-left:10px">
                                            @isset($invdata['city_name'])
                                                {{ $invdata['city_name'] }},
                                            @endisset
                                            @isset($invdata['state_name'])
                                                {{ $invdata['state_name'] }},
                                            @endisset
                                            @isset($invdata['pincode'])
                                                {{ $invdata['pincode'] }}
                                            @endisset
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $invdata['email'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $invdata['mobile_1'] }} @if ($invdata['mobile_2'] != null && $invdata['mobile_2'] != '')
                                                / {{ $invdata['mobile_2'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (isset($invdata['gst_no']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>GSTIN No: @isset($invdata['gst_no'])
                                                        {{ $invdata['gst_no'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (isset($invdata['pan']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>Pan No: @isset($invdata['pan'])
                                                        {{ $invdata['pan'] }}
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
                                <table width="100%">
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
                                    {{-- <tr>
                                        <td class="font-weight-bold text-center bgblue">currency</td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td class="text-center">{{ $invdata['currency'] }}</td>
                                    </tr> --}}

                                </table>
                                <table width="100%" class="table-striped">
                                    <tr class="bgblue text-center">
                                        <th colspan="2">
                                            bankdetails
                                        </th>
                                    </tr>
                                    @if ($bankdetails['bank_name'] != null && $bankdetails['bank_name'] != '')
                                        <tr class="">
                                            <td>Bank Name</td>
                                            <td>{{ $bankdetails['bank_name'] }}</td>
                                        </tr>
                                    @endif
                                    <tr class="">
                                        <td>Holder Name</td>
                                        <td>{{ $bankdetails['holder_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>A/C No</td>
                                        <td>{{ $bankdetails['account_no'] }}</td>
                                    </tr>
                                    {{-- @if ($bankdetails['swift_code'] != null && $bankdetails['swift_code'] != '')
                                        <tr class="">
                                            <td>Swift Code</td>
                                            <td>{{ $bankdetails['swift_code'] }}</td>
                                        </tr>
                                    @endif --}}
                                    <tr>
                                        <td>IFSC Code</td>
                                        <td>{{ $bankdetails['ifsc_code'] }}</td>
                                    </tr>
                                    @if ($bankdetails['branch_name'] != null && $bankdetails['branch_name'] != '')
                                        <tr class="">
                                            <td>Branch</td>
                                            <td>{{ $bankdetails['branch_name'] }}</td>
                                        </tr>
                                    @endif
                                </table>
                                <table width="100%">
                                    <tr class="bgblue">
                                        <th class="font-weight-bold  bgblue" style="padding-left:10px">
                                            Transporter
                                        </th>
                                    </tr>
                                    @if (isset($transportdetails['name']))
                                        <tr class="font-weight-bold">
                                            <td class="textblue" style="padding-left:10px">
                                                @isset($transportdetails['name'])
                                                    {{ $transportdetails['name'] }}
                                                @endisset
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($transportdetails['address'])
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $transportdetails['address'] }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="padding-left:10px">
                                            @isset($transportdetails['city_name'])
                                                {{ $transportdetails['city_name'] }},
                                            @endisset
                                            @isset($transportdetails['state_name'])
                                                {{ $transportdetails['state_name'] }},
                                            @endisset
                                            @isset($transportdetails['pincode'])
                                                {{ $transportdetails['pincode'] }}
                                            @endisset
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $transportdetails['email'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $transportdetails['mobile_1'] }} @if ($transportdetails['mobile_2'] != null && $transportdetails['mobile_2'] != '')
                                                / {{ $transportdetails['mobile_2'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (isset($transportdetails['gst_no']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>GSTIN No: @isset($transportdetails['gst_no'])
                                                        {{ $transportdetails['gst_no'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif
                                    @if (isset($transportdetails['pan']))
                                        <tr>
                                            <td style="padding-left:10px">
                                                <b>Pan No: @isset($transportdetails['pan'])
                                                        {{ $transportdetails['pan'] }}
                                                    @endisset
                                                </b>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table cellspacing="0" cellpadding="0" width="100%" class="border-left-right">
                    <tr>
                        <td style="padding-left:10px; ">
                            <b>
                                HSN Code:
                                @isset($invdata['HSN'])
                                    {{ $invdata['HSN'] }}
                                @endisset
                            </b>
                             <b>
                                Description:
                                @isset($invdata['Description'])
                                    {{ $invdata['Description'] }}
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
                            @foreach ($firstRowCols as $col)
                                <th style="text-align: center; width: {{ $col['column_width'] }}% !important;">
                                    {{ strtoupper(str_replace('_', ' ', $col['column_name'])) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $srno = 0; @endphp
                        @foreach ($products as $row)
                            @php $srno++; @endphp

                            {{-- Main first row --}}
                            <tr>
                                @php
                                    $loopnumber++;
                                @endphp
                                <td style="text-align: center; width:4%;">{{ $srno }}</td>
                                @foreach ($firstRowCols as $col)
                                    @php

                                        $key = str_replace(' ', '_', $col['column_name']);

                                        $val = $row[$key] ?? '';
                                        if ($key == 'discount') {
                                            $val = $val . ' %';
                                        }
                                    @endphp

                                    @if ($key == 'amount')
                                        <td style="text-align: right;" class="currencysymbol">
                                            {{ Number::currency($val, in: $invdata['currency']) }}
                                        </td>
                                    @else
                                        <td style="text-align: center;">
                                            {!! nl2br(e($val)) !!}
                                        </td>
                                    @endif
                                @endforeach
                            </tr>

                            {{-- Wrapped columns rows --}}
                            @foreach ($wrappedCols as $col)
                                @php
                                    $loopnumber++;
                                    $key = str_replace(' ', '_', $col['column_name']);
                                    $val = $row[$key] ?? '';
                                    $label = strtoupper(str_replace('_', ' ', $key));
                                @endphp
                                <tr>
                                    <td></td> {{-- empty # column --}}
                                    <td colspan="{{ count($firstRowCols) }}">
                                        <strong>{{ $label }}:</strong> {!! nl2br(e($val)) !!}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if ($loopnumber < $blankrows)
                            @php
                                $blankrows -= $loopnumber;
                            @endphp

                            @for ($blankrow = 1; $blankrow <= $blankrows; $blankrow++)
                                <tr>
                                    <td></td>
                                    @for ($j = 0; $j < count($firstRowCols); $j++)
                                        @if ($j == ceil(count($firstRowCols) / 2) - 1)
                                            <td style="text-align: center">-</td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endfor
                                </tr>
                            @endfor
                        @endif

                        {{-- end product data --}}
                    </tbody>
                </table>

                <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                    width="100%">
                    <tbody>
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="{{ $colspan }}" class="text-right left removeborder">
                                Subtotal
                            </td>
                            <td style="width: {{ $amountColumnWidth }}%;"
                                class="right removeborder currencysymbol text-right" id="subtotal">
                                {{ Number::currency($invdata['total'], in: $invdata['currency']) }}
                            </td>
                        </tr>
                        @if ($othersettings['gst'] == 0)
                            @if ($invdata['sgst'] > 0)
                                <tr style="font-size:15px;text-align: right">
                                    <td colspan="{{ $colspan }}" style="text-align: right"
                                        class="left removeborder ">
                                        SGST({{ $othersettings['sgst'] }}%)
                                    </td>
                                    <td style="text-align: right ;width: {{ $amountColumnWidth }}%;"
                                        class="currencysymbol" id="sgst">
                                        {{ Number::currency($invdata['sgst'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                            @endif
                            @if ($invdata['cgst'] > 0)
                                <tr style="font-size:15px;text-align: right">
                                    <td colspan="{{ $colspan }}" style="text-align: right"
                                        class="left removeborder ">
                                        CGST({{ $othersettings['cgst'] }}%)
                                    </td>
                                    <td style="text-align: right;width: {{ $amountColumnWidth }}%;"
                                        class=" currencysymbol" id="cgst">
                                        {{ Number::currency($invdata['cgst'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                            @endif
                        @else
                            @if ($invdata['gst'] > 0)
                                <tr style="font-size:15px;text-align: right">
                                    <td colspan="{{ $colspan }}" style="text-align: right"
                                        class="left removeborder ">
                                        GST({{ $othersettings['sgst'] + $othersettings['cgst'] }}%)
                                    </td>
                                    <td style="text-align: right;width: {{ $amountColumnWidth }}%;"
                                        class="currencysymbol " id="gst">
                                        {{ Number::currency($invdata['gst'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                        @unless ($roundof == 0)
                            <tr style="font-size:15px;text-align: right">
                                <td colspan="{{ $colspan }}" class="text-right left removeborder">
                                    Round of
                                </td>
                                <td style="width: {{ $amountColumnWidth }}%;" class="right currencysymbol text-right">
                                    {{ $sign }} {{ Number::currency($roundof, in: $invdata['currency']) }}
                                </td>
                            </tr>
                        @endunless
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="{{ $colspan }}" class="text-right left removeborder">
                                <b>Total</b>
                            </td>
                            <td style="width: {{ $amountColumnWidth }}%;" class="right currencysymbol text-right">
                                {{ Number::currency($invdata['grand_total'], in: $invdata['currency']) }}
                            </td>
                        </tr>
                        <tr class="removeborder">
                            <td colspan="{{ $colspan + 1 }}" class="text-right"
                                style="width: {{ $amountColumnWidth }}%;vertical-align: middle; text-align: right;font-style:italic;border-bottom:transparent;text-transform:uppercase;">
                                <strong>{{ $invdata['currency'] }} {{ $words }} Only</strong>
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
