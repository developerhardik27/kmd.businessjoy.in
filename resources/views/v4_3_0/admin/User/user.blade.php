@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Users
@endsection
@section('table_title')
    Users
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
@if (session('user_permissions.adminmodule.user.add') == '1')
    @section('addnew')
        {{ route('admin.adduser') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Add New User" class="">+ Add
                New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data" class="dataTable display table table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>ContactNo</th>
                <th>CompanyName</th>
                <th>UserRole</th>
                <th>Status</th>
                @if (session('user_id') == 1)
                    <th>Login</th>
                @endif
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
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';

            let table = '';

            var search = {!! json_encode($search) !!}


            // fetch & show user data in table
            function loaddata() {
                loadershow();

                table = $('#data').DataTable({
                    search: {
                        search: search
                    },
                    language: {
                        lengthMenu: '_MENU_ &nbsp;Entries per page'
                    },
                    destroy: true, // allows re-initialization
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('user.datatable') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
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
                    columns: [

                        {
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'firstname',
                            name: 'firstname',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'lastname',
                            name: 'lastname',
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
                            data: 'company_name',
                            name: 'company_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'user_role',
                            name: 'user_role',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            orderable: true,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = ``;
                                @if (session('user_permissions.adminmodule.user.edit') == '1')
                                    if (data == 1) {
                                        actionBtns += `<div id="status_${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="Inactive"> 
                                            <button data-status="${row.id}" class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button>
                                        </div>
                                        `;
                                    } else {
                                        actionBtns += `<div id="status_${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="Active">
                                                <button data-status="${row.id}" class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button>
                                            </div>
                                        `;
                                    }
                                @endif
                                return actionBtns;
                            }
                        },
                        @if (session('user_id') == 1)
                            {
                                data: 'id',
                                name: 'id',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    return `
                                        <span>
                                            <button type="button" data-user-id='${data}' class="super-login-btn btn btn-outline-primary btn-rounded btn-sm my-0">
                                                Login
                                            </buton>
                                        </span>
                                    `;
                                }
                            },
                        @endif {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                actionBtns = '';
                                @if (session('user_permissions.adminmodule.user.view') == '1')
                                    actionBtns += ` 
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="View User Details">
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
                            render: function(data, type, row) {
                                actionBtns = '';
                                @if (session('user_permissions.adminmodule.user.edit') == '1')
                                    var editUserUrl = "{{ route('admin.edituser', '__userid__') }}"
                                        .replace('__userid__', data);
                                    actionBtns += ` 
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <a href="${editUserUrl}">
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0 mb-2">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.adminmodule.user.delete') == '1')
                                    actionBtns += ` 
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-id= '${data}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif

                                return actionBtns;
                            }
                        },

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

            //call function for load user in table
            loaddata();

            //  user status update active to deactive              
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
                        changeuserstatus(statusid, 0);
                    }
                );
            });

            //  user status update deactive to  active            
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
                        changeuserstatus(statusid, 1);
                    }
                );
            });

            function changeuserstatus(userid, statusvalue) {
                let userStatusUpdateUrl = "{{ route('user.statusupdate', '__userId__') }}".replace('__userId__',
                    userid);
                $.ajax({
                    type: 'PUT',
                    url: userStatusUpdateUrl,
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
                                $('#status_' + userid).html('<button data-status= ' +
                                    userid +
                                    ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                );
                            } else {
                                $('#status_' + userid).html('<button data-status= ' +
                                    userid +
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
                        let userDeleteUrl = "{{ route('user.delete', '__deleteId__') }}".replace(
                            '__deleteId__',
                            deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: userDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    table.draw();
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

            $(document).on('click','.super-login-btn',function(){
                loadershow(); 
                let targetId = $(this).data('user-id');
                let targetUrl = "{{route('admin.superadminloginfromanyuser','__userId__')}}".replace('__userId',targetId);
                  $.ajax({
                    type: 'get',
                    url: targetUrl, 
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            window.location = response.redirectUrl; // after succesfully data submit redirect on list page
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } 
                    },  
                    error: function(xhr, status, error) { // if calling api request error 
                        // loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        }
                    }
                })
            });

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view'); // get userid
                $.each(global_response.data, function(key, user) {
                    if (user.id == data) { // get user record
                        $('#details').append(`
                                <tr>
                                    <th>Name</th>                         
                                    <td>${user.firstname} ${user.lastname != null ? user.lastname : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>                         
                                    <td>${user.email != null ? user.email : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Contact</th>                         
                                    <td>${user.contact_no != null ? user.contact_no : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Pincode</th>                         
                                    <td>${user.pincode != null ? user.pincode : '-'}</td>
                                </tr>
                                <tr>
                                    <th>City</th>                         
                                    <td>${user.city_name != null ? user.city_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>State</th>                         
                                    <td>${user.state_name != null ? user.state_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>                         
                                    <td>${user.country_name != null ? user.country_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Company Name</th>                         
                                    <td>${user.company_name != null ? user.company_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Created On</th>                         
                                    <td>${user.created_at_formatted != null ? user.created_at_formatted : '-'}</td>
                                </tr>
                                @if (session('admin_role') == 1) 
                                    <tr>
                                        <th>Created By</th>            
                                        <td>${user.creator_firstname} ${user.creator_lastname != null ? user.creator_lastname : '-'}</td>
                                    </tr>
                                @endif
                        `)
                    }
                });
            });
        });
    </script>
@endpush
