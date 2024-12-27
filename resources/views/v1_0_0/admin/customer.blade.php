@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')
@section('page_title')
    {{ config('app.name') }} - Customers
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
@if (session('user_permissions.invoicemodule.customer.add') == '1')
    @section('addnew')
        {{ route('admin.addinvoicecustomer') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Customer"  class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data" class="table display table-bordered table-responsive-sm table-responsive-md table-responsive-lg  table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>CompanyName</th>
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

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';

            // function for  get customers data and set it into table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('customer.index') }}',
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.customer != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed                              
                            $.each(response.customer, function(key, value) {
                                $('#data').append(`<tr>
                                                    <td>${id}</td>
                                                    <td>${value.firstname}</td>
                                                    <td>${value.lastname}</td>
                                                    <td>${(value.company_name != null) ?value.company_name : '-'}</td>
                                                    <td>${(value.contact_no != null) ?value.contact_no : '-'}</td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.edit') == '1')
                                                            ${value.is_active == 1 ? '<span data-toggle="tooltip" data-placement="bottom" data-original-title="InActive" id=status_'+value.id+ '> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></span>'  : '<span data-toggle="tooltip" data-placement="bottom" data-original-title="Active" id=status_'+value.id+ '><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button></span>'}
                                                        @else
                                                          -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (session('user_permissions.invoicemodule.customer.view') == '1')
                                                            <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Customer Details">
                                                                <button type="button" data-view = '${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                    <i class="ri-indent-decrease"></i>
                                                                </button>
                                                            </span>
                                                        @else
                                                          -    
                                                        @endif
                                                    </td>
                                                    @if (session('user_permissions.invoicemodule.customer.edit') == '1' ||
                                                            session('user_permissions.invoicemodule.customer.delete') == '1')
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.customer.edit') == '1')
                                                                <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Customer">
                                                                    <a href='EditCustomer/${value.id}'>
                                                                        <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                            <i class="ri-edit-fill"></i>
                                                                        </button>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.invoicemodule.customer.delete') == '1')
                                                                <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Customer Details">
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
                                "destroy": true, //use for reinitialize datatable
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#data').append(`<tr><td colspan='8' >No Data Found</td></tr>`);
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
            //call data function for load customer data
            loaddata();

            //  customer status update active to deactive              
            $(document).on("click", ".status-active", function() {
                if (confirm('Are you really want to change status to inactive ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/customer/statusupdate/' + statusid,
                        data: {
                            status: '0',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                loaddata();
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

            //  customer status update  deactive to active            
            $(document).on("click", ".status-deactive", function() {
                if (confirm('Are you really want to change status to active ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/customer/statusupdate/' + statusid,
                        data: {
                            status: '1',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                loaddata();
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


            // record delete 
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var $deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/customer/delete/' + $deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            loaderhide();
                            if (response.status == 200) {
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            }
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

            // view record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.customer, function(key, customer) {
                    if (customer.id == data) { 
                            $('#details').append(`
                                <tr>
                                    <th>Customer Company Name</th>       
                                    <td>${(customer.company_name != null) ?customer.company_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name</th>       
                                    <td>${(customer.firstname != null) ?customer.firstname : '-'} ${(customer.lastname != null) ?customer.lastname : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>       
                                    <td>${(customer.email != null) ?customer.email : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>       
                                    <td>${(customer.contact_no != null) ?customer.contact_no : '-'}</td>
                                </tr>
                                <tr>
                                    <th>GST Number</th>       
                                    <td>${(customer.gst_no != null) ?customer.gst_no : '-'}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>       
                                    <td>${(customer.house_no_building_name != null) ? customer.house_no_building_name : '-'} , ${customer.road_name_area_colony != null ? customer.road_name_area_colony : ''}</td>
                                </tr>
                                <tr>
                                    <th>Pincode</th>       
                                    <td>${(customer.pincode != null) ?customer.pincode : '-'}</td>
                                </tr>
                                <tr>
                                    <th>City</th>       
                                    <td>${(customer.city_name != null) ?customer.city_name : '-'}</td>
                                </tr>
                                <tr>
                                    <th>State</th>       
                                    <td>${(customer.state_name != null) ?customer.state_name : ''}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>       
                                    <td>${(customer.country_name != null) ?customer.country_name : ''}</td>
                                </tr>
                            `);
                    

                    }
                });

            });

        });
    </script>
@endpush
