@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    Inventory - Product Categories
@endsection
@section('table_title')
    Product Categories
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
@if (session('user_permissions.inventorymodule.productcategory.add') == '1')
    @section('addnew')
        {{ route('admin.addproductcategory') }}
    @endsection
    @section('addnewbutton')
        <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Category"
            class="btn btn-sm btn-primary">
            <span class="">+ Add New</span>
        </button>
    @endsection
@endif
@section('table-content')
    <table id="data"
        class="table  table-bordered display table-responsive-sm table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Category Name</th>
                <th>Parent Category</th>
                <th>Status</th> 
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
            // load bank details data in table 
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('productcategory.index') }}",
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // Clear and destroy the existing DataTable instance
                        if ($.fn.dataTable.isDataTable('#data')) {
                            $('#data').DataTable().clear().destroy();
                        }

                        // Clear the existing table body
                        $('#tabledata').empty();
                        // Check if response has product category data
                        if (response.status == 200 && response.productcategory.length > 0) {
                            // Append the new rows
                            var id = 1;
                            $.each(response.productcategory, function(key, value) {
                                var parentCatName = null;
                                if (value.parent_id != null) {
                                    parentCatName = fetchParentCategoryName(value.parent_id,
                                        response.productcategory);
                                }
                                let productcategoryEditUrl =
                                    "{{ route('admin.editproductcategory', '__productcategoryId__') }}"
                                    .replace('__productcategoryId__', value.id);

                                $('#tabledata').append(`
                                    <tr>
                                        <td>${id}</td>
                                        <td>${value.cat_name != null ? value.cat_name : '-'}</td>
                                        <td>${parentCatName != null ? parentCatName : ' '}</td>
                                        <td>
                                            @if (session('user_permissions.inventorymodule.productcategory.edit') == '1')
                                                ${value.is_active == 1 ? 
                                                    '<span id=status_'+value.id+' data-toggle="tooltip" data-placement="bottom" data-original-title="InActive"> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></span>' : 
                                                    '<span data-toggle="tooltip" data-placement="bottom" data-original-title="Active" id=status_'+value.id+'><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button></span>'
                                                }
                                            @else
                                                -
                                            @endif
                                        </td> 
                                        <td>
                                            @if (session('user_permissions.inventorymodule.productcategory.edit') == '1')
                                                <span>
                                                    <a href="${productcategoryEditUrl}">
                                                        <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit" type="button" data-id= '${value.id}' class="btn btn-success btn-rounded btn-sm my-0">
                                                            <i  class="ri-edit-fill"></i>
                                                        </button>
                                                    </a>
                                                </span>
                                            @else
                                                -
                                            @endif
                                            @if (session('user_permissions.inventorymodule.productcategory.delete') == '1')
                                                <span>
                                                    <button data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" type="button" data-id= '${value.id}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                        <i  class="ri-delete-bin-fill"></i>
                                                    </button>
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                `);
                                id++;
                            });

                            // Reinitialize DataTable after rows are appended
                            $('#data').DataTable({
                                "destroy": true, // Ensures DataTable is reinitialized
                            });

                            // Re-initialize tooltips (since new rows have been added)
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        } else if (response.status == 500) {
                            // Show error message if no data found
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            // After appending "No Data Found", re-initialize DataTable so it works properly
                            $('#data').DataTable({
                            }); 
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) {
                        loaderhide();
                        console.log(xhr.responseText);
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

            //call function for loaddata
            loaddata();


            function fetchParentCategoryName(parentId, records) {
                var parentName = null; // Default to null in case no parent is found.

                $.each(records, function(key, value) {
                    if (value.id == parentId) {
                        parentName = value.cat_name; // Assign the parent category name
                        return false; // Stop the loop once we find the match
                    }
                });

                return parentName; // Return the parent category name
            }

            //  bank status update active to  deactive              
            $(document).on("click", ".status-active", function() {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    "it's all sub category will be also inactive, if exists. still you change status to inactive?", // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        loadershow();
                        var statusid = element.data('status');
                        changeproductcategorystatus(statusid, 0);
                    }
                );
            });

            //  bank status update  deactive to  active            
            $(document).on("click", ".status-deactive", function() {
                element = $(this);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    "it's all sub category will be also active, if exists. still you change status to active?", // Text
                    'Yes, change', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        loadershow();
                        var statusid = element.data('status');
                        changeproductcategorystatus(statusid, 1);
                    }
                );
            });

            // function for change product category status (active/inactive)
            function changeproductcategorystatus(productcategoryid, statusvalue) {
                let productCategoryStatusUpdateUrl =
                    "{{ route('productcategory.statusupdate', '__productcategoryId__') }}".replace(
                        '__productcategoryId__', productcategoryid);
                $.ajax({
                    type: 'PUT',
                    url: productCategoryStatusUpdateUrl,
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

            // delete product category             
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                let productCategoryDeleteUrl = "{{ route('productcategory.delete', '__deleteId__') }}"
                    .replace(
                        '__deleteId__', deleteid);
                var row = this;
                showConfirmationDialog(
                    'Are you sure?', // Title
                    "it's all sub category will be also delete, if exists. still you delete this record?", // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: productCategoryDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
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
        });
    </script>
@endpush
