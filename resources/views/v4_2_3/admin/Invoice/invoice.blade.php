@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Invoicelist
@endsection
@section('table_title')
    Invoice
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

@if (session('user_permissions.invoicemodule.invoice.add') == '1')
    @section('addnew')
        {{ route('admin.addinvoice') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Create New Invoice">+ Create
                New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Invoice Date</th>
                <th>Customer/Company Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Invoice</th>
                <th>Payment</th>
                <th>Action</th>
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
                        <span class="modal_error-msg" id="error-transid" style="color: red"></span><br>
                        <label for="paidamount">Received Amount</label>
                        <input type="number" name="paidamount" class="form-control" id="paidamount"
                            placeholder="Received Amount" required />
                        <span class="modal_error-msg" id="error-paidamount" style="color: red"></span><br>
                        <label for="paid_by">Paid By</label>
                        <input type="text" name="paid_by" class="form-control" id="paid_by"
                            placeholder="Who Paid Amount" />
                        <span class="modal_error-msg" id="error-paid_by" style="color: red"></span><br>
                        <label for="payment_type">How They Paid</label>
                        <select class="form-control" name="payment_type" id="payment_type">
                            <option selected="" disabled="">Select Payment Type</option>
                            <option value="Online Payment">Online Payment</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                        </select>
                        <span class="modal_error-msg" id="error-payment_type" style="color: red"></span><br>
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
        let isEventBound = false;
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            @if (Session::has('message'))
                Toast.fire({
                    icon: "error",
                    title: 'You have not any column for download this invoice'
                });
            @endif

            let table = '';

            var global_response = '';

            var search = {!! json_encode($search) !!}

            // function for  get invoice data and set it table
            function loaddata() {
                loadershow();
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
                        url: "{{ route('invoice.inv_list') }}",
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
                    search: {
                        search: search
                    },
                    columns: [

                        {
                            data: 'inv_no',
                            name: 'inv_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'inv_date_formatted',
                            name: 'inv_date_formatted',
                            orderable: false,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'grand_total',
                            name: 'grand_total',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return `${row.currency_symbol} ${row.grand_total}`;
                            }
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
                                    actions = `  
                                        <select data-status='${row.id}' data-original-value="${row.status}" class="status" id="status_${row.id}" name="" required >
                                            <option value='part_payment' ${row.status == "part_payment" ? 'selected' : ''} disabled>Part Payment</option>
                                            <option value='paid' ${row.status == "paid" ? 'selected' : ''} disabled>Paid</option>
                                            <option value='pending' ${row.status == "pending" ? 'selected' : ''}>Pending</option>
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
                            defaultContent: '-',
                            render: function(data, type, row) {
                                actions = '-';
                                @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                    let generateInvoicePdfUrl =
                                        "{{ route('invoice.generatepdf', '__invoiceId__') }}"
                                        .replace('__invoiceId__', row.id);
                                    actions = `                                             
                                        <span data-toggle="tooltip" data-placement="left" data-original-title="Download Invoice Pdf">
                                            <a href=${generateInvoicePdfUrl} target='_blank' id='pdf'>
                                                <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0" ><i class="ri-download-line"></i></button>
                                            </a>
                                        </span>
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
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let generateInvoiceReceiptAllUrl = "{{ route('invoice.generaterecieptll', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                                actions = ''; 
                                if (row.status != 'paid') {
                                    actions += `                                             
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Pay">
                                            <button data-toggle="modal" data-target="#paymentmodal" data-amount="${row.grand_total}" data-id='${row.id}' class='btn btn-sm btn-primary my-0 leadid paymentformmodal'>
                                                <i class='ri-paypal-fill'></i>
                                            </button>
                                        </span>
                                    `;
                                }
                                if (row.part_payment == 1 && row.status == 'paid' && row.pending_amount != null) {
                                    actions += `                                             
                                        <span> 
                                            <a href=${generateInvoiceReceiptAllUrl} target='_blank'>
                                                    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Combined Receipt"  class="reciept-btn btn btn-primary btn-rounded btn-sm my-0" >
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
                                                <button  class="btn-info reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" data-toggle="tooltip" data-placement="right" data-original-title="Download Single Receipt" >
                                                    <i class="ri-download-line"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                }
                                return actions;
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.invoicemodule.invoice.edit') == '1') 
                                    if(row.is_editable == 1){
                                        let invoiceEditUrl = "{{ route('admin.editinvoice', '__invoiceId__') }}".replace('__invoiceId__', row.id);
                                        actionBtns += `
                                            <span>  
                                                <a href=${invoiceEditUrl}>
                                                    <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Invoice" class="edit-btn btn btn-success btn-rounded btn-sm my-0">
                                                        <i class="ri-edit-fill"></i>
                                                    </button>
                                                </a>
                                            </span>
                                        `;
                                    }
                                @endif

                                @if (session('user_permissions.invoicemodule.invoice.delete') == '1')
                                    actionBtns += `
                                         <span>
                                            <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Invoice" class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif

                                return actionBtns;
                            }
                        }
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
            //call data function for load customer data
            loaddata();

            // record delete 
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                let invoiceDeleteUrl = "{{ route('invoice.delete', '__deleteId__') }}".replace(
                    '__deleteId__', deleteid);

                showConfirmationDialog(
                    'Are you sure?',
                    'to delete this invoice?',
                    'Yes, delete it',
                    'No, cancel',
                    'question',
                    () => {
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: invoiceDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: " {{ session()->get('company_id') }} ",
                                user_id: " {{ session()->get('user_id') }} "
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
                                        title: "invoice not deleted."
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

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.invoice, function(key, invoice) {
                    if (invoice.id == data) {
                        $.each(invoice, function(fields, value) {
                            $('#details').append(`
                                <tr>
                                    <th>${fields}</th>
                                    <td>${value}</td>
                                    </tr>
                            `);
                        })
                    }
                });
            });

            //status change function
            function statuschange(id, value) {
                loadershow();
                let invoiceStatusUrl = "{{ route('invoice.status', '__id__') }}".replace('__id__', id);
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
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
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

            // form reset every time when on click make payment button
            $(document).on('click', '.paymentformmodal', function() {
                $('#paymentform')[0].reset();
                var invoiceid = $(this).data('id');
                var amount = $(this).data('amount');
                $('#inv_id').val(invoiceid);
                loadershow();
                let pendingPaymentDetailsUrl =
                    "{{ route('paymentdetails.pendingpayment', '__invoiceId__') }}".replace(
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
                            console.log(response.payment)
                            $('#paidamount').val(response.payment[0].pending_amount);
                            $('#paidamount').attr('max', response.payment[0].pending_amount);
                        } else {
                            $('#paidamount').val(amount);
                            $('#paidamount').attr('max', amount);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });

            })

            // payment details 
            $(document).on('click', '.viewpayment', function() {
                loadershow();
                $('#details').html('');
                var invoiceid = $(this).data('id');
                let paymentDetailsSearchUrl = "{{ route('paymentdetails.search', '__invoiceId__') }}"
                    .replace('__invoiceId__', invoiceid);
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
                                    "{{ route('invoice.generatereciept', '__invoiceId__') }}"
                                    .replace('__invoiceId__', value.id);
                                $('#details').append(`
                                    <tr>
                                        <td>
                                            <div class="col-md-10 float-left">
                                                <div><b>Payment date : </b> ${value.datetime}</div>
                                                <div><b>Total Amount : </b> ${value.amount}</div>
                                                <div><b>Paid Amount : </b> ${value.paid_amount}</div>
                                                <div><b>Pending Amount: </b> ${value.pending_amount}</div>
                                                <div><b>Paid By: </b>  ${value.paid_by != null ? value.paid_by : '-'}</div>
                                            </div>    
                                            <div class="col-md-2 float-right">
                                                <a href=${generateInvoiceReceiptUrl} class="float-right"  target='_blank'>
                                                    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Single Receipt"  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >
                                                        <i class='ri-download-cloud-fill'></i>
                                                    </button>
                                                </a>
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
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            })

            // reset payment details in modal when modal will close
            $("#exampleModalScrollable").on("hidden.bs.modal", function() {
                $('#details').html('');
                $('#addfooterbutton').html('');
            });

            // payment form submit 
            $('#paymentform').submit(function(event) {
                $('#modal_error-msg').text('');
                event.preventDefault();
                loadershow();
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('paymentdetails.store') }}",
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
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
                            var errorMessage = "";
                            try {
                                var responseJSON = JSON.parse(xhr.responseText);
                                errorMessage = responseJSON.message || "An error occurred";
                            } catch (e) {
                                errorMessage = "An error occurred";
                            }
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush
