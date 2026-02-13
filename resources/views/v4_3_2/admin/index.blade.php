@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterpage')
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

        #Order-chart {
            min-height: 400px;
            /* keep chart height */
        }

        .no-data-message {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
            font-size: 18px;
            font-weight: 500;
            color: #6c757d;
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
                                                    <span>
                                                        {{ $val == 'BrokerInvoice' ? 'Brokrage Bill' : ucfirst($val) }}
                                                    </span>

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
                            {{-- {{ dd(session('allmenu')) }} --}}
                            <div class="tab-pane fade @if (session('menu') == 'admin') active show @endif"
                                id="admindashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>admin Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-6 col-lg-12">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Admin Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="admin-chart"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade @if (session('menu') == 'tea') active show @endif"
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
                                                    <div class="float-right my-1">
                                                        <select name="invoicesbymonths" id="invoicesbymonths"
                                                            class="float-right form-control m-0 p-0">
                                                            <option disabled>Select Month</option>
                                                            <option value="all">Select All</option>
                                                            <option value="current" selected>Current Month</option>
                                                        </select>
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
                                                        <h4 class="card-title"><span id="invoice_status_title"></span>
                                                            Invoices
                                                        </h4>
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
                            <div class="tab-pane fade @if (session('menu') == 'BrokerInvoice') active show @endif"
                                id="BrokerInvoicedashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>BrokerInvoice Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-6 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Brokrage Bill Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="BrokerInvoice-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_bro_inv">0</h1>
                                                        <p>Brokrage Bill</p>
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
                                                        <h4 class="card-title">Monthly Brokrage Bill</h4>
                                                    </div>
                                                    <div class="float-right my-1">
                                                        <select name="BrokerInvoicesbymonths" id="BrokerInvoicesbymonths"
                                                            class="float-right form-control m-0 p-0">
                                                            <option disabled>Select Month</option>
                                                            <option value="all">Select All</option>
                                                            <option value="current" selected>Current Month</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Paid Brokrage Bill"
                                                                        class="btn btn-success btn-sm"
                                                                        id='BrokerInvoicepaiddata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        PAID
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-success">
                                                                <span id="BrokerInvoicepaid">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Part Payment Brokrage Bill"
                                                                        class="btn btn-info btn-sm"
                                                                        id='BrokerInvoicepartpaymentdata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Part Payment
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-info">
                                                                <span id="BrokerInvoicepartpayment">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Pending Brokrage Bill"
                                                                        class="btn btn-secondary btn-sm"
                                                                        id="BrokerInvoicependingdata">
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        PENDING
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-secondary">
                                                                <span id="BrokerInvoicepending">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Canceled Brokrage Bill"
                                                                        class="btn btn-danger btn-sm"
                                                                        id="BrokerInvoicecanceldata"><span><i
                                                                                class="ri-list-check"></i></span>CANCEL</button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-danger"><span
                                                                    id="BrokerInvoicecancel">0</span></div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6><button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Overdue Brokrage Bill"
                                                                        class="btn btn-warning btn-sm"
                                                                        id="BrokerInvoiceduedata"><span><i
                                                                                class="ri-list-check"></i></span>OVER
                                                                        DUE</button></h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-warning"><span
                                                                    id="BrokerInvoicedue">0</span></div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title"><span
                                                                id="BrokerInvoice_status_title"></span>
                                                            Brokrage Bill
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div class="table-responsive scrollable-table" style="width: 100%">
                                                        <table class="table mb-0  table-borderless w-100" width="100%"
                                                            style="text-align: center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Brokrage Bill No</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col" class="text-right">Amount</th>
                                                                    <th scope="col" class="text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="BrokerInvoicedata">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade @if (session('menu') == 'Order') active show @endif"
                                id="Orderdashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>Order Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Order Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">

                                                    <div class="row mb-4 align-items-center">
                                                        <div class="col-md-3 col-sm-12">
                                                          
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <select id="monthSelect" class="form-control shadow-sm">
                                                                <option value="all">All Months</option>
                                                                <option value="1">January</option>
                                                                <option value="2">February</option>
                                                                <option value="3">March</option>
                                                                <option value="4">April</option>
                                                                <option value="5">May</option>
                                                                <option value="6">June</option>
                                                                <option value="7">July</option>
                                                                <option value="8">August</option>
                                                                <option value="9">September</option>
                                                                <option value="10">October</option>
                                                                <option value="11">November</option>
                                                                <option value="12">December</option>
                                                            </select>
                                                        </div>

                                                    </div>

                                                    <!-- Chart Area -->
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <div id="Order-chart" style="min-height: 350px;"></div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_order">0</h1>
                                                        <p>Order</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane fade @if (session('menu') == 'quotation') active show @endif"
                                id="quotationdashboard" role="tabpanel">
                                <div class="container-fluid">
                                    {{-- <p>Quotation Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-md-6 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Quotation Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="quotation-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_quotation">0</h1>
                                                        <p>Quotation</p>
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
                                                        <h4 class="card-title">Monthly Quotations</h4>
                                                    </div>
                                                    <div class="float-right my-1">
                                                        <select name="quotationsbymonths" id="quotationsbymonths"
                                                            class="float-right form-control m-0 p-0">
                                                            <option disabled>Select Month</option>
                                                            <option value="all">Select All</option>
                                                            <option value="current" selected>Current Month</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Accepted Quotations"
                                                                        class="btn btn-success btn-sm"
                                                                        id='quotationaccepteddata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Accepted
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-success">
                                                                <span id="quotationaccepted">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Pending Quotations"
                                                                        class="btn btn-secondary btn-sm"
                                                                        id="quotationpendingdata">
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Pending Approval
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-secondary">
                                                                <span id="quotationpending">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Rejected Quotations"
                                                                        class="btn btn-info btn-sm"
                                                                        id='quotationrejecteddata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Rejected
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-info">
                                                                <span id="quotationrejected">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Expired Quotations"
                                                                        class="btn btn-danger btn-sm"
                                                                        id="quotationexpireddata">
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Expired
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-danger">
                                                                <span id="quotationexpired">0</span>
                                                            </div>
                                                        </li>
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button data-toggle="tooltip" data-placement="bottom"
                                                                        data-original-title="View Revised Quotations"
                                                                        class="btn btn-warning btn-sm"
                                                                        id="quotationreviseddata">
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Revised
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-warning"><span
                                                                    id="quotationrevised">0</span></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title"><span id="quotation_status_title"></span>
                                                            Quotations
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div class="table-responsive scrollable-table" style="width: 100%">
                                                        <table class="table mb-0  table-borderless w-100" width="100%"
                                                            style="text-align: center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Quotation</th>
                                                                    <th scope="col">Date</th>
                                                                    <th scope="col" class="text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="quotationdata">

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
                                    {{-- <p>lead Dashboard</p> --}}
                                    <div class="row">
                                        <div class="col-lg-4 col-md-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Lead Count</h4>
                                                    </div>
                                                    <div class="float-right my-1">
                                                        <select name="leadrange" id="leadrange"
                                                            class="float-right form-control m-0 p-0">
                                                            <option disabled>Select</option>
                                                            <option value="today" selected>Today</option>
                                                            <option value="this_week">This Week</option>
                                                            <option value="this_month">This Month</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button class="btn btn-success btn-sm"
                                                                        id='newleaddata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        New Lead
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-success">
                                                                <span id="newleadcount">0</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Today's Due Followup</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <ul class="suggestions-lists m-0 p-0">
                                                        <li class="d-flex mb-4 align-items-center">
                                                            <div class="media-support-info ml-3">
                                                                <h6>
                                                                    <button class="btn btn-danger btn-sm"
                                                                        id='duefollowupdata'>
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>
                                                                        Due Follow Up
                                                                    </button>
                                                                </h6>
                                                            </div>
                                                            <div class="profile-icon iq-bg-danger">
                                                                <span id="duefollowup">0</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Lead Conversion Rate</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <button type="button" class="btn btn-success">
                                                        Conversion(%) <span id="conversionrate"
                                                            class="badge badge-light ml-2">0</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Lead Monthly Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="lead-monthly-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-5">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height"
                                                style="background: transparent;">
                                                <div class="iq-card-body rounded p-0"
                                                    style="background: url( {{ asset('admin/images/page-img/01.png') }} ) no-repeat;    background-size: cover; height: 415px;">
                                                    <div class="iq-caption">
                                                        <h1 id="total_lead">0</h1>
                                                        <p>Total Lead</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7 col-lg-7">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Status Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="lead-pie-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade @if (session('menu') == 'logistic') active show @endif"
                                id="logisticdashboard" role="tabpanel">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Consignment Daily Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div id="consignment-daily-chart" style="width: 100%; height: 400px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade @if (session('menu') == 'developer') active show @endif"
                                id="developerdashboard" role="tabpanel">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Company Wise Slow Page Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div class="filter-block container mb-4">
                                                        <div
                                                            class="align-items-center d-flex flex-column justify-content-center">
                                                            <div class="m-0 mb-sm-1 mr-sm-1">
                                                                <select class="form-control dateFilter">
                                                                    <option value="1_month" selected>Last 1 Month</option>
                                                                    <option value="3_months">Last 3 Months</option>
                                                                    <option value="6_months">Last 6 Months</option>
                                                                    <option value="1_year">Last 1 Year</option>
                                                                    <option value="custom">Custom Range</option>
                                                                </select>
                                                            </div>
                                                            <div class="custom-dates mr-sm-1" style="display:none;">
                                                                <div class="row">
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="font-weight-bold">From Date:</label>
                                                                        <input type="date"
                                                                            class="form-control fromDate" />
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="font-weight-bold">To Date:</label>
                                                                        <input type="date"
                                                                            class="form-control toDate" />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="">
                                                                <button type="button"
                                                                    class="dateFilterApplyBtn btn btn-primary btn-block m-0"
                                                                    data-fn="fetchDataAndDrawSlowPageCompanyWiseChart">
                                                                    Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="company-slow-pages-chart"
                                                        style="width: 100%; height: 400px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div
                                                class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                                                <div class="iq-card-header d-flex justify-content-between">
                                                    <div class="iq-header-title">
                                                        <h4 class="card-title">Day Wise Slow Page Chart</h4>
                                                    </div>
                                                </div>
                                                <div class="iq-card-body">
                                                    <div class="filter-block container mb-4">
                                                        <div
                                                            class="align-items-center d-flex flex-column justify-content-center">
                                                            <div class="m-0 mb-sm-1 mr-sm-1">
                                                                <select class="form-control dateFilter">
                                                                    <option value="1_month" selected>Last 1 Month</option>
                                                                    <option value="3_months">Last 3 Months</option>
                                                                    <option value="6_months">Last 6 Months</option>
                                                                    <option value="1_year">Last 1 Year</option>
                                                                    <option value="custom">Custom Range</option>
                                                                </select>
                                                            </div>
                                                            <div class="custom-dates mr-sm-1" style="display:none;">
                                                                <div class="row">
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="font-weight-bold">From Date:</label>
                                                                        <input type="date"
                                                                            class="form-control fromDate" />
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="font-weight-bold">To Date:</label>
                                                                        <input type="date"
                                                                            class="form-control toDate" />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="">
                                                                <button type="button"
                                                                    class="dateFilterApplyBtn btn btn-primary btn-block m-0"
                                                                    data-fn="fetchAndDrawDailySlowPagesChart">
                                                                    Apply
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="daily-slow-pages-chart" style="width: 100%; height: 400px;">
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
                                                    <div class="float-right my-1">
                                                        <select name="reminderbydays" id="reminderbydays"
                                                            class="float-right form-control m-0 p-0">
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
                                                                <h6>
                                                                    <button class="btn btn-danger btn-sm"
                                                                        id="reminderinprogressdata">
                                                                        <span>
                                                                            <i class="ri-list-check"></i>
                                                                        </span>In Progress
                                                                    </button>
                                                                </h6>
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
                                                        <h4 class="card-title"><span
                                                                id="reminder_status_title"></span>Upcoming
                                                            Reminders
                                                        </h4>
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

            // Show/hide custom date inputs
            $(document).on('change', '.dateFilter', function() {
                const $block = $(this).closest('.filter-block');
                if ($(this).val() === 'custom') {
                    $block.find('.custom-dates').show();

                    // Set 'To Date' input to today automatically
                    const today = new Date().toISOString().split('T')[0]; // format YYYY-MM-DD
                    $block.find('.toDate').val(today);
                } else {
                    $block.find('.custom-dates').hide();
                }
            });

            // Apply button click handler
            $(document).on('click', '.dateFilterApplyBtn', function() {
                const $block = $(this).closest('.filter-block');
                const dateFilter = $block.find('.dateFilter').val();
                const fromDate = $block.find('.fromDate').val();
                const toDate = $block.find('.toDate').val();
                const fnName = $(this).data('fn');

                // Validation
                if (dateFilter === 'custom') {
                    if (toDate < fromDate) {
                        Toast.fire({
                            icon: "error",
                            title: 'To Date cannot be earlier than From Date.'
                        });
                        return;
                    }
                }

                // Call function from functionMap if exists
                if (fnName == 'fetchDataAndDrawSlowPageCompanyWiseChart') {
                    fetchDataAndDrawSlowPageCompanyWiseChart({
                        date_filter: dateFilter,
                        from_date: fromDate,
                        to_date: toDate
                    });
                } else if (fnName == 'fetchAndDrawDailySlowPagesChart') {
                    fetchAndDrawDailySlowPagesChart({
                        date_filter: dateFilter,
                        from_date: fromDate,
                        to_date: toDate
                    });

                } else {
                    Toast.fire({
                        icon: "error",
                        title: `Function "${fnName}" is not defined.`
                    });
                }
            });

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
                            partpayment = response.part_payment;
                            $('#invoicepartpayment').text(partpayment.length);
                            invoicepartpaymentdata = response.part_payment;
                        }
                        if (response.paid) {
                            paid = response.paid;
                            $('#invoicepaid').text(paid.length);
                            invoicepaiddata = response.paid;
                        }
                        if (response.pending) {
                            pending = response.pending;
                            $('#invoicepending').text(pending.length);
                            invoicependingdata = response.pending;
                            pendingd();
                        }

                        if (response.cancel) {
                            cancel = response.cancel;
                            $('#invoicecancel').text(cancel.length)
                            invoicecanceldata = response.cancel;
                        }
                        if (response.due) {
                            due = response.due;
                            $('#invoicedue').text(due.length)
                            invoiceduedata = response.due;
                        }
                        if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                            loaderhide();
                        }
                    }
                });
            }

            // part payment invoices
            function partpaymentd() {
                $('#invoicedata').html('');
                $('#invoice_status_title').text('part payment');
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
                $('#invoice_status_title').text('paid');
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
                $('#invoice_status_title').text('pending');
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
                $('#invoice_status_title').text('canceld');
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
                $('#invoice_status_title').text('over due');
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

                            // Sort invoicesData by month (ascending order)
                            invoicesData.sort((a, b) => a.month - b.month);

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
                    $('#invoicesbymonths ,#BrokerInvoicesbymonths, #quotationsbymonths').append(`
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


            // quotation dashboard start 

            var quotationaccepteddata = '';
            var quotationpendingdata = '';
            var quotationrejecteddata = '';
            var quotationexpireddata = '';
            var quotationreviseddata = '';


            function totalQuotationCount() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('quotation.totalquotation') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        $('#total_quotation').text(response.quotation);
                    }
                });
            }

            function quotation(month = null) {
                quotationmonth = 'current';
                if (month) {
                    quotationmonth = month;
                }

                // get all quotation
                $.ajax({
                    type: 'GET',
                    url: "{{ route('quotation.status_list') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        quotationmonth: quotationmonth,
                    },
                    success: function(response) {

                        quotationaccepteddata = quotationpendingdata = quotationrejecteddata =
                            quotationexpireddata = quotationreviseddata = '';

                        $('#quotationaccepted').text(0);
                        $('#quotationpending').text(0);
                        $('#quotationrejected').text(0);
                        $('#quotationexpired').text(0);
                        $('#quotationrevised').text(0);


                        if (response == '') {
                            $('#quotationdata').html(
                                "<tr><td colspan='4' class='text-center'>No Data Found</td></tr>");
                        }

                        if (response.accepted) {
                            accepted = response.accepted;
                            $('#quotationaccepted').text(accepted.length);
                            quotationaccepteddata = response.accepted;
                        }

                        if (response.pending) {
                            pending = response.pending;
                            $('#quotationpending').text(pending.length);
                            quotationpendingdata = response.pending;
                            quotationpendingd();
                        }

                        if (response.rejected) {
                            rejected = response.rejected;
                            $('#quotationrejected').text(rejected.length);
                            quotationrejecteddata = response.rejected;
                        }

                        if (response.expired) {
                            expired = response.expired;
                            $('#quotationexpired').text(expired.length)
                            quotationexpireddata = response.expired;
                        }

                        if (response.revised) {
                            revised = response.revised;
                            $('#quotationrevised').text(revised.length)
                            quotationreviseddata = response.revised;
                        }
                        if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                            loaderhide();
                        }
                    }
                });
            }

            // accepted quotations
            function quotationacceptedd() {
                $('#quotationdata').html('');
                $('#quotation_status_title').text('Accepted');
                if (quotationaccepteddata != '') {
                    $.each(quotationaccepteddata, function(key, value) {
                        $('#quotationdata').append(` 
                            <tr>
                                <td>${value.quotation_number}</td>
                                <td>${value.quotation_date_formatted}</td> 
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-success">${(value.status).replace('_', ' ')}</div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#quotationdata').append(`
                        <tr>
                            <td colspan=4>still not any quotation accepted in this month </td>
                        </tr>
                    `);
                }

            }

            // rejected quotations
            function quotationrejectedd() {
                $('#quotationdata').html('');
                $('#quotation_status_title').text('Rejected');
                if (quotationrejecteddata != '') {
                    $.each(quotationrejecteddata, function(key, value) {
                        $('#quotationdata').append(` 
                            <tr>
                                <td>${value.quotation_number}</td>
                                <td>${value.quotation_date_formatted}</td> 
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-success">${value.status}</div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#quotationdata').append(`
                        <tr>
                            <td colspan=4>still not any quotation rejected in this month </td>
                        </tr>
                    `);
                }

            }

            // pending quotations
            function quotationpendingd() {
                $('#quotationdata').html('');
                $('#quotation_status_title').text('Pending Approval');
                if (quotationpendingdata != '') {
                    $.each(quotationpendingdata, function(key, value) {
                        $('#quotationdata').append(` 
                            <tr>
                                <td>${value.quotation_number}</td>
                                <td>${value.quotation_date_formatted}</td> 
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-secondary">${value.status}</div>
                                </td>                                        
                            </tr>
                        `);
                    });
                } else {
                    $('#quotationdata').append(`
                        <tr>
                            <td colspan=4'>No data Found</td>
                        </tr>
                    `);
                }
            }

            // Expired quotations
            function quotationexpiredd() {
                $('#quotationdata').html('');
                $('#quotation_status_title').text('Expired');
                if (quotationexpireddata != '') {
                    $.each(quotationexpireddata, function(key, value) {
                        $('#quotationdata').append(`
                            <tr>
                                <td>${value.quotation_number}</td>
                                <td>${value.quotation_date_formatted}</td>     
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                </td>                                      
                            </tr>
                        `);
                    });
                } else {
                    $('#quotationdata').append(`
                        <tr>
                            <td colspan=4>still not any quotation expired in this month</td>
                        </tr>
                    `);
                }
            }

            // revised quotations
            function quotationrevisedd() {
                $('#quotationdata').html('');
                $('#quotation_status_title').text('Revised');
                if (quotationreviseddata != '') {
                    $.each(quotationreviseddata, function(key, value) {
                        $('#quotationdata').append(`
                            <tr>
                                <td>${value.quotation_number}</td>
                                <td>${value.quotation_date_formatted}</td>     
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                </td>                                      
                            </tr>
                        `);
                    });
                } else {
                    $('#quotationdata').append(`
                        <tr>
                            <td colspan=4>still not any quotation revised in this month</td>
                        </tr>
                    `);
                }
            }

            // Function to fetch data using jQuery Ajax
            function fetchDataAndDrawQuotationChart() {
                $.ajax({
                    url: "{{ route('quotation.chart') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(quotationsData) {
                        // Ensure quotationsData is an array of objects with the expected properties
                        if (Array.isArray(quotationsData) && quotationsData.length > 0 &&
                            quotationsData[
                                0]
                            .hasOwnProperty('month') && quotationsData[0].hasOwnProperty(
                                'total_quotations') && quotationsData[0].hasOwnProperty(
                                'accepted_quotations')
                        ) {
                            // Sort quotationsData by month (ascending order)
                            quotationsData.sort((a, b) => a.month - b.month);

                            const xAxisCategories = quotationsData.map(item => getMonthName(item
                                .month));

                            // Extract data for total and accepted quotations, rainfall, and temperature
                            const totalQuotationsData = quotationsData.map(item => parseInt(item
                                .total_quotations));
                            const paidQuotationsData = quotationsData.map(item => parseInt(item
                                .accepted_quotations));

                            // Chart configuration for displaying monthly quotation counts, accepted quotations, rainfall, and temperature with month names
                            Highcharts.chart("quotation-chart", {
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
                                    name: "Total Quotations",
                                    data: totalQuotationsData,
                                    color: "#fbc647",
                                    type: "column"
                                }, {
                                    name: "Accepted Quotations",
                                    data: paidQuotationsData,
                                    color: "#827af3",
                                    type: "spline",

                                }],
                                credits: {
                                    enabled: false
                                },
                            });
                        } else {

                            document.getElementById("quotation-chart").innerHTML =
                                '<p>You have no quotations to display.</p>';

                            Highcharts.chart("quotation-chart", {
                                credits: {
                                    enabled: false
                                },
                                chart: {
                                    type: "spline",
                                },
                                title: {
                                    text: "You have no quotations"
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
                                    name: "Quotations",
                                    color: "#827af3",
                                    type: "spline",

                                }]
                            });
                            console.error('Invalid data format received:', quotationsData);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }


            $('#quotationaccepteddata').on('click', function() {
                quotationacceptedd();
            });

            $('#quotationpendingdata').on('click', function() {
                quotationpendingd();
            });

            $('#quotationrejecteddata').on('click', function() {
                quotationrejectedd();
            });

            $('#quotationexpireddata').on('click', function() {
                quotationexpiredd();
            });

            $('#quotationreviseddata').on('click', function() {
                quotationrevisedd();
            });

            $('#quotationsbymonths').on('change', function() {
                quotation($(this).val());
            });

            function quotationdashboard() {

                totalQuotationCount(); // call totalquotation count function 

                quotation(); // call quotation function 

                quotationpendingd(); //call pending function when document load

                // Call the function to fetch quotation data and draw the initial chart
                fetchDataAndDrawQuotationChart();
                loaderhide();
            }

            // quotation dashboard end 

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
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
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

                            // Sort reminders by month
                            combinedData.reminders.sort((a, b) => a.month - b.month);

                            // Sort customers by month
                            combinedData.customers.sort((a, b) => a.month - b.month);

                            const xAxisCategories = combinedData['reminders'].map(item =>
                                getMonthName(
                                    item.month));

                            // Extract data for total and paid invoices, rainfall, and temperature
                            const totalRemindersData = combinedData['reminders'].map(item =>
                                parseInt(item.total_reminders));

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
            // reminder dashboard end  

            // lead dashboard
            function fetchDataAndDrawLeadChart() {
                $.ajax({
                    url: "{{ route('lead.chart') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(leadData) {
                        // Ensure leadData is an array of objects with the expected properties
                        if (Array.isArray(leadData) && leadData.length > 0 && leadData[0]
                            .hasOwnProperty('month') && leadData[0].hasOwnProperty('total_leads')) {
                            // Sort leadData by month (ascending order)
                            leadData.sort((a, b) => a.month - b.month);

                            const xAxisCategories = leadData.map(item => getMonthName(item.month));

                            // Extract data for total leads 
                            const totalLeadData = leadData.map(item => parseInt(item.total_leads));

                            // Chart configuration for displaying monthly lead counts with month names
                            Highcharts.chart("lead-monthly-chart", {
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
                                    name: "Total Leads",
                                    data: totalLeadData,
                                    color: "#fbc647",
                                    type: "column"
                                }],
                                credits: {
                                    enabled: false
                                },
                            });
                        } else {

                            document.getElementById("lead-monthly-chart").innerHTML =
                                '<p>You have no leads to display.</p>';

                            Highcharts.chart("lead-monthly-chart", {
                                credits: {
                                    enabled: false
                                },
                                chart: {
                                    type: "spline",
                                },
                                title: {
                                    text: "You have no leads"
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
                                    name: "leads",
                                    color: "#827af3",
                                    type: "spline",

                                }]
                            });
                            console.error('Invalid data format received:', leadData);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }


            function leadPieChart() {
                const chartContainer = $("#lead-pie-chart");
                const totalLeadEl = $("#total_lead");

                if (!chartContainer.length || !totalLeadEl.length) return;

                // Optional: Clear previous chart content
                chartContainer.html("");

                const params = new URLSearchParams({
                    user_id: "{{ session()->get('user_id') }}",
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}"
                });

                fetch("{{ route('lead.piechart') }}?" + params.toString())
                    .then(res => res.json())
                    .then(response => {
                        const apiData = response.lead;

                        if (!Array.isArray(apiData) || apiData.length === 0) {
                            chartContainer.html(
                                `<div style="text-align: center; padding: 2rem; font-weight: bold; color: #888;">No lead data found</div>`
                            );
                            totalLeadEl.text("0");
                            return;
                        }

                        // ✅ Set total leads count
                        totalLeadEl.text(apiData.reduce((sum, item) => sum + (parseInt(item.value) || 0), 0));

                        const colorPalette = [
                            "#827af3", "#b47af3", "#6ce6f4", "#27b345", "#c8c8c8",
                            "#ff9800", "#4caf50", "#f44336", "#9c27b0", "#00bcd4",
                            "#ffc107", "#8bc34a"
                        ];

                        apiData.forEach((item, index) => {
                            item.color = colorPalette[index % colorPalette.length];
                        });

                        const options = {
                            chart: {
                                width: '100%',
                                type: "pie",
                                height: 300
                            },
                            labels: apiData.map(d => d.name),
                            series: apiData.map(d => d.value),
                            colors: apiData.map(d => d.color),
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: "bottom"
                                    }
                                }
                            }]
                        };

                        const chart = new ApexCharts(document.querySelector("#lead-pie-chart"), options);
                        chart.render();
                    })
                    .catch(error => {
                        console.error("Chart data fetch error:", error);
                        chartContainer.html(
                            `<div style="text-align: center; padding: 2rem; color: red;">Error loading lead data</div>`
                        );
                        totalLeadEl.text("0");
                    });
            }

            function leaddashboardhelper() {
                $.ajax({
                    url: "{{ route('lead.leaddashboardhelper') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $('#duefollowup').text(response.lead.due_followups);
                            $('#conversionrate').text(response.lead.conversion_rate);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }

            function newleadcount() {
                loadershow();
                $.ajax({
                    url: "{{ route('lead.newleadcount') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        filter_leadrange: $('#leadrange').val(),
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $('#newleadcount').text(response.newleadcount);
                        }
                        loaderhide();
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error fetching data:', error);
                    }
                });
            }

            $('#leadrange').on('change', newleadcount);

            function leaddashboard() {
                loadershow();
                fetchDataAndDrawLeadChart();
                leadPieChart();
                leaddashboardhelper();
                newleadcount();
                loaderhide();
            }

            //lead dashboard end

            //logisticdashboard start

            function fetchDataAndDrawConsigmentChart() {
                $.ajax({
                    url: "{{ route('consignorcopy.chartdata') }}", // your route to fetch chart data
                    method: "GET",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(data) {
                        const categories = data.map(item => item
                            .date); // e.g., ["2025-07-01", "2025-07-02", ...]
                        const counts = data.map(item => item.total); // e.g., [3, 2, 5, ...]
                        const notes = data.map(item => {
                            if (Array.isArray(item.notes)) {
                                return item.notes.join(', ');
                            } else if (typeof item.notes === 'string') {
                                return item.notes;
                            } else {
                                return ''; // fallback
                            }
                        });

                        Highcharts.chart('consignment-daily-chart', {
                            chart: {
                                type: 'column',
                                scrollablePlotArea: {
                                    minWidth: 1000,
                                    scrollPositionX: 0
                                },
                                events: {
                                    load: function() {
                                        const container = this.renderTo;
                                        container.addEventListener('scroll', () => {
                                            this.pointer.chartPosition = null;
                                            this.tooltip.hide(
                                                0); // hide tooltip when scrolling
                                        }, true);
                                    }
                                }
                            },
                            title: {
                                text: 'Daily Consignments'
                            },
                            xAxis: {
                                categories: categories,
                                title: {
                                    text: 'Date'
                                },
                                labels: {
                                    rotation: -45,
                                    style: {
                                        fontSize: '10px'
                                    }
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Number of Consignments'
                                }
                            },
                            tooltip: {
                                useHTML: true,
                                outside: true,
                                formatter: function() {
                                    return `<strong>Date:</strong> ${this.x}<br/>
                                <strong>Count:</strong> ${this.y}<br/>
                                <strong>Consignment No:</strong> ${notes[this.point.index]}`;
                                },
                                positioner: function(labelWidth, labelHeight, point) {
                                    this.chart.pointer.chartPosition =
                                        null; // important fix
                                    return this.getPosition(labelWidth, labelHeight, point);
                                }
                            },
                            series: [{
                                name: 'Consignments',
                                data: counts,
                                color: '#17a2b8'
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }

            function logisticdashboard() {
                loadershow();
                fetchDataAndDrawConsigmentChart();
                loaderhide();
            }

            //logisticdashboard end

            //developermodule start
            let slowPageCompanyChartInstance;
            let slowPageDailyChartInstance;

            function fetchDataAndDrawSlowPageCompanyWiseChart(params = {}) {
                loadershow();
                // Destroy existing chart instance if exists
                if (slowPageCompanyChartInstance) {
                    slowPageCompanyChartInstance.destroy();
                    slowPageCompanyChartInstance = null;
                }

                $.ajax({
                    url: "{{ route('slowpages.companywisechartdata') }}", // Replace with actual route
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        date_filter: params.date_filter || '1_month',
                        from_date: params.from_date || '',
                        to_date: params.to_date || ''
                    },
                    success: function(data) {
                        if (!Array.isArray(data)) {
                            Toast.fire({
                                icon: "error",
                                title: data.message || "Unexpected error"
                            });
                            loaderhide();
                            return;
                        }

                        const categories = data.map(item => item.company_name);
                        const totalSeconds = data.map(item => parseFloat(item.total_seconds).toFixed(
                            2));
                        const totalCounts = data.map(item => parseInt(item.total_count));

                        slowPageCompanyChartInstance = Highcharts.chart('company-slow-pages-chart', {
                            chart: {
                                zoomType: 'xy'
                            },
                            title: {
                                text: 'Company-wise Slow Page Analysis'
                            },
                            xAxis: [{
                                categories: categories,
                                crosshair: true,
                                labels: {
                                    rotation: -45
                                }
                            }],
                            yAxis: [{ // Primary yAxis for Seconds
                                title: {
                                    text: 'Load Time (Seconds)'
                                }
                            }, { // Secondary yAxis for Count
                                title: {
                                    text: 'Slow Page Count'
                                },
                                opposite: true
                            }],
                            tooltip: {
                                shared: true,
                                useHTML: true,
                                formatter: function() {
                                    return `<strong>${this.x}</strong><br>
                                <span style="color:${this.points[0].color}">\u25CF</span> Load Time: <b>${this.points[0].y} sec</b><br>
                                <span style="color:${this.points[1].color}">\u25CF</span> Count: <b>${this.points[1].y}</b>`;
                                }
                            },
                            series: [{
                                name: 'Load Time (sec)',
                                type: 'spline',
                                data: totalSeconds.map(
                                    Number), // convert string back to number
                                tooltip: {
                                    valueSuffix: ' sec'
                                },
                                yAxis: 0,
                                color: '#17a2b8'
                            }, {
                                name: 'Page Count',
                                type: 'column',
                                data: totalCounts,
                                yAxis: 1,
                                tooltip: {
                                    valueSuffix: ' pages'
                                },
                                color: '#ffc107'
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                        loaderhide();
                    },
                    error: function(xhr) {
                        loaderhide();
                        console.error("Failed to fetch chart data:", xhr.responseText);
                    }
                });
            }

            function fetchAndDrawDailySlowPagesChart(params = {}) {
                loadershow();

                // Destroy existing chart instance if exists
                if (slowPageDailyChartInstance) {
                    slowPageDailyChartInstance.destroy();
                    slowPageDailyChartInstance = null;
                }

                $.ajax({
                    url: '{{ route('slowpages.dailyreport') }}',
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} ",
                        date_filter: params.date_filter || '1_month',
                        from_date: params.from_date,
                        to_date: params.to_date
                    },
                    success: function(data) {
                        if (!Array.isArray(data)) {
                            Toast.fire({
                                icon: "error",
                                title: data.message || "Unexpected error"
                            });
                            loaderhide();
                            return;
                        }

                        const dates = data.map(item => item.date);
                        const avgSecs = data.map(item => item.avg_seconds);
                        const totalLogs = data.map(item => item.total_logs);

                        slowPageDailyChartInstance = Highcharts.chart('daily-slow-pages-chart', {
                            chart: {
                                type: 'column',
                                zoomType: 'xy'
                            },
                            title: {
                                text: 'Daily Slow Pages Report'
                            },
                            xAxis: {
                                categories: dates,
                                title: {
                                    text: 'Date'
                                }
                            },
                            yAxis: [{
                                title: {
                                    text: 'Avg Load Time (s)'
                                }
                            }, {
                                title: {
                                    text: 'Log Count'
                                },
                                opposite: true
                            }],
                            series: [{
                                    name: 'Avg Load Time',
                                    type: 'spline',
                                    data: avgSecs,
                                    yAxis: 0,
                                    tooltip: {
                                        valueSuffix: ' s'
                                    }
                                },
                                {
                                    name: 'Log Count',
                                    type: 'column',
                                    data: totalLogs,
                                    yAxis: 1
                                }
                            ],
                            credits: {
                                enabled: false
                            }
                        });
                    },
                    error: function() {
                        toast.info('Error loading daily report.');
                    },
                    complete: loaderhide
                });
            }

            function developerdashboard() {
                loadershow();
                fetchDataAndDrawSlowPageCompanyWiseChart();
                fetchAndDrawDailySlowPagesChart();
                loaderhide();
            }
                  //developermodule end
                  // admin Dashboard start
            function fetchAdminDashboardData() {
                $.ajax({
                    url: "{{ route('admindeshbord') }}", // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(adminData) {
                        let companyData = adminData.admindashboard.companyData;

                        let companies = [];
                        let totalUsers = [];
                        let loggedInUsers = [];

                        companyData.forEach(item => {
                            let index = companies.indexOf(item.company_name);

                            if (index === -1) {
                                // New company, add to arrays
                                companies.push(item.company_name);
                                totalUsers.push(item.total_users);
                                loggedInUsers.push(item.user_login);
                            } else {
                                // Existing company, add to totals
                                totalUsers[index] += item.total_users;
                                loggedInUsers[index] += item.user_login;
                            }
                        });

                        console.log(companies, totalUsers, loggedInUsers);
                        Highcharts.chart("admin-chart", {
                            chart: {
                                type: "line" // base chart type
                            },
                            title: {
                                text: "Company Users & Logged-in Users"
                            },
                            xAxis: {
                                categories: companies,
                                crosshair: true
                            },
                            yAxis: {
                                title: {
                                    text: "Number of Users"
                                }
                            },
                            series: [{
                                    name: "Total Users",
                                    data: totalUsers,
                                    color: "#fbc647", // yellow/orange
                                    type: "column" // column bars
                                },
                                {
                                    name: "Logged-in Users",
                                    data: loggedInUsers,
                                    color: "#827af3", // purple/blue
                                    type: "spline" // line
                                }
                            ],
                            tooltip: {
                                enabled: true,

                            },
                            credits: {
                                enabled: false
                            }
                        });



                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }

            function admindashboard() {
                loadershow();
                fetchAdminDashboardData();
                loaderhide();
            }
            // Admin Dashboard End
         


      
                // Order Dashboard start
               function totalorderCount() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('order.totalorder') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        $('#total_order').text(response.order);
                    }
                });
            }



            function fetchOrderChart(month = 'all') {
                $.ajax({
                    url: "{{ route('order.chart') }}",
                    method: "GET",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        month
                    },
                    success: function(data) {
                        if (!data || !data.length) {
                            $('#Order-chart').html(`
                                    <div class="no-data-message">
                                        No data available
                                    </div>
                                `);
                            return;
                        }

                        let categories = [];
                        let totalOrders = [];
                        let totalKg = [];
                        let totalAmount = [];

                        const hasBuyer = data[0].hasOwnProperty('buyer_party');
                        const hasMonth = data[0].hasOwnProperty('month');

                        if (hasMonth && !hasBuyer) {
                            categories = data.map(d => getMonthName(d.month));
                        } else if (hasBuyer && !hasMonth) {
                            categories = data.map(d => d.buyer_party); // map ID → name if needed
                        } else if (hasBuyer && hasMonth) {
                            categories = data.map(d => `${d.buyer_party} - ${getMonthName(d.month)}`);
                        } else {
                            categories = ['Summary'];
                        }

                        totalOrders = data.map(d => Number(d.total_orders) || 0);
                        totalKg = data.map(d => Number(d.total_kg) || 0);
                        totalAmount = data.map(d => Number(d.total_amount) || 0);

                        Highcharts.chart('Order-chart', {
                            chart: {
                                zoomType: 'xy'
                            },
                            title: {
                                text: ' '
                            },
                            xAxis: [{
                                categories,
                                crosshair: true
                            }],
                            yAxis: [{ // Primary Y axis for Orders
                                    labels: {
                                        format: '{value}',
                                        style: {
                                            color: '#fbc647'
                                        }
                                    },
                                    title: {
                                        text: 'Total Orders',
                                        style: {
                                            color: '#fbc647'
                                        }
                                    }
                                },
                                {
                                    title: {
                                        text: 'Amount (₹)',
                                        style: {
                                            color: '#827af3'
                                        }
                                    },
                                    labels: {
                                        formatter: function() {
                                            return '₹ ' + Number(this.value)
                                                .toLocaleString('en-IN');
                                        },
                                        style: {
                                            color: '#827af3'
                                        }
                                    },
                                    opposite: true
                                }

                            ],
                            tooltip: {
                                shared: true,
                                formatter: function() {
                                    let s = '<b>' + this.x + '</b>';

                                    this.points.forEach(point => {
                                        if (point.series.name === 'Total Amount') {
                                            s += '<br/>' + point.series.name +
                                                ': ₹ ' +
                                                Number(point.y).toLocaleString(
                                                    'en-IN');
                                        } else {
                                            s += '<br/>' + point.series.name +
                                                ': ' +
                                                point.y.toLocaleString('en-IN');
                                        }
                                    });

                                    return s;
                                }
                            },

                            series: [{
                                    name: 'Total Orders',
                                    type: 'column',
                                    data: totalOrders,
                                    color: '#fbc647',
                                    tooltip: {
                                        valueSuffix: ' orders'
                                    }
                                },
                                {
                                    name: 'Total Amount',
                                    type: 'spline',
                                    yAxis: 1,
                                    data: totalAmount,
                                    color: '#28a745',
                                    tooltip: {
                                        valuePrefix: '₹'
                                    }
                                }
                            ],
                            credits: {
                                enabled: false
                            }
                        });
                    },
                    error: function(err) {
                        console.error('Error fetching chart data', err);
                        $('#Order-chart').html('<p>Error loading data.</p>');
                    }
                });
            }

            // Helper

            $('#monthSelect').on('change', function() {
                let selectedMonth = $(this).val() || 'all';
                fetchOrderChart(selectedMonth);
            });
            $('#monthSelect').select2({
                placeholder: "Select Month",
                allowClear: true,
                width: '100%'
            });

            // Initial load (optional)


            function Orderdashboard() {
                fetchOrderChart('all');
                totalorderCount();
            }
            // Order Dashboard End

            // Broker Bill Dashboard start
            function totalBrokerInvoiceCount() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokrage.totalinvoice') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}"
                    },
                    success: function(response) {
                        $('#total_bro_inv').text(response.invoice);
                    }
                });
            }

            function Brokerinvoice(month = null) {
                invoicemonth = 'current';
                if (month) {
                    invoicemonth = month;
                }

                // get all invoice
                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokrage.status_list') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        invoicemonth: invoicemonth,
                    },
                    success: function(response) {

                        BrokerInvoicepartpaymentdata = BrokerInvoicepaiddata =
                            BrokerInvoicependingdata =
                            BrokerInvoicecanceldata = BrokerInvoiceduedata = '';

                        $('#BrokerInvoicepartpayment').text(0);
                        $('#BrokerInvoicepaid').text(0);
                        $('#BrokerInvoicepending').text(0);
                        $('#BrokerInvoicecancel').text(0);
                        $('#BrokerInvoicedue').text(0);


                        if (response == '') {
                            $('#BrokerInvoicedata').html(
                                "<tr><td colspan='4' class='text-center'>No Data Found</td></tr>");
                        }

                        if (response.part_payment) {
                            partpayment = response.part_payment;
                            $('#BrokerInvoicepartpayment').text(partpayment.length);
                            BrokerInvoicepartpaymentdata = response.part_payment;

                        }
                        if (response.paid) {
                            paid = response.paid;
                            $('#BrokerInvoicepaid').text(paid.length);
                            BrokerInvoicepaiddata = response.paid;
                        }
                        if (response.pending) {
                            pending = response.pending;
                            $('#BrokerInvoicepending').text(pending.length);
                            BrokerInvoicependingdata = response.pending;
                            Brokerpendingd();
                        }

                        if (response.cancel) {
                            cancel = response.cancel;
                            $('#BrokerInvoicecancel').text(cancel.length)
                            BrokerInvoicecanceldata = response.cancel;
                        }

                        if (response.due) {
                            due = response.due;
                            $('#BrokerInvoicedue').text(due.length)
                            BrokerInvoiceduedata = response.due;

                        }
                        if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                            loaderhide();
                        }
                    }
                });
            }

            function Brokerpendingd() {
                $('#BrokerInvoicedata').html('');
                $('#BrokerInvoice_status_title').text('Pending');
                if (BrokerInvoicependingdata != '') {

                    $.each(BrokerInvoicependingdata, function(key, value) {

                        $('#BrokerInvoicedata').append(` 
                            <tr>
                                <td>${value.invoice_no}</td>
                                <td>${value.inv_date_formatted}</td>
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-secondary">${value.status}</div>
                                </td>                                        
                            </tr>
                        `);
                    });
                } else {
                    $('#BrokerInvoicedata').append(`
                        <tr>
                            <td colspan=4'>No data Found</td>
                        </tr>
                    `);
                }
            }

            function Brokerpartpaymentd() {
                $('#BrokerInvoicedata').html('');
                $('#BrokerInvoice_status_title').text('Part Payment');
                if (BrokerInvoicepartpaymentdata != '') {
                    $.each(BrokerInvoicepartpaymentdata, function(key, value) {
                        $('#BrokerInvoicedata').append(` 
                            <tr>
                                <td>${value.invoice_no}</td>
                                <td>${value.inv_date_formatted}</td>
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-success">${(value.status).replace('_',' ')}</div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#BrokerInvoicedata').append(`
                        <tr>
                            <td colspan=4>still not any Brokrage Bill status part payment in this month </td>
                        </tr>
                    `);
                }

            }

            function Brokerpaidd() {
                $('#BrokerInvoicedata').html('');
                $('#BrokerInvoice_status_title').text('Paid');
                if (BrokerInvoicepaiddata != '') {
                    $.each(BrokerInvoicepaiddata, function(key, value) {
                        $('#BrokerInvoicedata').append(` 
                            <tr>
                                <td>${value.invoice_no}</td>
                                <td>${value.inv_date_formatted}</td>
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-success">${value.status}</div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#BrokerInvoicedata').append(`
                        <tr>
                            <td colspan=4>still not any Brokrage Bill paid in this month </td>
                        </tr>
                    `)
                }

            }



            // cancled BrokerInvoices
            function Brokercanceld() {
                $('#BrokerInvoicedata').html('');
                $('#BrokerInvoice_status_title').text('Cancel');
                if (BrokerInvoicecanceldata != '') {
                    $.each(BrokerInvoicecanceldata, function(key, value) {
                        $('#BrokerInvoicedata').append(`
                            <tr>
                                <td>${value.invoice_no}</td>
                                <td>${value.inv_date_formatted}</td>                                        
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                </td>                                      
                            </tr>
                        `);
                    });
                } else {
                    $('#BrokerInvoicedata').append(`
                        <tr>
                            <td colspan=4>still not any Brokrage Bill cancel in this month</td>
                        </tr>
                    `);
                }
            }

            // overdue BrokerInvoices
            function Brokerdued() {
                $('#BrokerInvoicedata').html('');
                $('#BrokerInvoice_status_title').text('Over due');
                if (BrokerInvoiceduedata != '') {
                    $.each(BrokerInvoiceduedata, function(key, value) {
                        $('#BrokerInvoicedata').append(`
                            <tr>
                                <td>${value.invoice_no}</td>
                                <td>${value.inv_date_formatted}</td>                                        
                                <td class="text-right">${value.grand_total}</td>
                                <td class="text-center">
                                    <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                </td>                                      
                            </tr>
                        `);
                    });
                } else {
                    $('#BrokerInvoicedata').append(`
                        <tr>
                            <td colspan=4>still not any Brokrage Bill overdue in this month</td>
                        </tr>
                    `);
                }
            }

            function fetchDataAndDrawBrokerInvoiceChart() {
                $.ajax({
                    url: "{{ route('brokrage.chart') }}", // Replace this with your Laravel backend endpoint
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

                            // Sort invoicesData by month (ascending order)
                            invoicesData.sort((a, b) => a.month - b.month);

                            const xAxisCategories = invoicesData.map(item => getMonthName(item
                                .month));

                            // Extract data for total and paid invoices, rainfall, and temperature
                            const totalInvoicesData = invoicesData.map(item => parseInt(item
                                .total_invoices));
                            const paidInvoicesData = invoicesData.map(item => parseInt(item
                                .paid_invoices));

                            // Chart configuration for displaying monthly invoice counts, paid invoices, rainfall, and temperature with month names
                            Highcharts.chart("BrokerInvoice-chart", {
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

                            document.getElementById("BrokerInvoice-chart").innerHTML =
                                '<p>You have no invoices to display.</p>';

                            Highcharts.chart("BrokerInvoice-chart", {
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

            $('#BrokerInvoicepartpaymentdata').on('click', function() {
                Brokerpartpaymentd();
            });
            $('#BrokerInvoicepaiddata').on('click', function() {
                Brokerpaidd();
            });
            $('#BrokerInvoicependingdata').on('click', function() {
                Brokerpendingd();
            });
            $('#BrokerInvoicecanceldata').on('click', function() {
                Brokercanceld();
            });
            $('#BrokerInvoiceduedata').on('click', function() {
                Brokerdued();
            });

            $('#BrokerInvoicesbymonths').on('change', function() {
                Brokerinvoice($(this).val());
            });

            function BrokerInvoicedashboard() {
                // // Call the function to fetch invoice data and draw the initial chart
                fetchDataAndDrawBrokerInvoiceChart();
                totalBrokerInvoiceCount(); // call totalBrokerInvoiceCount function 

                Brokerinvoice(); // call Brokerinvoice function 

                Brokerpendingd();

                loaderhide();
            }
            // Broker Bill Dashboard End 
            @if (session('menu') == 'admin')
                admindashboard();
            @endif
            @if (session('menu') == 'tea')
                invoicedashboard();
            @endif
            @if (session('menu') == 'Order')
                Orderdashboard();
            @endif
            @if (session('menu') == 'BrokerInvoice')
                BrokerInvoicedashboard();
            @endif
            @if (session('menu') == 'quotation')
                quotationdashboard();
            @endif
            @if (session('menu') == 'lead')
                leaddashboard();
            @endif
            @if (session('menu') == 'logistic')
                logisticdashboard();
            @endif
            @if (session('menu') == 'reminder')
                reminderdashboard();
            @endif
            @if (session('menu') == 'developer')
                developerdashboard();
            @endif

            loaderhide();

            $(document).on('click', '.dynamicdashboard', function() {
                var currentdashboard = $(this).data('dashboard');
                eval(currentdashboard + 'dashboard()');
            });
        });
    </script>
@endpush
