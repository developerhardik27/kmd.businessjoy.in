@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Customer Support
@endsection

@section('table_title')
    Customer Support
@endsection

@section('style')
    {{-- customersupport style --}}
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

        .select2 {
            min-width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('advancefilter')
    <div class="col-sm-12 text-right">
        <button class="btn btn-sm btn-primary m-0 mr-3" data-toggle="tooltip" data-placement="bottom"
            data-original-title="Filters" onclick="showOffCannvas()">
            <i class="ri-filter-line"></i>
        </button>
    </div>
@endsection

@section('sidebar-filters')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6>Customer Support</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-1">
                        <label for="filter_status" class="form-label mt-1">Filter Status</label>
                        <select class="filter form-control w-100 m-2" id="filter_status" multiple>
                            <option value='Open'>Open</option>
                            <option value='In Progress'>In Progress</option>
                            <option value='Resolved'>Resolved</option>
                            <option value='Cancelled'>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 mb-1">
                        <label for="filter_assigned_to" class="form-label mt-1">Assigned To</label>
                        <select class="filter form-control m-2" id="filter_assigned_to" multiple>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Call</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <label for="filter_call_count" class="form-label">Number Of Call:</label>
                        <input type="number" id="filter_call_count" placeholder="Number Of Call"
                            class="filter form-control">
                    </div>
                    <div class="col-md-6 mb-1">
                        <label for="filter_last_call" class="form-label">Last Call:</label>
                        <input type="date" id="filter_last_call" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Created Date</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="filter_from_date" class="form-label">From:</label>
                        <input type="date" id="filter_from_date" class="filter form-input form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="filter_to_date" class="form-label">To:</label>
                        <input type="date" id="filter_to_date" class="filter form-input form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (session('user_permissions.customersupportmodule.customersupport.add') == '1')
    @section('addnew')
        {{ route('admin.addcustomersupport') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add Ticket"
            class="btn btn-sm btn-primary">
            <span class="">+ Ticket</span>
        </button>
    @endsection
@endif


@section('table-content')
    <table id="data" class="table display table-bordered w-100  table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Complain Desc.</th>
                <th>Status</th>
                <th>History</th>
                <th>createdon</th>
                <th>no.of calls</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>

    {{-- modal for add call history module  --}}
    <div class="modal fade" id="addcallhistory" tabindex="-1" role="dialog" aria-labelledby="addcallhistoryTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addcallhistoryTitle"><b>Call History</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="customersupporthistoryform">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="company_id" id="company_id">
                            <input type="hidden" name="csid" id="csid">
                            <input type="hidden" name="user_id" id="created_by">
                            <input type="hidden" name="token" id="token">
                            <div class="col-12">
                                Datetime:
                                <input type="datetime-local" name="call_date" id="call_date"class="form-control">
                                <span class="error-msg" id="error-call_date" style="color: red"></span>
                            </div>
                            <br />
                            <div class="col-12 mt-2">
                                Notes:
                                <textarea name="history_notes" id="history_notes" cols="" rows="2" class="form-control"></textarea>
                                <span class="error-msg" id="error-history_notes" style="color: red"></span>
                            </div>
                            <br />
                            <div class="col-12 mt-2">
                                Status:
                                <select class="form-control" id="call_status" name="call_status">
                                    <option value='Open'>Open</option>
                                    <option value='In Progress'>In Progress</option>
                                    <option value='Resolved'>Resolved</option>
                                    <option value='Cancelled'>Cancelled</option>
                                </select>
                                <span class="error-msg" id="error-call_status" style="color: red"></span>
                            </div>
                            <br>
                            <div class="col-12 mt-2">
                                <input type="checkbox" name="no_of_calls" id="no_of_calls" value="1"> <label
                                    for="no_of_calls">Nubmer Of Calls</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" value="submit" class="btn btn-sm btn-primary">
                            <button type="button" class="btn btn-danger resethistoryform" data-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    {{-- modal for view call history module  --}}
    <div class="modal fade" id="viewcallhistory" tabindex="-1" role="dialog" aria-labelledby="viewcallhistoryTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewcallhistoryTitle"><b>Call History</b></h5>
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
        $(document).ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            function decodeHTML(html) {
                let txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value;
            }

            $('#history_notes').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['table']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                placeholder: 'Add Notes',
                tabsize: 2,
                height: 100
            });

            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.customersupportindex') }}",
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

            function loadFilters() {
                return new Promise((resolve, reject) => {
                    var filterData = JSON.parse(sessionStorage.getItem('filterData'));
                    if (filterData) {
                        $.each(filterData, function(key, value) {
                            if (value != ' ') {
                                $('#' + key).val(value);
                            }
                        });
                        loaddata();

                        $('#filter_status').trigger('change');
                        $('#filter_assigned_to').trigger('change');

                        sessionStorage.removeItem('filterData');
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
                    const [userDataResponse] = await Promise.all([
                        getUserData()
                    ]);

                    // Check if user data is successfully fetched
                    if (userDataResponse.status == 200 && userDataResponse.user != '') {
                        // You can update your HTML with the data here if needed     
                        $.each(userDataResponse.user, function(key, value) {
                            var optionValue = value.firstname + ' ' + value.lastname;
                            $('#filter_assigned_to').append(
                                `<option value="${optionValue}">${optionValue}</option>`);
                        });
                        $('#filter_assigned_to').val('');
                        $('#filter_assigned_to').select2({
                            search: true,
                            placeholder: 'Select User',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                        loaderhide();
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse.message
                        });
                        loaderhide();
                    } else {
                        $('#filter_assigned_to').val('');
                        $('#filter_assigned_to').select2({
                            search: true,
                            placeholder: 'No user found',
                            allowClear: true // Optional: adds "clear" (x) button
                        });
                        loaderhide();
                    }

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

            $('#filter_status').val('');
            $('#filter_status').select2({
                search: true,
                placeholder: 'Select Status',
                allowClear: true // Optional: adds "clear" (x) button
            });

            var global_response = '';
            let table = '';

            var search = {!! json_encode($search) !!}

            // get and set customer support history list in the table
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
                        url: "{{ route('customersupport.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_from_date = $('#filter_from_date').val();
                            d.filter_to_date = $('#filter_to_date').val();
                            d.filter_status = $('#filter_status').val();
                            d.filter_assigned_to = $('#filter_assigned_to').val();
                            d.filter_last_call = $('#filter_last_call').val();
                            d.filter_call_count = $('#filter_call_count').val();
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
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                options = `
                                    <span style="cursor:pointer;" class="view-btn d-flex mb-2" data-view = '${row.id}' data-toggle="modal" data-target="#exampleModalScrollable" >
                                        <b><i class="fas fa-user pr-2"></i></b> ${row.name || '-'}
                                    </span>
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-envelope pr-2"></i></b>
                                        <a href="mailto:${row.email || ''}" style='text-decoration:none;'>${row.email || '-'}</a>
                                    </span>
                                    <span class='d-flex mb-2'>
                                        <b><i class="fas fa-phone-alt pr-2"></i></b>
                                        <a href="tel:${row.contact_no || ''}" style='text-decoration:none;'> ${row.contact_no || '-'}</a>
                                    </span> 
                                `;
                                return options;

                            }
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return `
                                    <span  class="d-inline-block text-truncate mb-2" style="max-width: 150px;">
                                        <div>${row.notes ? decodeHTML(row.notes) : '-'}</div>
                                    </span> 
                                `;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                    return `
                                        <select class="status form-control-sm" data-original-value="${row.status}" data-statusid=${row.id} id='status_${row.id}'>
                                            <option value='Open' ${row.status == 'Open' ? 'selected' : ''}>Open</option>
                                            <option value='In Progress' ${row.status == 'In Progress' ? 'selected' : ''}>In Progress</option>
                                            <option value='Resolved' ${row.status == 'Resolved' ? 'selected' : ''}>Resolved</option>
                                            <option value='Cancelled' ${row.status == 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                        </select> 
                                    `;
                                @endif
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Add Call History">
                                        <button data-toggle="modal" data-target="#addcallhistory" data-id='${row.id}' class='btn btn-sm btn-primary csid'>
                                            <i class='ri-time-fill'></i>
                                        </button>
                                    </span>
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Call History">
                                        <button data-toggle="modal" data-target="#viewcallhistory" data-id='${row.id}' title='view Call History' class='btn btn-sm btn-info viewcallhistory'>
                                            <i class='ri-eye-fill'></i>
                                        </button>
                                    </span>
                                `;
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'number_of_call',
                            name: 'number_of_call',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Whatsapp Message">
                                        <a class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${row.contact_no}">
                                            <i class="ri-whatsapp-line text-white"></i>
                                        </a>
                                    </span> 
                                `;

                                @if (session('user_permissions.customersupportmodule.customersupport.edit') == '1')
                                    actionBtns += `
                                         <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <button type="button" data-id='${row.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                <i class="ri-edit-fill"></i>
                                            </button>  
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.customersupportmodule.customersupport.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-uid='${row.id}' class="dltbtn btn btn-danger btn-rounded btn-sm my-1">
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

            // show individual customer support history record into the popupbox
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, ticket) {
                    if (ticket.id == data) {
                        $('#details').append(`
                            <tr> 
                                <td>Ticket Number</td>
                                <th>${ticket.ticket || '-'}</th>
                            </tr> 
                            <tr> 
                                <td>Website Url</td>
                                <th style="text-transform: none;">
                                    <a href="${ticket.web_url || '#'}" target="_blank">${ticket.web_url || '-'}</a>
                                </th>
                            </tr> 
                            <tr> 
                                <td>Name</td>
                                <th>${ticket.name || '-'}</th>
                            </tr>  
                            <tr>
                                <td>email</td>
                                <th>${ticket.email || '-'}</th>
                            </tr>
                            <tr>
                                <td>contact Number</td>
                                <th>${ticket.contact_no || '-'}</th>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <th>${ticket.status || '-'}</th>
                            </tr>
                            <tr>
                                <td>Last Call</td>
                                <th>${ticket.last_call || '-'}</th>
                            </tr>
                            <tr>
                                <td>Follow up</td>
                                <th>${ticket.number_of_call || '-'}</th>
                            </tr>
                            <tr>
                                <td>Created On</td>
                                <th>${ticket.created_at_formatted || '-'}</th>
                            </tr>
                            <tr> 
                                <td>Assigned To</td>
                                <th>${ticket.assigned_to || '-'}</th>
                            <tr>
                            <tr>
                                <td >Notes</td>
                                <th class='text-wrap'><div>${ticket.notes ? decodeHTML(ticket.notes) : '-'}</div></th>
                            </tr>
                        `);
                    }
                });
            });


            // change customer support status
            $(document).on('change', '.status', function() {
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
                        var statusid = element.data('statusid');
                        var fieldid = element.attr('id');
                        var statusvalue = $('#' + fieldid).val();
                        element.data('original-value', statusvalue);
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('customersupport.changestatus') }}",
                            data: {
                                statusid: statusid,
                                statusvalue: statusvalue,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                loaderhide();
                                if (data.status == false) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                    loaderhide();
                                } else {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    table.draw();
                                }
                            }
                        });
                    },
                    () => {
                        // Error callback
                        loaderhide();
                        var fieldid = element.attr('id');
                        $('#' + fieldid).val(oldstatus);
                    }
                );
            })

            //  on click edit button this will be save advanced filter data on 
            // local server session and redirect update page
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
                filter_from_date = $('#filter_from_date').val();
                fitler_to_date = $('#fitler_to_date').val();
                filter_status = $('#filter_status').val();
                filter_assigned_to = $('#filter_assigned_to').val();
                filter_last_call = $('#filter_last_call').val();
                filter_call_count = $('#filter_call_count').val();

                data = {
                    filter_status,
                    filter_assigned_to,
                    filter_last_call,
                    filter_call_count,
                    filter_from_date,
                    fitler_to_date
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                editCustomerSupportUrl =
                    "{{ route('admin.editcustomersupport', '__customersupportid__') }}"
                    .replace('__customersupportid__', editid);

                // console.log(data);
                window.location.href = editCustomerSupportUrl;
            });


            // delete customer support record
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
                        // Success callback
                        loadershow();
                        $.ajax({
                            url: "{{ route('customersupport.delete') }}",
                            type: 'PUT',
                            data: {
                                id: id,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                loaderhide();
                                if (data.status == false) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                    loaderhide();
                                } else {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    $(row).closest("tr").fadeOut();
                                }

                            }
                        });
                    },
                    () => {
                        // Error callback
                    }
                );
            })


            $('.applyfilters').on('click', function() {
                table.draw();
                hideOffCanvass();
            });

            // remove all filters
            $('.removefilters').on('click', function() {
                // Refresh the multiselect dropdown to reflect changes
                $('#filter_status').val(null).trigger('change');
                $('#filter_assigned_to').val(null).trigger('change');

                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('#filter_last_call').val('');
                $('#filter_call_count').val('');

                table.draw();
                hideOffCanvass(); // close OffCanvass

            });

            //    customersupporthistory form 
            $(document).on('click', '.csid', function() {
                csid = $(this).data('id');
                $('#csid').val(csid);
                $.each(global_response.data, function(key, ticket) {
                    if (ticket.id == csid) {
                        $('#addcallhistoryTitle').html(
                            `<b>Call History</b> - ${ticket.name}`);
                    }
                });
                // make current date and time and set in the customer support history form intput
                var now = new Date();
                var formattedDateTime = now.getFullYear() + '-' +
                    ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + now.getDate()).slice(-2) + 'T' +
                    ('0' + now.getHours()).slice(-2) + ':' +
                    ('0' + now.getMinutes()).slice(-2);

                $('#history_notes').summernote('code', '');
                $('#call_date').val(formattedDateTime);
                $('#created_by').val("{{ session()->get('user_id') }}");
                $('#company_id').val("{{ session()->get('company_id') }}");
                $('#token').val("{{ session()->get('api_token') }}");
            });


            // view call history
            $(document).on('click', '.viewcallhistory', function() {
                $('.historyrecord').html(' ');
                loadershow();
                var historyid = $(this).data('id');
                $.each(global_response.data, function(key, ticket) {
                    if (ticket.id == historyid) {
                        $('#viewcallhistoryTitle').html(
                            `<b>Call History</b> - ${ticket.name}`);
                    }
                });
                let customerSupportHistorySearchUrl =
                    "{{ route('customersupporthistory.search', '__historyId__') }}".replace(
                        '__historyId__', historyid);
                $.ajax({
                    type: 'GET',
                    url: customerSupportHistorySearchUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 & response.customersupporthistory != '') {
                            $.each(response.customersupporthistory, function(key, value) {
                                $('.historyrecord').append(`
                                <div class="col-12">
                                    <b>Status:</b> ${value.call_status} <br>
                                    <b>Complain Description:</b> <div> ${value.history_notes} </div> <br>
                                    <small> ${value.call_date}</small>
                                    <hr/>
                                </div>
                            `);
                            });
                        } else if (response.status == 500) {
                            if (data.status == false) {
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
                        if (data.status == false) {
                            Toast.fire({
                                icon: "error",
                                title: errorMessage
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.resethistoryform', function() {
                $('#customersupporthistoryform')[0].reset();
                $('#history_notes').summernote('code', '');
            });

            // customersupporthistoryform submit 
            $('#customersupporthistoryform').submit(function(e) {
                e.preventDefault();
                loadershow();
                $('.error-msg').text('');
                const formdata = $(this).serializeArray();
                formdata.push({
                    name: "notes",
                    value: $('#history_notes').summernote('code')
                });
                $.ajax({
                    type: 'POST',
                    url: "{{ route('customersupporthistory.store') }}",
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#history_notes').summernote('code', '');
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                             Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#customersupporthistoryform')[0].reset();
                            $('#addcallhistory').modal('hide');
                            table.draw();
                            
                        }else{ 
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });  
                        }
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        $('#history_notes').summernote('code', '');
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
                            if (data.status == false) {
                                Toast.fire({
                                    icon: "error",
                                    title: errorMessage
                                });
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush
