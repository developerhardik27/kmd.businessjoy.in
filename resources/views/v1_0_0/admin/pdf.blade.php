<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>pdf</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Invoice
                <strong id="inv_date"></strong>
                <span class="float-right"> <strong>Status:</strong> Pending</span>
            </div>
            <div class="card-body">
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <div>
                            <strong class="text-primary">Oceanmnc</strong>
                        </div>
                        <div>2/225 Arved Transcube Plaza, Ranip</div>
                        <div>Ahmedabad, Gujarat, 382480</div>
                        <div>Email: info@webz.com.pl</div>
                        <div>Phone: +91 9998-1118-74</div>
                    </div>
                    <div class="col-sm-6  h-50 float-right">
                        <img src="/admin/images/logo.png" alt="logo" height="100px">
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-sm-6 ">
                        <h6 class=" bg-primary">Bill To:</h6>
                        <div>
                            <strong id="toname"></strong>
                        </div>
                        <div id="toaddress"></div>
                    </div>
                    <div class="col-sm-6 mt--3">
                        <div class="text-center bg-primary">
                            <strong>INV#</strong>
                            <strong class="float-right ">DATE</strong>
                        </div>
                        <div class="text-center">
                            <strong>123</strong>
                            <strong class="float-right ">23-2-2023</strong>
                        </div>
                        <div class="text-center bg-primary">
                            <strong>Customer id</strong>
                            <strong class="float-right ">Terms</strong>
                        </div>
                        <div class="text-center">
                            <strong>327</strong>
                            <strong class="float-right ">BANK TRANSFER</strong>
                        </div>
                        <div class="text-center bg-primary">
                            <strong>Bank Details</strong>
                        </div>
                        <div class="text-center">
                            <table id="" class="table table-sm table-striped ">
                                <tr>
                                    <th class="center">Holder Name</th>
                                    <td class="">Jay Patel</td>
                                </tr>
                                <tr>
                                    <th>A/c No</th>
                                    <td>50100262552878</td>
                                </tr>
                                <tr>
                                    <th>Swift Code</th>
                                    <td>HDFCINBBXXX</td>
                                </tr>
                                <tr>
                                    <th class="right">IFSC Code</th>
                                    <td>HDFC0002534</td>
                                </tr>
                                <tr>
                                    <th class="center">Branch Name</th>
                                    <td>Collector Office</td>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive-sm">
            <table id="data" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th class="right">Unit Cost</th>
                        <th class="center">Qty</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="row mt--2">
            <div class="col-lg-8  col-sm-5  bg-primary">
                Thank You For Your buisness!
            </div>
            <div class="col-lg-4 col-sm-5 ml-auto">
                <table class="table table-clear">
                    <tbody>
                        <tr>
                            <td class="left">
                                <strong>Subtotal</strong>
                            </td>
                            <td class="right" id="subtotal"></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong>Gst (18%)</strong>
                            </td>
                            <td class="right" id="gst"></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong>Total</strong>
                            </td>
                            <td class="right">
                                <strong id="total"></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
