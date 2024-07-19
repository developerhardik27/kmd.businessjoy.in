@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')

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
    <table id="data"
        class="table  table-bordered display table-responsive-sm table-responsive-md  table-striped text-center">
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
        <tbody>

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

            var global_response = ''; // declare global variable for using company detail globally
            // function for get company data and load into table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('company.index') }}',
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // You can update your HTML with the data here if needed
                        if (response.status == 200 && response.company != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.company, function(key, value) {
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td>${value.name != null ? value.name : '-'}</td>
                                                    <td>${value.email != null ? value.email : '-'}</td>
                                                    <td>${value.contact_no != null ? value.contact_no : '-'}</td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.edit') == '1') 
                                                            ${value.is_active == 1 ? '<div id=status_'+value.id+ ' data-toggle="tooltip" data-placement="bottom" data-original-title="Deactive"> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></div>'  : '<div id=status_'+value.id+ ' data-toggle="tooltip" data-placement="bottom" data-original-title="Active"><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button></div>'}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.adminmodule.company.view') == '1')
                                                            <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                                                <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                    <i class="ri-indent-decrease"></i>
                                                                </button>
                                                            </span>
                                                        @else
                                                          -
                                                        @endif
                                                    </td>
                                                    @if (session('user_permissions.adminmodule.company.edit') == '1' ||
                                                            session('user_permissions.adminmodule.company.delete') == '1')
                                                        <td> 
                                                            @if (session('user_permissions.adminmodule.company.edit') == '1')
                                                                <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                                                    <a href='EditCompany/${value.id}'>
                                                                        <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                            <i class="ri-edit-fill"></i>
                                                                        </button>
                                                                    </a>
                                                                </span>
                                                            @endif                                                        
                                                            @if (session('user_permissions.adminmodule.company.delete') == '1')
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
                                </tr>`);
                                id++;
                                $('[data-toggle="tooltip"]').tooltip('dispose');
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize jquery datatable
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#data').append(`<tr><td colspan='6' >No Data Found</td></tr>`);
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr.responseText); // Log the full error response for debugging
                        var errorMessage = "";
                        try {
                            var responseJSON = JSON.parse(xhr.responseText);
                            errorMessage = responseJSON.message || "An error occurred";
                        } catch (e) {
                            errorMessage = "An error occurred";
                        }
                        toastr.error(errorMessage);
                    }
                });
            }
            //call loaddata function for make ajax call
            loaddata(); // this function is for get company details

            //  Company status update active to deactive              
            $(document).on("click", ".status-active", function() {
                if (confirm('Are you really want to change status to inactive ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/company/statusupdate/' + statusid,
                        data: {
                            status: '0',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button>'
                                );
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error('something went wrong !');
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
                            toastr.error(errorMessage);
                        }
                    });
                }
            });

            //  Company status update deactive to  active            
            $(document).on("click", ".status-deactive", function() {
                if (confirm('Are you really want to change status to active ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/company/statusupdate/' + statusid,
                        data: {
                            status: '1',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                );
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error('something went wrong !');
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
                            toastr.error(errorMessage);
                        }
                    });
                }
            });


            // delete company             
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'post',
                        url: '/api/company/delete/' + $deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            user_id: "{{ session()->get('user_id') }}",
                            company_id: "{{ session()->get('company_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                $(row).closest("tr").fadeOut();
                                toastr.success(response.message);
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error('something went wrong !');
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
                            toastr.error(errorMessage);
                        }
                    });
                }
            });

            // view company data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.company, function(key, company) {
                    if (company.id == data) {
                        console.log(company);
                        $('#details').append(`
                                    <tr>
                                        <th>Name</th>
                                        <td>${company.name != null ? company.name : '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>${company.email  != null ? company.email : '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact</th>
                                        <td>${company.contact_no  != null ? company.contact_no : '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>GST Number</th>
                                        <td>${company.gst_no  != null ? company.gst_no : '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>${(company.house_no_building_name != null) ? company.house_no_building_name : '-'} ${company.road_name_area_colony != null ? company.road_name_area_colony : ''}</td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td>${company.city_name  != null ? company.city_name : '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>State</th>
                                        <td>${company.state_name  != null ? company.state_name : '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Contry</th>
                                        <td>${company.country_name  != null ? company.country_name : '-'}</td>
                                    </tr> 
                            `);
                    }
                });
            });
        });
    </script>
@endpush
