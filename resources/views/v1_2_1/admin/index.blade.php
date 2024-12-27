@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterpage')
@section('page_title')
    {{ config('app.name') }} - Dashboard
@endsection
@section('style')
    <style>
        .scrollable-table {
            max-height: 300px;
            /* Set the maximum height for the table */
            overflow-y: auto;
            /* Add vertical scrollbar if content overflows */

        }

        .iq-edit-profile.nav-pills .nav-link.active,
        .iq-edit-profile.nav-pills .show>.nav-link {
            color: var(--iq-white) !important;
        }
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <span class="font-size-26">Dashboard</span>
                </div>
                <div class="col-lg-12 mt-2">
                    <div class="iq-card">
                        <div class="iq-card-body p-0">
                            <div class="iq-edit-list">
                                @if (count(session('allmenu')) > 1)
                                    <ul class="iq-edit-profile d-flex nav nav-pills">
                                        @foreach (session('allmenu') as $val)
                                            <li data-dashboard="{{ $val }}"
                                                class="col-md-{{ ceil(12 / count(session('allmenu'))) }} p-0 dynamicdashboard">
                                                <a class="nav-link {{ $val == session('menu') ? 'active' : '' }}"
                                                    data-toggle="pill" href="#{{ $val }}dashboard">
                                                    <span>{{ ucfirst($val) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="iq-edit-list-data">
                        <div class="tab-content">
                            <div class="tab-pane fade @if (session('menu') == 'invoice') active show @endif"
                                id="invoicedashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>Invoice Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-6 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Invoice Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="invoice-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_inv">0</h1>
                                                        <p>Invoice</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Monthly Invoices</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Paid Invoices"
                                                                        class="btn btn-success btn-sm" id='invoicepaiddata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        PAID
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-success">
                                                                <span id="invoicepaid">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Part Payment Invoices"
                                                                        class="btn btn-info btn-sm"
                                                                        id='invoicepartpaymentdata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Part Payment
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-info">
                                                                <span id="invoicepartpayment">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Pending Invoices"
                                                                        class="btn btn-secondary btn-sm"
                                                                        id="invoicependingdata">
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        PENDING
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-secondary">
                                                                <span id="invoicepending">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Canceled Invoices"
                                                                        class="btn btn-danger btn-sm"
                                                                        id="invoicecanceldata"><span><i
                                                                                class="ri-list-check"></i></span>CANCEL</button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-danger"><span
                                                                    id="invoicecancel">0</span></div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Overdue Invoices"
                                                                        class="btn btn-warning btn-sm"
                                                                        id="invoiceduedata"><span><i
                                                                                class="ri-list-check"></i></span>OVER
                                                                        DUE</button></h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-warning"><span
                                                                    id="invoicedue">0</span></div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title"><span id="status_title"></span> Invoices
                                                        </h4>
                                                    </div>
                                                    <div class="float-right my-1">
                                                        <select name="invoicesbymonths" id="invoicesbymonths"
                                                            class="float-right form-control">
                                                            <option disabled>Select Month</option>
                                                            <option value="all">Select All</option>
                                                            <option value="current" selected>Current Month</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div class="table-responsive scrollable-table" style="width: 100%">
                                                        <table class="table mb-0  table-borderless w-100" width="100%"
                                                            style="text-align: center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Invoice</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col" class="text-right">Amount</th>
                                                                    <th scope="col" class="text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="invoicedata">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade @if (session('menu') == 'reminder') active show @endif"
                                id="reminderdashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>Reminder Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-6 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="reminder-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_customers">0</h1>
                                                        <p>Total Customers</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Reminders</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button class="btn btn-secondary btn-sm"
                                                                        id="reminderpendingdata"><span><i
                                                                                class="ri-list-check"></i></span>PENDING</button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-secondary"><span
                                                                    id="reminderpending">0</span></div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button class="btn btn-danger btn-sm"
                                                                        id="reminderinprogressdata"><span><i
                                                                                class="ri-list-check"></i></span>In
                                                                        Progress</button></h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-danger"><span
                                                                    id="reminderinprogress">0</span></div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button class="btn btn-warning btn-sm"
                                                                        id="remindercompleteddata"><span><i
                                                                                class="ri-list-check"></i></span>Completed</button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-warning"><span
                                                                    id="remindercompleted">0</span></div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title"><span id="status_title"></span>Upcoming
                                                            Reminders
                                                        </h4>
                                                    </div>
                                                    <div class="float-right my-1">
                                                        <select name="reminderbydays" id="reminderbydays"
                                                            class="float-right form-control">
                                                            <option disabled>Select Days</option>
                                                            <option value="7">7 Days</option>
                                                            <option value="15">15 Days</option>
                                                            <option value="30" selected>1 Month</option>
                                                            <option value="180">6 Months</option>
                                                            <option value="365">1 Year</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">

                                                    <div class="table-responsive scrollable-table" style="width: 100%">
                                                        <table class="table mb-0  table-borderless w-100" width="100%"
                                                            style="text-align: center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Customer</th>
                                                                    <th scope="col">Area</th>
                                                                    <th scope="col">Reminder Date</th>
                                                                    <th scope="col">Contact</th>
                                                                    <th scope="col">Product</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="reminderdata">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade @if (session('menu') == 'lead') active show @endif"
                                id="leaddashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>Lead Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-6 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="lead-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_leads">0</h1>
                                                        <p>Total Leads</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Leads</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button class="btn btn-secondary btn-sm"
                                                                        id="leadpendingdata"><span><i
                                                                                class="ri-list-check"></i></span>PENDING</button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-secondary"><span
                                                                    id="leadpending">0</span></div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button class="btn btn-danger btn-sm"
                                                                        id="leadinprogressdata"><span><i
                                                                                class="ri-list-check"></i></span>In
                                                                        Progress</button></h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-danger"><span
                                                                    id="leadinprogress">0</span></div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button class="btn btn-warning btn-sm"
                                                                        id="leadcompleteddata"><span><i
                                                                                class="ri-list-check"></i></span>Completed</button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-warning"><span
                                                                    id="leadcompleted">0</span></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title"><span id="status_title"></span>
                                                            Leads </h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div class="col-md-3 float-right my-1">
                                                        <select name="reminderbydays" id="leadbydays"
                                                            class="float-right form-control">
                                                            <option disabled>Select Days</option>
                                                            <option value="7">7 Days</option>
                                                            <option value="15">15 Days</option>
                                                            <option value="30" selected>1 Month</option>
                                                            <option value="180">6 Months</option>
                                                            <option value="365">1 Year</option>
                                                        </select>
                                                    </div>

                                                    <div class="table-responsive scrollable-table" style="width: 100%">
                                                        <table class="table mb-0  table-borderless w-100" width="100%"
                                                            style="text-align: center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Customer</th>
                                                                    <th scope="col">Area</th>
                                                                    <th scope="col">Reminder Date</th>
                                                                    <th scope="col">Contact</th>
                                                                    <th scope="col">Product</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="leaddata">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            var invoicepartpaymentdata = '';
            var invoicepaiddata = '';
            var invoicependingdata = '';
            var invoicecanceldata = '';
            var invoiceduedata = '';


            function totalInvoiceCount() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('invoice.totalinvoice') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        $('#total_inv').text(response.invoice);
                    }
                });
            }

            function invoice(month = null) {
                invoicemonth = 'current';
                if (month) {
                    invoicemonth = month;
                }

                // get all invoice
                $.ajax({
                    type: 'GET',
                    url: "{{ route('invoice.status_list') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        invoicemonth: invoicemonth,
                    },
                    success: function(response) {

                        invoicepartpaymentdata = invoicepaiddata = invoicependingdata =
                            invoicecanceldata = invoiceduedata = '';

                        $('#invoicepartpayment').text(0);
                        $('#invoicepaid').text(0);
                        $('#invoicepending').text(0);
                        $('#invoicecancel').text(0);
                        $('#invoicedue').text(0);


                        if (response == '') {
                            $('#invoicedata').html(
                                "<tr><td colspan='4' class='text-center'>No Data Found</td></tr>");
                        }

                        if (response.part_payment) {
                            console.log('part');
                            partpayment = response.part_payment;
                            $('#invoicepartpayment').text(partpayment.length);
                            invoicepartpaymentdata = response.part_payment;
                        }
                        if (response.paid) {
                            console.log('paid');
                            paid = response.paid;
                            $('#invoicepaid').text(paid.length);
                            invoicepaiddata = response.paid;
                        }
                        if (response.pending) {
                            console.log('pending');
                            pending = response.pending;
                            $('#invoicepending').text(pending.length);
                            invoicependingdata = response.pending;
                            pendingd();
                        }

                        if (response.cancel) {
                            console.log('cancel');
                            cancel = response.cancel;
                            $('#invoicecancel').text(cancel.length)
                            invoicecanceldata = response.cancel;
                        }

                        if (response.due) {
                            console.log('due');
                            due = response.due;
                            $('#invoicedue').text(due.length)
                            invoiceduedata = response.due;
                        }
                        if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        }
                    }
                });
            }

            // part payment invoices
            function partpaymentd() {
                $('#invoicedata').html('');
                $('#status_title').text('part payment');
                if (invoicepartpaymentdata != '') {
                    $.each(invoicepartpaymentdata, function(key, value) {
                        $('#invoicedata').append(` 
                            <tr>
                                <td>${value.inv_no}</td>
                                <td>${value.inv_date_formatted}</td>
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-success">${(value.status).replace('_',' ')}</div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#invoicedata').append(`
                        <tr>
                            <td colspan=4>still not any invoice status part payment in this month </td>
                        </tr>
                    `);
                }

            }

            // paid invoices
            function paidd() {
                $('#invoicedata').html('');
                $('#status_title').text('paid');
                if (invoicepaiddata != '') {
                    $.each(invoicepaiddata, function(key, value) {
                        $('#invoicedata').append(` 
                            <tr>
                                <td>${value.inv_no}</td>
                                <td>${value.inv_date_formatted}</td>
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-success">${value.status}</div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#invoicedata').append(`
                        <tr>
                            <td colspan=4>still not any invoice paid in this month </td>
                        </tr>
                    `)
                }

            }

            // pending invoices
            function pendingd() {
                $('#invoicedata').html('');
                $('#status_title').text('pending');
                if (invoicependingdata != '') {
                    $.each(invoicependingdata, function(key, value) {
                        $('#invoicedata').append(` 
                            <tr>
                                <td>${value.inv_no}</td>
                                <td>${value.inv_date_formatted}</td>
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-secondary">${value.status}</div>
                                </td>                                        
                            </tr>
                        `);
                    });
                } else {
                    $('#invoicedata').append(`
                        <tr>
                            <td colspan=4'>No data Found</td>
                        </tr>
                    `);
                }
            }

            // cancled invoices
            function canceld() {
                $('#invoicedata').html('');
                $('#status_title').text('canceld');
                if (invoicecanceldata != '') {
                    $.each(invoicecanceldata, function(key, value) {
                        $('#invoicedata').append(`
                            <tr>
                                <td>${value.inv_no}</td>
                                <td>${value.inv_date_formatted}</td>                                        
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                </td>                                      
                            </tr>
                        `);
                    });
                } else {
                    $('#invoicedata').append(`
                        <tr>
                            <td colspan=4>still not any invoice cancel in this month</td>
                        </tr>
                    `);
                }
            }

            // overdue invoices
            function dued() {
                $('#invoicedata').html('');
                $('#status_title').text('over due');
                if (invoiceduedata != '') {
                    $.each(invoiceduedata, function(key, value) {
                        $('#invoicedata').append(`
                            <tr>
                                <td>${value.inv_no}</td>
                                <td>${value.inv_date_formatted}</td>                                        
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                </td>                                      
                            </tr>
                        `);
                    });
                } else {
                    $('#invoicedata').append(`
                        <tr>
                            <td colspan=4>still not any invoice overdue in this month</td>
                        </tr>
                    `);
                }
            }

            // Function to map month numbers to month names
            function getMonthName(monthNumber) {
                const months = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                return months[monthNumber - 1];
            }

            // Function to fetch data using jQuery Ajax
            function fetchDataAndDrawInvoiceChart() {
                $.ajax({
                    url: "{{ route('invoice.chart') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(invoicesData) {
                        // Ensure invoicesData is an array of objects with the expected properties
                        if (Array.isArray(invoicesData) && invoicesData.length > 0 && invoicesData[
                                0]
                            .hasOwnProperty('month') && invoicesData[0].hasOwnProperty(
                                'total_invoices') && invoicesData[0].hasOwnProperty('paid_invoices')
                        ) {
                            const xAxisCategories = invoicesData.map(item => getMonthName(item
                                .month));

                            // Extract data for total and paid invoices, rainfall, and temperature
                            const totalInvoicesData = invoicesData.map(item => parseInt(item
                                .total_invoices));
                            const paidInvoicesData = invoicesData.map(item => parseInt(item
                                .paid_invoices));

                            // Chart configuration for displaying monthly invoice counts, paid invoices, rainfall, and temperature with month names
                            Highcharts.chart("invoice-chart", {
                                chart: {
                                    type: "spline",
                                },
                                title: {
                                    text: "Monthly Data"
                                },
                                xAxis: {
                                    categories: xAxisCategories,
                                    crosshair: true
                                },
                                yAxis: {
                                    title: {
                                        text: "Values"
                                    }
                                },
                                series: [{
                                    name: "Total Invoices",
                                    data: totalInvoicesData,
                                    color: "#fbc647",
                                    type: "column"
                                }, {
                                    name: "Paid Invoices",
                                    data: paidInvoicesData,
                                    color: "#827af3",
                                    type: "spline",

                                }],
                                credits: {
                                    enabled: false
                                },
                            });
                        } else {

                            document.getElementById("invoice-chart").innerHTML =
                                '<p>You have no invoices to display.</p>';

                            Highcharts.chart("invoice-chart", {
                                credits: {
                                    enabled: false
                                },
                                chart: {
                                    type: "spline",
                                },
                                title: {
                                    text: "You have no invoices"
                                },
                                xAxis: {
                                    crosshair: true
                                },
                                yAxis: {
                                    title: {
                                        text: "Values"
                                    }
                                },
                                series: [{
                                    name: "Invoices",
                                    color: "#827af3",
                                    type: "spline",

                                }]
                            });
                            console.error('Invalid data format received:', invoicesData);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }


            $('#invoicepartpaymentdata').on('click', function() {
                partpaymentd();
            });
            $('#invoicepaiddata').on('click', function() {
                paidd();
            });
            $('#invoicependingdata').on('click', function() {
                pendingd();
            });
            $('#invoicecanceldata').on('click', function() {
                canceld();
            });
            $('#invoiceduedata').on('click', function() {
                dued();
            });

            $('#invoicesbymonths').on('change', function() {
                invoice($(this).val());
            });

            function getLastSixMonthsWithNamesAndNumbers() {
                const months = [{
                        number: 1,
                        name: "January"
                    },
                    {
                        number: 2,
                        name: "February"
                    },
                    {
                        number: 3,
                        name: "March"
                    },
                    {
                        number: 4,
                        name: "April"
                    },
                    {
                        number: 5,
                        name: "May"
                    },
                    {
                        number: 6,
                        name: "June"
                    },
                    {
                        number: 7,
                        name: "July"
                    },
                    {
                        number: 8,
                        name: "August"
                    },
                    {
                        number: 9,
                        name: "September"
                    },
                    {
                        number: 10,
                        name: "October"
                    },
                    {
                        number: 11,
                        name: "November"
                    },
                    {
                        number: 12,
                        name: "December"
                    }
                ];

                const currentDate = new Date();
                const currentMonth = currentDate.getMonth(); // 0-11 for Jan-Dec
                let lastMonths = [];

                // Start from the month before the current month
                for (let i = 1; i <= currentMonth; i++) {
                    const monthIndex = (currentMonth - i + 12) % 12; // Wrap around  
                    lastMonths.push(months[monthIndex]);
                }

                lastMonths = lastMonths.reverse(); // Reverse to show from the oldest to the newest

                $.each(lastMonths, function(key, month) {
                    $('#invoicesbymonths').append(`
                        <option value="${month.number}">${month.name}</option>
                    `);
                });

            }


            getLastSixMonthsWithNamesAndNumbers();

            function invoicedashboard() {


                totalInvoiceCount(); // call totalinvoice count function 

                invoice(); // call invoice function 

                pendingd(); //call pending function when document load

                // Call the function to fetch invoice data and draw the initial chart
                fetchDataAndDrawInvoiceChart();
                loaderhide();
            }

            // invoice dashboard end

            // reminder dashboard start

            function reminders() {
                // get all reminders
                $.ajax({
                    type: 'GET',
                    url: "{{ route('reminder.status_list') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        var totalreminders = 0;
                        if (response.pending) {
                            pending = response.pending;
                            $('#reminderpending').text(pending.length)
                        }

                        if (response.in_progress) {
                            in_progress = response.in_progress;
                            $('#reminderinprogress').text(in_progress.length)
                        }

                        if (response.completed) {
                            completed = response.completed;
                            $('#remindercompleted').text(completed.length)
                        }
                        if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        }
                    }
                });
            }

            function reminderCustomers() {
                // get total customers count
                $.ajax({
                    type: 'GET',
                    url: "{{ route('remindercustomer.count') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $('#total_customers').text(response.customer);
                        }
                    }
                });
            }

            // get reminder by days  
            function getreminderbydays(days) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('reminder.reminderbydays') }}",
                    data: {
                        days: days,
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        $('#reminderdata').html('');
                        if (response.status == 200 && response.reminder != '') {
                            $.each(response.reminder, function(key, value) {
                                $('#reminderdata').append(`
                                    <tr>
                                        <td>${value.name}</td>
                                        <td>${value.area}</td>
                                        <td>${value.next_reminder_date}</td>
                                        <td>${value.contact_no}</td>
                                        <td>${value.product_name}</td>
                                    </tr>
                                `);
                            })
                        } else {
                            $('#reminderdata').append(`
                                    <tr>
                                        <td colspan='4'>No Reminder Found</td>
                                    </tr>
                            `);
                        }
                    }
                });
            }

            $('#reminderbydays').on('change', function() {
                days = $(this).val();
                getreminderbydays(days);
            })

            // Function to fetch data using jQuery Ajax
            function fetchDataAndDrawReminderChart() {
                $.ajax({
                    url: "{{ route('reminder.chart') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(combinedData) {
                        // Ensure invoicesData is an array of objects with the expected properties
                        if (Array.isArray(combinedData['reminders']) && combinedData['reminders']
                            .length > 0 && combinedData['reminders'][0]
                            .hasOwnProperty('month') && combinedData['reminders'][0].hasOwnProperty(
                                'total_reminders') &&
                            Array.isArray(combinedData['customers']) && combinedData['customers']
                            .length > 0 && combinedData['customers'][0]
                            .hasOwnProperty('month') && combinedData['customers'][0].hasOwnProperty(
                                'total_customers')
                        ) {
                            const xAxisCategories = combinedData['reminders'].map(item =>
                                getMonthName(
                                    item.month));

                            // Extract data for total and paid invoices, rainfall, and temperature
                            const totalRemindersData = combinedData['reminders'].map(item =>
                                parseInt(
                                    item
                                    .total_reminders));

                            // Extract data for total and paid invoices, rainfall, and temperature
                            const totalCustomersData = combinedData['customers'].map(item =>
                                parseInt(
                                    item
                                    .total_customers));

                            // Chart configuration for displaying monthly invoice counts, paid invoices, rainfall, and temperature with month names
                            Highcharts.chart("reminder-chart", {
                                chart: {
                                    type: "spline",
                                },
                                title: {
                                    text: "Monthly Data"
                                },
                                xAxis: {
                                    categories: xAxisCategories,
                                    crosshair: true
                                },
                                yAxis: {
                                    title: {
                                        text: "Values"
                                    }
                                },
                                series: [{
                                    name: "Total Reminders",
                                    data: totalRemindersData,
                                    color: "#fbc647",
                                    type: "column"
                                }, {
                                    name: "Total Customers",
                                    data: totalCustomersData,
                                    color: "#827af3",
                                    type: "spline",

                                }],
                                credits: {
                                    enabled: false
                                },
                            });
                        } else {

                            document.getElementById("reminder-chart").innerHTML =
                                '<p>You have no Reminders to display.</p>';

                            Highcharts.chart("reminder-chart", {
                                chart: {
                                    type: "spline",
                                },
                                title: {
                                    text: "You have no reminders"
                                },
                                xAxis: {
                                    crosshair: true
                                },
                                yAxis: {
                                    title: {
                                        text: "Values"
                                    }
                                },
                                series: [{
                                    name: "Reminders",
                                    color: "#827af3",
                                    type: "spline",

                                }],
                                credits: {
                                    enabled: false
                                },
                            });
                            console.error('Invalid data format received:', combinedData);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }

            function reminderdashboard() {

                reminders(); // call reminder function
                reminderCustomers(); // call reminder customer function
                getreminderbydays($('#reminderbydays').val());

                // Call the function to fetch data and draw the initial chart
                fetchDataAndDrawReminderChart();
                loaderhide();

            }

            @if (session('menu') == 'invoice')
                invoicedashboard();
            @endif
            @if (session('menu') == 'reminder')
                reminderdashboard();
            @endif
            loaderhide();

            $(document).on('click', '.dynamicdashboard', function() {
                var currentdashboard = $(this).data('dashboard');
                eval(currentdashboard + 'dashboard()');
            });
        });
    </script>
@endpush
