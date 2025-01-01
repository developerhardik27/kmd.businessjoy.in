@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')
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
    <table id="data"
        class="dataTable display no-footer table table-bordered table-responsive table-responsive-lg table-responsive-md table-responsive-sm table-responsive-xl table-striped text-center">
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
            // fetch & show user data in table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('user.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.user != '') {
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed     
                            $.each(response.user, function(key, value) {
                                var userLogin =
                                    "{{ route('admin.superadminloginfromanyuser', '__userId__') }}"
                                    .replace('__userId__', value.id);
                                $('#data').append(`<tr>
                                                        <td>${id}</td>
                                                        <td>${value.firstname  != null ? value.firstname : '-'}</td>
                                                        <td> ${value.lastname != null ? value.lastname  : '-'} </td>
                                                        <td>${value.email != null ? value.email : '-'}</td>
                                                        <td>${value.contact_no != null ? value.contact_no : '-'}</td>
                                                        <td>${value.company_name != null ? value.company_name : '-'}</td>
                                                        <td>${value.user_role != null ? value.user_role : '-'}</td>
                                                        <td>
                                                            @if (session('user_permissions.adminmodule.user.edit') == '1') 
                                                                ${value.is_active == 1 ? '<div id=status_'+value.id+ ' data-toggle="tooltip" data-placement="bottom" data-original-title="Inactive"> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></div>'  : '<div id=status_'+value.id+ ' data-toggle="tooltip" data-placement="bottom" data-original-title="Active"><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button></div>'}
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                        @if (session('user_id') == 1)
                                                            <td>
                                                                <span>
                                                                    <a href="${userLogin}" class="view-btn btn btn-outline-primary btn-rounded btn-sm my-0">
                                                                        Login
                                                                    </a>
                                                                </span>
                                                            </td>    
                                                        @endif
                                                        <td>
                                                            @if (session('user_permissions.adminmodule.user.view') == '1') 
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="View User Details">
                                                                    <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                        <i class="ri-indent-decrease"></i>
                                                                    </button>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        @if (session('user_permissions.adminmodule.user.edit') == '1' ||
                                                                session('user_permissions.adminmodule.user.delete') == '1')
                                                            <td>
                                                                @if (session('user_permissions.adminmodule.user.edit') == '1') 
                                                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                                                        <a href='EditUser/${value.id}'>
                                                                            <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                                <i class="ri-edit-fill"></i>
                                                                            </button>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                                @if (session('user_permissions.adminmodule.user.delete') == '1') 
                                                                    <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                                                        <button type="button" data-id= '${value.id}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                                            <i class="ri-delete-bin-fill"></i>
                                                                        </button>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        @else
                                                          <td> - </td>  
                                                        @endif    
                                                    </tr>`)
                                id++;
                                $('[data-toggle="tooltip"]').tooltip('dispose');
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                            var search = {!! json_encode($search) !!}

                            $('#data').DataTable({

                                "search": {
                                    "search": search
                                },
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#data').append(`<tr><td colspan='10' >No Data Found</td></tr>`);
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
                                    $(row).closest("tr").fadeOut();
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

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view'); // get userid
                $.each(global_response.user, function(key, user) {
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
