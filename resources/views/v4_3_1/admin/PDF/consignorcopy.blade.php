@php
    $consignorcopy = $data['consignorcopy'];
    $companydetails = $data['companydetails'];
    $tandc = $data['t_and_c'];
    $consignor = $data['consignor'];
    $consignee = $data['consignee'];
    $othersettings = $data['othersettings'];
    $copies = $data['copies'];
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{ ucfirst($companydetails['name']) }} - Consignor Copy</title>
    <!-- Favicon -->
    <link rel="stylesheet" href="{{ public_path('admin/css/bootstrap.min.css')}}">

    <style>
        @page {
            margin: 15px;
            margin-top: 8px;
        }

        table {
            margin: 0 !important;
            padding: 0 !important;
        }

        p {
            margin: 0 !important;
            padding: 0 !important;
        }

        .text-center {
            text-align: center !important;
        }

        table th {
            text-align: center;
        }

        .vertical-align-center {
            vertical-align: center !important;
        }

        table {
            font-size: 12px;
        }

        h2 {
            font-size: 18px;
            margin: 0;
        }

        .content img {
            max-width: 300px !important;
            max-height: 150px;
        }

        td,
        th {
            vertical-align: top !important;
            padding: 0.5px !important;
            margin: 0 !important;
            font-size: 12px;
        }

        b {
            margin: 0px !important;
            padding: 2px !important;
            font-size: 12px !important;
        }

        .d-flex {
            display: flex !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .vertical-align-center {
            vertical-align: middle !important;
        }

        .ml-5 {
            margin-left: 1rem !important;
        }

        .mb-2,
        .mt-5 {
            margin: 0 0 !important;
        }

        span {
            margin: 0px !important;
            padding: 0px !important;
        }

        /* ✅ Repeating watermark: fixed position */
        .watermark {
            position: fixed;
            top: 35%;
            left: 15%;
            width: 70%;
            text-align: center;
            opacity: 0.15 !important;
            z-index: 0;
            font-size: 80px;
            color: #000;
        }

        /* ✅ For image watermark, just change this class */
        .watermark img {
            /* max-width: 400px; */
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .pdf-header { 
            text-align: right;
            font-size: 12px;
            text-transform: capitalize;
            z-index: 10; 
        }
    </style>
</head>

<body>
    
    {{--  Image Watermark --}}
    <div class="watermark">
        @if ($companydetails['watermark_img'] != '')
            <img src="{{ public_path('uploads/' . $companydetails['watermark_img']) }}" alt="WaterMark">
        @endif
    </div>
    @foreach ($copies as $copy)
     @php 
        $god = json_decode($companydetails['god_names'], true)
     @endphp
     @if($othersettings['god_name_show/hide'])
           <table cellspacing="0" cellpadding="0" width="100%" style="font-size:12px;">
            <tr>
                <td width="33%" style="text-align:left; vertical-align:middle;">
                    <span  style="font-family: 'Noto Sans Gujarati';">
                     {{ $god[0] ?? '' }}
                    </span>
                    
                    {{-- @if(isset($god[0]))
                        @php $safeName = preg_replace('/[^\p{L}\p{N}]+/u', '_', $god[0]); @endphp
                        <img src="{{ public_path('uploads/' . $companydetails['company_id']) . '/' . $safeName . '.SVG' }}"
                            alt="Company logo"
                            style="max-width:300px; max-height:150px;">
                    @endif --}}
                </td>

            <td width="33%" style="text-align:center; vertical-align:middle;">
                  <span  style="font-family: 'Noto Sans Gujarati';">
                     {{ $god[1] ?? '' }}
                    </span>
                    {{-- @if(isset($god[1]))
                        @php $safeName = preg_replace('/[^\p{L}\p{N}]+/u', '_', $god[1]); @endphp
                        <img src="{{ public_path('uploads/' . $companydetails['company_id']) . '/' . $safeName . '.SVG' }}"
                            alt="Company logo"
                            style="max-width:300px; max-height:150px;">
                    @endif --}}
                </td>

                <td width="33%" style="text-align:right; vertical-align:middle;">
                       <span  style="font-family: 'Noto Sans Gujarati';">
                    {{ $god[2] ?? '' }}
                    </span>
                    {{-- @if(isset($god[2]))
                        @php $safeName = preg_replace('/[^\p{L}\p{N}]+/u', '_', $god[2]); @endphp
                        <img src="{{ public_path('uploads/' . $companydetails['company_id']) . '/' . $safeName . '.SVG' }}"
                            alt="Company logo"
                            style="max-width:300px; max-height:150px;">
                    @endif --}}
                </td>
            </tr>
        </table>

            @endif
        <div class="pdf-header" @style(['page-break-before:always' => $loop->iteration > 1])>
            {{$copy}} copy
        </div>

        <main>
            <div class="content">
                <table class="w-100 table">
                    <tr>
                        <td class="vertical-align-center">
                            <div class="text-center vertical-align-center" style="height: auto">
                                @if ($companydetails['img'] != '')
                                    <img src="{{ public_path('uploads/' . $companydetails['img']) }}"
                                        alt="Company logo">
                                @endif
                            </div>
                        </td>
                        <td class="vertical-align-center">
                            <h2>
                                {{ ucfirst($companydetails['name']) }}
                            </h2>
                            <p>
                                {{ $companydetails['house_no_building_name'] }},
                                {{ $companydetails['road_name_area_colony'] }}
                            </p>
                            <p>
                                {{ $companydetails['city_name'] }},
                                {{ $companydetails['pincode'] }},
                                {{ $companydetails['state_name'] }},
                                {{ $companydetails['country_name'] }}
                            </p>
                            @if ($companydetails['email'])
                                <p>
                                    <b>Email :</b> {{ $companydetails['email'] }}
                                </p>
                            @endif
                            @if ($companydetails['contact_no'])
                                <p>
                                    <b>Contact :</b> {{ $companydetails['contact_no'] }} @if($companydetails['alternative_number'] != null && $companydetails['alternative_number'] != '') / {{ $companydetails['alternative_number'] }} @endif
                                </p>
                            @endif
                            @isset($companydetails['transporter_id'])
                                <p>
                                    <b>Transporter ID:</b> {{ $companydetails['transporter_id'] }}
                                </p>
                            @endisset
                        </td>
                    </tr>
                </table>

                <table class="w-100 table">
                    <tr>
                        <td>
                            <b>Consignment Note No. : </b><span>{{ $consignorcopy['consignment_note_no'] }}</span>
                        </td>
                        <td>
                            <b>Loading Date : </b><span>{{ $consignorcopy['loading_date_formatted'] }}</span>
                        </td>
                        <td>
                            <b>Stuffing Date : </b><span>{{ $consignorcopy['stuffing_date_formatted'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Truck No. : </b><span>{{ $consignorcopy['truck_number'] }}</span>
                        </td>
                        <td>
                            <b>Driver Name : </b><span>{{ $consignorcopy['driver_name'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Licence No. : </b><span>{{ $consignorcopy['licence_number'] }}</span>
                        </td>
                        <td>
                            <b>Mobile No. : </b><span>{{ $consignorcopy['mobile_number'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>From : </b><span>{{ $consignorcopy['from'] }}</span>
                        </td>
                        <td>
                            <b>To : </b><span>{{ $consignorcopy['to'] }}</span>
                        </td>
                        <td colspan="2">
                            <b>To : </b><span>{{ $consignorcopy['to_2'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <b>GST Tax Payable By : </b><span>{{ strtoupper($consignorcopy['gst_tax_payable_by']) }}</span>
                        </td>
                    </tr>
                </table>

                <table class="table table-bordered">
                    <tr>
                        <td style="width: 50%">
                            <b>Consignor : </b>
                            <span>{{ $consignorcopy['consignor'] }}</span>
                            <p class="ml-5">{{ $consignor['consignor_address'] }}</p>
                            <b>GSTIN : </b><span>{{ $consignor['gst_no'] }}</span><br>
                            <b>PANNO. : </b><span>{{ $consignor['pan_number'] }}</span>
                        </td>
                        <td style="width:50%;">
                            <b>Consignee : </b>
                            <span>{{ $consignorcopy['consignee'] }}</span>
                            <p class="ml-5">{{ $consignee['consignee_address'] }}</p>
                            <b>GSTIN : </b><span>{{ $consignee['gst_no'] }}</span><br>
                            <b>PANNO. : </b><span>{{ $consignee['pan_number'] }}</span>
                            <hr style="margin: 0;padding:0;width:100%">
                            <b>CHA : </b><span>{{ $consignorcopy['cha'] }}</span>
                        </td>
                    </tr>
                </table>

                <div class="page-break"></div>

                <table class="table w-100 table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle">No.of Pallets</th>
                            <th rowspan="2" style="vertical-align: middle">NATURE OF GOODS (Said to contain)
                            </th>
                            <th colspan="2">
                                WEIGHT
                                @if ($consignorcopy['weight_type'])
                                    <span>({{ $consignorcopy['weight_type'] }})</span>
                                @endif
                            </th>
                            <th colspan="2">FREIGHT</th>
                        </tr>
                        <tr>
                            <th>ACTUAL</th>
                            <th>CHARGED</th>
                            <th>PAID</th>
                            <th>TO PAY</th>
                        </tr>
                        <tr>
                            <td rowspan="3">-</td>
                            <td rowspan="2" class="text-left">
                                <div class="mb-2 ">
                                    <b>Type : </b><span> {{ strtoupper($consignorcopy['type']) }}</span>
                                </div>
                                <div>
                                    <b>Container No. : </b><span>{{ $consignorcopy['container_no'] }}</span>

                                    <b>Size : </b><span>{{ $consignorcopy['size'] }}</span>

                                </div>
                                <div class="mb-2">
                                    <b>Shipping Line : </b><span>{{ $consignorcopy['shipping_line'] }}</span>
                                </div>
                                <div class="mb-2">
                                    <b>Seal No : </b><span>{{ $consignorcopy['seal_no'] }}</span>
                                </div>
                                <div class="mb-2">
                                    <b>BE / INV NO. : </b><span>{{ $consignorcopy['be_inv_no'] }}</span>
                                </div>
                                <div class="mb-2">
                                    <b>PORT : </b><span>{{ $consignorcopy['port'] }}</span>
                                </div>
                                <div class="mb-2">
                                    <b>POD : </b><span>{{ $consignorcopy['pod'] }}</span>
                                </div>
                                <div class="mb-2">

                                    <b>Service : </b><span>{{ strtoupper($consignorcopy['service']) }}</span>

                                    <b>SAC CODE : </b><span>{{ $consignorcopy['sac_code'] }}</span>

                                </div>
                            </td>
                            <td>
                                <span>{{ $consignorcopy['actual'] }}</span>
                            </td>
                            <td>
                                <span>{{ $consignorcopy['charged'] }}</span>
                            </td>
                            <td rowspan="2" class="text-center">
                                <div>
                                    <span>{{ $consignorcopy['paid'] }}</span>
                                </div>
                                <div class="mt-5">
                                    <span>To</span><br>
                                    <span>Be</span><br>
                                    <span>Billed</span><br>
                                    <span>At</span><br>
                                    <span>{{ $companydetails['city_name'] }}</span><br>
                                </div>
                            </td>
                            <td rowspan="2">
                                <div>
                                    <span>{{ $consignorcopy['to_pay'] }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>Value (RS). </b><span>{{ $consignorcopy['value'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <p>GOODS BOOKED AS OWNER'S RISK.</p>
                                <span>Not responsible for Breakage, Leakage, Damage Goods or Fires.</span>
                            </td>
                            <td colspan="2" rowspan="2" class="text-center">
                                <p><b>For,{{ ucfirst($companydetails['name']) }}</b> </p>
                                @if ($othersettings['authorized_signatory'] == 'company_signature' && isset($companydetails['pr_sign_img']))
                                    <img src="{{ public_path('uploads/' . $companydetails['pr_sign_img']) }}"
                                        alt="Company logo" style="max-height:100px;">
                                @else
                                    <br><br><br>
                                @endif
                                <p>Authorized Signatory</p>
                            </td>
                        </tr>
                        <tr class="text-left">
                            <td colspan="4">
                                <div class="align-items-baseline d-flex justify-content-between mb-2">
                                    <span>Reached At Factory :</span>
                                    <b>Date : </b>
                                    @if ($consignorcopy['reached_at_factory_date_formatted'])
                                        <span>{{ $consignorcopy['reached_at_factory_date_formatted'] }}</span>
                                    @else
                                        <span>______________</span>
                                    @endif  
                                    <b>Time : </b>
                                    @if ($consignorcopy['reached_at_factory_time_formatted'])
                                        <span>{{ $consignorcopy['reached_at_factory_time_formatted'] }}</span>
                                    @else
                                        <span>______________</span>
                                    @endif 
                                </div>
                                <div class="align-items-baseline d-flex justify-content-between mb-2">
                                    <span>Left From Factory :</span>
                                    <b>Date : </b>
                                    @if ($consignorcopy['left_from_factory_date_formatted'])
                                        <span>{{ $consignorcopy['left_from_factory_date_formatted'] }}</span>
                                    @else
                                        <span>______________</span>
                                    @endif  

                                    <b>Time : </b>
                                    @if ($consignorcopy['left_from_factory_time_formatted'])
                                        <span>{{ $consignorcopy['left_from_factory_time_formatted'] }}</span>
                                    @else
                                        <span>______________</span>
                                    @endif 
                                </div>
                            </td>
                        </tr>
                    </thead>
                </table>
                <table class="w-100 table table-bordered">
                    <tr>
                        <td>
                            <b>OUR COMPANY'S PAN NO : <span>{{ $companydetails['pan_number'] }}<span> </b>
                            <br>
                            @if ($companydetails['gst_no'])
                                <b>OUR COMPANY'S GST NO : <span>{{ $companydetails['gst_no'] }}<span> </b>
                                <br>
                            @endif
                            <b>STATE : <span>{{ $companydetails['state_name'] }}<span>,
                                        STATE CODE : <span>{{ $companydetails['state_code'] }}<span> </b>
                        </td>
                    </tr>
                    @isset($tandc)
                        <tr>
                            <td class="p-0">
                                <p class="text-center bg-light m-0"><b>Terms & Conditions</b></p>
                                <div class="m-2" style="font-size: 10px"> {!! $tandc[0] !!}</div>
                            </td>
                        </tr>
                    @endisset
                </table>
            </div>
        </main>
    @endforeach
</body>

</html>
