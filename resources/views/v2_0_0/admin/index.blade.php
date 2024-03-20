@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.masterpage')
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
    </style>
@endsection
@section('page-content')
    <div id="content-page" class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-lg-7">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Invoice Status Chart</h4>
                            </div>
                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ri-more-fill"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a>
                                        <a class="dropdown-item" href="#"><i
                                                class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                        <a class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                        <a class="dropdown-item" href="#"><i
                                                class="ri-printer-fill mr-2"></i>Print</a>
                                        <a class="dropdown-item" href="#"><i
                                                class="ri-file-download-fill mr-2"></i>Download</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div id="invoice-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height" style="background: transparent;">
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
                                        <h6><button class="btn btn-success btn-sm" id='paidata'><span><i
                                                        class="ri-list-check"></i></span>PAID</button></h6>
                                    </div>
                                    <div class="profile-icon iq-bg-success"><span id="paid">0</span></div>
                                </li>
                                <li class="d-flex mb-4 align-items-center">
                                    <div class="media-support-info ml-3">
                                        <h6><button class="btn btn-secondary btn-sm" id="pendingdata"><span><i
                                                        class="ri-list-check"></i></span>PENDING</button></h6>
                                    </div>
                                    <div class="profile-icon iq-bg-secondary"><span id="pending">0</span></div>
                                </li>
                                <li class="d-flex mb-4 align-items-center">
                                    <div class="media-support-info ml-3">
                                        <h6><button class="btn btn-danger btn-sm" id="canceldata"><span><i
                                                        class="ri-list-check"></i></span>CANCEL</button></h6>
                                    </div>
                                    <div class="profile-icon iq-bg-danger"><span id="cancel">0</span></div>
                                </li>
                                <li class="d-flex mb-4 align-items-center">
                                    <div class="media-support-info ml-3">
                                        <h6><button class="btn btn-warning btn-sm" id="duedata"><span><i
                                                        class="ri-list-check"></i></span>OVER DUE</button></h6>
                                    </div>
                                    <div class="profile-icon iq-bg-warning"><span id="due">0</span></div>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title"><span id="status_title"></span> Invoices </h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <div class="table-responsive scrollable-table" style="width: 100%">
                                <table class="table mb-0  table-borderless w-100" width="100%" style="text-align: center">
                                    <thead>
                                        <tr>
                                            <th scope="col">Invoice</th>
                                            <th scope="col">Date</th>
                                            <th scope="col" class="text-right">Amount</th>
                                            <th scope="col" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="data">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- Page Content  -->

