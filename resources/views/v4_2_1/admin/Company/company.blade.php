@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Company
@endsection
@section('table_title')
    Company
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

@if (session('user_permissions.adminmodule.company.add') == '1')
    @section('addnew')
        {{ route('admin.addcompany') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Company" class="">+ Add
                New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data" class="table table-bordered display table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>ContactNo</th>
                <th>status</th>
                <th>View</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            /**
             * api token , companyId and userId  are required in every ajax request for all action *************
             * response status == 200 that means response succesfully recieved
             *  response status == 500 that means database not found
             *  response status == 422 that means api has not got valid or required data
             */


            var global_response = ''; // declare global variable for use company detail globally
            // function for get company data and load into table
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
                        url: "{{ route('company.index') }}",
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
                            data: 'email',
                            name: 'email',
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
                            data: 'is_active',
                            name: 'is_active',
                            orderable: true,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let actionBtns = '';

                                @if (session('user_permissions.adminmodule.company.edit') == '1')
                                    if (data == 1) {
                                        actionBtns += `
                                            <div id="status_${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="Inactive">
                                                <button data-status="${row.id}" class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button>
                                            </div>
                                        `;
                                    } else {
                                        actionBtns += `
                                            <div id=status_"${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="Active">
                                                <button data-status= "${row.id}" class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button>
                                            </div>
                                        `;
                                    }
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
                                let actionBtns = '';

                                @if (session('user_permissions.adminmodule.company.view') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button" data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
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
                                let actionBtns = '';

                                @if (session('user_permissions.adminmodule.company.edit') == '1')
                                    var editCompanyUrl =
                                        "{{ route('admin.editcompany', '__companyId__') }}"
                                        .replace(
                                            '__companyId__', data);
                                    actionBtns += ` 
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <a href="${editCompanyUrl}">
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.adminmodule.company.delete') == '1')
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
            //call loaddata function for make ajax call
            loaddata(); // this function is for get company details

            //  Company status update active to deactive              
            $(document).on("click", ".status-active", function() {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change status to inactive?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var statusid = element.data('status');
                        chanecompanystatus(statusid, 0);
                    },

                )
            });

            //  Company status update deactive to  active            
            $(document).on("click", ".status-deactive", function() {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to change status to active?', // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        var statusid = element.data('status');
                        chanecompanystatus(statusid, 1);
                    },

                )
            });

            function chanecompanystatus(companyid, statusvalue) {
                let companyStatusUpdateUrl = "{{ route('company.statusupdate', '__companyId__') }}"
                    .replace('__companyId__', companyid);
                $.ajax({
                    type: 'PUT',
                    url: companyStatusUpdateUrl,
                    data: {
                        status: statusvalue,
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            if (statusvalue == 1) {
                                $('#status_' + companyid).html('<button data-status= ' +
                                    companyid +
                                    ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                );
                            } else {
                                $('#status_' + companyid).html('<button data-status= ' +
                                    companyid +
                                    ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button>'
                                );
                            }
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "something went wrong!"
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


            // delete company             
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
                        let companyDeleteUrl = "{{ route('company.delete', '__deleteId__') }}".replace(
                            '__deleteId__', deleteid);
                        $.ajax({
                            type: 'POST',
                            url: companyDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                user_id: "{{ session()->get('user_id') }}",
                                company_id: "{{ session()->get('company_id') }}"
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    table.draw();
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                } else if (response.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: response.message
                                    });
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: "something went wrong!"
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

            // view company data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, company) {
                    if (company.id == data) {
                        console.log(company);
                        $('#details').append(`
                                    <tr>
                                        <th>Name</th>
                                        <td>${company.name || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>${company.email  || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact</th>
                                        <td>${company.contact_no || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>GST Number</th>
                                        <td>${company.gst_no  || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>${[company.house_no_building_name,company.road_name_area_colony].join(',')}</td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td>${company.city_name || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>State</th>
                                        <td>${company.state_name  || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Contry</th>
                                        <td>${company.country_name  || '-'}</td>
                                    </tr> 
                                    <tr>
                                        <th>Created On</th>
                                        <td>${company.created_at_formatted  || '-'}</td>
                                    </tr> 
                                    @if (session('admin_role') == 1) 
                                        <tr>
                                            <th>Created By</th>
                                            <td>${[company.creator_firstname,company.creator_lastname ].join(' ')}</td>
                                        </tr> 
                                    @endif
                            `);
                    }
                });
            });
        });
    </script>
@endpush
