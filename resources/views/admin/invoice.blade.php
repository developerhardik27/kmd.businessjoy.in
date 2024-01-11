@extends('admin.mastertable')

@section('page_title')
    Invoicelist
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

        .modal {
            display: none;
            justify-content: center;
            align-items: center;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .modal-title {
            font-weight: bold;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-line {
            border: 1px solid #ddd;
            margin: 10px 0;
        }

        .modal-body {
            padding-top: 10px;
            text-align: center;
            width: 100%;
        }
    </style>
@endsection

@if (session('user_permissions.invoicemodule.invoice.add') === '1')
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
                <th>reciept</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Payment Details</span>
                <span class="close">&times;</span>
            </div>

            <div class="modal-body">
                <form id="paymentform">
                    @csrf
                    <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                        placeholder="token" required />
                    <input type="hidden" name="inv_id" value="" id="inv_id">
                    <input type="text" name="transid" class="form-control" id="transid" value=""
                        placeholder="Transaction id" required />
                    <span class="modal_error-msg" id="error-transid" style="color: red"></span><br>
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
                    <button type="submit" id="" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn iq-bg-danger">Reset</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        let isEventBound = false;
        $('document').ready(function() {
            var global_response = '';
            // function for  get customers data and set it table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('invoice.inv_list') }}',
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        token: "{{ session()->get('api_token') }}"
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
                                                            @if(session('user_permissions.invoicemodule.invoice.edit') === '1')
                                                                <select data-status='${value.id}' class="status" id="status_${value.id}" name="" required >
                                                                    <option value='paid'>paid</option>
                                                                    <option value='pending'>pending</option>
                                                                    <option value='cancel'>cancel</option>
                                                                    <option value='due'>over Due</option>
                                                                </select>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        <td> 
                                                            @if(session('user_permissions.invoicemodule.invoice.view') === '1')
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
                                                            @if(session('user_permissions.invoicemodule.invoice.view') === '1')
                                                                <span class="">
                                                                    <a href='/admin/generatepdf/${value.id}' target='_blank' id='pdf'>
                                                                        <button type="button" data-toggle="modal" class="download-btn btn btn-info btn-rounded btn-sm my-0">Download</button>
                                                                    </a>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(session('user_permissions.invoicemodule.invoice.view') === '1')
                                                                ${value.status === 'paid' ? '<div id=reciept_'+value.id+ '> <a href=/admin/generatereciept/'+value.id+ '><button  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >download</button></a></div>'  : ' '}
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(session('user_permissions.invoicemodule.invoice.delete') === '1')
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
                        } else {
                            $('#data').append(`<tr><td colspan='6' >No Data Found</td></tr>`);
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


            // record delte 
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/invoice/delete/' + $deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                $(row).closest("tr").fadeOut();
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
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            toastr.success(response.message)
                            if (value == 'paid') {
                                $('#reciept_' + id).append(
                                    `<a href='/admin/generatereceipt/${id}'><button  class="reciept-btn btn btn-outline-dark btn-rounded btn-sm my-0" >download</button></a>`
                                );
                                loaderhide();
                            } else {
                                $('#reciept_' + id).html('');
                                loaderhide();
                            }
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

            //call status change
            $(document).on("change", ".status", function() {
                var statusid = $(this).data('status');
                var status = $(this).val();
                if (status == 'paid') {
                    $("#myModal").css("display", "flex");
                    $('#inv_id').val(statusid);

                    if (!isEventBound) {
                        // Bind the submit event
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
                                        statuschange(statusid, status);
                                        document.getElementById("paymentform").reset();
                                        $("#myModal").css("display", "none");
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
                        // Set the flag to indicate that the event is already bound
                        isEventBound = true;
                    }
                } else {
                    statuschange(statusid, status);
                    loaderhide();
                }
            });

            $(".close").click(function() {
                $("#myModal").css("display", "none");
            });

            // Close the modal when clicking outside the modal content
            $(window).click(function(event) {
                if (event.target.id === "myModal") {
                    $("#myModal").css("display", "none");
                }
            });

        });
    </script>
@endpush
