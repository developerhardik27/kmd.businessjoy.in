@php
    // echo  (count($products[0])-2);
    // echo "<pre>";
    // print_r($productscolumn);
    // print_r($products);
    // print_r($invdata);
    // print_r($companydetails);
    // print_r($bankdetails);
    // print_r($othersettings);
    // die();

    // convert number to spelling:
    $words = Number::spell($invdata['grand_total']);

    $total;
    $roundof;
    $sign = '';

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

@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('app.name') }} - invoicePDf</title>
    {{-- <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}"> 
    <link rel="stylesheet" href="{{ asset('admin/css/typography.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
            font-size: 14px;
        }

        .bgblue {
            background-color: #002060;
            /* background-color: rgb(10 8 108 / 99%); */
            color: rgb(255, 253, 253);
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
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
            font-size: 25px;
            font-weight: bolder;
        }

        .horizontal-border td {
            border-bottom: 1px solid black;
        }

        .horizontal-border td {
            border-bottom: 1px solid transparent;
        }



        #data td {
            border-bottom: 1px solid black;
            border-right: 1px solid black;
        }

        #data td:last-child {
            border-right: none;
        }

        /* #data tbody tr:last-child td {
            border-bottom: none;
        } */

        #data .removeborder {
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
            page-break-inside: avoid;
            /* Prevent table from breaking across pages */
        }

        #data td,
        th {
            white-space: normal;
        }

        td {
            padding: 0px 5px;
        }

        .currencysymbol {
            font-family: DejaVu Sans;
            sans-serif;
        }
    </style>
</head>

