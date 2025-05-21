@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    Blog
@endsection
@section('table_title')
    Blog
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

@section('addnew')
    {{ route('admin.addblog') }}
@endsection

@section('addnewbutton')
    <button type="button" data-toggle="tooltip" data-placement="bottom" data-original-title="Add New Blog"
        class="btn btn-sm btn-primary">
        <span class="">+ Add New Blog</span>
    </button>
@endsection

@section('table-content')
    <table id="data" class="table table-bordered display table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Published Date</th>
                <th>Published By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
@endsection


@push('ajax')
    <script>
        loaderhide();
    </script>
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';
            // load blog in table 
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
                        url: "{{ route('blog.datatable') }}",
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
                    columns: [{
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'title',
                            name: 'title',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'author',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name:  'author' 
                        }, 
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = `
                                    <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Vist Blog">
                                        <a href="https://oceanmnc.com/${row.slug}" target="_blank"> 
                                            <button type="button"  class="btn btn-info btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-link"></i>
                                            </button>
                                        </a>
                                    </span>
                                `;
                                @if (session('user_permissions.blogmodule.blog.view') == '1')
                                     actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.blogmodule.blog.edit') == '1')
                                    let editBlogUrl =
                                        `{{ route('admin.editblog', '__id__') }}`.replace(
                                            '__id__', data);
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <a href='${editBlogUrl}'>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0 mb-2">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.blogmodule.blog.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-id= '${data}' class=" del-btn btn btn-danger btn-rounded btn-sm my-0 mb-2">
                                                <i class="ri-delete-bin-fill"></i>
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

            // delete bank             
            $(document).on("click", ".del-btn", function() {

                var deleteid = $(this).data('id');
                var row = this;
                let blogDeleteUrl = "{{ route('blog.delete', '__deleteId__') }}".replace(
                    '__deleteId__',
                    deleteid);
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this blog!', // Text
                    'Yes, delete it!', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    function() {
                        loadershow();
                        $.ajax({
                            type: 'PUT',
                            url: blogDeleteUrl,
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

            // view bank data in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, blog) {
                    if (blog.id == data) {
                        $('#details').append(`
                            <tr>
                                <th>Categories</th>
                                <td>${blog.categories || '-'}</td>
                            </tr>
                            <tr>
                                <th>Tags</th>
                                <td>${blog.tags || '-'}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>${blog.title || '-'}</td>
                            </tr>
                            <tr>
                                <th>Short Description</th>
                                <td>${blog.short_desc || '-'}</td>
                            </tr>
                            <tr>
                                <th>Published On</th>
                                <td>${blog.created_at_formatted || '-'}</td>
                            </tr>
                            <tr>
                                <th>Published By</th>
                                <td>${blog.author || '-'}</td>
                            </tr>
                        `);
                    }
                });
            });
        });
    </script>
@endpush
