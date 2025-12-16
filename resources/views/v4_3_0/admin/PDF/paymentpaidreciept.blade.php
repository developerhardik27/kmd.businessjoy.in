@php

    $words = Number::spell($invdata['grand_total']); // convert total amount to words

    $total;
    $roundof;
    $sign = '';
    $withgst = false;

    if ($invdata['gst'] > 0 || $invdata['sgst'] > 0 || $invdata['cgst'] > 0) {
        $withgst = true; // if invoice created with gst
    }

    if ($invdata['gst'] != 0) {
        $total = $invdata['total'] + $invdata['gst'];
    } elseif ($invdata['sgst'] != 0 && $invdata['cgst'] != 0) {
        $total = $invdata['total'] + $invdata['sgst'] + $invdata['cgst'];
    } else {
        $total = $invdata['total'];
    }

    //count round off
    if ($invdata['grand_total'] > $total) {
        $value = $invdata['grand_total'] - $total;
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $roundof = '+' . $roundof;
        }
    } else {
        $value = $total - $invdata['grand_total'];
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $roundof = '-' . $roundof;
        }
    }

    $othersettings = json_decode($othersettings['gstsettings'], true);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Payment Reciept</title>
    <link rel="stylesheet" href="{{ public_path('admin/css/bootstrap.min.css')}}">
    <style>
        .bottom-border-input {
            border: none;
            /* Remove default border */
            border-bottom: 1px solid black;
            /* Apply transparent bottom border by default */
            outline: none;
            /* Remove default input focus outline */
        }

        .bottom-border-input.active {
            border-bottom: 1px solid black;
            /* Display bottom border when input is active (clicked) */
        }

        .cname {
            font-size: 15px;
            font-weight: bolder;
        }

        .textblue {
            color: #092d75;
            font: bolder
        }

        * {
            margin: 0;
            padding: 3px 3px;
        }

        input {
            margin-top: 10px
        }

        table {
            /* padding: 0; */
        }

        .currencysymbol {
            font-family: DejaVu Sans, sans-serif;
        }

        .horizontal-border {
            width: 100%;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
        }

        .horizontal-border td {
            border-bottom: 1px solid black;
        }

        .horizontal-border th {
            border-top: 1px solid grey;
            border-bottom: 1px solid grey;
        }

        .removetdborder {
            border: 1px solid transparent !important;
        }

        .default {
            margin: 0px !important;
            padding: 0px !important;
        }

        header {
            position: fixed;
            top: -20px;
            left: 0px;
            right: 5px;
            height: 50px;
            text-align: center;
            line-height: 35px;
            color: grey;
            font-size: 10px;
        }

        body {
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        #footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
            color: grey;
        }

        #pdtable tr,
        #pdtable td {
            margin: 0;
            padding: 0px 2px;
        }

        .removepadding {
            padding: 0px 3px;
        }

        #data td,
        th {
            white-space: normal;
            word-wrap: break-word;
        }

        #data td {
            line-break: anywhere !important;
        }
    </style>
</head>

