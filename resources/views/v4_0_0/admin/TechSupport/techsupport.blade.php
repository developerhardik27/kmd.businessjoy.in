@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Technical Support
@endsection
@section('table_title')
    Technical Support
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
    <div class="col-12 p-0">
        <div class="card">
            <div class="card-header">
                <h6>Status</h6>
            </div>
            <div class="card-body">
                <select class="filter form-control w-100 select2" id="filter_status" multiple>
                    <option value='pending'>Pending</option>
                    <option value='in_progress'>In Progress</option>
                    <option value='resolved'>Resolved</option>
                    <option value='cancelled'>Cancelled</option>
                </select>
            </div>
        </div>
        @if (session('user_permissions.adminmodule.techsupport.alldata') == '1')
            <div class="card">
                <div class="card-header">
                    <h6>Assigned To</h6>
                </div>
                <div class="card-body">
                    <select name="filter_assigned_to" class="form-control filter" id="filter_assigned_to" multiple>
                    </select>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h6>Created On</h6>
            </div>
            <div class="card-body">
                <div class="row"> 
                    <div class="col-6 mb-1">
                        <label for="filter_from_date" class="form-label">From:</label>
                        <input type="date" id="filter_from_date" class="form-input form-control">
                    </div>
                    <div class="col-6 mb-1">
                        <label for="filter_to_date" class="form-label">To:</label>
                        <input type="date" id="filter_to_date" class="form-input form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@if (session('user_permissions.adminmodule.techsupport.add') == '1')
    @section('addnew')
        {{ route('admin.addtechsupport') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add Ticekt"
            class="btn btn-sm btn-primary">
            <span class="">+ Ticket</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Details</th>
                <th>Ticket Number</th>
                <th>Status</th>
                <th>createdon</th>
                <th>Action</th>
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

            let table = '';

            // refresh tooltip for dynamic data
            function managetooltip() {
                $('body').find('[data-toggle="tooltip"]').tooltip('dispose');
                // Reinitialize tooltips
                $('body').find('[data-toggle="tooltip"]').tooltip();
            }

            // get user data and return new promise
            function getUserData() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('user.techsupportindex') }}",
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

            // get filterd tech data if has any filter otherwise called without filter and return new promise
            function loadFilters() {
                return new Promise((resolve, reject) => {
                    var filterData = JSON.parse(sessionStorage.getItem('filterData'));

                    if (filterData) {

                        $.each(filterData, function(key, value) {
                            if (value != ' ') {
                                $(`#${key}`).val(value);
                            }
                        });

                        loaddata();
                        $('#filter_status').val('');
                        $('#filter_assigned_to').val('');
                        sessionStorage.removeItem('filterData');

                        // Trigger change event to ensure multiselect UI updates
                        $('#filter_status, #filter_assigned_to').trigger('change');

                        sessionStorage.removeItem('filterData');
                        resolve(); // Resolve the promise here after all actions
                    } else {
                        // If no filter data, resolve immediately
                        resolve();
                        loaddata();
                    }
                });
            }

            // intialize  function async and await
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
                                `<option value="${value.id}">${optionValue}</option>`);
                        });
                        $('#filter_assigned_to').val('');
                        $('#filter_assigned_to').select2({
                            search: true,
                            placeholder: 'Select  User',
                            mulitple: true,
                            allowClear: true // Optional: adds "clear" (x) button
                        }); // search bar in assinged to user list
                    } else if (userDataResponse.status == 500) {
                        Toast.fire({
                            icon: "error",
                            title: userDataResponse
                        });
                        loaderhide();
                    } else {
                        $('#filter_assigned_to').append(`<option> No User Found </option>`);
                        loaderhide();
                    }

                    // Load filters
                    await loadFilters(); 

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
            // make multiple dropdown to designable multiple dropdown
            $('#filter_status').val('');
            $('#filter_status').select2({
                search: true,
                placeholder: 'Select Status',
                mulitple: true,
                allowClear: true // Optional: adds "clear" (x) button
            }); // search bar in status

            // get and set customer support history list in the table
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
                        url: "{{ route('techsupport.index') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.filter_status = $('#filter_status').val();
                            d.filter_assigned_to = $('#filter_assigned_to').val();
                            d.filter_from_date = $('#filter_from_date').val();
                            d.filter_to_date = $('#filter_to_date').val();
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Somethint went wrong!'
                                })
                            }

                            global_response = json;

                            return json.data;
                        },
                        complete:function(){
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
                            data: 'first_name',         // key from JSON (used for searching & sorting)
                            name: 'first_name',         // server-side field name or alias
                            orderable: false,          // if you want to disable sorting
                            searchable: false,
                            render: function (data, type, row) {
                                return `
                                    <span class="view-btn d-flex mb-2" data-view="${row.id}" data-toggle="modal" data-target="#exampleModalScrollable" style="cursor:pointer;">
                                        <b><i class="fas fa-user pr-2"></i></b> ${row.first_name || ''} ${row.last_name || ''}
                                    </span>
                                    <span class="d-flex mb-2">
                                        <b><i class="fas fa-envelope pr-2"></i></b>
                                        <a href="mailto:${row.email}" style="text-decoration:none;">${row.email || ''}</a>
                                    </span>
                                    <span class="d-flex mb-2">
                                        <b><i class="fas fa-phone-alt pr-2"></i></b>
                                        <a href="tel:${row.contact_no}" style="text-decoration:none;">${row.contact_no || ''}</a>
                                    </span>
                                    @if (session('user_id') == 1)
                                        <span class="d-flex mb-2"><b><i class="fas fa-building pr-2"></i></b> ${row.company_name || ''}</span>
                                    @endif
                                `;
                            }
                        },
                        {
                            data: 'ticket',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'ticket'
                        },  
                        {
                            data: 'status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'status',
                            render : function(data, type, row){
                                @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                    return `     
                                        <select class="status form-control-sm" data-original-value="${row.status}" data-statusid=${row.id} id='status_${row.id}'>
                                            <option value='pending' ${row.status == 'pending' ? 'selected' : ''}>Pending</option>
                                            <option value='in_progress' ${row.status == 'in_progress' ? 'selected' : ''}>In Progress</option>
                                            <option value='resolved' ${row.status == 'resolved' ? 'selected' : ''}>Resolved</option>
                                            <option value='cancelled' ${row.status == 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                        </select>
                                    `;
                                @else
                                    return ` ${row.status.replace('_',' ') || ''}`;
                                @endif
                            }
                        },
                        {
                            data: 'created_at_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'created_at_formatted'
                        }, 
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.adminmodule.techsupport.edit') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Send Message">
                                            <a title="Send Whatapp Message" class='btn btn-success btn-sm my-1' target="_blank" href="https://wa.me/${row.contact_no}">
                                                <i class="ri-whatsapp-line text-white"></i>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if(session('user_permissions.adminmodule.techsupport.edit') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Ticket">
                                            <button type="button" data-id='${row.id}' class="btn btn-warning btn-rounded btn-sm my-1 editbtn">
                                                <i class="ri-edit-fill"></i>
                                            </button>  
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.adminmodule.techsupport.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Ticket">
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
                        // Ensure ticket.attachment is an array
                        let attachments = Array.isArray(ticket.attachment) ? ticket.attachment : (
                            ticket.attachment ? JSON.parse(ticket.attachment) : []);
                        $('#details').append(`
                            <tr> 
                                <th>Ticket Number</th>
                                <td>${ticket.ticket}</td>
                            </tr> 
                            <tr> 
                                <th>First Name</th>
                                <td>${ticket.first_name}</td>
                            </tr> 
                            <tr> 
                                <th>Last Name</th>
                                <td>${ticket.last_name}</td>
                            </tr> 
                            <tr>
                                <th>Email</th>
                                <td>${ticket.email}</td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td>${ticket.contact_no}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>${ticket.status}</td>
                            </tr>
                            <tr>
                                <th>Module Name</th>
                                <td>${ticket.module_name}</td>
                            <tr>
                                <th>Issue type</th>
                                <td>${ticket.issue_type}</td>
                            </tr>
                            <tr>
                                <th>Created On</th>
                                <td>${ticket.created_at_formatted}</td>
                            </tr>
                            <tr>
                                <th >Notes</th>
                                <td class='text-wrap'><div>${ticket.description ? decodeHTML(ticket.description) : '-'}</div></div></td>
                            </tr>
                            <tr>
                                <th >Remarks</th>
                                <td class='text-wrap'>${ticket.remarks != null ? ticket.remarks : '-'}</td>
                            </tr>
                            <tr>
                                <th>Attachments</th>
                                <td>
                                    ${attachments.length > 0
                                        ? attachments.map(attachment => 
                                            `<a class='text-primary font-weight-bold' href='/uploads/files/${attachment}' target='_blank'>${attachment}</a>`
                                        ).join('<br>') // Display each attachment on a new line
                                        : '-'
                                    }
                                </td>
                            </tr>
                        `);
                    }
                });
            });

            function decodeHTML(html) {
                let txt = document.createElement("textarea");
                txt.innerHTML = html;
                return txt.value;
            }


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
                        var statusvalue = $(`#${fieldid}`).val();
                        element.data('original-value', statusvalue);
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('techsupport.changestatus') }}",
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
                        $(`#${fieldid}`).val(oldstatus);
                    }
                );
            })

            //  on click edit button this will be save advanced filter data on 
            // local server session and redirect update page
            $(document).on("click", '.editbtn', function() {
                editid = $(this).data('id');
                // loadershow();
                filter_from_date = $('#filter_from_date').val();
                filter_to_date = $('#filter_to_date').val();
                filter_status = $('#filter_status').val();
                filter_assigned_to = $('#filter_assigned_to').val(); 


                data = {
                    filter_from_date,
                    filter_to_date,
                    filter_status,
                    filter_assigned_to 
                }

                sessionStorage.setItem('filterData', JSON.stringify(data));

                // console.log(data);
                var editTechSupportUrl = "{{ route('admin.edittechsupport', '__editid__') }}".replace(
                    '__editid__', editid);
                window.location.href = editTechSupportUrl;
            });


            // delete customer support record
            $(document).on("click", ".dltbtn", function() {

                var id = $(this).data('uid');
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, ', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();

                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('techsupport.delete') }}",
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
                                    });;
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
                    }
                );

            });

 
            // call advance filter function on change sidebar filter
            $('.applyfilters').on('click', function(e) {
                e.preventDefault();
                table.draw();
                hideOffCanvass(); // close OffCanvass
            });


            // remove all filters
            $('.removefilters').on('click', function() {
                $('#filter_from_date').val('');
                $('#filter_from_date').val(''); 
                // clear 
                $('#filter_status').val(null).trigger('change');
                $('#filter_assigned_to').val(null).trigger('change');
                table.draw();
                hideOffCanvass(); // close OffCanvass
            });

        });
    </script>
@endpush
