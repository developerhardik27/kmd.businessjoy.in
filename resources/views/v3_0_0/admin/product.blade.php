@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.mastertable')
@section('page_title')
    {{ config('app.name') }} - Products
@endsection
@section('table_title')
    Products
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
@if (session('user_permissions.inventorymodule.product.add') == '1')
    @section('addnew')
        {{ route('admin.addproduct') }}
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary">
            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Product" class="">+ Add
                New</span>
        </button>
    @endsection
@endif

@section('table-content')
    <table id="data"
        class="table display table-bordered table-responsive-sm table-responsive-md table-striped text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Product</th> 
                <th>Status</th>
                <th>Inventory</th>
                <th>Category</th>
                <th>Type</th>
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
            // fetch & show products data in table
            function loaddata() {
                loadershow();
                $.ajax({
                    type: 'GET',
                    url: "{{ route('product.index') }}",
                    data: {
                        user_id: {{ session()->get('user_id') }},
                        company_id: {{ session()->get('company_id') }},
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        // Clear and destroy the existing DataTable instance
                        if ($.fn.dataTable.isDataTable('#data')) {
                            $('#data').DataTable().clear().destroy();
                        }

                        // Clear the existing table body
                        $('#tabledata').empty();
                        if (response.status == 200 && response.product != '') {
                            global_response = response;
                            var id = 1;
                            // You can update your HTML with the data here if needed
                            $.each(response.product, function(key, value) {
                                $('#tabledata').append(`<tr>
                                                    <td>${id}</td>
                                                    <td>${value.name}</td> 
                                                    <td>${value.is_active == 1 ?  `<span class="badge border border-success text-success">Active</span>`:`<span class="badge border border-error text-error">InActive</span>`}</td>
                                                    <td>
                                                        ${value.track_quantity == 1 ? `<span ${value.available_stock < 10 ? `class="text-danger"` : ''} >${value.available_stock} in stock </span>`: 'Inventory not tracked'}
                                                    </td>
                                                    <td>
                                                        ${value.category_name != null ? value.category_name : ''}
                                                    </td>
                                                    <td>
                                                        ${value.product_type != null ? value.product_type : ''}
                                                    </td>
                                                    @if (session('user_permissions.inventorymodule.product.edit') == '1' ||
                                                            session('user_permissions.inventorymodule.product.delete') == '1')
                                                        <td>
                                                            @if (session('user_permissions.inventorymodule.product.edit') == '1')
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                                                    <a href='EditProduct/${value.id}'>
                                                                        <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                                            <i class="ri-edit-fill"></i>
                                                                        </button>
                                                                    </a>
                                                                </span>
                                                            @endif
                                                            @if (session('user_permissions.inventorymodule.product.delete') == '1')
                                                                <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
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
                             // After appending "No Data Found", re-initialize DataTable so it works properly
                             $('#data').DataTable({
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
            // call function for show data in table
            loaddata();


            // delete product
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
                        let productDeleteUrl = "{{ route('product.delete', '__deleteId__') }}".replace(
                            '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: productDeleteUrl,
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
                                        title: 'something went wrong!'
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

            //view product detail
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.product, function(key, product) {
                    if (product.id == data) {
                        $('#details').append(`
                            <tr>
                                <th>Product Name</th>
                                <td>${product.name ? product.name : '-'}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>${product.description ? product.description : '-'}</td>
                            </tr>
                            <tr>
                                <th>SKU (Stock keeping Unit)</th>
                                <td>${product.sku ? product.sku : '-'}</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>${product.unit ? product.unit : '-'}</td>
                            </tr>
                            <tr>
                                <th>Price Per Unit</th>
                                <td>${product.price_per_unit ? product.price_per_unit : '-'}</td>
                            </tr>
                        `);

                    }
                });
            });
        });
    </script>
@endpush
