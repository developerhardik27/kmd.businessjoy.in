@extends('admin.mastertable')
@section('page_title')
    Users
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
@if (session('user_permissions.invoicemodule.user.add') === '1')
    @section('addnew')
        {{ route('admin.adduser') }}
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
                <th>Id</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>ContactNo</th>
                <th>CompanyName</th>
                <th>UserRole</th>
                <th>status</th>
                <th>View</th>
                <th>Action</th>
            </tr>
        </thead>

    </table>
@endsection

@push('ajax')
    <script>
        $('document').ready(function() {
            var global_response = '';
            // fetch & show user data in table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('user.index') }}',
                    data: {
                        user_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.user != '') {
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed     
                            $.each(response.user, function(key, value) {
                                $('#data').append(`<tr>
                                                        <td>${id}</td>
                                                        <td>${value.firstname}</td>
                                                        <td> ${value.is_lastname !== '' ? value.lastname  : ''} </td>
                                                        <td>${value.email}</td>
                                                        <td>${value.contact_no}</td>
                                                        <td>${value.company_name}</td>
                                                        <td>${value.user_role}</td>
                                                        <td>
                                                            @if(session('user_permissions.invoicemodule.user.edit') === '1') 
                                                                ${value.is_active === 1 ? '<div id=status_'+value.id+ '> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></div>'  : '<div id=status_'+value.id+ '><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button></div>'}
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(session('user_permissions.invoicemodule.user.view') === '1') 
                                                                <span>
                                                                    <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                        <i class="ri-indent-decrease"></i>
                                                                    </button>
                                                                </span>
                                                            @else
                                                              -    
                                                            @endif
                                                        </td>
                                                        @if (session('user_permissions.invoicemodule.user.edit') === '1' || session('user_permissions.invoicemodule.user.delete') === '1')
                                                            <td>
                                                                @if(session('user_permissions.invoicemodule.user.edit') === '1') 
                                                                    <span>
                                                                        <a href='EditUser/${value.id}'>
                                                                            <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                                <i class="ri-edit-fill"></i>
                                                                            </button>
                                                                        </a>
                                                                    </span>
                                                                @endif
                                                                @if(session('user_permissions.invoicemodule.user.delete') === '1') 
                                                                    <span class="">
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
                            });
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize datatable
                            });
                            loaderhide();
                        } else {
                            $('#data').append(`<tr><td colspan='10' >No Data Found</td></tr>`);
                            loaderhide();
                        }
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }
            //call function for load user in table
            loaddata();

            //  user status update deactive              
            $(document).on("click", ".status-active", function() {
                if (confirm('Are you really want to change status to inactive ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/user/statusupdate/' + statusid,
                        data: {
                            status: '0',
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button>'
                                );
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
                            }
                        }
                    });
                }
            });

            //  user status update  active            
            $(document).on("click", ".status-deactive", function() {
                if (confirm('Are you really want to change status to active ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/user/statusupdate/' + statusid,
                        data: {
                            status: '1',
                            token: "{{ session()->get('api_token') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                );
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
                            }
                        }
                    });
                }
            });

            // record delte 
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/user/delete/' + $deleteid,
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
                $.each(global_response.user, function(key, user) {
                    if (user.id == data) {
                        $.each(user, function(fields, value) {
                            if ((fields == 'created_by' && fields != null) || (fields ==
                                    'updated_by' && fields != null)) {
                                var name = value;
                                $.each(global_response.user, function(userkey, username) {

                                    if (username.id == name) {
                                        $('#details').append(`<tr>
                                    <th>${fields}</th>                         
                                    <td>${username.firstname + ' ' + username.lastname}</td>
                                    </tr>`)
                                    }
                                });
                            } else {
                                $('#details').append(`<tr>
                                    <th>${fields}</th>                         
                                    <td>${value}</td>
                                    </tr>`)
                            }

                        })
                    }
                });
            });
        });
    </script>
@endpush
