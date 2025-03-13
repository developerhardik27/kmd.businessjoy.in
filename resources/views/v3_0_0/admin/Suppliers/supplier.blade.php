@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')
@section('page_title')
    {{ config('app.name') }} - Suppliers
@endsection
@section('table_title')
    Suppliers
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
@if (session('user_permissions.inventorymodule.supplier.add') == '1')
    @section('addnew')
        {{ route('admin.addsupplier') }}
    @endsection
    @section('addnewbutton')
        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Supplier"
            class="btn btn-sm btn-primary">
            <span class="">+ New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data"
        class="table display table-bordered table-responsive-sm table-responsive-md table-responsive-lg  table-striped text-center">
        <thead>
            <tr>
                <th>Supplier Id</th>
                <th>Supplier</th> 
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

            // function for  get suppliers data and set it into datatable
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('supplier.index') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.supplier != '') {
                            $('#data').DataTable().destroy();
                            $('#tabledata').empty();
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed                              
                            $.each(response.supplier, function(key, value) {
                                let editsupplierUrl =
                                    "{{ route('admin.editsupplier', '__supplierid__') }}"
                                    .replace('__supplierid__', value.id);
                                $('#data').append(`<tr>
                                                <td>${value.id}</td>
                                                <td>${value.suppliername ||'-' }</td> 
                                                <td>${(value.company_name != null) ?value.company_name : '-'}</td>
                                                <td>${(value.contact_no != null) ?value.contact_no : '-'}</td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.supplier.edit') == '1')
                                                        ${value.is_active == 1 ?
                                                            `<span data-toggle="tooltip" data-placement="bottom" data-original-title="InActive" id="status_${value.id}">
                                                                <button data-status='${value.id}' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >
                                                                    active
                                                                </button>
                                                            </span>`  : 
                                                            `<span data-toggle="tooltip" data-placement="bottom" data-original-title="Active" id="status_${value.id}">
                                                                <button data-status="${value.id}" class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >
                                                                    Inactive
                                                                </button>
                                                            </span>`
                                                        }
                                                    @else
                                                      -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (session('user_permissions.inventorymodule.supplier.view') == '1')
                                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View supplier Details">
                                                            <button type="button" data-view='${value.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                                                <i class="ri-indent-decrease"></i>
                                                            </button>
                                                        </span>
                                                    @else
                                                      -    
                                                    @endif
                                                </td>
                                                @if (session('user_permissions.inventorymodule.supplier.edit') == '1' || session('user_permissions.inventorymodule.supplier.delete') == '1')
                                                    <td>
                                                        @if (session('user_permissions.inventorymodule.supplier.edit') == '1')
                                                            <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit supplier">
                                                                <a href=${editsupplierUrl}>
                                                                    <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                        <i class="ri-edit-fill"></i>
                                                                    </button>
                                                                </a>
                                                            </span>
                                                        @endif
                                                        @if (session('user_permissions.inventorymodule.supplier.delete') == '1')
                                                            <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete supplier Details">
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
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            $('#data').DataTable({
                                "destroy": true, //use for reinitialize datatable
                            });
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
                        Toast.fire({
                            icon: "error",
                            title: errorMessage
                        });
                    }
                });
            }

            //call data function for load supplier data
            loaddata();

            //  supplier status update active to deactive              
            $(document).on("click", ".status-active", function() {
                element = $(this)
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
                        changesupplierstatus(statusid, 0);
                    }
                );
            });

            //  supplier status update  deactive to active            
            $(document).on("click", ".status-deactive", function() {
                element = $(this)
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
                        changesupplierstatus(statusid, 1);
                    }
                );
            });

            // function for change supplier status (active/inactive)
            function changesupplierstatus(supplierid, statusvalue) {
                let supplierStatusUpdateUrl = "{{ route('supplier.statusupdate', '__supplierId__') }}".replace(
                    '__supplierId__', supplierid);
                $.ajax({
                    type: 'PUT',
                    url: supplierStatusUpdateUrl,
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
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });;
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
                var $deleteid = $(this).data('id');
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
                        let supplierDeleteUrl = "{{ route('supplier.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: supplierDeleteUrl,
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


            // view all details of specific record
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $('#viewmodaltitle').html('<b>supplier Details</b>');
                $.each(global_response.supplier, function(key, supplier) {
                    if (supplier.id == data) {
                        $('#details').append(`
                        <tr>
                            <th>Company Name</th>       
                            <td>${supplier.company_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>supplier Name</th>       
                            <td>${supplier.suppliername || '-'}</td>
                        </tr>
                        <tr>
                            <th>Email</th>       
                            <td>${supplier.email || '-'}</td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>       
                            <td>${supplier.contact_no || '-'}</td>
                        </tr>
                        <tr>
                            <th>GST Number</th>       
                            <td>${supplier.gst_no || '-'}</td>
                        </tr>
                        <tr>
                            <th>Address</th>       
                            <td>${supplier.house_no_building_name || '-'}  ${supplier.road_name_area_colony || ''}</td>
                        </tr>
                        <tr>
                            <th>Pincode</th>       
                            <td>${supplier.pincode || '-'}</td>
                        </tr>
                        <tr>
                            <th>City</th>       
                            <td>${supplier.city_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>State</th>       
                            <td>${supplier.state_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>Country</th>       
                            <td>${supplier.country_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>Created On</th>       
                            <td>${supplier.created_at_formatted || '-'}</td>
                        </tr>  
                    `);
                    }
                });

            });

        });
    </script>
@endpush
