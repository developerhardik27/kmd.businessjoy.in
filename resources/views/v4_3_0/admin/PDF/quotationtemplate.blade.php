@php
    // convert number to spelling:
    $words = Number::spell($quotationdata['grand_total']);

    $total;
    $roundof;
    $sign = '';
    $withgst = false;

    if ($quotationdata['gst'] > 0 || $quotationdata['sgst'] > 0 || $quotationdata['cgst'] > 0) {
        $withgst = true;
    }

    if ($quotationdata['gst'] != 0) {
        $total = $quotationdata['total'] + $quotationdata['gst'];
    } else {
        $total = $quotationdata['total'] + $quotationdata['sgst'] + $quotationdata['cgst'];
    }

    if ($quotationdata['grand_total'] > $total) {
        $value = $quotationdata['grand_total'] - $total;
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $sign = '+';
        }
    } else {
        $value = $total - $quotationdata['grand_total'];
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $sign = '-';
        }
    }

    $toFullName = trim(($quotationdata['firstname'] ?? '') . ' ' . ($quotationdata['lastname'] ?? ''));
    $toCompany = $quotationdata['company_name'] ?? '';

    $othersettings = json_decode($othersettings['gstsettings'], true);

    $loopnumber = 0; // array for alignment column type text or longtext

    $blankrows = 10;

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

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Quotation PDf</title>
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
            border: 1px solid black;
            color: rgb(255, 253, 253);
            font-size: 14px;
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

        .border {
            border: 1px solid !important;
        }

        .full-height {
            height: '100%' !important;
        }

        table {
            width: 100%;
            border-spacing: 10px;
            page-break-inside: auto;
            table-layout: auto;
            /* Prevent table from breaking across pages */
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

        .data .removeborder {
            border-right: none;
        }

        #data td,
        th {
            white-space: normal;
            word-wrap: break-word;
        }

        #data td {
            line-break: anywhere !important;
        }

        td {
            padding: 0px 5px;
            word-wrap: break-word;
        }

        .currencysymbol {
            font-family: DejaVu Sans;
            sans-serif;
        }

        #footer {
            position: fixed;
            bottom: 0px;
            width: 100%;
        }

        #tcspan * {
            margin: 0;
            padding: 0,
        }
    </style>
</head>