<body>
    <header>
        <div style="float: right">
            Receipt | {{ $companydetails['name'] }}
        </div>
    </header>
    <div class="container">
        <table width='100%' class="maintable" cellspacing=0 cellpadding=0>
            <tr>
                <td style="vertical-align: top;width:33%;">
                    <div style="display: inline-block;">
                        <img @if ($companydetails['img'] != '') src="{{ public_path('uploads/' . $companydetails['img']) }}" @endif
                            class="rounded mt-auto mx-auto d-block" alt="signature" style="max-width: 150px">
                    </div>
                </td>
                <td valign=top class="default" style="width:33%;">
                    <span class="textblue firstrow cname default"
                        style="display:block;">{{ $companydetails['name'] }}</span>
                    <span class="default" style="display:block;">{!! nl2br(e(wordwrap($companydetails['house_no_building_name'], 40, "\n", true))) !!}</span>
                    <span class="default" style="display:block;">{!! nl2br(e(wordwrap($companydetails['road_name_area_colony'], 40, "\n", true))) !!}</span>
                    <span class="default" style="display:block;">{{ $companydetails['city_name'] }},
                        {{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}
                    </span>
                    @isset($companydetails['email'])
                        <span class="default" style="display:block;">Email: {{ $companydetails['email'] }}</span>
                    @endisset
                    <span class="default" style="display:block;">Contact: {{ $companydetails['contact_no'] }}</span>
                </td>
                <td style="vertical-align: top;width:33%;">
                    <span style="display: block">
                        TAX INVOICE
                    </span>
                    @if ($withgst)
                        <span>GSTIN No: @isset($companydetails['gst_no'])
                                {{ $companydetails['gst_no'] }}
                            @endisset
                        </span>
                    @endif
                    @isset($companydetails['transporter_id'])
                        <span>Transporter ID: {{ $companydetails['transporter_id'] }} </span>
                    @endisset
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span style="width:100%;display:block;border-bottom:1px solid grey;"></span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top">
                    <span class="default textblue firstrow cname" style="display:block;" id="">Bill To</span>
                    @if (isset($invdata['firstname']) || isset($invdata['lastname']))
                        <span class="default" style="display:block;">
                            @isset($invdata['firstname'])
                                {{ $invdata['firstname'] }}
                            @endisset
                            @isset($invdata['lastname'])
                                {{ $invdata['lastname'] }}
                            @endisset
                        </span>
                    @endif
                    @if (isset($invdata['company_name']))
                        <span class="default" style="display:block;">
                            {{ $invdata['company_name'] }}
                        </span>
                    @endif
                    @isset($invdata['house_no_building_name'])
                        <span class="default" style="display:block;">{!! nl2br(e(wordwrap($invdata['house_no_building_name'], 40, "\n", true))) !!}</span>
                    @endisset
                    @isset($invdata['road_name_area_colony'])
                        <span class="default" style="display:block;">{!! nl2br(e(wordwrap($invdata['road_name_area_colony'], 40, "\n", true))) !!}</span>
                    @endisset
                    <span class="default" style="display:block;">
                        @isset($invdata['city_name'])
                            {{ $invdata['city_name'] }},
                        @endisset
                        @isset($invdata['state_name'])
                            {{ $invdata['state_name'] }},
                        @endisset
                        @isset($invdata['pincode'])
                            {{ $invdata['pincode'] }}
                        @endisset
                    </span>
                    <span class="default"style="display:block;">{{ $invdata['email'] }}</span>
                    <span class="default">{{ $invdata['contact_no'] }}</span>
                </td>
                <td style="vertical-align: top">
                    <table id="pdtable">
                        @if (count($payment) == 1)
                            <tr>
                                <td><b>Date</b></td>
                                <td style="text-align: right;">
                                    {{ \Carbon\Carbon::parse($payment[0]['datetime'])->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td><b>Method</b></td>
                                <td style="text-align: right;"> {{ $payment[0]['paid_type'] }}</td>
                            </tr>
                            @isset($payment[0]['transaction_id'])
                                <tr>
                                    <td><b>Transcation Id</b></td>
                                    <td style="text-align: right;"> {{ $payment[0]['transaction_id'] }}</td>
                                </tr>
                            @endisset
                            @isset($payment[0]['paid_by'])
                                <tr>
                                    <td><b>Paid By</b></td>
                                    <td style="text-align: right;"> {{ $payment[0]['paid_by'] }}</td>
                                </tr>
                            @endisset
                            <tr>
                                <td><b>Receipt #</b></td>
                                <td style="text-align: right;">{{ $payment[0]['receipt_number'] }}</td>
                            </tr>
                        @endif    
                        <tr>
                            <td><b>Invoice #</b></td>
                            <td style="text-align: right;">{{ $invdata['inv_no'] }}</td>
                        </tr>
                        @if ($withgst)
                            <tr>
                                <td><b>GST #</b></td>
                                <td style="text-align: right;">
                                    @isset($invdata['gst_no'])
                                        {{ $invdata['gst_no'] }}
                                    @endisset
                                </td>
                            </tr>
                        @endif 
                    </table>
                </td>
            </tr>
        </table>
        @if (count($payment) > 1)
            <table style="table-layout:fixed;" id="data" cellspacing=0 cellpadding=0 class="w-100"
                width="100">
                <tbody>
                    <tr>
                        <td id="data" colspan="3">
                            <table id="data" cellspacing=0 cellpadding=0 class="horizontal-border"
                                width="100">
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Receipt</th>
                                    <th>Total Amount</th>
                                    <th>Received Amount</th>
                                    <th>TDS</th>
                                    <th>Pedning Amount</th>
                                </tr>
                                @foreach ($payment as $value)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($value['datetime'])->format('d-m-Y') }}</td>
                                        <td>{{ $value['paid_type'] }}</td>
                                        <td>{{ $value['receipt_number'] }}</td>
                                        <td>{{ $value['amount'] }}</td>
                                        <td>{{ $value['paid_amount'] }}</td>
                                        <td>{{ $value['tds_amount'] }}</td>
                                        <td>{{ $value['pending_amount'] }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <table style="table-layout:fixed;" id="data" cellspacing=0 cellpadding=0 class="horizontal-border"
                width="100">
                <thead>
                    <tr class="bgblue">
                        <th style="width:4%;text-align:center;">#</th>
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

                    {{-- end product data --}}
                </tbody>
            </table>
            <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                width="100%">
                <tbody>
                    <tr class="removepadding">
                        <td style="text-align: right;"
                            class="left removetdborder  ">
                            Subtotal
                        </td>
                        <td style="text-align: right;width:20% !important;" class="right removetdborder currencysymbol" id="subtotal">
                            {{ Number::currency($invdata['total'], in: $invdata['currency']) }}
                        </td>
                    </tr>
                    @if ($othersettings['gst'] == 0)
                        @if ($invdata['sgst'] > 0)
                            <tr class="removepadding">
                                <td style="text-align: right;"
                                    class="left removetdborder removepadding">
                                    SGST({{ $othersettings['sgst'] }}%)
                                </td>
                                <td style="text-align: right;width:20% !important;" class="currencysymbol removetdborder removepadding"
                                    id="sgst">
                                    {{ Number::currency($invdata['sgst'], in: $invdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                        @if ($invdata['cgst'] > 0)
                            <tr class="removepadding">
                                <td style="text-align: right;"
                                    class="left removetdborder removepadding">
                                    CGST({{ $othersettings['cgst'] }}%)
                                </td>
                                <td style="text-align: right;width:20% !important;" class="currencysymbol removetdborder removepadding"
                                    id="cgst">
                                    {{ Number::currency($invdata['cgst'], in: $invdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                    @else
                        @if ($invdata['gst'] > 0)
                            <tr class="removepadding">
                                <td style="text-align: right;"
                                    class="left removetdborder removepadding">
                                    GST({{ $othersettings['sgst'] + $othersettings['cgst'] }}%)
                                </td>
                                <td style="text-align: right;width:20% !important;" class="currencysymbol removetdborder removepadding"
                                    id="gst">
                                    {{ Number::currency($invdata['gst'], in: $invdata['currency']) }}
                                </td>
                            </tr>
                        @endif
                    @endif
                    @unless ($roundof == 0)
                        <tr class="removepadding" style="font-size:15px;text-align: right;">
                            <td
                                class="left removetdborder removepadding">
                                Round of
                            </td>
                            <td style="text-align: right;width:20% !important;" class="right currencysymbol  removepadding">
                                {{ $sign }} {{ Number::currency($roundof, in: $invdata['currency']) }}
                            </td>
                        </tr>
                    @endunless
                    <tr class="" style="font-size:15px;text-align: right;">
                        <td class="left removetdborder">
                            <b>Total</b>
                        </td>
                        <td style="text-align: right;width:20% !important;" class="right removetdborder currencysymbol">
                            {{ Number::currency($invdata['grand_total'], in: $invdata['currency']) }}
                        </td>
                    </tr>
                    <tr class="" style="font-size:15px;text-align: right;">
                        <td class="left removetdborder">
                            <b>Amount Received</b>
                        </td>
                        <td style="text-align: right;width:20% !important;" class="right removetdborder currencysymbol">
                            {{ Number::currency($payment[0]['paid_amount'], in: $invdata['currency']) }}
                        </td>
                    </tr>
                    <tr class="" style="font-size:15px;text-align: right;">
                        <td class="left removetdborder">
                            <b>TDS Amount</b>
                        </td>
                        <td style="text-align: right;width:20% !important;" class="right removetdborder currencysymbol">
                            {{ Number::currency($payment[0]['tds_amount'], in: $invdata['currency']) }}
                        </td>
                    </tr>
                    <tr class="" style="font-size:15px;text-align: right;">
                        <td class="left removetdborder">
                            <b>Paid Amount</b>
                        </td>
                        <td style="text-align: right;width:20% !important;" class="right removetdborder currencysymbol">
                            {{ Number::currency($payment[0]['amount'] - $payment[0]['pending_amount'], in: $invdata['currency']) }}
                        </td>
                    </tr>
                    <tr class="" style="font-size:15px;text-align: right;">
                        <td class="left removetdborder">
                            <b>Pending Amount</b>
                        </td>
                        <td style="text-align: right;width:20% !important;" class="right removetdborder currencysymbol">
                            <b>{{ Number::currency($payment[0]['pending_amount'], in: $invdata['currency']) }}</b>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
        <table width='100%' class="maintable" cellspacing=0 cellpadding=0>
            <tr>
                <td colspan="2">
                    <div style="display: inline-block;">
                        For : {{ $companydetails['name'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="display: inline-block;">
                        <img @if ($companydetails['pr_sign_img'] != '') src="{{ public_path('uploads/' . $companydetails['pr_sign_img']) }}" @endif
                            class="rounded mt-auto mx-auto d-block" alt="signature" style="max-width: 150px">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="display: inline-block;">
                        Signature
                    </div>
                </td>
            </tr>
        </table>

    </div>
    <footer>
        <div class="mt-1" style="font-size: 12px" id="footer">
            <span class="float-left">
                <small>This is a computer-generated document.
                    @unless ($companydetails['pr_sign_img'])
                        No signature is required.
                    @endunless
                </small>
            </span>
            <span class="float-right"><small>{{ date('d-M-Y, h:i A') }}</small></span>
        </div>
    </footer>
</body>

</html>
