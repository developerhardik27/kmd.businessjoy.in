@php
    // echo  (count($products[0])-2);
    // echo "<pre>";
    // print_r($productscolumn);
    // print_r($products);
    // print_r($invdata);
    // print_r($companydetails);
    // print_r($bankdetails);
    // die();
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

        #data tbody tr:last-child td {
            border-bottom: none;
        }

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
            border: solid;
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
    </style>
</head>

<body>
    <div class="card-body table-wrapper">
        <table width="100%" class="horizontal-border">
            <tbody class="border">
                <tr class="firstrow" valign='bottom' style="padding: 5px;">
                    <td rowspan="" colspan="2" style="padding:px;"><span class=" textblue firstrow"
                            style="display:block;" id="cname">{{ $companydetails['name'] }}</span>
                        <span style="display:block;">{{ $companydetails['address'] }}</span>
                        <span style="display:block;">{{ $companydetails['city_name'] }},
                            {{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}</span>
                        <span style="display:block;">Email: {{ $companydetails['email'] }}</span>
                        <span><b>GSTIN No: {{ $companydetails['gst_no'] }}</b></span>
                    </td>
                    <td colspan="2" class="" style="text-align: center">
                        <img @if ($companydetails['img'] != '') src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}"
                        @else
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('admin/images/bjlogo2.png'))) }}" @endif
                            class="rounded mt-auto mx-auto d-block" alt="logo" height="100px">
                    </td>
                </tr>
                <tr valign='top' class="firstrow">
                    <td style="padding:;" colspan="2">
                        <span style="display:block;">Contact No: {{ $companydetails['contact_no'] }}</span>
                        <span><b>GSTIN No: {{ $companydetails['gst_no'] }}</b></span>
                    </td>
                    <td colspan="2" style="padding: 0;">
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
                                    <td>{{ $invdata['inv_date'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;" colspan="2">
                        <div class="bgblue" style="margin-right: 100px;">BILL TO </div>
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
                    <td rowspan="" valign='top' colspan="2">
                        <div>{{ $invdata['address'] }}</div>
                        <div>{{ $invdata['city_name'] }},{{ $invdata['state_name'] }},{{ $invdata['pincode'] }}
                        </div>
                        <div>{{ $invdata['email'] }}</div>
                        <div>{{ $invdata['contact_no'] }}</div>
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
                                    <th>#</th>
                                    @forelse ($productscolumn as $val)
                                        @php
                                            $columnname = strtoupper(str_replace('_', ' ', $val));
                                        @endphp
                                        <th>{{ $columnname }}</th>
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
                                        <td style="text-align: center"> {{ $srno }}</td>
                                        @foreach ($row as $val)
                                            <td style="text-align: center">{{ $val }}</td>
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
                                    <td colspan="@php echo (count($products[0])-2); @endphp" style="text-align: right"
                                        class="left removeborder  ">
                                        Subtotal
                                    </td>
                                    <td class="removeborder"></td>
                                    <td class="removeborder"></td>
                                    <td style="text-align: center" class="right removeborder " id="subtotal">
                                        {{ $invdata['total'] }}.00</td>
                                </tr>
                                <tr class=" ">
                                    <td colspan="@php echo (count($products[0])-2); @endphp" style="text-align: right"
                                        class="left removeborder ">
                                        GST(18%)
                                    </td>
                                    <td class="removeborder"></td>
                                    <td class="removeborder"></td>
                                    <td style="text-align: center" class=" " id="gst">
                                        {{ $invdata['gst'] }}.00</td>
                                </tr>
                                <tr class="" style="font-size:15px;text-align: right">
                                    <td colspan="@php echo (count($products[0])-2); @endphp"
                                        class="left removeborder"><b>Total</b></td>
                                    <td class="removeborder "></td>
                                    <td class="removeborder"></td>
                                    <td style="text-align: center" class="right   ">
                                        {{ $invdata['grand_total'] }}.00
                                    </td>
                                </tr>
                                <tr class="removeborder">
                                    <td colspan="@php echo (count($products[0])+1); @endphp" class="bgblue  bgspecial"
                                        style="vertical-align: middle; text-align: center;font-style:italic">
                                        <strong class="">Thank You For Your business!</strong>
                                    </td>
                                </tr>
                                <tr class="removeborder">
                                    <td colspan="@php echo (count($products[0])+1); @endphp"
                                        style="vertical-align: middle; text-align:left;white-space: pre-line;">
                                        Terms And Condtions :- </br> {{ $invdata['t_and_c'] }}
                                    </td>
                                </tr>
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
            </tbody>
        </table>
    </div>
</body>

</html>
