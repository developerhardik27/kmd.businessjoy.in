@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Transporter Bills
@endsection
@section('table_title')
    Transporter Bills
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
@if (session('user_permissions.logisticmodule.transporterbilling.add') == '1')
    @section('addnew')
        {{ route('admin.addtransporterbilling') }}
    @endsection
    @section('addnewbutton')
        <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Bill"
            class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif

@section('advancefilter')
    <div class="col-sm-12 text-right">
        <button class="btn btn-sm btn-primary m-0 mr-3" data-toggle="tooltip" data-placement="bottom"
            data-original-title="Filters" onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection

@section('sidebar-filters')
    <div class="col-12 p-0">
        <div class="card">
            <div class="card-header">
                <h6>LR Number</h6>
            </div>
            <div class="card-body">
                <input type="text" class="form-control filter" name="filter_lr_no" id="filter_lr_no" placeholder="LR No">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Container Number</h6>
            </div>
            <div class="card-body">
                <input type="text" class="form-control filter" name="filter_container_no" id="filter_container_no"
                    placeholder="Container No">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Bill Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="filter_bill_date_from">From</label>
                        <input type="date" class="form-control filter" name="filter_bill_date_from"
                            id="filter_bill_date_from">
                    </div>
                    <div class="col-6">
                        <label for="filter_bill_date_to">To</label>
                        <input type="date" class="form-control filter" name="filter_bill_date_to"
                            id="filter_bill_date_to">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Vehicle Number</h6>
            </div>
            <div class="card-body">
                <input type="text" class="form-control filter" name="filter_vehicle_no" id="filter_vehicle_no"
                    placeholder="Truck No">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Party</h6>
            </div>
            <div class="card-body">
                <select class="form-control filter" name="filter_party" id="filter_party">
                </select>
            </div>
        </div>

    </div>
@endsection