<body>
    <main>
        <div class="table-wrapper">
            <table cellspacing=0 cellpadding=0 width="100%">
                <tbody>
                    <tr>
                        <td style="text-align: center;text-transform:uppercase;" class="bgblue">
                            <b>Quoatation</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            @if ($companydetails['img'] != '')
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}"
                                    alt="Company logo" style="max-width: 300px; max-height: 150px;">
                            @endif
                        </td>
                    </tr>
                    <tr class="textblue">
                        <td style="text-align: center">
                            <p id="cname" class="textblue m-0">{{ $companydetails['name'] }}</p>
                            <span><b>Address:</b></span>
                            {{ $companydetails['house_no_building_name'] }} ,
                            {{ $companydetails['road_name_area_colony'] }} ,
                            {{ $companydetails['city_name'] }},{{ $companydetails['state_name'] }},
                            {{ $companydetails['pincode'] }}
                        </td>
                    </tr>
                    <tr class="textblue">
                        <td style="text-align: center;padding-bottom:10px;">
                            <span><b>Contact:</b></span> {{ $companydetails['contact_no'] }} |
                            {{ $companydetails['email'] }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0;vertical-align:top">
                            <table width="100%" style="table-layout: fixed">
                                <tr>
                                    <td class="bgblue">
                                        <span class="font-weight-bold">To : </span>
                                        @if ($toFullName && $toCompany)
                                            {{ $toFullName }}, {{ $toCompany }}
                                        @elseif ($toFullName)
                                            {{ $toFullName }}
                                        @elseif ($toCompany)
                                            {{ $toCompany }}
                                        @endif
                                    </td>
                                    <td class="bgblue">
                                        <span class="font-weight-bold">Quote No. :</span>
                                        {{ $quotationdata['quotation_number'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bgblue">
                                        <span class="font-weight-bold">Contact :</span>
                                        {{ $quotationdata['contact_no'] }}
                                    </td>
                                    <td class="bgblue">
                                        <span class="font-weight-bold">Date :</span>
                                        {{ \Carbon\Carbon::parse($quotationdata['quotation_date_formatted'])->format('d-m-Y') }}
                                    </td>
                                </tr>
                                @isset($quotationdata['email'])
                                    <tr>
                                        <td class="bgblue">
                                            <span class="font-weight-bold">Email :</span> {{ $quotationdata['email'] }}
                                        </td>
                                        <td class="bgblue">
                                            <span class="font-weight-bold">Valid till:</span>
                                            {{ \Carbon\Carbon::parse($quotationdata['quotation_date_formatted'])->addDays($quotationdata['overdue_date'])->format('d-m-Y') }}
                                        </td>
                                    </tr>
                                @endisset
                                <tr>
                                    <td colspan="2" class="bgblue">
                                        <span class="font-weight-bold">Address:</span>
                                        @isset($quotationdata['house_no_building_name'])
                                            {{ $quotationdata['house_no_building_name'] }} ,
                                        @endisset

                                        @isset($quotationdata['road_name_area_colony'])
                                            {{ $quotationdata['road_name_area_colony'] }} ,
                                        @endisset

                                        @isset($quotationdata['city_name'])
                                            {{ $quotationdata['city_name'] }}
                                        @endisset

                                        @isset($quotationdata['state_name'])
                                            , {{ $quotationdata['state_name'] }}
                                        @endisset

                                        @isset($quotationdata['pincode'])
                                            , {{ $quotationdata['pincode'] }}
                                        @endisset
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>


            <table style="table-layout:fixed;" border="" cellspacing=0 cellpadding=0
                class="horizontal-border border data" width="100%">

                <thead>
                    <tr>
                        <th style="width:4%;text-align:center;">ID</th>
                        @foreach ($firstRowCols as $col)
                            <th style="text-align: center; width: {{ $col['column_width'] }}% !important;">
                                {{ strtoupper(str_replace('_', ' ', $col['column_name'])) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                {{-- product data  --}}
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
                                    $textAlign = $col['column_type'] == 'longtext' ? 'left' : 'center';
                                @endphp

                                @if ($key == 'amount')
                                    <td style="text-align: right;" class="currencysymbol">
                                        {{ Number::currency($val, in: $quotationdata['currency']) }}
                                    </td>
                                @else
                                    <td style="text-align:{{$textAlign}};">
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
                    {{-- end product data  --}}
                    <tr>
                        <td colspan="{{ $colspan }}" class="text-right left">
                            Subtotal
                        </td>
                        <td class="right currencysymbol text-right" id="subtotal">
                            {{ Number::currency($quotationdata['total'], in: $quotationdata['currency']) }}
                        </td>
                    </tr>
                    @if ($othersettings['gst'] == 0)
                        @if ($quotationdata['sgst'] >= 1)
                            <tr>
                                <td colspan="{{ $colspan }}" style="text-align: right"
                                    class="left ">
                                    SGST({{ $othersettings['sgst'] }}%)
                                </td>
                                <td style="text-align: right ;" class="currencysymbol" id="sgst">
                                    {{ Number::currency($quotationdata['sgst'], in: $quotationdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                        @if ($quotationdata['cgst'] >= 1)
                            <tr>
                                <td colspan="{{ $colspan }}" style="text-align: right"
                                    class="left ">
                                    CGST({{ $othersettings['cgst'] }}%)
                                </td>
                                <td style="text-align: right" class=" currencysymbol" id="cgst">
                                    {{ Number::currency($quotationdata['cgst'], in: $quotationdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                    @else
                        @if ($quotationdata['gst'] >= 1)
                            <tr>
                                <td colspan="{{ $colspan }}" style="text-align: right"
                                    class="left ">
                                    GST({{ $othersettings['sgst'] + $othersettings['cgst'] }}%)
                                </td>
                                <td style="text-align: right" class="currencysymbol " id="gst">
                                    {{ Number::currency($quotationdata['gst'], in: $quotationdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                    @endif
                    @unless ($roundof == 0)
                        <tr style="font-size:15px;text-align: right">
                            <td colspan="{{ $colspan }}" class="text-right left">
                                Round of
                            </td>
                            <td class="right currencysymbol text-right">
                                {{ $sign }} {{ Number::currency($roundof, in: $quotationdata['currency']) }}
                            </td>
                        </tr>
                    @endunless
                    <tr style="font-size:15px;text-align: right">
                        <td colspan="{{ $colspan }}" class="text-right left">
                            <b>Total</b>
                        </td>
                        <td class="right currencysymbol text-right">
                            {{ Number::currency($quotationdata['grand_total'], in: $quotationdata['currency']) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="{{ ++$colspan }}" class="text-right"
                            style="vertical-align: middle; text-align: right;font-style:italic;text-transform:uppercase;">
                            <strong>{{ $quotationdata['currency'] }} {{ $words }} Only</strong>
                        </td>
                    </tr>
                </tbody>
            </table>


            <table class="horizontal-border border">
                <tr class="bgblue">
                    <td style="vertical-align: top;">
                        @isset($quotationdata['notes'])
                            <span style="margin-top: 0"><b>Notes :- </b></span><br>
                            <div>{!! nl2br(e($quotationdata['notes'])) !!} </div>
                        @endisset
                        @isset($quotationdata['t_and_c'])
                            <span style="margin-top: 0"><b>Terms And Conditions :- </b></span>
                            <div id="tcspan"> {!! $quotationdata['t_and_c'] !!}</div>
                        @endisset
                    </td>
                </tr>
            </table>
            <div class="mt-1" style="font-size: 12px" id="footer">
                <span class="float-left"><small>This is a computer-generated document. No signature is
                        required.</small></span>
                <span class="float-right"><small>{{ date('d-M-Y, h:i A') }}</small></span>
            </div>
        </div>
    </main>
</body>

</html>
