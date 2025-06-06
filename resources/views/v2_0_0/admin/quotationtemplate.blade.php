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

    $othersettings = json_decode($othersettings['gstsettings'], true);

    $loopnumber = []; // array for alignment column type text or longtext
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Quotation PDf</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }} " />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
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
            page-break-inside: avoid;
            table-layout: auto;
            /* Prevent table from breaking across pages */
        }

        #data td,
        th {
            white-space: normal;
            word-wrap: break-word; 
        }

        #data td{
            line-break: anywhere !important;
        }

        td{
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
                <tr>
                    <td style="text-align: center;text-transform:uppercase;" class="bgblue">
                       <b>Quoatation</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <div style="display: inline-block;margin-top:20px;">
                            @if ($companydetails['img'] != '')
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}"
                                    class="rounded mt-auto mx-auto d-block" alt="Company logo" style="max-width: 150px">
                            @endif
                        </div>
                        <p id="cname" class="textblue">{{ $companydetails['name'] }}</p>
                    </td>
                </tr>
                <tr class="textblue">
                    <td style="text-align: center">
                        <span><b>Address:</b></span> 
                        {{ $companydetails['house_no_building_name'] }} , {{ $companydetails['road_name_area_colony'] }} , {{ $companydetails['city_name'] }},{{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}
                    </td>
                </tr>
                <tr class="textblue">
                    <td style="text-align: center;padding-bottom:50px;">
                        <span><b>Contact:</b></span>  {{ $companydetails['contact_no'] }} |  {{ $companydetails['email'] }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:0;vertical-align:top"> 
                        <table width="100%" style="table-layout: fixed">
                            <tr>
                                <td class="font-weight-bold bgblue">
                                 To :  @isset($quotationdata['firstname'])
                                            {{ $quotationdata['firstname'] }}
                                        @endisset
                                        @isset($quotationdata['lastname'])
                                            {{ $quotationdata['lastname'] }}
                                        @endisset
                                </td>
                                <td class="font-weight-bold bgblue">
                                    Date : {{ \Carbon\Carbon::parse($quotationdata['quotation_date_formatted'])->format('d-m-Y') }}
                                </td>
                            </tr>   
                            <tr>
                                <td class="font-weight-bold bgblue">
                                    Contact : {{ $quotationdata['contact_no'] }}
                                </td>
                                <td class="font-weight-bold bgblue">
                                    Valid till: {{ \Carbon\Carbon::parse($quotationdata['quotation_date_formatted'])->addDays($quotationdata['overdue_date'])->format('d-m-Y') }}
                                </td>
                            </tr> 
                            @isset($quotationdata['email']) 
                                <tr>
                                    <td colspan="2" class="font-weight-bold bgblue">
                                        Email : {{$quotationdata['email']}}
                                    </td>
                                </tr>
                            @endisset 
                            <tr>
                                <td colspan="2" class="font-weight-bold bgblue">
                                    Address: 
                                    @isset($quotationdata['house_no_building_name'])
                                            {{ $quotationdata['house_no_building_name'] }} , 
                                        @endif

                                        @isset($quotationdata['road_name_area_colony'])
                                            {{ $quotationdata['road_name_area_colony'] }} ,
                                        @endif

                                        @isset($quotationdata['city_name'])
                                          {{ $quotationdata['city_name'] }}
                                        @endif

                                        @isset($quotationdata['state_name'])
                                          , {{ $quotationdata['state_name'] }}
                                        @endif

                                        @isset($quotationdata['pincode'])
                                            , {{ $quotationdata['pincode'] }}
                                        @endif
                                </td>
                            </tr> 
                        </table>
                    </td> 
                </tr>

                <tr>
                    <td id="data" style="padding:0;">
                        <table border="border" style="table-layout:fixed;" cellspacing=0 cellpadding=0 width="100%">
                            <tr>
                                <th><span style="padding-left: 5px;width:4%;"> # </span></th>
                                @forelse ($productscolumn as $column)
                                    @php
                                        $columnname = strtoupper(str_replace('_', ' ', $column['column_name']));
                                    @endphp

                                    @if ($column['column_type'] == 'longtext')
                                        @php
                                            $loopnumber[] = $loop->iteration;
                                        @endphp
                                    @endif
                                    <th
                                        style="text-align: center;width: {{ $column['column_width'] != 'auto' ? $column['column_width'] . '% !important' : 'auto' }};">
                                        {{ $columnname }}
                                    </th>
                                @empty
                                    <th>-</th>
                                @endforelse
                            </tr>
                            {{-- product data  --}}
                            @php $srno = 0 ; @endphp
                            @foreach ($products as $row)
                                @php $srno++ ; @endphp
                                <tr>
                                    <td style="text-align: center;width:4%">{{ $srno }}</td>
                                    @foreach ($row as $key => $val)
                                        @if ($loop->last)
                                            <td style="text-align:right;" class="currencysymbol"> 
                                                {{ Number::currency($val, in: $quotationdata['currency']) }}
                                            </td>
                                        @elseif (in_array($loop->iteration, $loopnumber))
                                            @php
                                                $textAlign =
                                                    strpos($val, "\n") !== false || strpos($val, '<br>') !== false
                                                        ? 'left'
                                                        : 'center';
                                            @endphp

                                            <td style="text-align:{{ $textAlign }};">
                                                {!! nl2br(e($val)) !!}
                                            </td>
                                        @else
                                            <td style="text-align:center;">
                                                {!! nl2br(e($val)) !!}
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
                            {{-- end product data  --}}
                            <tr>
                                <td colspan="@php echo (count($products[0])); @endphp"
                                    class="text-right left">
                                    Subtotal
                                </td>
                                <td class="right currencysymbol text-right" id="subtotal">
                                    {{ Number::currency($quotationdata['total'], in: $quotationdata['currency']) }}
                                </td>
                            </tr>
                            @if ($othersettings['gst'] == 0)
                                @if ($quotationdata['sgst'] >= 1)
                                    <tr>
                                        <td colspan="@php echo (count($products[0])); @endphp" style="text-align: right"
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
                                        <td colspan="@php echo (count($products[0])); @endphp" style="text-align: right"
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
                                        <td colspan="@php echo (count($products[0])); @endphp"
                                            style="text-align: right" class="left ">
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
                                    <td colspan="@php echo (count($products[0])); @endphp"
                                        class="text-right left">
                                        Round of
                                    </td>
                                    <td class="right currencysymbol text-right">
                                        {{ $sign }} {{ Number::currency($roundof, in: $quotationdata['currency']) }}
                                    </td>
                                </tr>
                            @endunless
                            <tr style="font-size:15px;text-align: right">
                                <td colspan="@php echo (count($products[0])); @endphp"
                                    class="text-right left">
                                    <b>Total</b>
                                </td>
                                <td class="right currencysymbol text-right">
                                    {{ Number::currency($quotationdata['grand_total'], in: $quotationdata['currency']) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="@php echo (count($products[0])+1); @endphp" class="text-right"
                                    style="vertical-align: middle; text-align: right;font-style:italic;text-transform:uppercase;">
                                    <strong>{{ $quotationdata['currency'] }} {{ $words }} Only</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>  

                <tr class="bgblue">
                    <td style="vertical-align: top;">
                        @isset($quotationdata['notes'])
                            <span style="margin-top: 0"><b>Notes :- </b></span><br>
                            <div>{!! nl2br(e($quotationdata['notes'])) !!} </div>
                        @endisset
                        @isset($quotationdata['t_and_c'])
                            <span style="margin-top: 0"><b>Terms And Condtions :- </b></span>
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