@section('table-content')
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>Bill No</th>
                <th>LR No</th>
                <th>Party</th>
                <th>Bill Date</th>
                <th>Container No</th>
                <th>Vehicle No</th>
                <th>Amount</th>
                <th>Payment Status</th>
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
                        <input type="hidden" name="bill_id" id="bill_id">
                        <label for="transid">Transaction ID</label>
                        <input type="text" name="transid" class="form-control" id="transid"
                            placeholder="Transaction id" />
                        <p class="modal_error-msg mb-1" id="error-transid" style="color: red"></p>
                        <label for="payment_date">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" id="payment_date" required />
                        <p class="modal_error-msg mb-1" id="error-payment_date" style="color: red"></p>
                        Total Amount :-&nbsp;<span class="mb-1 text-info" id="info-total_amount">0</span>,
                        &nbsp;Total Paid Amount :-&nbsp;<span class="mb-1 text-info"
                            id="info-total_paid_amount">0</span><br>
                        <label for="paidamount">Paid Amount</label>
                        <input type="number" name="paidamount" class="form-control" id="paidamount"
                            placeholder="Paid Amount" required />
                        <p class="modal_error-msg mb-1" id="error-paidamount" style="color: red"></p>
                        Pending Amount :-&nbsp;<span class="mb-1 text-info" id="info-pending_amount">0</span><br>
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
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" cols="30" rows="2" class="form-control" placeholder="Remarks"></textarea>
                        <p class="modal_error-msg mb-1" id="error-remarks" style="color: red"></p>
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

            var table = '';


            function getPartyData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('billingparty.getpartylist') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(error) {
                            reject(error);
                        }
                    });
                });
            }


            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [partyDataResponse] = await Promise.all(
                        [
                            getPartyData()
                        ]);

                    // Check if user data is successfully fetched
                    if (partyDataResponse.status == 200 && partyDataResponse.party != '') {
                        // You can update your HTML with the data here if needed
                        $.each(partyDataResponse.party, function(key, value) {
                            const partyDetails = [value.firstname, value.lastname, value
                                .company_name, value.contact_no
                            ].filter(Boolean).join(' - ');

                            if (value.is_active == 1) {
                                $('#filter_party').append(
                                    `<option value='${value.id}'>${partyDetails}</option>`
                                )
                            }
                        });
                        $('#filter_party').val('');
                        $('#filter_party').select2({
                            search: true,
                            placeholder: 'Select a Consignee',
                            allowClear: true // Optional: adds "clear" (x) button
                        }); // search bar in party list
                    } else if (response.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    } else {
                        $('#filter_party').append(`<option disabled '>No Data found </option>`);
                    }

                    loaderhide();

                    // Further code execution after successful AJAX calls and HTML appending

                    await loaddata();

                } catch (error) {
                    console.error('Error:', error);
                    loaderhide();
                    $('#filter_party').append(`<option disabled '>No Data found </option>`);

                    $('#data').DataTable({
                        responsive: true,
                        "destroy": true, //use for reinitialize datatable
                    });
                }
            }

            initialize();


            // load transporter bill data in table 
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
                        url: "{{ route('transporterbill.list') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_lr_no = $('#filter_lr_no').val();
                            d.filter_container_no = $('#filter_container_no').val();
                            d.filter_bill_date_from = $('#filter_bill_date_from').val();
                            d.filter_bill_date_to = $('#filter_bill_date_to').val();
                            d.filter_vehicle_no = $('#filter_vehicle_no').val();
                            d.filter_party = $('#filter_party').val();
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Somethint went wrong!'
                                })
                            }
                            return json.data;
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
                    order: [],
                    columns: [{
                            data: 'bill_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'bill_no'
                        },
                        {
                            data: 'lr_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'lr_no'
                        },
                        {
                            data: 'party',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'party'
                        },
                        {
                            data: 'bill_date_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'bill_date_formatted'
                        },
                        {
                            data: 'con_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'con_no'
                        },
                        {
                            data: 'vehicle_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'vehicle_no'
                        },
                        {
                            data: 'amount',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'amount'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                actions = '-';
                                @if (session('user_permissions.logisticmodule.transporterbilling.edit') == '1')
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
                                actions = '';
                                if (row.status != 'paid' || row.pending_amount > 0) {
                                    actions += `                                             
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Pay">
                                            <button data-toggle="modal" data-target="#paymentmodal" data-amount="${row.amount}" data-id='${row.id}' class='btn btn-sm btn-primary m-0 paymentformmodal'>
                                                <i class='ri-paypal-fill'></i>
                                            </button>
                                        </span>
                                    `;
                                }
                                if (row.part_payment == 1 || row.status == 'paid') {
                                    actions += `                                             
                                        <span data-toggle="tooltip" data-placement="right" data-original-title="View All Payments"> 
                                            <button  data-id='${row.id}' data-toggle='modal' data-target='#exampleModalScrollable' class='btn btn-sm btn-info my-0 viewpayment' >
                                                <i class='ri-eye-fill'></i> 
                                            </button> 
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
                                @if (session('user_permissions.logisticmodule.transporterbilling.edit') == '1')
                                    let billEditUrl =
                                        "{{ route('admin.edittransporterbilling', '__billId__') }}"
                                        .replace('__billId__', row.id);
                                    actionBtns += `
                                        <span>  
                                            <a href=${billEditUrl}>
                                                <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Bill" class="edit-btn btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.logisticmodule.transporterbilling.delete') == '1')
                                    actionBtns += `
                                         <span>
                                            <button type="button" data-id='${row.id}' data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Bill" class="del-btn btn btn-danger btn-rounded btn-sm my-0">
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

            // filters 

            //apply filters

            $('#applyfilters').on('click', function() {
                table.draw();
                hideOffCanvass(); // close OffCanvass
            });


            //remove filters
            $('#removefilters').on('click', function() {
                $('input.filter').val('');
                // Clear Select2 filters properly and trigger change
                $('#filter_party').val(null).trigger('change');
                hideOffCanvass(); // close OffCanvass
                table.draw();
            });


            // delete transporter bill             
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                let tbillDltUrl = "{{ route('transporterbill.delete', '__deleteId__') }}".replace(
                    '__deleteId__', deleteid);
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record ?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: tbillDltUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: "succesfully deleted"
                                    });
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

            //status change function
            function statuschange(id, value) {
                loadershow();
                let transporterBillStatusUrl = "{{ route('transporterbill.statusupdate', '__id__') }}".replace(
                    '__id__', id);
                $.ajax({
                    type: 'PUT',
                    url: transporterBillStatusUrl,
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
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message || "Status not updated."
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
                var billid = $(this).data('id');
                var amount = $(this).data('amount');
                var totalpaidamount = 0;
                var pendingamount = 0;
                $('#bill_id').val(billid);
                loadershow();
                let pendingPaymentDetailsUrl =
                    "{{ route('billingpaymentdetails.pendingpayment', '__billId__') }}".replace(
                        '__billId__',
                        billid);
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
                            totalpaidamount = amount - response.payment[0].pending_amount;
                            $('#paidamount').val(response.payment[0].pending_amount);
                            $('#paidamount').attr('max', response.payment[0].pending_amount);
                            $('#info-total_amount').text(amount);
                            $('#info-total_paid_amount').text(totalpaidamount);
                            $('#info-pending_amount').val(response.payment[0].pending_amount);
                        } else {
                            $('#paidamount').val(amount);
                            $('#paidamount').attr('max', amount);
                            $('#info-total_amount').text(amount);
                            $('#info-total_paid_amount').text(totalpaidamount);
                            $('#info-pending_amount').val(pendingamount);
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

            $('#paidamount').on('change keyup', function() {
                var paidamount = $(this).val();
                var totalamount = parseInt($('#info-total_amount').text()) || 0;
                var totalpaid = parseInt($('#info-total_paid_amount').text()) || 0;
                var pendingamount = totalamount - totalpaid - paidamount;
                $('#info-pending_amount').text(pendingamount);
            });

            // payment details 
            $(document).on('click', '.viewpayment', function() {
                loadershow();
                var billId = $(this).data('id'); 
                viewpayment(billId);
            })

            function viewpayment(billId){
                $('#details').html('');
                let paymentDetailsSearchUrl = "{{ route('billingpaymentdetails.search', '__billId__') }}"
                    .replace('__billId__', billId);
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
                                $('#details').append(`
                                    <tr>
                                        <td>
                                            <div class="col-md-12">
                                                <div class="float-right">
                                                    <button data-id="${value.id}" data-bill-id="${billId}" class="btn btn-sm btn-danger pay-del-btn">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                                <div><b>Payment date : </b> ${value.datetime}</div>
                                                <div><b>Total Amount : </b> ${value.amount}</div>
                                                <div><b>Paid Amount : </b> ${value.paid_amount}</div>
                                                <div><b>Pending Amount: </b> ${value.pending_amount}</div>
                                                <div><b>Paid By: </b>  ${value.paid_by != null ? value.paid_by : '-'}</div>
                                                <div style="white-space:pre-line;"><b>Remarks: </b> ${value.remarks || '-'}</div>
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

            // show today date as default payment date in modal when modal will open
            $("#paymentmodal").on("shown.bs.modal", function() {
                const today = new Date().toISOString().split('T')[0]; // YYYY-MM-DD format
                $('#payment_date').val(today);
            });

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
                    url: "{{ route('billingpaymentdetails.store') }}",
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
                        handleAjaxError(xhr);
                    }
                });
            });

            // delete transporter bill payment             
            $(document).on("click", ".pay-del-btn", function() {
                var deleteid = $(this).data('id');
                var billId = $(this).data('bill-id');
                let tbillPaymentDltUrl = "{{ route('billingpaymentdetails.deletepayment', '__deleteId__') }}".replace(
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
                            url: tbillPaymentDltUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: "succesfully deleted"
                                    });
                                    viewpayment(billId);
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

        });
    </script>
@endpush