<body>
    <div class="card-body table-wrapper">
        <table width="100%" class="horizontal-border">
            <tbody class="border">
                <tr class="firstrow" valign='bottom' style="padding: 5px;">
                    <td rowspan="" colspan="2" style="padding:5px;"><span class=" textblue firstrow"
                            style="display:block;" id="cname">{{ $companydetails['name'] }}</span>
                        <span style="display:block;">{!! nl2br(e(wordwrap($companydetails['address'], 40, "\n", true))) !!}</span>
                        <span style="display:block;">{{ $companydetails['city_name'] }},
                            {{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}</span>
                        <span style="display:block;">Email: {{ $companydetails['email'] }}</span>
                    </td>
                    <td colspan="2" style="text-align: center;width:50%;">
                        <div style="display: inline-block;">
                            <img @if ($companydetails['img'] != '') src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}" @endif
                                class="rounded mt-auto mx-auto d-block" alt="Company logo" style="max-width: 120px">
                        </div>
                    </td>
                </tr>
                <tr valign='top' class="firstrow">
                    <td style="width:250px;" colspan="2">
                        <span style="display:block;">Contact No: {{ $companydetails['contact_no'] }}</span>
                        @if (isset($companydetails['gst_no']))
                            <span><b>GSTIN No: {{ $companydetails['gst_no'] }}</b></span>
                        @endif
                    </td>
                    <td colspan="2" style="padding: 0;width:50%;">
                        <table width='100%' height='100%' style="text-align: center;margin-top:2px;"
                            class="horizontal-border full-height">
                            <thead>
                                <tr>
                                    <td class="font-weight-bold  bgblue">INVOICE#</td>
                                    <td class="font-weight-bold  bgblue">DATE</td>
                                </tr>
                            </thead>
                            <tbody style="">
                                <tr style="font-weight: bolder">
                                    <td>{{ $invdata['inv_no'] }}</td>
                                    <td> {{ \Carbon\Carbon::parse($invdata['inv_date'])->format('d-m-Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;" colspan="2">
                        <div class="bgblue" style="margin-right: 100px;">
                            <span style="padding-left:5px">
                                BILL TO
                            </span>
                        </div>
                    </td>
                    <td class="bgblue" style="text-align: center">customer id</td>
                    <td class="bgblue" style="text-align: center">payment type</td>
                </tr>
                <tr style="font-weight: bolder">
                    <td colspan="2">
                        <div class="textblue"><b>{{ $invdata['firstname'] }} {{ $invdata['lastname'] }}</b></div>
                    </td>
                    <td style="text-align: center">{{ $invdata['cid'] }}</td>
                    <td style="text-align: center">{{ $invdata['payment_type'] }}</td>
                </tr>
                <tr>
                    <td rowspan="" valign='top' style="width: 250px:padding:5px" colspan="2">
                        <div> {!! nl2br(e(wordwrap($invdata['address'], 40, "\n", true))) !!}</div>
                        <div>
                            @isset($invdata['city_name'])
                                {{ $invdata['city_name'] }},
                            @endisset
                            @isset($invdata['state_name'])
                                {{ $invdata['state_name'] }},
                            @endisset
                            @isset($invdata['pincode'])
                                {{ $invdata['pincode'] }}
                            @endisset
                        </div>
                        <div>{{ $invdata['email'] }}</div>
                        <div>{{ $invdata['contact_no'] }}</div>
                        @if (isset($invdata['gst_no']))
                            <div><b>GSTIN No: {{ $invdata['gst_no'] }}</b></div>
                        @endif
                    </td>
                    <td colspan="2" style="padding: 0">
                        <table width="100%" class="horizontal-border">
                            <thead class="bgblue">
                                <tr class="bgblue">
                                    <td colspan="2" style="text-align: center">Bank Details</td>
                                </tr>
                            </thead>
                            <tbody style="text-align: left">
                                <tr class="bgsilver">
                                    <td>Holder Name</td>
                                    <td>{{ $bankdetails['holder_name'] }}</td>
                                </tr>
                                <tr>
                                    <td>A/c No</td>
                                    <td>{{ $bankdetails['account_no'] }}</td>
                                </tr>
                                <tr class="bgsilver">
                                    <td>Swift Code</td>
                                    <td>{{ $bankdetails['swift_code'] }}</td>
                                </tr>
                                <tr>
                                    <td>IFSC Code</td>
                                    <td>{{ $bankdetails['ifsc_code'] }}</td>
                                </tr>
                                <tr class="bgsilver">
                                    <td>Branch Name</td>
                                    <td>{{ $bankdetails['branch_name'] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 0" id="data">
                        <table id="data" cellspacing=0 cellpadding=0 class=" horizontal-border" width="100">
                            <thead>
                                <tr class="bgblue">
                                    <th><span style="padding-left: 5px"> # </span></th>
                                    @forelse ($productscolumn as $val)
                                        @php
                                            $columnname = strtoupper(str_replace('_', ' ', $val));
                                        @endphp

                                        <th style="text-align: center">{{ $columnname }}</th>

                                    @empty
                                        <th>-</th>
                                    @endforelse
                                </tr>
                            </thead>
                            <tbody>
                                @php $srno = 0 ; @endphp
                                @foreach ($products as $row)
                                    @php $srno++ ; @endphp
                                    <tr>
                                        <td style="text-align: center">{{ $srno }}</td>
                                        @foreach ($row as $key => $val)
                                            @if ($key === array_key_last($row))
                                                <td style="text-align:right;" class="currencysymbol">
                                                    {{-- {{ $invdata['currency_symbol'] }}
                                                    {{ formatDecimal($val) }} --}}
                                                    {{ Number::currency($val, in: $invdata['currency']) }}
                                                </td>
                                            @else
                                                <td style="text-align:center;">
                                                    @if (strlen($val) > 40)
                                                        @php
                                                            $val = wordwrap($val, 40, '<br>', true);
                                                        @endphp
                                                        {!! $val !!}
                                                    @else
                                                        {{ $val }}
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                @for ($i = 0; $i < 10 - $srno; $i++)
                                    <tr style="text-align: center">
                                        @for ($j = 0; $j < count($products[0]); $j++)
                                            @if ($j == ceil(count($products[0]) / 2))
                                                <td style="text-align: center">-</td>
                                            @endif
                                            <td></td>
                                        @endfor
                                    </tr>
                                @endfor
                                <tr>
                                    <td colspan="@php echo (count($products[0])); @endphp" style="text-align: right"
                                        class="left removeborder">
                                        Subtotal
                                    </td>
                                    <td style="text-align: right" class="right removeborder currencysymbol "
                                        id="subtotal">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ formatDecimal($invdata['total']) }} --}}
                                        {{ Number::currency($invdata['total'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                                @if ($othersettings['gst'] == 0)
                                    @if ($invdata['sgst'] >= 1)
                                        <tr class=" ">
                                            <td colspan="@php echo (count($products[0])); @endphp"
                                                style="text-align: right" class="left removeborder ">
                                                SGST({{ $othersettings['sgst'] }}%)
                                            </td>
                                            <td style="text-align: right ;" class="currencysymbol" id="sgst">
                                                {{-- {{ $invdata['currency_symbol'] }}  {{ formatDecimal($invdata['sgst']) }} --}}
                                                {{ Number::currency($invdata['sgst'], in: $invdata['currency']) }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($invdata['cgst'] >= 1)
                                        <tr class=" ">
                                            <td colspan="@php echo (count($products[0])); @endphp"
                                                style="text-align: right" class="left removeborder ">
                                                CGST({{ $othersettings['cgst'] }}%)
                                            </td>
                                            <td style="text-align: right" class=" currencysymbol" id="cgst">
                                                {{-- {{ $invdata['currency_symbol'] }}  {{ formatDecimal($invdata['cgst']) }} --}}
                                                {{ Number::currency($invdata['cgst'], in: $invdata['currency']) }}
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($invdata['gst'] >= 1)
                                        <tr class=" ">
                                            <td colspan="@php echo (count($products[0])); @endphp"
                                                style="text-align: right" class="left removeborder ">
                                                GST({{ $othersettings['sgst'] + $othersettings['cgst'] }}%)
                                            </td>
                                            <td style="text-align: right" class="currencysymbol " id="gst">
                                                {{-- {{ $invdata['currency_symbol'] }} {{ formatDecimal($invdata['gst']) }} --}}
                                                {{ Number::currency($invdata['gst'], in: $invdata['currency']) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif

                                <tr class="" style="font-size:15px;text-align: right">
                                    <td colspan="@php echo (count($products[0])); @endphp" class="left removeborder">
                                        Round of
                                    </td>
                                    <td style="text-align: right" class="right currencysymbol">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ $roundof }} --}}
                                       {{$sign}} {{ Number::currency($roundof, in: $invdata['currency']) }}
                                    </td>
                                </tr>
                                <tr class="" style="font-size:15px;text-align: right">
                                    <td colspan="@php echo (count($products[0])); @endphp" class="left removeborder">
                                        <b>Total</b>
                                    </td>
                                    <td style="text-align: right" class="right currencysymbol">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ $invdata['grand_total'] }}.00 --}}
                                        {{ Number::currency($invdata['grand_total'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                                <tr class="removeborder">
                                    <td colspan="@php echo (count($products[0])+1); @endphp" class=""
                                        style="vertical-align: middle; text-align: right;font-style:italic">
                                        <strong class="">{{ $invdata['currency'] }}
                                            {{ $words }} Only</strong>
                                    </td>
                                </tr>
                                <tr class="removeborder">
                                    <td colspan="@php echo (count($products[0])+1); @endphp" class="bgblue  bgspecial"
                                        style="vertical-align: middle; text-align: center;font-style:italic">
                                        <strong class="">Thank You For Your business!</strong>
                                    </td>
                                </tr>
                                {{-- <tr class="removeborder">
                                    <td colspan="@php echo (count($products[0])+1); @endphp"
                                        style="vertical-align: middle; text-align:left;white-space: pre-line;">
                                        Terms And Condtions :- </br> {{ $invdata['t_and_c'] }}
                                    </td>
                                </tr> --}}
                                {{-- <tr>
                                    <td style="text-align: center"
                                        colspan="@php echo (count($products[0])+1); @endphp">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;color:rgb(159, 157, 157)"
                                        colspan="@php echo (count($products[0])+1); @endphp">Notes: Any
                                        changes in future will be
                                        chargeable.</td>
                                </tr>
                                <tr>
                                    <td style="text-align: center"
                                        colspan="@php echo (count($products[0])+1); @endphp"> For any query, Please
                                        contact <br>
                                        <b> [Jay Patel, +91 9998-1118-74, info@oceanmnc.com]</b>
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 0">
                        <table id="data" cellspacing=0 cellpadding=0 class=" horizontal-border" width="100">
                            <tbody>
                                <tr class="removeborder">
                                    <td colspan="@php echo (count($products[0])+1); @endphp"
                                        style="vertical-align: middle; text-align:left;white-space: pre-line;">
                                        Terms And Condtions :- </br>{!! $invdata['t_and_c'] !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
