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
        class="table table-bordered display table-striped w-100">
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
            var table = '';
            // load bank details data in table 
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
                        url: "{{ route('productcategory.datatable') }}",
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
                            data: 'cat_name',
                            name: 'cat_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'parent_id',
                            name: 'parent_id',
                            orderable: false,
                            searchable: false,
                            defaultContent: ' ',
                            render: function(data, type, row) {
                                if(data != null){
                                    // return parent category name
                                    return fetchParentCategoryName(data,global_response.data);
                                }
                            }
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                statusBtn = `
                                    <span id="status_${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="Active">
                                        <button data-status="${row.id}" class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button>
                                    </span>
                                `;
                                if (data == 1) {
                                    statusBtn = `
                                        <span id="status_${row.id}" data-toggle="tooltip" data-placement="bottom" data-original-title="InActive">
                                            <button data-status="${row.id}" class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button>
                                        </span>
                                    `;
                                }
                                return statusBtn;
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let actionBtns = ``;
                                 @if (session('user_permissions.inventorymodule.productcategory.edit') == '1')
                                    let productcategoryEditUrl =
                                    "{{ route('admin.editproductcategory', '__productcategoryId__') }}"
                                    .replace('__productcategoryId__', row.id);
                                    actionBtns += `
                                        <span>
                                            <a href="${productcategoryEditUrl}">
                                                <button data-toggle="tooltip" data-placement="bottom" data-original-title="Edit" type="button" data-id='${row.id}' class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i  class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.inventorymodule.productcategory.delete') == '1')
                                    actionBtns += `
                                        <span>
                                            <button data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" type="button" data-id= '${row.id}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0">
                                                <i  class="ri-delete-bin-fill"></i>
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
        });
    </script>
@endpush
