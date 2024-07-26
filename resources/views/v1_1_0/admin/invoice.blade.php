@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

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
    <table id="data"
        class="table display table-bordered table-responsive-sm table-responsive-md table-responsive-lg table-striped text-center">
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
        <div class="modal-dialog modal-dialog-scrollable" role="document">
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
                        <input type="number" name="paidamount" class="form-control" id="paidamount" placeholder="Received Amount"
                            required />
                        <span class="modal_error-msg" id="error-paidamount" style="color: red"></span><br>
                        <label for="paid_by">Paid By</label>
                        <input type="text" name="paid_by" class="form-control" id="paid_by" placeholder="Who Paid Amount" />
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
                toastr.error('You have not any column for download this invoice');
            @endif

            var global_response = '';
            // function for  get customers data and set it table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('invoice.inv_list') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }} "
                    },
                    success: function(response) {
                        if (response.status == 200 && response.invoice != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            // You can update your HTML with the data here if needed
                            $.each(response.invoice, function(key, value) {
                                var customer = '';
                                if (value.firstname != null) {
                                    customer += value.firstname;
                                }

                                if (value.lastname != null) {
                                    if (customer.length > 0) {
                                        customer += ' '; // Add space between firstname and lastname if both are present
                                    }
                                    customer += value.lastname;
                                }
                                if (value.company_name != null) {
                                    if (customer.length > 0) {
                                        customer += ' / '; // 
                                    }
                                    customer += value.company_name;
                                }
                                $('#data').append(`<tr>
                                                        <td>${value.id}</td>
                                                        <td>${value.inv_date}</td>
                                                        <td>${customer}</td>
                                                        <td>${value.currency_symbol} ${value.grand_total}</td>
                                                        <td> 
                                                            @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                                                                <select data-status='${value.id}' data-original-value="${value.status}" class="status" id="status_${value.id}" name="" required >
                                                                    <option value='part_payment' disabled>Part Payment</option>
                                                                    <option value='paid' disabled>Paid</option>
                                                                    <option value='pending'>Pending</option>
                                                                    <option value='cancel'>Cancel</option>
                                                                    <option value='due'>Over Due</option>
                                                                </select>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                                                <span data-toggle="tooltip" data-placement="left" data-original-title="Download Invoice Pdf">
                                                                    <a href='/admin/generatepdf/${value.id}' target='_blank' id='pdf'>
                                                                        <button type="button" class="download-btn btn btn-info btn-rounded btn-sm my-0" ><i class="ri-download-line"></i></button>
                                                                    </a>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        <td>
                                                            ${(value.status != 'paid') ? `
                                                                                                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Pay">
                                                                                                                        <button data-toggle="modal" data-target="#paymentmodal" data-amount="${value.grand_total}" data-id='${value.id}' class='btn btn-sm btn-primary my-0 leadid paymentformmodal'>
                                                                                                                            <i class='ri-paypal-fill'></i>
                                                                                                                        </button>
                                                                                                                    </span>
                                                                                                         ` : ''
                                                            }
                                                            ${(value.part_payment == 1 && value.status == 'paid' && value.pending_amount != null) ? `    
                                                                                                <span> 
                                                                                                    <a href=/admin/generaterecieptall/${value.id} target='_blank'>
                                                                                                            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Combined Receipt"  class="reciept-btn btn btn-info btn-outline-dark btn-rounded btn-sm my-0" >
                                                                                                                <i class="ri-download-line"></i>
                                                                                                            </button>
                                                                                                    </a>
                                                                                                </span>
                                                                                ` : ''
                                                            }
                                                            ${(value.part_payment == 1) ? `    
                                                                                                            <span data-toggle="tooltip" data-placement="right" data-original-title="View All Reciept"> 
                                                                                                                <button  data-id='${value.id}' data-toggle='modal' data-target='#exampleModalScrollable' class='btn btn-sm btn-info my-0 viewpayment' >
                                                                                                                        <i class='ri-eye-fill'></i> 
                                                                                                                </button> 
                                                                                                            </span>
                                                                                                         ` : ''
                                                            }
                                                            
                                                            ${(value.part_payment == 0 && value.status == 'paid') ? `    
                                                                                                <span> 
                                                                                                    <a href=/admin/generaterecieptall/${value.id}  target='_blank' >
                                                                                                        <button  class="btn-info reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" data-toggle="tooltip" data-placement="right" data-original-title="Download Single Receipt" >
                                                                                                            <i class="ri-download-line"></i>
                                                                                                        </button>
                                                                                                    </a>
                                                                                                </span>
                                                                                 ` : ''
                                                            }
                                                            
                                                          
                                                        </td>
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.invoice.edit') == '1') 
                                                             ${(value.is_editable == 1)?  
                                                                    `  <span>
                                                                            <a href='EditInvoice/${value.id}'>
                                                                                <button type="button" data-id='${value.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Invoice" class="edit-btn btn btn-success btn-rounded btn-sm my-0">
                                                                                    <i class="ri-edit-fill"></i>
                                                                                </button>
                                                                            </a>
                                                                        </span>
                                                                    `
                                                                : ''
                                                            }
                                                            @endif
                                                            @if (session('user_permissions.invoicemodule.invoice.delete') == '1')
                                                                <span>
                                                                    <button type="button" data-id='${value.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Invoice" class="del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                                        <i class="ri-delete-bin-fill"></i>
                                                                    </button>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                      
                                                    </tr>`);
                                $(`#status_${value.id}`).val(value.status);
                            });

                            var search = {!! json_encode($search) !!}
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                            $('#data').DataTable({
                                "order": [
                                    [0, 'desc']
                                ],
                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#data').append(`<tr><td colspan='7' >No Data Found</td></tr>`);
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
                        toastr.error(errorMessage);
                    }
                });
            }
            //call data function for load customer data
            loaddata();

            // record delete 
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this invoice ?')) {
                    loadershow();
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/invoice/delete/' + $deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: " {{ session()->get('company_id') }} ",
                            user_id: " {{ session()->get('user_id') }} "
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message)
                                loaddata();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error('Invoice not Delete');
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
                            toastr.error(errorMessage);
                        }
                    });
                }
            });

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.invoice, function(key, invoice) {
                    if (invoice.id == data) {
                        $.each(invoice, function(fields, value) {
                            $('#details').append(`<tr>
                                    <th>${fields}</th>
                                    <td>${value}</td>
                                    </tr>`)
                        })
                    }
                });
            });

            //status change function
            function statuschange(id, value) {
                loadershow();
                $.ajax({
                    type: 'put',
                    url: '/api/inv_status/' + id,
                    data: {
                        status: value,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            toastr.success(response.message)
                            loaddata();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error('Status not Updated');
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
                        toastr.error(errorMessage);
                    }
                });
            }

            //call status change function
            $(document).on("change", ".status", function() {
                var oldstatus = $(this).data('original-value'); //get original invoice status value
                var statusid = $(this).data('status'); // get invoice id
                var status = $(this).val(); //get current value
                if (confirm('Are you Sure to Change this Record ?')) {
                    loadershow();
                    $(this).data('original-value', status); // set current value to original value
                    statuschange(statusid, status);
                    loaderhide();
                } else {
                    $('#status_' + statusid).val(
                        oldstatus
                        ); // if user will cancelled to change status then set original value as it is
                }
            });

            // form reset every time when on click make payment button
            $(document).on('click', '.paymentformmodal', function() {
                $('#paymentform')[0].reset();
                var invoiceid = $(this).data('id');
                var amount = $(this).data('amount');
                $('#inv_id').val(invoiceid);
                loadershow();
                $.ajax({
                    type: 'get',
                    url: "/api/pendingpayment/" + invoiceid,
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
                        toastr.error(errorMessage);
                    }
                });

            })

            // payment details 
            $(document).on('click', '.viewpayment', function() {
                loadershow();
                $('#details').html('');
                var invoiceid = $(this).data('id');
                $.ajax({
                    type: 'get',
                    url: "/api/paymentdetail/" + invoiceid,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $.each(response.paymentdetail, function(key, value) {
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
                                                <a href=/admin/generatereciept/${value.id} class="float-right"  target='_blank'>
                                                    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Download Single Receipt"  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >
                                                        <i class='ri-download-cloud-fill'></i>
                                                    </button>
                                                </a>
                                            </div>    
                                            
                                        </td>
                                    </tr>
                                `)

                            });
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#details').html(`<tr>
                                        <td>
                                           No data Found
                                        </td>
                                    </tr>`);
                            toastr.error(response.message);
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
                        toastr.error(errorMessage);
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
                            toastr.success(response.message);
                            loaddata();
                            $('#paymentform')[0].reset();
                            $('#paymentmodal').modal('hide');
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
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
                            toastr.error(errorMessage);
                        }
                    }
                });
            });

        });
    </script>
@endpush
