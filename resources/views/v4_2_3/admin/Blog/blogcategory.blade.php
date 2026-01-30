@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Blog Category
@endsection
@section('title')
    Blog Category
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

@section('form-content')
    <form id="blogcategoryform" name="blogcategoryform">
        @csrf
        <div class="form-group">
            <div id="newblogcategoryform" class="form-row d-none">
                <div class="col-sm-5">
                    <input type="hidden" name="token" id="token" value="{{ session('api_token') }}">
                    <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ session('user_id') }}">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="text" maxlength="20" class="form-control form-input" name="category_name"
                        placeholder="Category Name" id="category_name">
                    <span class="error-msg" id="error-category_name" style="color: red"></span>
                </div>
                <div class="col-sm-5">
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Submit"
                        class="btn btn-primary m-1">Save</button>
                    <button type="reset" class="btn iq-bg-danger m-1" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset">Reset</button>
                    <button type="button" id="cancelbtn" class="btn iq-bg-secondary m-1" data-toggle="tooltip"
                        data-placement="bottom" data-original-title="Cancel">Cancel</button>
                </div>
            </div>
            <div id="newCategoryBtnDiv" class="form-row ">
                <div class="col-sm-12">
                    <button type="btn" id="newCategoryBtn" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Add New Category" class="btn btn-primary float-right">+ Add New
                        Category</button>
                </div>
            </div>
        </div>
    </form>
    <hr>
    <table id="data" class="table table-bordered display table-striped w-100">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Category Name</th>
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

            let table = '' ;

            $('#newCategoryBtn').on('click', function(e) {
                e.preventDefault();
                $('#newblogcategoryform').removeClass('d-none');
                $('#newCategoryBtnDiv').addClass('d-none');
            });

            $('#cancelbtn').on('click', function(e) {
                e.preventDefault();
                $('#newblogcategoryform').addClass('d-none');
                $('#newCategoryBtnDiv').removeClass('d-none');
                $('#category_name').val('');
                $('#edit_id').val('');
            });


            // fetch column name and append into column list table
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
                        url: "{{ route('blogcategory.datatable') }}",
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
                            searchable: true,
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
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = `
                                    <span>
                                        <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Category" data-id='${data}'
                                                class="btn edit-btn btn-success btn-rounded btn-sm my-0 mb-2">
                                            <i class="ri-edit-fill"></i>
                                        </button>
                                    </span>
                                    <span>
                                        <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Category" data-id= '${data}'
                                            class=" del-btn btn btn-danger btn-rounded btn-sm my-0 mb-2">
                                            <i class="ri-delete-bin-fill"></i>
                                        </button>
                                    </span>
                                `; 
                                return actionBtns;
                            }
                        }
                    ],

                    pagingType: "full_numbers",
                    drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip({
                            boundary: 'window',
                            offset: '0, 10' // Push tooltip slightly away from the button
                        });
                        $('.error-msg').text('');

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


            // edit column if it is not used for any
            $(document).on("click", ".edit-btn", function() {
                loadershow();
                var editid = $(this).data('id');
                $('#newblogcategoryform').removeClass('d-none');
                $('#newCategoryBtnDiv').addClass('d-none');
                let blogCategoryEditUrl = "{{ route('blogcategory.edit', '__editId__') }}".replace(
                    '__editId__', editid);
                $.ajax({
                    type: 'GET',
                    url: blogCategoryEditUrl,
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.blogcategory != '') {
                            var blogcategorydata = response.blogcategory;
                            $('#edit_id').val(editid);
                            $('#category_name').val(blogcategorydata.cat_name);
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });

            });


            // delete column if it is not has data of any
            $(document).on("click", ".del-btn", function() {
                var deleteid = $(this).data('id');
                var row = this;
                let blogCategoryDeleteUrl =
                    "{{ route('blogcategory.delete', '__deleteId__') }}"
                    .replace('__deleteId__', deleteid);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'if you will delete it, then it will be removed from blog automatically if it in use!', // Text
                    'Yes, delete it!', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    function() {
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: blogCategoryDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: {{ session()->get('company_id') }},
                                user_id: {{ session()->get('user_id') }},
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
                                        title: response.message
                                    });
                                }
                                loaderhide();
                            },
                            error: function(error) {
                                loaderhide();
                                Toast.fire({
                                    icon: "error",
                                    title: "something went wrong!"
                                });
                            }
                        });
                    }
                );
            });


            // add or edit column form submit
            $('#blogcategoryform').submit(function(e) {
                e.preventDefault();
                loadershow();
                var editid = $('#edit_id').val()
                var columndata = $(this).serialize();
                if (editid != '') {
                    url = "{{ route('blogcategory.update', '__editId__') }}".replace('__editId__', editid);
                } else {
                    url = "{{ route('blogcategory.store') }}"
                }
                $.ajax({
                    type: "POST",
                    url: url,
                    data: columndata,
                    success: function(response) {
                        if (response.status == 200) {
                            $('#edit_id').val('');
                            $('#newblogcategoryform').addClass('d-none');
                            $('#newCategoryBtnDiv').removeClass('d-none');
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#category_name').val('');
                            table.draw();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                        } else {
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
                    }
                });
            });
        });
    </script>
@endpush
