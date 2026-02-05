@php
    //   dd($data);
    $invdata = $data['invoice'];
    // dd($invdata);
    // $words = Number::spell($invdata['grand_total']); // convert total amount to words

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
    if ($invdata['grandTotalAmount'] > $total) {
        $value = $invdata['grandTotalAmount'] - $total;
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $roundof = '+' . $roundof;
        }
    } else {
        $value = $total - $invdata['grandTotalAmount'];
        $roundof = number_format((float) $value, 2, '.', '');
        if ($roundof != 0) {
            $roundof = '-' . $roundof;
        }
    }

    // $othersettings = json_decode($othersettings['gstsettings'], true);

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

    $companydetails = $data['mainCompanyData'];
    $payment = $data['paymentdetail'];
    function numberToWords($number)
    {
        $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
        return 'Rupees ' . ucfirst($formatter->format($number));
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Payment Reciept</title>
    <link rel="stylesheet" href="{{ public_path('admin/css/bootstrap.min.css') }}">
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
    {{-- {{ dd( $data['gardenCompanyData']) }} --}}
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
                   
                        <span>GSTIN No: @isset($companydetails['gst_no'])
                                {{ $companydetails['gst_no'] }}
                            @endisset
                        </span><br>
                   
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
                    @if (isset($data['gardenCompanyData']['company_name']))
                        <span class="default" style="display:block;">
                            @isset($data['gardenCompanyData']['company_name'])
                                {{ $data['gardenCompanyData']['company_name'] }}
                            @endisset
                        </span>
                    @endif
                    {{-- @if (isset($invdata['company_name']))
                        <span class="default" style="display:block;">
                            {{ $invdata['company_name'] }}
                        </span>
                    @endif --}}
                    @isset($data['gardenCompanyData']['address'])
                        <span class="default" style="display:block;">{!! nl2br(e(wordwrap($data['gardenCompanyData']['address'], 40, "\n", true))) !!}</span>
                    @endisset

                    <span class="default" style="display:block;">
                        @isset($data['gardenCompanyData']['city_name'])
                            {{ $data['gardenCompanyData']['city_name'] }},
                        @endisset
                        @isset($data['gardenCompanyData']['state_name'])
                            {{ $data['gardenCompanyData']['state_name'] }},
                        @endisset
                        @isset($data['gardenCompanyData']['pincode'])
                            {{ $data['gardenCompanyData']['pincode'] }}
                        @endisset
                    </span>
                    <span class="default"style="display:block;">{{ $data['gardenCompanyData']['email'] }}</span>
                    <span class="default"> {{ $data['gardenCompanyData']['mobile_1'] }} @if ($data['gardenCompanyData']['mobile_2'] != null && $data['gardenCompanyData']['mobile_2'] != '')
                            / {{ $data['gardenCompanyData']['mobile_2'] }}
                        @endif
                    </span>
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
                            <td style="text-align: right;">{{ $invdata['invoice_no'] }}</td>
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
            <table style="table-layout:fixed;" id="data" cellspacing=0 cellpadding=0 class="w-100" width="100">
                <tbody>
                    <tr>
                        <td id="data" colspan="3">
                            <table id="data" cellspacing=0 cellpadding=0 class="horizontal-border" width="100">
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
            <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class="horizontal-border border data"
                width="100%">
                <thead>
                    <tr class="bgblue">
                        <th style="width:4%;text-align:center;">ID</th>
                        <th style="width:4%;text-align:center;">Buyer</th>
                        <th style="width:4%;text-align:center;">Inv No</th>
                        <th style="width:4%;text-align:center;">Inv Date</th>
                        <th style="width:4%;text-align:center;">Pkgs</th>
                        <th style="width:4%;text-align:center;">Kgs</th>
                        <th style="width:4%;text-align:center;">Amount</th>
                        <th style="width:4%;text-align:center;">Comm</th>
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
                    $paid_amount = $payment[0]['paid_amount'];
                    $tds_amount = $payment[0]['tds_amount'];
                    $paid_amounts = $payment[0]['amount'] - $payment[0]['pending_amount'];
                    $pending_amount = $payment[0]['pending_amount'];
                    $totalamount  = $invdata['totalamount'];

                    $roundedTotal = round($grandTotal);
                    $roundOff = $roundedTotal - $grandTotal;
                @endphp

                <tbody>

                    @forelse ($usedInvoices as $key => $row)
                        <tr>
                            <td style="text-align:center;">{{ $key + 1 }}</td>
                            <td style="text-align:center;">{{ $row->buyer_name ?? '-' }}</td>
                            <td style="text-align:center;">{{ $row->inv_no ?? '-' }}</td>
                            <td style="text-align:center;">{{ $row->inv_date ?? '-' }}</td>
                            <td style="text-align:center;">{{ $row->bags ?? 0 }}</td>
                            <td style="text-align:center;">{{ number_format($row->net_kg ?? 0, 2) }}</td>
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
                </tbody>
            </table>
            <table style="table-layout:fixed;" cellspacing=0 cellpadding=0 class=" data"
                width="100%">
                <tbody>
                    <tr style="font-size:15px;text-align: right">
                        <td colspan="10" class="text-right left removeborder">
                            Subtotal
                        </td>
                        <td style="width:15%;" class="right removeborder currencysymbol text-right" id="subtotal">
                           ₹{{ number_format($totalamount, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right">
                        <td colspan="10" style="text-align: right" class="left removeborder ">
                            IGST(18%)
                        </td>
                        <td style="text-align: right ;width:15%;" class="currencysymbol" id="sgst">
                            ₹{{ number_format($igst, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right">
                        <td colspan="10" style="text-align: right" class="left removeborder ">
                            SGST(0%)
                        </td>
                        <td style="text-align: right ;width:15%;" class="currencysymbol" id="sgst">
                            ₹{{ number_format(0, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right">
                        <td colspan="10" style="text-align: right" class="left removeborder ">
                            CGST(0%)
                        </td>
                        <td style="text-align: right;width: 20%;" class=" currencysymbol" id="cgst">
                            ₹{{ number_format(0, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right">
                        <td colspan="10" class="text-right left removeborder">
                            Round of
                        </td>
                        <td style="width: 20%;" class="right currencysymbol text-right">
                            ₹ {{ number_format($roundOff, 2) }}
                        </td>
                    </tr>

                    <tr style="font-size:15px;text-align: right">
                        <td colspan="10" class="text-right left removeborder">
                            <b>Total</b>
                        </td>
                        <td style="width: 20%;" class="right currencysymbol text-right">
                            ₹{{ number_format($grandTotal, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right;">
                        <td colspan="10" class="text-right left removeborder">
                            <b>Amount Received</b>
                        </td>
                        <td style="width: 20%;" class="right currencysymbol text-right">
                            ₹{{ number_format($paid_amount, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right;">
                        <td colspan="10" class="text-right left removeborder">
                            <b>TDS Amount</b>
                        </td>
                        <td style="width: 20%;" class="right currencysymbol text-right">

                            ₹{{ number_format($tds_amount, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right;">
                        <td colspan="10" class="text-right left removeborder">
                            <b>Paid Amount</b>
                        </td>
                        <td style="width: 20%;" class="right currencysymbol text-right">

                            ₹{{ number_format($paid_amounts, 2) }}
                        </td>
                    </tr>
                    <tr style="font-size:15px;text-align: right;">
                        <td colspan="10" class="text-right left removeborder">
                            <b>Pending Amount</b>
                        </td>
                        <td style="width: 20%;" class="right currencysymbol text-right">
                            <b>
                                ₹{{ number_format($pending_amount, 2) }}</b>
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
