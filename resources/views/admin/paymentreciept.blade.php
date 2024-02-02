 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>pdf</title>
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
         table{
            /* padding: 0; */
         }
     </style>
 </head>

 <body>
     <div class="container">
         <table width='100%'  cellspacing=0 cellpadding=0>
            <tr>

                <td colspan="4" class=" textblue firstrow cname" style="text-align:center ">
                    <input type="text" value='PAYMENT RECEIPT' class="bottom-border-input" style="width: 100%">
                </td>
            </tr>
             <tr>
                 <td colspan="2" valign=top style="text-align: center">
                     <span class=" textblue firstrow cname" style="display:block;" id=" ">Oceanmnc</span>
                     <span style="display:block;">2/225 Arved Transcube Plaza,Ranip</span>
                     <span style="display:block;">Ahmedabad, Gujarat, 382480</span>
                     <span style="display:block;">Email: info@webz.com.pl</span>
                     <span style="display:block;">Contact No: +91 9998-1118-74</span>
                     <span>GSTIN No: 24DMLPP9818M1Z6</span><br><br>

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
                <td style="text-align: " ><b>Payment Date</b></td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                    value = '{{ $payment['created_at'] }} '></td>
                <td rowspan="4" style="text-align: center;background:lightgreen;" ><b>Amount Received <br>Rs {{ $invdata['grand_total'] }}.00 </b></td>
             </tr>
             <tr>
                <td style="text-align: "><b>Paid by</b></td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                    value = '{{ $payment['paid_by']  }} '> </td>
             </tr>
             <tr>
                <td style="text-align: "><b>Reference Number</b></td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                    value = '{{ $payment['transaction_id'] }} '> </td>
             </tr>
             <tr>
                <td style="text-align: "><b>Payment Mode</b> </td>
                <td colspan="2"><input type="text" class="bottom-border-input" style="width:95%"
                    value = '{{ $payment['paid_type'] }}'></td>
             </tr>
             <br><br>
             <tr>
                <td colspan="2" style="text-align: ">
                    <table width='100%' border="1px solid" cellspacing=0 cellpadding=0>
                        <tr><td colspan="2">Payment Details:</td></tr>
                        <tr>
                            <td>Invoice Amount</td>
                            <td>{{ $invdata['grand_total'] }}.00 Rs</td>
                        </tr>
                        <tr>
                            <td>Paid Amount</td>
                            <td>{{ $invdata['grand_total'] }}.00 Rs</td>
                        </tr>
                        <tr>
                            <td>Pending Amout</td>
                            <td>0</td>
                        </tr>
                    </table>
                </td>
                <td style="text-align: center" colspan="2">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('admin/images/logo.png'))) }}"
                        class="rounded mt-auto mx-auto d-block" alt="logo" height="100px"><br>
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
