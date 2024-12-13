@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.masterlayout')

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
                    <input type="text" class="form-control form-input" name="category_name" placeholder="Category Name"
                        id="category_name">
                    <span class="error-msg" id="error-category_name" style="color: red"></span>
                </div>
                <div class="col-sm-2 mt--2">
                    <button type="submit" data-toggle="tooltip" data-placement="bottom" data-original-title="Submit"
                        class="btn btn-primary"><i class="ri-check-line"></i></button>
                    <button type="reset" class="btn iq-bg-danger" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Reset"><i class="ri-refresh-line"></i></button>
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
    <table id="data"
        class="table  table-bordered display table-responsive-sm table-responsive-md table-striped text-center">
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


            $('#newCategoryBtn').on('click', function(e) {
                e.preventDefault();
                $('#newblogcategoryform').removeClass('d-none');
                $('#newCategoryBtnDiv').addClass('d-none');
            })


            // fetch column name and append into column list table
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $('.error-msg').text('');
                $.ajax({
                    type: 'GET',
                    url: "{{ route('blogcategory.index') }}",
                    data: {
                        token: "{{ session()->get('api_token') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        user_id: "{{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.blogcategory != '') {
                            global_response = response;
                            var id = 1;
                            $.each(response.blogcategory, function(key, value) {
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td>${value.cat_name}</td>
                                                        <td>  
                                                            <span>
                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit Category" data-id='${value.id}'
                                                                     class="btn edit-btn btn-success btn-rounded btn-sm my-1">
                                                                    <i class="ri-edit-fill"></i>
                                                                </button>
                                                            </span>
                                                            <span>
                                                                <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Category" data-id= '${value.id}'
                                                                    class=" del-btn btn btn-danger btn-rounded btn-sm my-1">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        </td>
                                                    </tr>`)
                                id++;
                            });
                            $('[data-toggle="tooltip"]').tooltip('dispose');
                            $('[data-toggle="tooltip"]').tooltip();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            $('#tabledata').append(`<tr><td colspan='3' >No Data Found</td></tr>`)
                        }
                        loaderhide();
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            //call function for loaddata
            loaddata();


            // edit column if it is not used for any invoice
            $(document).on("click", ".edit-btn", function() {
                if (confirm("You want edit this Category ?")) {
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
                                toastr.error(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            loaderhide();
                        },
                        error: function(error) {
                            loaderhide();
                            console.error('Error:', error);
                        }
                    });
                }
            });


            // delete column if it is not has data of any invoice
            $(document).on("click", ".del-btn", function() {
                if (confirm(
                        'Are you sure to delete this category?'
                    )) {
                    var deleteid = $(this).data('id');
                    var row = this;
                    loadershow();
                    let blogCategoryDeleteUrl = "{{ route('blogcategory.delete', '__deleteId__') }}"
                        .replace('__deleteId__', deleteid);
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
                                toastr.success(response.message);
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                            loaderhide();
                        },
                        error: function(error) {
                            loaderhide();
                            toastr.error('Something Went Wrong !');
                        }
                    });
                }
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
                            toastr.success(response.message);
                            $('#category_name').val('');
                            loaddata();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                        } else {
                            toastr.error(response.message);
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
                            toastr.error(errorMessage);
                        }
                    }
                });
            });
        });
    </script>
@endpush
