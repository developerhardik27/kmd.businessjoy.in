@php
    $consignorcopy = $data['consignorcopy'];
    $companydetails = $data['companydetails'];
    $tandc = $data['t_and_c'];
    $consignor = $data['consignor'];
    $consignee = $data['consignee']; 
    $othersettings = $data['othersettings']; 
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - Consignor Copy</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }} " />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <style>
        table {
            margin: 0 !important;
            padding: 0 !important;
        }

        p {
            margin: 0;
            padding: 0;
        }
 
        .text-center{
            text-align: center !important;
        }

        table th {
            text-align: center;
        }

        .vertical-align-center{
            vertical-align: center !important;
        }
 

         /* Make tables scrollable on small screens */
    @media only screen and (max-width: 768px) {
 
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            font-size: 14px;
        }

        h2 {
            font-size: 18px;
        }

        img {
            max-width: 100px !important;
        }

        td, th {
            padding: 4px !important;
        }

         
        .d-flex {
            display: block !important;
        }

        .justify-content-between{
            justify-content: space-between !important;
        }

        .vertical-align-center{
            vertical-align: center !important;
        }

        .ml-5 {
            margin-left: 1rem !important;
        }

        .mb-2, .mt-5 {
            margin: 0.5rem 0 !important;
        }
    }

    </style>
</head>

