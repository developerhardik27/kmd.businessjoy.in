{{-- @php
print_r($data);
    die();
@endphp --}}

@section('page_title')
    Invoice Details
@endsection
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>invoiceView</title>
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }} " />
    {{-- <link rel="stylesheet" href="{{ asset('admin/css/bootstrap.min.css') }}"> 
    <link rel="stylesheet" href="{{ asset('admin/css/typography.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        .bgblue {

            background-color: rgb(10 8 108 / 99%);
            color: #cae8ff;
            font-family: monospace;
        }

        .bglightblue {
            background-color: rgba(41, 115, 194, 0.761);
        }

        .textblue {
            color: rgba(27, 98, 189, 0.804);
            font: bolder
        }

        body {
            min-height: 100%;
            max-height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card ">
            <div class="card-header">
                Invoice
                <strong id="inv_date"></strong>
                <span class="float-right" id="status"> <strong>Status:</strong> </span>
            </div>
            <div class="card-body">
                <table>
                    <tr>
                        <td class="w-50">
                            <table class="w-100 float-left">
                                <tbody>
                                    <tr class="textblue">
                                        <td class="font-weight-bold">{{ $data['companydetails']['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span
                                                class="w-100 float-left">{{ $data['companydetails']['address'] }}</span>
                                            <span class="w-100 float-left">{{ $data['companydetails']['city_name'] }},
                                                {{ $data['companydetails']['state_name'] }},
                                                {{ $data['companydetails']['pincode'] }}</span>
                                            <span class="w-100 float-left">Email:
                                                {{ $data['companydetails']['email'] }}</span>
                                            <span class="w-100 float-left">Phone:
                                                {{ $data['companydetails']['contact_no'] }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </td>
                        <td class="w-50">
                            <table class="w-100 float-right">
                                <tr class="">
                                    <td> <img
                                            @if ($data['companydetails']['img'] != '') src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $data['companydetails']['img']))) }}"
                                            @else   
                                              src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('admin/images/bjlogo2.png'))) }}" @endif
                                            class="rounded mt-auto mx-auto d-block" alt="logo" height="150px"></td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td valint='top'>
                            <table class="w-100 float-left">
                                <tbody>
                                    <tr class="bgblue">
                                        <td class="px-2 font-weight-bold">Bill To</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span id="toname" class="font-weight-bold textblue"></span><br>
                                            <span id="toaddress"></span>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>

                            <table class="w-100 float-right table-striped">
                                <tr class="bgblue">
                                    <td class="font-weight-bold px-2 bgblue">INV#</td>
                                    <td class="font-weight-bold px-2 bgblue">DATE</td>
                                </tr>
                                <tr>
                                    <td id="id"></td>
                                    <td id="invoice_date"></td>
                                </tr>
                                <tr class="bgblue">
                                    <td class="font-weight-bold px-2 bgblue">Customer id</td>
                                    <td class="font-weight-bold px-2 bgblue">Payment Method</td>
                                </tr>
                                <tr>
                                    <td id="customer_id"></td>
                                    <td id="payment_type"></td>
                                </tr>
                                <tr class="bgblue">
                                    <td class="font-weight-bold px-2 bgblue" colspan="2">Bank Details</td>

                                </tr>
                                <tr>
                                    <td class="font-weight-bold px-2">Holder Name</td>
                                    <td>{{ $data['bankdetails']['holder_name'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold px-2">A/c No</td>
                                    <td>{{ $data['bankdetails']['account_no'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold px-2">Swift Code</td>
                                    <td>{{ $data['bankdetails']['swift_code'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold px-2">IFSC Code</td>
                                    <td>{{ $data['bankdetails']['ifsc_code'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold px-2">Branch Name</td>
                                    <td>{{ $data['bankdetails']['branch_name'] }}
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="w-100">
                            <table id="data" class="table-responsive-sm table table-striped table-bordered"
                                style="width: 100%;">
                                <thead>
                                    <tr class="bgblue" id="dynamiccol">
                                        <th class="center">#</th>

                                    </tr>
                                </thead>
                                <tbody id="dynamicval">
                                </tbody>
                                <tbody>
                                    <tr class="bglightblue">
                                        <td rowspan="4"  colspan="" class="bgblue"
                                            style="vertical-align: middle; text-align: center;" id="dynamiccolspan">
                                            <strong class="">Thank You For Your business!</strong>
                                        </td>
                                        <td class="left bgblue ">
                                            <strong>Subtotal</strong>
                                        </td>
                                        <td class="right bgblue" id="subtotal"></td>
                                    </tr>
                                    <tr>

                                        <td class="left bglightblue">
                                            <strong>Gst (18%)</strong>
                                        </td>
                                        <td class="right bglightblue" id="gst"></td>
                                    </tr>
                                    <tr>
                                        <td class="left bgblue">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="right bgblue">
                                            <strong id="total"></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <script src="{{ asset('admin/js/jquery.min.js') }} "></script>
    <script>
        $('document').ready(function() {
            $.ajax({
                type: 'GET',
                url: '/api/invoice/inv_details/' + {{ $id }},
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: " {{ session()->get('company_id') }} "
                },
                success: function(response) {
                    if (response.status == 200 && response.invoice != '') {
                        // You can update your HTML with the data here if needed
                        
                        $('#dynamiccolspan').attr('colspan',(response.columns.length-1));

                        $.each(response.columns, function(key, value) {
                            var columnName = value.replace(/_/g, ' ');
                            $('#dynamiccol').append(`
                                   <th class='center'>${columnName}</th>
                            `);
                        })

                        var srno = 0;
                        $.each(response.invoice, function(key, value) {
                            srno++;
                            var row = `<tr><td>${srno}</td>`;
                            $.each(response.columns, function(key2, val) {
                                row += `<td>${value[val]}</td>`;
                            });
                            row += `</tr>`;
                            $('#data').append(row);
                        });
                    }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                    } else {
                        $('#dynamicval').append(`<tr><td colspan='6' >No Data Found</td></tr>`);
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            $.ajax({
                type: 'GET',
                url: '/api/invoice/' + {{ $id }},
                data: {
                    token: "{{ session()->get('api_token') }}",
                    company_id: " {{ session()->get('company_id') }} "
                },
                success: function(response) {
                    if (response.status == 200 && response.invoice != '') {
                        $.each(response.invoice, function(key, value) {
                            $('#status').append(value.status);
                            $('#inv_date').append(value.inv_date);
                            $('#toname').append(`${value.firstname} ${value.lastname}`);
                            $('#toaddress').append(
                                `${value.address} <br> ${value.city_name} , ${value.state_name} , ${value.pincode} <br> ${value.email} <br> ${value.contact_no}`
                            );
                            $('#id').append(value.inv_no);
                            $('#invoice_date').append(value.inv_date);
                            $('#customer_id').append(value.cid);
                            $('#payment_type').append(value.payment_type);
                            $('#subtotal').append(value.total + '.00 Rs');
                            $('#gst').append(value.gst + '.00 Rs');
                            $('#total').append(value.grand_total + '.00 Rs');
                        })

                    }else if(response.status == 500){
                            toastr.error(response.message);
                            loaderhide();
                    } else {
                        $('#data').append(`<tr><td colspan='6' >No Data Found</td></tr>`)
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        })
    </script>
