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

    if($companydetails['img'] != ''){
        $isVerticalAlign = true ;
    }

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - invoicePDf</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }} " />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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
            table-layout: auto; /* Prevent table from breaking across pages */
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

        .vertical-align-custom{
            vertical-align: start !important;
        }
    </style>
</head>

<body>
    <main>
        <div class=" table-wrapper">
            <table cellspacing=0 cellpadding=0 width="100%" class="border">
                <tbody>
                    <tr>
                        <td colspan="3" class="text-center bgblue">TAX Invoice</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;padding:0;vertical-align:top">
                            <table width="100%">
                                <tr class="textblue">
                                    <th id="cname" style="padding-left:10px">
                                        {{ $companydetails['name'] }}
                                    </th>
                                </tr>
                                @isset($companydetails['house_no_building_name'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $companydetails['house_no_building_name'] }}
                                        </td>
                                    </tr>
                                @endisset
                                @isset($companydetails['road_name_area_colony'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $companydetails['road_name_area_colony'] }}
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
                                        {{ $companydetails['contact_no'] }}
                                    </td>
                                </tr>
                                @if ($withgst)
                                    <tr>
                                        <td style="padding-left:10px">
                                            <b>GSTIN No: @isset($companydetails['gst_no'])
                                                    {{ $companydetails['gst_no'] }}
                                                @endisset </b>
                                        </td>
                                    </tr>
                                @endif
                                @isset($companydetails['transporter_id'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            <b>Transporter ID:</b> {{ $companydetails['transporter_id'] }}
                                        </td>
                                    </tr>
                                @endisset
                            </table>
                            <table width="100%">
                                <tr class="bgblue">
                                    <th class="font-weight-bold  bgblue" style="padding-left:10px">
                                        Bill to
                                    </th>
                                </tr>
                                @if (isset($invdata['firstname']) || isset($invdata['lastname']))
                                    <tr class="font-weight-bold">
                                        <td class="textblue" style="padding-left:10px">
                                            @isset($invdata['firstname'])
                                                {{ $invdata['firstname'] }}
                                            @endisset
                                            @isset($invdata['lastname'])
                                                {{ $invdata['lastname'] }}
                                            @endisset
                                        </td>
                                    </tr>
                                @endif
                                @if ($invdata['company_name'] != '' && $invdata['company_name'] != null)
                                    <tr class="font-weight-bold">
                                        <td class="textblue" style="padding-left:10px">
                                            {{ $invdata['company_name'] }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($invdata['house_no_building_name'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $invdata['house_no_building_name'] }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($invdata['road_name_area_colony'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $invdata['road_name_area_colony'] }}
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
                                        {{ $invdata['contact_no'] }}
                                    </td>
                                </tr>
                                @if ($withgst)
                                    <tr>
                                        <td style="padding-left:10px">
                                            <b>GSTIN No: @isset($invdata['gst_no'])
                                                    {{ $invdata['gst_no'] }}
                                                @endisset
                                            </b>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td style="width: 10%;"></td>
                        <td style="width: 40%;padding:0;text-align: center;" @class(['vertical-align-custom' => $isVerticalAlign])>
                            <div style="display: inline-block;" class="vertical-align-center">
                                @if ($companydetails['img'] != '')
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}"
                                        class="rounded mt-auto mx-auto d-block" alt="Company logo"
                                        style="max-width: 300px;max-height:150px;">
                                @endif
                            </div>
                            <table width="100%">
                                <tr>
                                    <td class="font-weight-bold  text-center bgblue">Invoice ID</td>
                                    <td class="font-weight-bold text-center bgblue">date</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td class="text-center">{{ $invdata['inv_no'] }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($invdata['inv_date_formatted'])->format('d-m-Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-center  bgblue">customer id</td>
                                    <td class="font-weight-bold text-center bgblue">currency</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td class="text-center">{{ $invdata['cid'] }}</td>
                                    <td class="text-center">{{ $invdata['currency'] }}</td>
                                </tr>

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
                                @if ($bankdetails['swift_code'] != null && $bankdetails['swift_code'] != '')
                                    <tr class="">
                                        <td>Swift Code</td>
                                        <td>{{ $bankdetails['swift_code'] }}</td>
                                    </tr>
                                @endif
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
                        </td>
                    </tr>
                </tbody>
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
                        @if ($invdata['sgst'] >= 1)
                            <tr style="font-size:15px;text-align: right">
                                <td colspan="{{ $colspan }}" style="text-align: right" class="left removeborder ">
                                    SGST({{ $othersettings['sgst'] }}%)
                                </td>
                                <td style="text-align: right ;width: {{ $amountColumnWidth }}%;" class="currencysymbol"
                                    id="sgst">
                                    {{ Number::currency($invdata['sgst'], in: $invdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                        @if ($invdata['cgst'] >= 1)
                            <tr style="font-size:15px;text-align: right">
                                <td colspan="{{ $colspan }}" style="text-align: right" class="left removeborder ">
                                    CGST({{ $othersettings['cgst'] }}%)
                                </td>
                                <td style="text-align: right;width: {{ $amountColumnWidth }}%;"
                                    class=" currencysymbol" id="cgst">
                                    {{ Number::currency($invdata['cgst'], in: $invdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                    @else
                        @if ($invdata['gst'] >= 1)
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
