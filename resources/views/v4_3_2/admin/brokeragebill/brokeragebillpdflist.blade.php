@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Broker Bill Pdf List
@endsection
@section('table_title')
    Broker Bill Pdf
@endsection

@section('style')
    <style>
        .ui-widget-header {
            background: #1518b117 !important;
            border: 1px solid #e1ded9 !important;
        }

        .btn-info {
            background-color: #253566 !important;
            border-color: #253566 !important;
            color: white;
        }

        .btn-info:hover {
            background-color: #39519b !important;
            color: rgb(255, 255, 255);
        }

        .btn-success {
            background-color: #67d5a5d9 !important;
            border-color: var(--iq-success) !important;
            color: black !important;
        }

        .btn-success:hover {
            background-color: #16d07ffa !important;
            border-color: var(--iq-success) !important;
            color: rgb(250, 250, 250) !important;
        }
    </style>
@endsection
{{-- @if (session('user_permissions.teamodule.brokeragebill.add') == '1')
    @section('addnew')
        {{ route('admin.brokeragebillform') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New brokerage bill"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif --}}
@section('table-content')
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Invoice No </th>
                <th>Invoice Date </th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Invoice</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
    <div class="modal fade" id="paymentmodal" tabindex="-1" role="dialog" aria-labelledby="viewpaymentmodalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewpaymentmodalTitle"><b>Payment</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="paymentform">
                    <div class="modal-body">
                        @csrf
                        <div class="payment_details">
                            <input type="hidden" name="user_id" class="form-control" value="{{ session('user_id') }}"
                                required />
                            <input type="hidden" name="company_id" class="form-control" value="{{ session('company_id') }}"
                                required />
                            <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                                placeholder="token" required />
                            <input type="hidden" name="inv_id" id="inv_id">
                            <label for="transid">Transaction ID</label>
                            <input type="text" name="transid" class="form-control" id="transid"
                                placeholder="Transaction id" />
                            <p class="modal_error-msg mb-1" id="error-transid" style="color: red"></p>
                            <label for="payment_date">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" id="payment_date" required />
                            <p class="modal_error-msg mb-1" id="error-payment_date" style="color: red"></p>
                            Total Amount :-&nbsp;<span class="mb-1 text-info" id="info-total_amount">0</span>,
                            &nbsp;Received Amount :-&nbsp;<span class="mb-1 text-info"
                                id="info-total_received_amount">0</span><br>
                            <label for="paidamount">New Amount</label>
                            <input type="number" name="paidamount" class="form-control" id="paidamount"
                                placeholder="New Amount" required />
                            <p class="modal_error-msg mb-1" id="error-paidamount" style="color: red"></p>
                            Pending Amount :-&nbsp;<span class="mb-1 text-info info-pending_amount">0</span><br>
                            <label for="paid_by">Paid By</label>
                            <input type="text" name="paid_by" class="form-control" id="paid_by"
                                placeholder="Who Paid Amount" />
                            <p class="modal_error-msg mb-1" id="error-paid_by" style="color: red"></p>
                            <label for="payment_type">How They Paid</label>
                            <select class="form-control" name="payment_type" id="payment_type">
                                <option selected="" disabled="">Select Payment Type</option>
                                <option value="Online Payment">Online Payment</option>
                                <option value="Cash">Cash</option>
                                <option value="Check">Check</option>
                            </select>
                            <p class="modal_error-msg mb-1" id="error-payment_type" style="color: red"></p>
                        </div>
                        <div class="tds_details">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="tds_applicable"
                                    id="tds_applicable">
                                <label class="form-check-label" for="tds_applicable">
                                    TDS Applicable
                                </label>
                                <p class="modal_error-msg mb-1" id="error-tds_applicable" style="color: red"></p>
                            </div>
                            <div class="tds_inputs" style="display: none">
                                <hr>
                                <label for="tds_amount">TDS Amount</label>
                                <input type="number" name="tds_amount" class="form-control" id="tds_amount"
                                    placeholder="TDS Amount" />
                                <p class="modal_error-msg mb-1" id="error-tds_amount" style="color: red"></p>
                                Pending Amount :-&nbsp;<span class="mb-1 text-info info-pending_amount">0</span><br>
                                <label for="challan_no">Challan No</label>
                                <input type="text" name="challan_no" class="form-control" id="challan_no"
                                    placeholder="Challan No" />
                                <p class="modal_error-msg mb-1" id="error-challan_no" style="color: red"></p>
                                <label for="status">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option selected="" disabled="">Select Status</option>
                                    <option value="Recorded">Recorded</option>
                                    <option value="Mapped to Challan">Mapped to Challan</option>
                                    <option value="Filed in Return">Filed in Return</option>
                                    <option value="Reconciled (matches 26AS)"> Reconciled (matches 26AS)</option>
                                </select>
                                <p class="modal_error-msg mb-1" id="error-status" style="color: red"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn iq-bg-danger">Reset</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
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

            var global_response = '';

            // function for  get brokerpurchases data and set it into datatable
            function loaddata() {
                table = $('#data').DataTable({
                    language: {
                        lengthMenu: '_MENU_ &nbsp;Entries per page'
                    },
                    destroy: true, // allows re-initialization
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('brokeragebill.brokeragebillpdflist') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Something went wrong!'
                                })
                            }

                            global_response = json;

                            return json.data;
                        },
                        complete: function() {
                            loaderhide();
                        },
                        error: function(xhr) {
                            global_response = '';
                            console.log(xhr.responseText);
                            Toast.fire({
                                icon: "error",
                                title: "Error loading data"
                            });
                        }
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'id'
                        },
                        {
                            data: 'invoice_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'invoice_no'
                        },
                        {
                            data: 'invoice_date',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'invoice_date'
                        },
                        {
                            data: 'totalamount',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'totalamount'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                actions = '-';
                                @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                                    options = '';
                                    if (row.part_payment == 1 && row.pending_amount != 0) {
                                        options = ` 
                                            <option value='part_payment' ${row.status == "part_payment" ? 'selected' : ''}>Part Payment</option>
                                            <option value='paid' ${row.status == "paid" ? 'selected' : ''} disabled>Paid</option>
                                            <option value='pending' ${row.status == "pending" ? 'selected' : ''} disabled>Pending</option>
                                        `;
                                    }
                                    if (row.pending_amount == 0) {
                                        options = `
                                            <option value='part_payment' ${row.status == "part_payment" ? 'selected' : ''} disabled>Part Payment</option>
                                            <option value='paid' ${row.status == "paid" ? 'selected' : ''}> Paid</option>
                                            <option value='pending' ${row.status == "pending" ? 'selected' : ''} disabled>Pending</option>
                                        `;
                                    }

                                    if (row.part_payment != 1 && row.part_payment != 0) {
                                        options = `
                                            <option value='part_payment' ${row.status == "part_payment" ? 'selected' : ''} disabled>Part Payment</option>
                                            <option value='paid' ${row.status == "paid" ? 'selected' : ''} disabled> Paid</option>
                                            <option value='pending' ${row.status == "pending" ? 'selected' : ''}>Pending</option>
                                        `;
                                    }
                                    actions = `  
                                        <select data-status='${row.id}' data-original-value="${row.status}" class="status" id="status_${row.id}" name="" required >
                                            ${options}
                                            <option value='cancel' ${row.status == "cancel" ? 'selected' : ''}>Cancel</option>
                                            <option value='due' ${row.status == "due" ? 'selected' : ''}>Over Due</option>
                                        </select>
                                    `;
                                @endif

                                return actions;

                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = ''
                                @if (session('user_permissions.teamodule.brokeragebill.view') == '1')
                                    let generatePdfUrl =
                                        "{{ route('brokragbill.generatebrokragebillpdf', '__gardenId__') }}"
                                        .replace('__gardenId__', data);
                                    actionBtns += `                                             
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Download Pdf">
                                            <a href=${generatePdfUrl} target='_blank' id='pdf'>
                                                <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0" ><i class="ri-download-line"></i></button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                return actionBtns;
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let generateInvoiceReceiptAllUrl =
                                    "{{ route('invoice.brokerBillgeneraterecieptall', '__invoiceId__') }}"
                                    .replace('__invoiceId__', row.id);
                                actions = '';
                                if (row.status != 'paid') {
                                    actions += `                                             
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Pay">
                                            <button data-toggle="modal" data-target="#paymentmodal" data-amount="${row.grand_total}" data-id='${row.id}' class='btn btn-sm btn-primary m-0 paymentformmodal'>
                                                <i class='ri-paypal-fill'></i>
                                            </button>
                                        </span>
                                    `;
                                }
                                if (row.part_payment == 1 && row.status == 'paid' && row
                                    .pending_amount == 0) {
                                    actions += `                                             
                                        <span> 
                                            <a href=${generateInvoiceReceiptAllUrl} target='_blank'>
                                                <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Combined Receipt"  class="reciept-btn btn btn-primary btn-rounded btn-sm m-0" >
                                                    <i class="ri-download-line"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                }
                                if (row.part_payment == 1) {
                                    actions += `                                             
                                        <span data-toggle="tooltip" data-placement="right" data-original-title="View All Reciept"> 
                                            <button  data-id='${row.id}' data-toggle='modal' data-target='#exampleModalScrollable' class='btn btn-sm btn-info my-0 viewpayment' >
                                                <i class='ri-eye-fill'></i> 
                                            </button> 
                                        </span>
                                    `;
                                }
                                if (row.part_payment == 0 && row.status == 'paid') {
                                    actions += `                                             
                                        <span> 
                                            <a href=${generateInvoiceReceiptAllUrl}  target='_blank' >
                                                <button  class="btn-info reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" data-toggle="tooltip" data-placement="bottom" data-original-title="Download Single Receipt">
                                                    <i class="ri-download-line"></i>
                                                </button>
                                            </a>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="right" data-original-title="Delete Payment Entry">
                                            <button data-id="${row.paymentid}" data-inv-id="${row.id}" class="btn btn-sm btn-outline-danger pay-del-btn">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </span>    
                                    `;
                                }
                                return actions;
                            }
                        },
                    ],

                    pagingType: "full_numbers",
                    drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip({
                            boundary: 'window',
                            offset: '0, 10' // Push tooltip slightly away from the button
                        });

                        // 👇 Jump to Page input injection
                        if ($('#jumpToPageWrapper').length === 0) {
                            let jumpHtml = `
                                    <div id="jumpToPageWrapper" class="d-flex align-items-center ml-3" style="gap: 5px;">
                                        <label for="jumpToPage" class="mb-0">Jump to page:</label>
                                        <input type="number" id="jumpToPage" min="1" class="dt-input" style="width: 80px;" />
                                        <button id="jumpToPageBtn" class="btn btn-sm btn-primary">Go</button>
                                    </div>
                                `;
                            $(".dt-paging").after(jumpHtml);
                        }


                        $(document).off('click', '#jumpToPageBtn').on('click', '#jumpToPageBtn',
                            function() {
                                let table = $('#data').DataTable();
                                // Check if table is initialized
                                if ($.fn.DataTable.isDataTable('#data')) {
                                    let page = parseInt($('#jumpToPage').val());
                                    let totalPages = table.page.info().pages;

                                    if (!isNaN(page) && page > 0 && page <= totalPages) {
                                        table.page(page - 1).draw('page');
                                    } else {
                                        Toast.fire({
                                            icon: "error",
                                            title: `Please enter a page number between 1 and ${totalPages}`
                                        });
                                    }
                                } else {

                                    Toast.fire({
                                        icon: "error",
                                        title: `DataTable not yet initialized.`
                                    });
                                }
                            }
                        );
                    }
                });

            }
            $(document).on("click", ".pay-del-btn", function() {
                var deleteid = $(this).data('id');
                var invId = $(this).data('inv-id');
                let invPaymentDltUrl = "{{ route('broker_paymentdetails.deletepayment', '__deleteId__') }}"
                    .replace(
                        '__deleteId__', deleteid);
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this payment record ?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: invPaymentDltUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message ||
                                            "succesfully deleted"
                                    });
                                    viewpayment(invId);
                                    table.draw();
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message ||
                                            "something went wrong!"
                                    });
                                }
                                loaderhide();
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    }
                );
            });

            function statuschange(id, value) {
                loadershow();
                let invoiceStatusUrl = "{{ route('broker_paymentdetail_.status', '__id__') }}".replace('__id__',
                    id);
                $.ajax({
                    type: 'PUT',
                    url: invoiceStatusUrl,
                    data: {
                        status: value,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "Status not updated."
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        handleAjaxError(xhr);
                    }
                });
            }

            //call status change function
            $(document).on("change", ".status", function() {
                var element = $(this);
                var oldstatus = element.data('original-value'); //get original invoice status value
                var statusid = element.data('status'); // get invoice id
                var status = element.val(); //get current value
                showConfirmationDialog(
                    'Are you sure?',
                    'to change this record status ?',
                    'Yes, change it',
                    'No, cancel',
                    'question',
                    () => {
                        loadershow();
                        element.data('original-value', status); // set current value to original value
                        statuschange(statusid, status);
                        loaderhide(); // Success callback
                    },
                    () => {
                        $('#status_' + statusid).val(oldstatus);
                    }
                );
            });
            $(document).on('click', '.viewpayment', function() {
                loadershow();
                var invoiceId = $(this).data('id');
                viewpayment(invoiceId);
            })

            function viewpayment(invoiceId) {
                $('#details').html('');
                let paymentDetailsSearchUrl = "{{ route('broker_paymentdetails.search', '__invoiceId__') }}"
                    .replace('__invoiceId__', invoiceId);
                $.ajax({
                    type: 'GET',
                    url: paymentDetailsSearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $.each(response.paymentdetail, function(key, value) {
                                let generateInvoiceReceiptUrl =
                                    "{{ route('invoice.brokerBillgeneratereciept', '__invoiceId__') }}"
                                    .replace('__invoiceId__', value.id);

                                let TDSDetails = '';
                                if (value.tds_amount && value.tds_amount > 0) {
                                    TDSDetails = ` 
                                        <div><b>TDS Amount : </b> ${value.tds_amount}</div>
                                        <div><b>Challan No : </b> ${value.challan_no}</div>
                                        <div><b>TDS Status : </b> ${value.tds_status}</div>
                                    `;
                                }
                                $('#details').append(`
                                    <tr>
                                        <td>
                                            <div class="col-md-10 float-left">
                                                <div><b>Payment date : </b> ${value.datetime}</div>
                                                <div><b>Total Amount : </b> ${value.amount}</div>
                                                <div><b>Paid Amount : </b> ${value.paid_amount}</div>
                                                ${TDSDetails}
                                                <div><b>Pending Amount: </b> ${value.pending_amount}</div>
                                                <div><b>Paid By: </b>  ${value.paid_by != null ? value.paid_by : '-'}</div>
                                            </div>    
                                            <div class="col-md-2 float-right p-0">
                                                <a href=${generateInvoiceReceiptUrl} target='_blank'>
                                                    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Single Receipt"  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >
                                                        <i class='ri-download-cloud-fill'></i>
                                                    </button>
                                                </a>
                                                <button data-id="${value.id}" data-inv-id="${invoiceId}" class="btn btn-sm btn-danger pay-del-btn float-right">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>    
                                            
                                        </td>
                                    </tr>
                                `)

                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#details').html(`
                                <tr>
                                    <td>
                                        No data Found
                                    </td>
                                </tr>
                            `);
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }
            $("#exampleModalScrollable").on("hidden.bs.modal", function() {
                $('#details').html('');
                $('#addfooterbutton').html('');
            });

            $('#paidamount, #tds_amount').on('change keyup', function() {
                var paidamount = $('#paidamount').val() || 0;
                var tdsamount = $('#tds_amount').val() || 0;
                var totalamount = parseInt($('#info-total_amount').text()) || 0;
                var totalreceived = parseInt($('#info-total_received_amount').text()) || 0;
                var pendingamount = totalamount - totalreceived - paidamount - tdsamount;
                $('.info-pending_amount').text(pendingamount);
            });

            $('#tds_applicable').on('change', function() {
                let val = $(this).is(':checked');
                $('.tds_inputs').hide();
                if (val) {
                    $('.tds_inputs').show();
                }
            });
            $('#paymentform').submit(function(event) {
                $('#modal_error-msg').text('');
                event.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('broker_paymentdetails.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            loaddata();
                            $('#paymentform')[0].reset();
                            $('#paymentmodal').modal('hide');
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            });
            $(document).on("click", ".generate-pdf", function() {
                let garden_id = $(this).data('id');
                loadershow();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('brokeragebill.brokeragebillpdf') }}",
                    data: {
                        garden_id: garden_id,
                        company_id: "{{ session('company_id') }}",
                        user_id: "{{ session('user_id') }}",
                        token: "{{ session('api_token') }}"
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                })
            });
            $(document).on('click', '.paymentformmodal', function() {
                $('#paymentform')[0].reset();
                $('.tds_inputs').hide();
                var invoiceid = $(this).data('id');
                var amount = $(this).data('amount');
                var totalreceivedamount = 0;
                var pendingamount = 0;
                $('#inv_id').val(invoiceid);
                loadershow();
                let pendingPaymentDetailsUrl =
                    "{{ route('broker_paymentdetails.pendingpayment', '__invoiceId__') }}".replace(
                        '__invoiceId__',
                        invoiceid);
                $.ajax({
                    type: 'GET',
                    url: pendingPaymentDetailsUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            totalreceivedamount = amount - response.payment[0].pending_amount;
                            $('#paidamount').val(response.payment[0].pending_amount);
                            $('#paidamount').attr('max', response.payment[0].pending_amount);
                            $('#info-total_amount').text(amount);
                            $('#info-total_received_amount').text(totalreceivedamount);
                            $('.info-pending_amount').text(pendingamount);
                        } else {
                            $('#paidamount').val(amount);
                            $('#paidamount').attr('max', amount);
                            $('#info-total_amount').text(amount);
                            $('#info-total_received_amount').text(totalreceivedamount);
                            $('.info-pending_amount').text(pendingamount);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });

            })


            $(document).on("click", ".view-btn", function() {
                let garden_id = $(this).data('view');

                $("#exampleModalScrollable .modal-dialog").addClass('modal-lg');
                $('#details').html('');
                loadershow();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('brokeragebill.getOtherData') }}",
                    data: {
                        garden_id: garden_id,
                        company_id: "{{ session('company_id') }}",
                        user_id: "{{ session('user_id') }}",
                        token: "{{ session('api_token') }}"
                    },
                    success: function(response) {
                        let totalRows = response.data.length;

                        if (totalRows === 0) {
                            $('#details').html('<tr><td colspan="6">No data found</td></tr>');
                            loaderhide();
                            return;
                        }
                        $('#details').append(`
                            <tr>
                                <th>Id</th>
                                <th>Invoice No</th>
                                <th>Brokerage</th>
                                <th>Grade</th>
                                <th>Bags</th>
                                <th>Total Kg</th>
                                <th>Rate Per kg </th>
                                <th>Total Broker</th>
                            </tr>
                        `);
                        let id = 1;
                        response.data.forEach(function(item, index) {
                            // Only show garden name for first row\
                            $('#viewmodaltitle').text(item.garden_name + `- Details`);
                            $('#viewmodaltitle').addClass('font-weight-bold');
                            $('#details').append(`
                            <tr>
                                <td>${id ?? '-'}</td>
                                <td>${item.invoice_no ?? '-'}</td>
                                <td>${item.brokerage ?? '0'}(%)</td>
                                <td>${item.grade ?? '-'}</td>
                                <td>${item.bags ?? '-'}</td>
                                <td>${item.net_kg ?? '-'}</td>
                                <td>${item.rate ?? '-'}</td>
                                <td>${((item.net_kg ?? 0) * (item.rate ?? 0) * (item.brokerage ?? 1))/100}
                            </tr>
                            
                        `);
                            id++;
                        });

                        loaderhide();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        loaderhide();
                    }
                });
            });

            loaddata();

            // record delete 
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        let brokerpurchaseDeleteUrl =
                            "{{ route('brokerpurchase.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: brokerpurchaseDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                loaderhide();
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    loaddata();
                                    table.drow();
                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                }
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                var errorMessage = "";
                                try {
                                    var responseJSON = JSON.parse(xhr.responseText);
                                    errorMessage = responseJSON.message ||
                                        "An error occurred";
                                } catch (e) {
                                    errorMessage = "An error occurred";
                                }
                                Toast.fire({
                                    icon: "error",
                                    title: errorMessage
                                });
                            }
                        });
                    }
                );
            });



        });
    </script>
@endpush
