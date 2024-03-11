@extends('admin.mastertable')

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
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data" class="table display table-bordered table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>invoice_Date</th>
                <th>customer_name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>View</th>
                <th>Pdf</th>
                <th>Payment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

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
                        <input type="hidden" name="inv_id" value="" id="inv_id">
                        <input type="text" name="transid" class="form-control" id="transid" value=""
                            placeholder="Transaction id" required />
                        <span class="modal_error-msg" id="error-transid" style="color: red"></span><br>
                        <input type="text" name="paidamount" class="form-control" id="paidamount" value=""
                            placeholder="Amount" required />
                        <span class="modal_error-msg" id="error-paidamount" style="color: red"></span><br>
                        <input type="text" name="paid_by" class="form-control" id="paid_by" value=""
                            placeholder="paid by" />
                        <span class="modal_error-msg" id="error-paid_by" style="color: red"></span><br>
                        <select class="form-control" name="payment_type" id="payment_type" required>
                            <option selected="" disabled="">Select payment type</option>
                            <option value="Online Payment">Online Payment</option>
                            <option value="Cash">Cash</option>
                            <option value="Net-Banking">Net-Banking</option>
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
                            global_response = response;
                            // You can update your HTML with the data here if needed
                            $.each(response.invoice, function(key, value) {
                                $('#data').append(`<tr>
                                                        <td>${value.inv_date}</td>
                                                        <td>${value.firstname} ${value.lastname}</td>
                                                        <td>${value.grand_total}</td>
                                                        <td> 
                                                            @if (session('user_permissions.invoicemodule.invoice.edit') == '1')
                                                                <select data-status='${value.id}' class="status" id="status_${value.id}" name="" required >
                                                                    <option value='part_payment' disabled>Part Payment</option>
                                                                    <option value='paid' disabled>paid</option>
                                                                    <option value='pending'>pending</option>
                                                                    <option value='cancel'>cancel</option>
                                                                    <option value='due'>over Due</option>
                                                                </select>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        <td> 
                                                            @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                                                <span class="">
                                                                    <a href='/admin/invoiceview/${value.id}' target='_blank'>
                                                                        <button type="button" data-view='${value.id}' data-toggle="modal" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                            <i class="ri-indent-decrease"></i>
                                                                        </button>
                                                                    </a>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.invoice.view') == '1')
                                                                <span class="">
                                                                    <a href='/admin/generatepdf/${value.id}' target='_blank' id='pdf'>
                                                                        <button type="button"  class="download-btn btn btn-info btn-rounded btn-sm my-0">Download</button>
                                                                    </a>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        <td>
                                                            ${(value.status != 'paid') ? `
                                                                                    <span>
                                                                                        <button data-toggle="modal" data-target="#paymentmodal" data-id='${value.id}' title='Make Payment' class='btn btn-sm btn-primary my-0 leadid paymentformmodal'>
                                                                                            <i class='ri-paypal-fill'></i>
                                                                                        </button>
                                                                                    </span>
                                                                         ` : ''
                                                            }
                                                            ${(value.part_payment == 1) ? `    
                                                                            <span> 
                                                                                <button  data-id='${value.id}' data-toggle='modal' data-target='#exampleModalScrollable' title='Download Payment Reciept' class='btn btn-sm btn-info my-0 viewpayment' >
                                                                                        <i class='ri-eye-fill'></i> 
                                                                                </button> 
                                                                            </span>
                                                                         ` : ''
                                                            }
                                                            ${(value.part_payment == 0 && value.status == 'paid') ? `    
                                                                <div > <a href=/admin/generaterecieptall/${value.id}><button  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >download</button></a></div>
                                                                         ` : ''
                                                            }
                                                          
                                                        </td>
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.invoice.delete') == '1')
                                                                <span class="">
                                                                    <button type="button" data-id='${value.id}' class="del-btn btn btn-danger btn-rounded btn-sm my-0">
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

                            $('#data').DataTable({

                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                            });
                            loaderhide();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('#data').append(`<tr><td colspan='8' >No Data Found</td></tr>`);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }
            //call data function for load customer data
            loaddata();


            // record delete 
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
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
                                loaderhide();
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            }
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
                            if (value == 'paid') {
                                $('#reciept_' + id).html(
                                    `<div><a href='/admin/generatereciept/${id}'><button  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >download</button></a><div>`
                                );
                                loaderhide();
                            } else {
                                $('#reciept_' + id).html('');
                                loaderhide();
                            }
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error('Status not Updated');
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        // Handle errors here
                        console.error('Error:', error);
                    }
                });
            }

            //call status change function
            $(document).on("change", ".status", function() {
                if (confirm('Are you Sure to Change this Record ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    var status = $(this).val();
                    statuschange(statusid, status);
                    loaderhide();
                }

            });
            
            // form reset every time when on click make payment button
            $(document).on('click', '.paymentformmodal', function() {
                $('#paymentform')[0].reset();
                var invoiceid = $(this).data('id');
                $('#inv_id').val(invoiceid);
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
                                $('#details').append(`<tr>
                                        <td>
                                            <div><b>Payment date : ${value.datetime}</b></div>
                                            <div><b>Total Amount : ${value.amount}</b></div>
                                            <div><b>Paid Amount : ${value.paid_amount}</b></div>
                                            <div><b>Pending Amount: ${value.pending_amount}</b></div>
                                            <div><b>Paid By: ${value.paid_by}</b></div>
                                            <a href=/admin/generatereciept/${value.id}><button title='Download Payment Reciept'  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" ><i class='ri-download-cloud-fill'></i></button></a>
                                        </td>
                                    </tr>`)
                                if (value.pending_amount == 0) {
                                    $('#addfooterbutton').html(`
                                      <a href=/admin/generaterecieptall/${value.inv_id}>
                                         <button title='Download Payment Reciept'  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >download</button>
                                      </a>
                                    `);
                                }
                            });
                            loaderhide();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            $('#details').html(`<tr>
                                        <td>
                                           No data Found
                                        </td>
                                    </tr>`);
                            loaderhide();
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[
                                    0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
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
                            loaderhide();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[
                                    0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            });

        });
    </script>
@endpush
