@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Reminder
@endsection
@section('table_title')
    Reminder
@endsection

@section('style')
    {{-- lead style --}}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('advancefilter')
    <div class="col-sm-12 text-right">
        <button type="button" class="btn btn-sm btn-primary m-0 mr-3" data-toggle="tooltip" data-placement="bottom"
            data-original-title="Filters" onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection

@section('sidebar-filters')
    <div class="col-12 p-0">
        <div class="card">
            <div class="card-header">
                <h6>Address</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_city">City</label>
                        <select class="filter form-control w-100 select2" id="filter_city" multiple>
                        </select>
                    </div>
                    <div class="col-12 mb-1">
                        <label for="filter_area">Area</label>
                        <select class="filter form-control w-100 select2" id="filter_area" multiple>
                        </select>
                    </div>
                    <div class="col-12 mb-1">
                        <label for="filter_pincode">Pincode</label>
                        <input type="text" id="filter_pincode" placeholder="Pincode" class="filter form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Customer</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <label for="filter_customer">Customers</label>
                        <select class="filter form-control select2" id="filter_customer" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Reminder</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_type">Type</label>
                        <select class="filter form-control" id="filter_type">
                            <option selected value="all">All</option>
                            <option value="paid">Paid</option>
                            <option value="free">Free</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="filter_reminder_status">Reminder Status</label>
                        <select class="filter form-control select2 w-100" id="filter_reminder_status" multiple>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <label for="filter_last_service">Last Service</label>
                        <input type="date" id="filter_last_service" class="filter form-control">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="filter_next_reminder">Next Reminder:</label>
                        <input type="date" id="filter_next_reminder" class="filter form-control">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="filter_from_date">From:</label>
                        <input type="date" id="filter_from_date" class="filter form-control">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="filter_to_date">To:</label>
                        <input type="date" id="filter_to_date" class="filter form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (session('user_permissions.remindermodule.reminder.add') == '1')
    @section('addnew')
        {{ route('admin.addreminder') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Reminder"
            class="btn btn-sm btn-primary">
            <span class="">+ Reminder</span>
        </button>
    @endsection
@endif


@section('table-content')
    <table id="data" class="table table-bordered w-100 display table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Reminder Status</th>
                <th>createdat</th>
                <th>Next Reminder</th>
                <th>Area</th>
                <th>Pincode</th>
                <th>&nbsp;&nbsp;&nbsp;Action&nbsp;&nbsp;&nbsp;</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        $(document).ready(function() {


            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data


            function getAreaNames() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.area') }}",
                        data: {
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function getCustomerName() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.customers') }}",
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }} ",
                            user_id: "{{ session()->get('user_id') }} "
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function getCitiesname() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('remindercustomer.city') }}",
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }} ",
                            user_id: "{{ session()->get('user_id') }} "
                        },
                        success: function(response) {
                            resolve(response);
                        },
                        error: function(xhr, status, error) { // if calling api request error 
                            loaderhide();
                            console.log(xhr
                                .responseText); // Log the full error response for debugging
                            handleAjaxError(xhr);
                            reject(xhr);
                        }
                    });
                });
            }

            function loadFilters() {
                return new Promise((resolve, reject) => {
                    var filterData = JSON.parse(sessionStorage.getItem('filterData'));
                    if (filterData) {
                        $.each(filterData, function(key, value) {
                            if (value != ' ') {
                                $('#' + key).val(value); // Removed `, true`
                            }
                        });
                        loaddata();

                        // Trigger change event to ensure multiselect UI updates
                        $('#filter_city, #filter_area , #filter_reminder_status, #filter_customer').trigger('change');

                        sessionStorage.removeItem('filterData');
                        loaderhide();
                        resolve(); // Resolve the promise here after all actions
                    } else {
                        // If no filter data, resolve immediately
                        resolve();
                        loaddata();
                    }
                });
            }


            async function initialize() {
                try {
                    // Perform AJAX calls concurrently
                    const [areaDataResponse, customerDataResponse, cityDataResponse] = await Promise.all([
                        getAreaNames(),
                        getCustomerName(),
                        getCitiesname()
                    ]);


                    // Check if area data is successfully fetched
                    if (areaDataResponse.status == 200 && areaDataResponse.area != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(areaDataResponse.area, function(key, value) {
                            var optionValue = value;
                            $('#filter_area').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#filter_area').val('');
                        $('#filter_area').select2({
                            search: true,
                            placeholder: 'Select Area',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (areaDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: areaDataResponse.message
                        });
                    } else {
                        $('#filter_area').val('');
                        $('#filter_area').select2({
                            search: true,
                            placeholder: 'No Area Found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }

                    // Check if customer data is successfully fetched
                    if (customerDataResponse.status == 200 && customerDataResponse.customer != '') {
                        // You can update your HTML with the data here if needed 
                        $.each(customerDataResponse.customer, function(key, value) {
                            var optionValue = value.name;
                            $('#filter_customer').append(
                                `<option value="${value.id}">${optionValue}</option>`);
                        });
                        $('#filter_customer').val('');
                        $('#filter_customer').select2({
                            search: true,
                            placeholder: 'Select Customer',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (customerDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: customerDataResponse.message
                        });
                    } else {
                        $('#filter_customer').val('');
                        $('#filter_customer').select2({
                            search: true,
                            placeholder: 'No customer found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }

                    // Check if city data is successfully fetched
                    if (cityDataResponse.status == 200 && cityDataResponse.city != '') {
                        //         if (response.status == 200 && response.sourcecolumn != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(cityDataResponse.city, function(key, value) {
                            $('#filter_city').append(
                                `<option value="${value.id}">${value.city_name}</option>`);
                        });
                        $('#filter_city').val('');
                        $('#filter_city').select2({
                            search: true,
                            placeholder: 'Select Customer',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    } else if (cityDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: cityDataResponse.message
                        });
                    } else {
                        $('#filter_city').val('');
                        $('#filter_city').select2({
                            search: true,
                            placeholder: 'No customer found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                    }
                    loaderhide();
                    // Load filters
                    await loadFilters();

                    // Further code execution after successful AJAX calls and HTML appending
                    // Your existing logic here

                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: "error",
                        title: "An error occurred while initializing"
                    });
                    loaderhide();
                }
            }

            initialize();

            var global_response = '';

            var table = '';

            var search = {!! json_encode($search) !!}

            $('#filter_reminder_status').val('');
            $('#filter_reminder_status').select2({
                search: true,
                placeholder: 'Select Reminder Status',
                allowClear: true // Optional: adds "clear" (x) button
            });


            // get lead data and set in the table
            function loaddata() {
                if ($('#filter_type').val() == null) {
                    $('#filter_type').val('all');
                }
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
                        url: "{{ route('reminder.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_from_date = $('#filter_from_date').val();
                            d.filter_to_date = $('#filter_to_date').val();
                            d.filter_pincode = $('#filter_pincode').val();
                            d.filter_reminder_status = $('#filter_reminder_status').val();
                            d.filter_customer = $('#filter_customer').val();
                            d.filter_city = $('#filter_city').val();
                            d.fitler_area = $('#fitler_area').val();
                            d.filter_last_service = $('#filter_last_service').val();
                            d.filter_next_reminder = $('#filter_next_reminder').val();
                            d.filter_type = $('#filter_type').val();
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
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return `
                                    <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${row.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                        <b><i class="fas fa-user pr-2"></i></b> ${row.name}
                                    </span>
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-envelope pr-2"></i></b>
                                        <a href="mailto:${row.email}" style='text-decoration:none;'>${row.email}</a>
                                    </span>
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-phone-alt pr-2"></i></b>
                                        <a href="tel:${row.contact_no}" style='text-decoration:none;'> ${row.contact_no}</a>
                                    </span>  
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-city pr-2"></i></b>
                                            ${row.city_name}
                                    </span>  
                                `;
                            }
                        },
                        {
                            data: 'reminder_status',
                            name: 'reminder_status',
                            orderable: false,
                            searchable: false,
                            defaultContent: ' ',
                            render: function(data, type, row) {
                                return `
                                    <select class="reminder_status form-control" data-original-value="${row.reminder_status}" data-reminder_status_id=${row.id} id="reminder_status_${row.id}" name="reminder_status_${row.id}">
                                        <option value='pending' ${row.reminder_status == 'pending' ? 'selected' : ''}>Pending</option>
                                        <option value='in_progress' ${row.reminder_status == 'in_progress' ? 'selected' : ''}>In Progress</option>
                                        <option value='completed' ${row.reminder_status == 'completed' ? 'selected' : ''}>Completed</option>
                                    </select>
                               `;
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at_formatted',
                            orderable: false,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'next_reminder_date',
                            name: 'next_reminder_date',
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
                            data: 'pincode',
                            name: 'pincode',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let actionBtns = `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Whatsapp Message">
                                        <a class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/91${row.contact_no}">
                                            <i class="ri-whatsapp-line text-white"></i>
                                        </a>
                                    </span>
                                `;
                                @if (session('user_permissions.remindermodule.reminder.edit') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <button type="button" data-id='${row.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                <i class="ri-edit-fill"></i>
                                            </button> 
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.remindermodule.reminder.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-uid= '${row.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
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

            // view individual reminder data
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, reminder) {
                    if (reminder.id == data) {
                        $('#details').append(`
                            <tr> 
                                <td>Customer Name</td>
                                <th>${reminder.name !== null ? reminder.name : ''}</th>
                            </tr>
                            <tr>
                                <td>email</td>
                                <th>${reminder.email !== null ? reminder.email : ''}</th>
                            </tr>
                            <tr>
                                <td>contact Number</td>
                                <th>${reminder.contact_no !== null ? reminder.contact_no : ''}</th>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <th>${reminder.address !== null ? reminder.address : ''}</th>
                            </tr>
                            <tr>
                                <td>Area</td>
                                <th>${reminder.area !== null ? reminder.area : ''}</th>
                            </tr>
                            <tr>
                                <td>City</td>
                                <th>${reminder.city_name !== null ? reminder.city_name : ''}</th>
                            </tr>
                            <tr>
                                <td>Customer Type</td>
                                <th>${reminder.customer_type !== null ? reminder.customer_type : ''}</th>
                            </tr>
                            <tr>
                                <td>Invoice id</td>
                                <th>${reminder.invoice_id !== null ? reminder.invoice_id : ''}</th>
                            </tr>
                            <tr>
                                <td>Reminder Status</td>
                                <th>${reminder.reminder_status !== null ? reminder.reminder_status : ''}</th>
                            </tr>
                            <tr>
                                <td>Service Completed Date</td>
                                <th>${reminder.service_completed_date !== null ? reminder.service_completed_date : ''}</th>
                            </tr>
                            <tr>
                                <td>Next Reminder Date</td>
                                <th>${reminder.next_reminder_date !== null ? reminder.next_reminder_date : ''}</th>
                            </tr>
                            <tr>
                                <td>Service type</td>
                                <th>${reminder.service_type !== null ? reminder.service_type : ''}</th>
                            </tr>
                            <tr>
                                <td>Product Name</td>
                                <th>${reminder.product_name !== null ? reminder.product_name : ''}</th>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <th>${reminder.amount !== null ? reminder.amount : ''}</th>
                            </tr>
                            <tr>
                                <td>Created On</td>
                                <th>${reminder.created_at_formatted !== null ? reminder.created_at_formatted : ''}</th>
                            </tr>
                            <tr>
                                <td>Before Service Notes</td>
                                <th class='text-wrap'>${reminder.before_service_note !== null ? reminder.before_service_note : ''}</th>
                            </tr>
                            <tr>
                                <td>After Service Notes</td>
                                <th class='text-wrap'>${reminder.after_service_note !== null ? reminder.after_service_note : ''}</th>
                            </tr>
                        `);
                    }
                });
            });


            // change reminder stage status
            $(document).on('change', '.reminder_status', function() {
                var element = $(this);
                var oldstatus = element.data('original-value');

                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change status?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var reminderstatusid = element.data('reminder_status_id');
                        var fieldid = element.attr('id');
                        var reminderstatusvalue = $('#' + fieldid).val();
                        element.data('original-value', reminderstatusvalue);
                        let last_service_date;
                        if (reminderstatusvalue == 'completed') {
                            var now = new Date();
                            last_service_date = now.getFullYear() + '-' +
                                ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
                                ('0' + now.getDate()).slice(-2) + 'T' +
                                ('0' + now.getHours()).slice(-2) + ':' +
                                ('0' + now.getMinutes()).slice(-2);
                        }
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('reminder.changestatus') }}",
                            data: {
                                last_service_date,
                                reminderstatusid,
                                reminderstatusvalue,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });

                                    if (reminderstatusvalue == 'completed') {
                                        showConfirmationDialog(
                                            'Status succesfully Updated.', // Title
                                            'now you want to create new reminder for this customer?', // Text
                                            'Yes, create', // Confirm button text
                                            'No, cancel', // Cancel button text
                                            'question', // Icon type (question icon)
                                            () => {
                                                // Success callback
                                                var addNewReminderUrl =
                                                    "{{ route('admin.addreminder', '__reminderstatusid__') }}"
                                                    .replace('__reminderstatusid__',
                                                        reminderstatusid);
                                                window.location.href =
                                                addNewReminderUrl;
                                            }
                                        );
                                    }
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                    var reminderstatusid = element.attr('id');
                                    $(`#${reminderstatusid}`).val(oldstatus);
                                } else {
                                    var reminderstatusid = element.attr('id');
                                    $(`#${reminderstatusid}`).val(oldstatus);
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message ||
                                            'Something went wrong'
                                    });
                                }
                                loaderhide();
                            },
                            error: function(xhr, status,
                                error) { // if calling api request error 
                                loaderhide();
                                var reminderstatusid = element.attr('id');
                                $(`#${reminderstatusid}`).val(oldstatus);
                                console.log(xhr
                                    .responseText
                                ); // Log the full error response for debugging
                                handleAjaxError(xhr);
                            }
                        });
                    },
                    () => {
                        // Error callback
                        loaderhide();
                        var reminderstatusid = element.attr('id');
                        $(`#${reminderstatusid}`).val(oldstatus);
                    }
                );
            })

            // lead edit redirect - save advanced filter data as it is on local storage session
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
                filter_pincode = $('#filter_pincode').val();
                filter_from_date = $('#filter_from_date').val();
                filter_to_date = $('#filter_to_date').val();
                filter_reminder_status = $('#filter_reminder_status').val();
                filter_customer = $('#filter_customer').val();
                filter_city = $('#filter_city').val();
                filter_area = $('#filter_area').val();
                filter_last_service = $('#filter_last_service').val();
                filter_next_reminder = $('#filter_next_reminder').val();
                filter_type = $('#filter_type').val();

                data = {
                    filter_pincode,
                    filter_from_date,
                    filter_to_date,
                    filter_reminder_status,
                    filter_customer,
                    filter_city,
                    filter_area,
                    filter_last_service,
                    filter_next_reminder,
                    filter_type
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                var editReminderUrl = "{{ route('admin.editreminder', '__editid__') }}".replace(
                    '__editid__', editid);
                window.location.href = editReminderUrl;
            });

            // lead delete
            $(document).on("click", ".dltbtn", function() {

                var id = $(this).data('uid');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('reminder.delete') }}",
                            data: {
                                id: id,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    table.draw();
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message ||
                                            'Something went wrong'
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
            })



            $('.applyfilters').on('click', function(e) {
                e.preventDefault();
                table.draw();
                hideOffCanvass();
            });

            // remove all filters
            $('.removefilters').on('click', function() {
                $('#filter_pincode').val('');
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('#filter_last_service').val('');
                $('#filter_next_reminder').val('');
                $('#filter_type').val('all');

                // Refresh the multiselect dropdown to reflect changes
                $('#filter_area').val(null).trigger('change');
                $('#filter_customer').val(null).trigger('change');
                $('#filter_city').val(null).trigger('change');
                $('#filter_reminder_status').val(null).trigger('change');
                hideOffCanvass();
                table.draw();
            });

        });
    </script>
@endpush
