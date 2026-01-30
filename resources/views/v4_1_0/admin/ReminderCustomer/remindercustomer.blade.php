@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Reminder Customers
@endsection
@section('table_title')
    Customers
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
@if (session('user_permissions.remindermodule.reminder.add') == '1')
    @section('addnew')
        {{ route('admin.addremindercustomer') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Customer" class="">+ Add
                New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>ContactNo</th>
                <th>Customer Type</th>
                <th>Area</th>
                <th>View</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>


    <div class="modal fade" id="viewreminder" tabindex="-1" role="dialog" aria-labelledby="viewreminderTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewreminderTitle"><b>Reminder History</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row historyrecord">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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

            var global_response = '';

            var table = '';

            // function for  get customers data and set it into table
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
                        url: "{{ route('remindercustomer.index') }}",
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
                            name: 'id',
                            orderable: true,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'contact_no',
                            name: 'contact_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'customer_type',
                            name: 'customer_type',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'area',
                            name: 'area',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                @if (session('user_permissions.remindermodule.remindercustomer.view') == '1')
                                    actionBtns = `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Customer Details">
                                            <button type="button" data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Reminder History">
                                            <button data-toggle="modal" data-target="#viewreminder" data-id='${data}' class='btn btn-sm btn-info mx-0 my-1 viewreminderhistory' >
                                                <i class='ri-eye-fill'></i>
                                            </button>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Customer Reminders">
                                            <button class="btn btn-sm btn-info viewonreminderpage" data-id='${data}'><i class="ri-alarm-line"></i></button>
                                        </span>
                                    `;

                                    return actionBtns;
                                @endif
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.remindermodule.remindercustomer.edit') == '1')
                                    var editReminderCustomerUrl =
                                        "{{ route('admin.editremindercustomer', '__editid__') }}"
                                        .replace('__editid__', data);
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <a href='${editReminderCustomerUrl}'>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.remindermodule.remindercustomer.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-id= '${data}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
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
                        $('[data-toggle="tooltip"]').tooltip();

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


            $(document).on('click', '.viewreminderhistory', function() {
                $('.historyrecord').html(' ');
                loadershow();
                var historyid = $(this).data('id');
                $('#viewonreminderpage').data('id', historyid);
                $.each(global_response.data, function(key, customer) {
                    if (customer.id == historyid) {
                        $('#viewreminderTitle').html(
                            `<b>Reminder History</b> - ${customer.name}`);
                    }
                });
                let customerRemindersUrl =
                    "{{ route('remindercustomer.customerreminders', '__historyId__') }}".replace(
                        '__historyId__', historyid);
                $.ajax({
                    type: 'GET',
                    url: customerRemindersUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 & response.customer != '') {
                            $.each(response.customer, function(key, value) {
                                $('.historyrecord').append(`
                                <div class="col-12">
                                    <b>Product Name:</b> ${value.product_name} <br>
                                    <b>Amount:</b>  ${value.amount} <br>
                                    <b>Reminder Status:</b>  ${value.reminder_status} <br>
                                    <b>Next Reminder Date:</b>  ${value.next_reminder_date} <br>
                                    <b>Created On:</b>  ${value.created_on} <br>
                                    <b>Created By:</b>  ${value.firstname}  ${value.lastname}<br>
                                    <hr>
                                </div>
                            `);
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('.historyrecord').append(`
                                <div class="col-12">
                                   No history Found 
                                </div>
                            `);
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
                        reject(errorMessage);
                    }
                });
            });


            $(document).on('click', '.viewonreminderpage', function() {
                var filter_customer = $(this).data('id');

                data = {
                    filter_customer
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                window.location.href = "{{ route('admin.reminder') }}";
            })


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
                        let reminderCustomerDeleteUrl =
                            "{{ route('remindercustomer.delete', '__deleteId__') }}"
                            .replace('__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: reminderCustomerDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                loaderhide();
                                if (response.status == 200) {
                                    $(row).closest("tr").fadeOut();
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
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

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, customer) {
                    if (customer.id == data) {
                        $('#details').append(`
                            <tr>
                                <th>Name</th>
                                <td>${customer.name}</td>
                            </tr>
                            <tr>
                                <th>email</th>
                                <td>${customer.email}</td>
                            </tr>
                            <tr>
                                <th>Contact</th>
                                <td>${customer.contact_no}</td>
                            </tr>
                            <tr>
                                <th>Pincode</th>
                                <td>${customer.pincode}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>${customer.address}</td>
                            </tr>
                            <tr>
                                <th>Area</th>
                                <td>${customer.area}</td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td>${customer.city_name}</td>
                            </tr>
                            <tr>
                                <th>State</th>
                                <td>${customer.state_name}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>${customer.country_name}</td>
                            </tr>
                            <tr>
                                <th>Customer Type</th>
                                <td>${customer.customer_type}</td>
                            </tr>
                        `);
                    }
                });

            });

        });
    </script>
@endpush
