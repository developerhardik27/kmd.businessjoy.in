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
            font-size: 25px;
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
    </style>
</head>

<body>
    <div class="container">
        <table width='100%' cellspacing=0 cellpadding=0>
            <tr>

                <td colspan="4" class=" textblue firstrow cname" style="text-align:center ">
                    <input type="text" value='PAYMENT RECEIPT' class="bottom-border-input" style="width: 100%">
                </td>
            </tr>
            <tr>
                <td colspan="2" valign=top style="text-align: center">
                    <span class=" textblue firstrow cname" style="display:block;">{{ $companydetails['name'] }}</span>
                    <span style="display:block;">{{ $companydetails['address'] }}</span>
                    <span style="display:block;">{{ $companydetails['city_name'] }},
                        {{ $companydetails['state_name'] }}, {{ $companydetails['pincode'] }}</span>
                    <span style="display:block;">Email: {{ $companydetails['email'] }}</span>
                    <span><b>GSTIN No: {{ $companydetails['gst_no'] }}</b></span>


                </td>
                <td style="text-align:center " colspan="2">
                    <span class=" textblue firstrow cname" style="display:block;" id="">Bill To</span>
                    <span style="display:block;"> {{ $invdata['firstname'] }} {{ $invdata['lastname'] }}</span>
                    <span style="display:block;">{{ $invdata['address'] }}</span>
                    <span
                        style="display:block;">{{ $invdata['city_name'] }},{{ $invdata['state_name'] }},{{ $invdata['pincode'] }}</span>
                    <span style="display:block;">{{ $invdata['email'] }}</span>
                    <span>{{ $invdata['contact_no'] }}</span><br><br>
                </td>
            </tr>

            <br>
            <tr>
                <td style="text-align: "><b>Payment Date</b></td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                        value = '{{ $payment[0]['datetime'] }} '></td>
                <td rowspan="4" style="text-align: center;background:lightgreen;"><b>Amount Received <br>Rs
                        {{ $payment[0]['amount'] }}.00 </b></td>
            </tr>
            <tr>
                <td style="text-align: "><b>Paid by</b></td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                        value = '{{ $payment[0]['paid_by'] }} '> </td>
            </tr>
            <tr>
                <td style="text-align: "><b>Reference Number</b></td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                        value = '{{ $payment[0]['transaction_id'] }} '> </td>
            </tr>
            <tr>
                <td style="text-align: "><b>Payment Mode</b> </td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                        value = '{{ $payment[0]['paid_type'] }}'></td>
            </tr>
            <br><br>
            <tr>
                <td colspan="2" style="text-align: ">
                    <table width='100%' border="1px solid" cellspacing=0 cellpadding=0>
                        <tr>
                            <td colspan="2">Payment Details:</td>
                        </tr>
                        <tr>
                            <td>Invoice Amount</td>
                            <td>{{ $payment[0]['amount'] }}.00 Rs</td>
                        </tr>
                        @foreach ($payment as $value)
                            <tr>
                                <td>Paid Amount</td>
                                <td>{{ $value['paid_amount'] }}.00 Rs - {{ $value['created_at'] }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td>Pending Amout</td>
                            <td>00.00 Rs</td>
                        </tr>
                    </table>
                </td>
                <td style="text-align: center" colspan="2">
                    <div style="display: inline-block;">
                        <img @if ($companydetails['pr_sign_img'] != '') src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $companydetails['img']))) }}"
                        @else
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('admin/images/bjlogo2.png'))) }}" @endif
                            class="rounded mt-auto mx-auto d-block" alt="logo" height="100px">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2" valign=bottom style="text-align: center"> signature</td>
            </tr>

        </table>
    </div>
</body>

</html>