<body>
    <main>
        <div class=" table-wrapper">

            <table class="w-100 table">
                <tr>
                    <td>
                        <div class="text-center">
                            @if ($companydetails['img'] != '')
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}"
                                    alt="Company logo" style="max-width: 150px">
                            @endif
                        </div>
                    </td>
                    <td class="vertical-align-center">
                        <h2>
                            {{ ucfirst($companydetails['name']) }}
                        </h2>
                        <p>
                            {{ $companydetails['house_no_building_name'] }} ,
                            {{ $companydetails['road_name_area_colony'] }}
                        </p>
                        <p>
                            {{ $companydetails['pincode'] }} ,
                            {{ $companydetails['city_name'] }} ,
                            {{ $companydetails['state_name'] }} ,
                            {{ $companydetails['country_name'] }}
                        </p>
                        @if ($companydetails['email'])
                            <p>
                                <b>Email :</b> {{ $companydetails['email'] }}
                            </p>
                        @endif 
                    </td>
                </tr>
            </table>

            <table class="w-100 table">
                <tr>
                    <td>
                        <b>Consignment Note No. : </b> <span>{{ $consignorcopy['consignment_note_no'] }}</span>
                    </td>
                    <td>
                        <b>Loading Date : </b> <span>{{ $consignorcopy['loading_date_formatted'] }}</span>
                    </td>
                    <td>
                        <b>Stuffing Date : </b> <span>{{ $consignorcopy['stuffing_date_formatted'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>Truck No. : </b> <span>{{ $consignorcopy['truck_number'] }}</span>
                    </td>
                    <td>
                        <b>Driver Name : </b> <span>{{ $consignorcopy['driver_name'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>Licence No. : </b> <span>{{ $consignorcopy['licence_number'] }}</span>
                    </td>
                    <td>
                        <b>Mobile No. : </b> <span>{{ $consignorcopy['mobile_number'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>From : </b> <span>{{ $consignorcopy['from'] }}</span>
                    </td>
                    <td>
                        <b>To : </b> <span>{{ $consignorcopy['to'] }}</span>
                    </td>
                    <td colspan="2">
                        <b>To : </b> <span>{{ $consignorcopy['to_2'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <b>GST Tax Payable By : </b> <span>{{ $consignorcopy['gst_tax_payable_by'] }}</span>
                    </td>
                </tr>
            </table>

            <table class="table table-bordered">
                <tr>
                    <td rowspan="2">
                        <b>Consignor : </b> 
                        <span>{{ $consignorcopy['consignor'] }}</span>
                        <p class="ml-5">{{ $consignor['consignor_address'] }}</p>
                        <b>GSTIN : </b> <span>{{ $consignor['gst_no'] }}</span><br>
                        <b>PANNO. : </b> <span>{{ $consignor['pan_number'] }}</span>
                    </td>
                    <td>
                        <b>Consignee : </b> 
                        <span>{{ $consignorcopy['consignee'] }}</span>
                        <p class="ml-5">{{ $consignee['consignee_address'] }}</p>
                        <b>GSTIN : </b> <span>{{ $consignee['gst_no'] }}</span><br>
                        <b>PANNO. : </b> <span>{{ $consignee['pan_number'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>CHA : </b> <span>{{ $consignorcopy['cha'] }}</span>
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
                                <b>Type : </b>
                                <span> {{ $consignorcopy['type'] }}</span>
                            </div>
                            <div>
                                <b>Container No. : </b>
                                <span>{{ $consignorcopy['container_no'] }}</span>
                                 
                                <b>Size : </b>
                                <span>{{ $consignorcopy['size'] }}</span>

                            </div>
                            <div class="mb-2">
                                <b>Shipping Line : </b>
                                <span>{{ $consignorcopy['shipping_line'] }}</span>
                            </div>
                            <div class="mb-2">
                                <b>Seal No : </b>
                                <span>{{ $consignorcopy['seal_no'] }}</span>
                            </div>
                            <div class="mb-2">
                                <b>BE / INV NO. : </b>
                                <span>{{ $consignorcopy['be_inv_no'] }}</span>
                            </div>
                            <div class="mb-2">
                                <b>PORT : </b>
                                <span>{{ $consignorcopy['port'] }}</span>
                            </div>
                            <div class="mb-2">
                                <b>POD : </b>
                                <span>{{ $consignorcopy['pod'] }}</span>
                            </div>
                            <div class="mb-2">

                                <b>Service : </b>
                                <span>{{ $consignorcopy['service'] }}</span>

                                <b>SAC CODE : </b>
                                <span>{{ $consignorcopy['sac_code'] }}</span>

                            </div>
                        </td>
                        <td>
                            <span>{{$consignorcopy['actual']}}</span>
                        </td>
                        <td>
                            <span>{{$consignorcopy['charged']}}</span>
                        </td>
                        <td rowspan="2" class="text-center">
                            <div>
                                <span>{{$consignorcopy['paid']}}</span>
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
                                <span>{{$consignorcopy['to_pay']}}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Value (RS).</b>
                            <span>{{$consignorcopy['value']}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p>GOODS BOOKED AS OWNER'S RISK.</p>
                            <span>Not responsible for Breakage, Leakage, Damage Goods or Fires.</span>
                        </td>
                        <td colspan="2" class="text-center" style="border-bottom: transparent;">
                             <p><b>For,{{ ucfirst($companydetails['name'])}}</b> </p>
                            @if ($othersettings['authorized_signatory'] == 'company_signature' && isset($companydetails['pr_sign_img'])) 
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['pr_sign_img']))) }}"
                                    alt="Company logo" style="max-width: 150px">
                            @endif 
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td colspan="4">
                            <div class="align-items-baseline d-flex justify-content-between mb-2">
                                <span>Reached At Factory :</span>
                                <b>Date : </b>
                                <span>{{$consignorcopy['reached_at_factory_date_formatted']}}</span>

                                <b>Time : </b>
                                <span>{{$consignorcopy['reached_at_factory_time_formatted']}}</span>
                            </div>
                            <div class="align-items-baseline d-flex justify-content-between mb-2">
                                <span>Left From Factory :</span>
                                <b>Date : </b>
                                <span>{{$consignorcopy['left_from_factory_date_formatted']}}</span>

                                <b>Time : </b>
                                <span>{{$consignorcopy['left_from_factory_time_formatted']}}</span>

                            </div>
                        </td>
                        <td colspan="2" class="text-center" style="vertical-align: bottom;border-top: transparent">
                            <p>Authorized Signatory</p>
                        </td>
                    </tr> 
                </thead>
            </table>
            <table class="w-100 table table-bordered">
                <tr>
                    <td>
                        <b>OUR COMPANY'S PAN NO :  <span>{{$companydetails['pan_number']}}<span> </b>
                        <br>
                        @if ($companydetails['gst_no'])
                            <b>OUR COMPANY'S GST NO :  <span>{{$companydetails['gst_no']}}<span> </b>
                            <br>
                        @endif
                        <b>STATE : <span>{{$companydetails['state_name']}}<span>, STATE CODE : <span>{{$companydetails['state_code']}}<span> </b>
                    </td>
                </tr>
                @isset($tandc)
                <tr>
                    <td class="p-0">
                        <p class="text-center bg-light m-0" ><b>Terms & Conditions</b></p>
                        <div class="m-2"> {!! $tandc[0] !!}</div>
                    </td>
                </tr>
                @endisset
            </table>
        </div>
    </main>
</body>

</html>
