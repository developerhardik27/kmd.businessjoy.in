@php

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
            $roundof = '+' . $roundof;
        }
    } else {
        $value = $total - $invdata['grand_total'];
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $roundof = '-' . $roundof;
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Payment Reciept</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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
            margin: 3px;
            padding: 5px;
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

        @page {
            margin: 100px 25px;
        }

        header {
            position: fixed;
            top: -20px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
            color: grey;
            font-size: 10px;
        }

        body {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            color: grey;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <header>
        <div style="float: left">
            {{ date('d-m-Y , h:i a') }}
        </div>
        <div style="float: right">
            Receipt | {{ $companydetails['name'] }}
        </div>
    </header>
    <div class="container">
        <table width='100%' class="maintable" cellspacing=0 cellpadding=0>
            <tr>
                <td style="vertical-align: top">
                    <div style="display: inline-block;">
                        <img @if ($companydetails['img'] != '') src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}" @endif
                            class="rounded mt-auto mx-auto d-block" alt="signature" style="max-width: 120px">
                    </div>
                </td>
                <td valign=top class="default">
                    <span class="textblue firstrow cname default"
                        style="display:block;">{{ $companydetails['name'] }}</span>
                    <span class="default" style="display:block;">{!! nl2br(e(wordwrap($companydetails['address'], 40, "\n", true))) !!}</span>
                    <span class="default" style="display:block;">{{ $companydetails['city_name'] }},
                        {{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}</span>
                    <span class="default" style="display:block;">Email: {{ $companydetails['email'] }}</span>
                    <span class="default" style="display:block;">Contact: {{ $companydetails['contact_no'] }}</span>
                </td>
                <td style="vertical-align: top">
                    <span style="display: block">
                        TAX INVOICE
                    </span>
                    @if (isset($companydetails['gst_no']))
                        <span>GSTIN No: {{ $companydetails['gst_no'] }}</span>
                    @endif
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
                    <span class="default" style="display:block;"> {{ $invdata['firstname'] }} {{ $invdata['lastname'] }}</span>
                    <span class="default" style="display:block;">{!! nl2br(e(wordwrap($invdata['address'], 40, "\n", true))) !!}</span>
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
                    <span class="default" style="display:block;">{{ $invdata['email'] }}</span>
                    <span class="default">{{ $invdata['contact_no'] }}</span>
                </td>
                <td style="vertical-align: top">
                    <table>
                        @if (count($payment) <= 1)
                        <tr>
                            <td><b>Date</b></td>
                            <td style="text-align: right">  {{ \Carbon\Carbon::parse($payment[0]['datetime'])->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <td><b>Method</b></td>
                            <td style="text-align: right"> {{ $payment[0]['paid_type'] }}</td>
                        </tr>
                        <tr>
                            <td><b>Receipt #</b></td>
                            <td style="text-align: right">{{$payment[0]['receipt_number']}}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><b>Invoice #</b></td>
                            <td style="text-align: right">{{ $invdata['inv_no'] }}</td>
                        </tr>
                        <tr>
                            <td><b>GST #</b></td>
                            <td style="text-align: right">{{ $invdata['gst_no'] }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            @if (count($payment) > 1){
                <tr>
                    <td id="data" colspan="3">
                        <table id="data" cellspacing=0 cellpadding=0 class="horizontal-border mt-5" width="100">
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Receipt</th>
                                <th>Total Amount</th>
                                <th>Received Amount</th>
                                <th>Pedning Amount</th>
                            </tr>
                            @foreach ($payment as $value)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($value['datetime'])->format('d-m-Y') }}</td>
                                    <td>{{ $value['paid_type'] }}</td>
                                    <td>{{$value['receipt_number']}}</td>
                                    <td>{{ $value['amount'] }}</td>
                                    <td>{{ $value['paid_amount'] }}</td>
                                    <td>{{ $value['pending_amount'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                }
            @else
                <tr>
                    <td id="data" colspan="3">
                        <table id="data" cellspacing=0 cellpadding=0 class="horizontal-border mt-5" width="100">
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
                                <tr>
                                    <td colspan="@php echo (count($products[0])); @endphp" style="text-align: right"
                                        class="left removetdborder  ">
                                        Subtotal
                                    </td>
                                    <td style="text-align: right" class="right removetdborder currencysymbol "
                                        id="subtotal">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ formatDecimal($invdata['total']) }} --}}
                                        {{ Number::currency($invdata['total'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                                @if ($othersettings['gst'] == 0)
                                    @if ($invdata['sgst'] >= 1)
                                        <tr class=" ">
                                            <td colspan="@php echo (count($products[0])); @endphp"
                                                style="text-align: right" class="left removetdborder ">
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
                                                style="text-align: right" class="left removetdborder ">
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
                                                style="text-align: right" class="left removetdborder ">
                                                GST({{ $othersettings['sgst'] + $othersettings['cgst'] }}%)
                                            </td>
                                            <td style="text-align: right" class="currencysymbol " id="gst">
                                                {{ Number::currency($invdata['gst'], in: $invdata['currency']) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endif

                                <tr class="" style="font-size:15px;text-align: right">
                                    <td colspan="@php echo (count($products[0])); @endphp" class="left removetdborder">
                                        Round of
                                    </td>
                                    <td style="text-align: right" class="right currencysymbol">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ $roundof }} --}}
                                        {{$sign}}  {{ Number::currency($roundof, in: $invdata['currency']) }}
                                    </td>
                                </tr>
                                <tr class="" style="font-size:15px;text-align: right">
                                    <td colspan="@php echo (count($products[0])); @endphp"
                                        class="left removetdborder">
                                        <b>Total</b>
                                    </td>
                                    <td style="text-align: right" class="right removetdborder currencysymbol">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ $invdata['grand_total'] }}.00 --}}
                                        {{ Number::currency($invdata['grand_total'], in: $invdata['currency']) }}
                                    </td>
                                </tr>
                                @foreach ($payment as $value)
                                    <tr class="" style="font-size:15px;text-align: right">
                                        <td colspan="@php echo (count($products[0])); @endphp"
                                            class="left removetdborder">
                                            <b>Paid Amount</b>
                                        </td>
                                        <td style="text-align: right" class="right removetdborder currencysymbol">
                                            {{-- {{ $invdata['currency_symbol'] }} {{ $invdata['grand_total'] }}.00 --}}
                                            {{ Number::currency($value['paid_amount'], in: $invdata['currency']) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="" style="font-size:15px;text-align: right">
                                    <td colspan="@php echo (count($products[0])); @endphp"
                                        class="left removetdborder">
                                        <b>Pending Amount</b>
                                    </td>
                                    <td style="text-align: right" class="right removetdborder currencysymbol">
                                        {{-- {{ $invdata['currency_symbol'] }} {{ $invdata['grand_total'] }}.00 --}}
                                        <b> {{ Number::currency(00, in: $invdata['currency']) }}</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endif
            <tr>
                <td>
                    <div style="display: inline-block;">
                        <img @if ($companydetails['pr_sign_img'] != '') src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['pr_sign_img']))) }}" @endif
                            class="rounded mt-auto mx-auto d-block" alt="signature" style="max-width: 120px">
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
