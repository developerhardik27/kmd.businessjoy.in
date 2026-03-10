@php
    $transport_details = $order['transport_details'];
    $buyer_details = $order['buyer_details'];
    $order_detias = $order['order_details'];
    $order = $order['order'];
    $words = Number::spell($order['finalAmount']);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Order Pdf</title>
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
            padding: 0;
        }

        .vertical-align-custom {
            vertical-align: start !important;
        }

        .blank-row td {
            height: 10px;
            border-top: 1px solid #fff !important;
            border-bottom: 1px solid #fff !important;
            background: #fff;
        }
    </style>
</head>

<body>
    <main>
        <div class="table-wrapper">
            <div class="table-wrapper">

                {{-- HEADER --}}
                <table cellspacing=0 cellpadding=0 width="100%" class="border">
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center bgblue">Order Pdf</td>
                        </tr>
                        <tr class="blank-row">
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            {{-- Buyer --}}
                            <td style="width:50%;padding:0;vertical-align:top">
                                <table width="100%">
                                    <tr class="bgblue">
                                        <th class="font-weight-bold bgblue" style="padding-left:10px">Buyer Details</th>
                                    </tr>
                                    @if (isset($buyer_details['name']))
                                        <tr class="font-weight-bold">
                                            <td class="textblue" style="padding-left:10px">{{ $buyer_details['name'] }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if (!empty($buyer_details['address']))
                                        <tr>
                                            <td style="padding-left:10px">{{ $buyer_details['address'] }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="padding-left:10px">
                                            @isset($buyer_details['city_name'])
                                                {{ $buyer_details['city_name'] }},
                                            @endisset
                                            @isset($buyer_details['state_name'])
                                                {{ $buyer_details['state_name'] }},
                                            @endisset
                                            @isset($buyer_details['pincode'])
                                                {{ $buyer_details['pincode'] }}
                                            @endisset
                                        </td>
                                    </tr>
                                    @if (!empty($buyer_details['email']))
                                        <tr>
                                            <td style="padding-left:10px">{{ $buyer_details['email'] }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="padding-left:10px">
                                            {{ $buyer_details['mobile_1'] ?? '' }}
                                            @if (!empty($buyer_details['mobile_2']))
                                                / {{ $buyer_details['mobile_2'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if (!empty($buyer_details['gst_no']))
                                        <tr>
                                            <td style="padding-left:10px"><b>GSTIN No:
                                                    {{ $buyer_details['gst_no'] }}</b></td>
                                        </tr>
                                    @endif
                                    @if (!empty($buyer_details['pan']))
                                        <tr>
                                            <td style="padding-left:10px"><b>Pan No: {{ $buyer_details['pan'] }}</b>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </td>

                            <td style="width:10%;"></td>

                            {{-- Transport (only if exists) --}}
                            <td style="width:40%;padding:0;text-align:center;vertical-align:top">
                                @if (isset($transport_details))
                                    <table width="100%">
                                        <tr class="bgblue">
                                            <th class="font-weight-bold bgblue" style="padding-left:10px">Transporter
                                            </th>
                                        </tr>
                                        @if (isset($transport_details['name']))
                                            <tr class="font-weight-bold">
                                                <td class="textblue" style="padding-left:10px">
                                                    {{ $transport_details['name'] }}</td>
                                            </tr>
                                        @endif
                                        @if (!empty($transport_details['address']))
                                            <tr>
                                                <td style="padding-left:10px">{{ $transport_details['address'] }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td style="padding-left:10px">
                                                @isset($transport_details['city_name'])
                                                    {{ $transport_details['city_name'] }},
                                                @endisset
                                                @isset($transport_details['state_name'])
                                                    {{ $transport_details['state_name'] }},
                                                @endisset
                                                @isset($transport_details['pincode'])
                                                    {{ $transport_details['pincode'] }}
                                                @endisset
                                            </td>
                                        </tr>
                                        @if (!empty($transport_details['email']))
                                            <tr>
                                                <td style="padding-left:10px">{{ $transport_details['email'] }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td style="padding-left:10px">
                                                {{ $transport_details['mobile_1'] ?? '' }}
                                                @if (!empty($transport_details['mobile_2']))
                                                    / {{ $transport_details['mobile_2'] }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if (!empty($transport_details['gst_no']))
                                            <tr>
                                                <td style="padding-left:10px"><b>GSTIN No:
                                                        {{ $transport_details['gst_no'] }}</b></td>
                                            </tr>
                                        @endif
                                        @if (!empty($transport_details['pan']))
                                            <tr>
                                                <td style="padding-left:10px"><b>Pan No:
                                                        {{ $transport_details['pan'] }}</b></td>
                                            </tr>
                                        @endif
                                    </table>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- ORDER META --}}
                <table cellspacing="0" cellpadding="0" width="100%" class="border-left-right">
                    <tr>
                        <td style="padding-left:10px;">
                            <b>Order Id: {{ $order['id'] ?? '' }}</b>
                            &nbsp;&nbsp;
                            @if (!empty($order['credit_days']))
                                <b>Credit Days: {{ $order['credit_days'] }}</b>
                                &nbsp;&nbsp;
                            @endif
                            @if (!empty($order['discount']) && $order['discount'] > 0)
                                <b>Discount: {{ $order['discount'] }} %</b>
                            @endif
                        </td>
                    </tr>
                </table>


                <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                    width="100%">
                    <thead>
                        <tr class="bgblue">
                            <th style="width:4%;text-align:center;">#</th>
                            <th style="width:12%;text-align:center;">DATE</th>
                            <th style="width:22%;text-align:center;">GARDEN NAME</th>
                            <th style="width:17%;text-align:center;">LOT / INVOICE</th>
                            <th style="width:13%;text-align:center;">GRADE</th>
                            <th style="width:10%;text-align:center;">BAGS</th>
                            <th style="width:10%;text-align:center;">KGS</th>
                            <th style="width:12%;text-align:center;">RATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $srno = 0;
                            $minRows = 10;
                        @endphp

                        @foreach ($order_detias as $row)
                            @php $srno++; @endphp
                            <tr>
                                <td style="text-align:center;width:4%;">{{ $srno }}</td>
                                <td style="text-align:center;">
                                    {{ !empty($row['created_at']) ? \Carbon\Carbon::parse($row['created_at'])->format('d-m-Y') : '-' }}
                                </td>
                                <td style="text-align:center;">{{ $row['garden_name'] ?? '-' }}</td>
                                <td style="text-align:center;">{{ $row['invoice_no'] ?? '-' }}</td>
                                <td style="text-align:center;">{{ $row['grade_name'] ?? '-' }}</td>
                                <td style="text-align:center;">{{ $row['bags'] ?? '0' }}</td>
                                <td style="text-align:center;">{{ $row['kg'] ?? '0' }}</td>
                                <td style="text-align:center;">{{ $row['rate'] ?? '0' }}</td>
                            </tr>
                        @endforeach

                        {{-- Fill blank rows up to minimum 10 --}}
                        @if ($srno < $minRows)
                            @for ($i = $srno + 1; $i <= $minRows; $i++)
                                <tr>
                                    <td style="text-align:center;">{{ $i }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>

                {{-- TOTALS — colspan 7 + 1 amount col = 8 total (matches line items) --}}
                <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                    width="100%">
                    <tbody>
                        <tr style="font-size:15px;text-align:right;">
                            <td colspan="7" class="text-right left removeborder">Subtotal</td>
                            <td style="width:12%;text-align:right;" class="removeborder">
                                {{ number_format((float) ($order['totalAmount'] ?? 0), 2) }}
                            </td>
                        </tr>

                        @if (!empty($order['discount']) && $order['discount'] > 0)
                            <tr style="font-size:15px;text-align:right;">
                                <td colspan="7" class="text-right left removeborder">
                                    Discount ({{ $order['discount'] }}%)
                                </td>
                                <td style="width:12%;text-align:right;" class="removeborder">
                                    - {{ number_format((float) ($order['discountAmount'] ?? 0), 2) }}
                                </td>
                            </tr>
                        @endif

                        <tr style="font-size:15px;text-align:right;">
                            <td colspan="7" class="text-right left removeborder"><b>Total</b></td>
                            <td style="width:12%;text-align:right;" class="removeborder">
                                <b>{{ number_format((float) ($order['finalAmount'] ?? 0), 2) }}</b>
                            </td>
                        </tr>

                        <tr class="removeborder">
                            <td colspan="8" class="text-right"
                                style="vertical-align:middle;text-align:right;font-style:italic;border-bottom:transparent;text-transform:uppercase;">
                                <strong>{{ $words }} Only</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- FOOTER --}}
                <table class="horizontal-border border">
                    <tr>
                        <td colspan="3" class="bgblue bgspecial"
                            style="vertical-align:middle;text-align:center;font-style:italic">
                            <strong>THANK YOU FOR YOUR BUSINESS!</strong>
                        </td>
                    </tr>
                </table>

                <div class="mt-1" style="font-size:12px;" id="footer">
                    <span class="float-left">
                        <small>This is system generated PDF. Signature is not required.</small>
                    </span>
                    <span class="float-right">
                        <small>{{ date('d-M-Y, h:i A') }}</small>
                    </span>
                </div>

            </div>
        </div>
    </main>
</body>

</html>