@push('ajax')
    <script>
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var paiddata = '';
            var pendingdata = '';
            var canceldata = '';
            var duedata = '';
            // get all invoice
            $.ajax({
                type: 'get',
                url: '{{ route('invoice.status_list') }}',
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    token: "{{ session()->get('api_token') }}",
                    company_id: "{{ session()->get('company_id') }}"
                },
                success: function(response) {
                    var totalinv = 0;
                    if (response.paid) {
                        paid = response.paid;
                        totalinv += parseInt(paid.length);
                        $('#paid').text(paid.length)
                        paiddata = response.paid;
                    }
                    if (response.pending) {
                        pending = response.pending;
                        totalinv = parseInt(totalinv) + parseInt(pending.length);
                        $('#pending').text(pending.length)
                        pendingdata = response.pending
                        pendingd();
                    }

                    if (response.cancel) {
                        cancel = response.cancel;
                        totalinv = parseInt(totalinv) + parseInt(cancel.length);
                        $('#cancel').text(cancel.length)
                        canceldata = response.cancel;
                    }

                    if (response.due) {
                        due = response.due;
                        totalinv = parseInt(totalinv) + parseInt(due.length);
                        $('#due').text(due.length)
                        duedata = response.due;
                    }

                    if (totalinv != '') {
                        $('#total_inv').text(totalinv);
                    }
                    if (response.status == 500) {
                        toastr.error(response.message);
                        loaderhide();
                    }
                }
            });
            
            // paid invoices
            function paidd() {
                $('#data').html('');
                $('#status_title').text('paid');
                if (paiddata != '') {
                    $.each(paiddata, function(key, value) {
                        $('#data').append(` <tr>
                                            <td>${value.inv_no}</td>
                                            <td>${value.inv_date}</td>
                                            <td class="text-right">${value.grand_total}</td>
                                            <td class="text-center">
                                                <div class="badge badge-pill iq-bg-success">${value.status}</div>
                                            </td>
                                        </tr>`);
                    });
                } else {
                    $('#data').append(`<tr><td colspan=6>still not any invoice paid in this month </td></tr>`)
                }

            }
            // pending invoices
            function pendingd() {
                $('#data').html('');
                $('#status_title').text('pending');
                if (pendingdata != '') {

                    $.each(pendingdata, function(key, value) {
                        $('#data').append(` <tr>
                                            <td>${value.inv_no}</td>
                                            <td>${value.inv_date}</td>
                                            <td class="text-right">${value.grand_total}</td>
                                            <td class="text-center">
                                                <div class="badge badge-pill iq-bg-secondary">${value.status}</div>
                                            </td>                                        
                                        </tr>`);
                    });
                } else {
                    $('#data').append(`<tr><td colspan=6'>No data Found</td></tr>`);
                }
            }
            //call pending function when document load
            pendingd();
             
            // cancled invoices
            function canceld() {
                $('#data').html('');
                $('#status_title').text('canceld');
                if (canceldata != '') {
                    $.each(canceldata, function(key, value) {
                        $('#data').append(` <tr>
                                            <td>${value.inv_no}</td>
                                            <td>${value.inv_date}</td>                                        
                                            <td class="text-right">${value.grand_total}</td>
                                            <td class="text-center">
                                                <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                            </td>                                      
                                        </tr>`);
                    });
                } else {
                    $('#data').append(`<tr><td colspan=6>still not any invoice cancel in this month</td></tr>`)
                }
            }
            
            // overdue invoices
            function dued() {
                $('#data').html('');
                $('#status_title').text('over due');
                if (duedata != '') {
                    $.each(duedata, function(key, value) {
                        $('#data').append(` <tr>
                                            <td>${value.inv_no}</td>
                                            <td>${value.inv_date}</td>                                        
                                            <td class="text-right">${value.grand_total}</td>
                                            <td class="text-center">
                                                <div class="badge badge-pill iq-bg-warning">${value.status}</div>
                                            </td>                                      
                                        </tr>`);
                    });
                } else {
                    $('#data').append(`<tr><td colspan=6>still not any invoice overdue in this month</td></tr>`)
                }
            }

            $('#paidata').on('click', function() {
                paidd();
            });
            $('#pendingdata').on('click', function() {
                pendingd();
            });
            $('#canceldata').on('click', function() {
                canceld();
            });
            $('#duedata').on('click', function() {
                dued();
            });


            // Function to map month numbers to month names
            function getMonthName(monthNumber) {
                const months = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                return months[monthNumber - 1];
            }

            // Function to fetch data using jQuery Ajax
            function fetchDataAndDrawChart() {
                $.ajax({
                    url: '/api/chart', // Replace this with your Laravel backend endpoint
                    method: 'GET',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(invoicesData) {
                        // Ensure invoicesData is an array of objects with the expected properties
                        if (Array.isArray(invoicesData) && invoicesData.length > 0 && invoicesData[0]
                            .hasOwnProperty('month') && invoicesData[0].hasOwnProperty(
                                'total_invoices') && invoicesData[0].hasOwnProperty('paid_invoices')) {
                            const xAxisCategories = invoicesData.map(item => getMonthName(item.month));

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

                                }]
                            });
                        } else {

                            document.getElementById("invoice-chart").innerHTML =
                                '<p>You have no invoices to display.</p>';

                            Highcharts.chart("invoice-chart", {
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

            // Call the function to fetch data and draw the initial chart
            fetchDataAndDrawChart();
            loaderhide();
        });
    </script>
@endpush
