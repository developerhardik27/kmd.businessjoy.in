@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')
@section('page_title')
    {{ config('app.name') }} - Grade
@endsection
@section('table_title')
    Grade
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

@section('table-content')
    @if (session('user_permissions.teamodule.grade.add') == '1' )
        <div class="float-right" id="newColBtnDiv">
            <button data-toggle="tooltip" id="newColBtn" data-placement="bottom" data-original-title="Add New Grade"
                class="btn btn-sm btn-primary">
                <span class="">+ New</span>
            </button>
        </div>
    @endcan
    <div id="gradeFormWrapper" class="d-none">
        <form id="gradeform">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div class="col-sm-6 mb-2">
                        <input type="hidden" id="edit_id" value="{{ $edit_id ?? '' }}">
                        <input type="hidden" name="token" class="form-control" value="{{ session('api_token') }}"
                            placeholder="token" required />
                        <input type="hidden" value="{{ session('user_id') }}" class="form-control" name="user_id">
                        <input type="hidden" value="{{ session('company_id') }}" class="form-control"
                            name="company_id">

                        <label for="grade">Grade name</label><span class="" style="color:red;">*</span>
                        <input type="text" id="grade" class="form-control " name='grade'
                            placeholder="grade  Name">
                        <span class="error-msg" id="error-grade" style="color: red"></span>
                    </div>
                    <div class="col-sm-12">
                        <button type="button" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Cancel" id="cancelbtn"
                            class="btn btn-secondary float-right">Cancel</button>
                        <button type="reset" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Reset Grade Details"
                            class="btn iq-bg-danger float-right mr-2">Reset</button>
                        <button type="submit" data-toggle="tooltip" data-placement="bottom"
                            data-original-title="Save Grade Details"
                            class="btn btn-primary float-right my-0">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <table id="data" class="table display table-bordered table-striped w-100">
        <thead>
            <tr>
                <th>Id</th>
                <th>Grade name</th>
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
            $('#newColBtn').on('click', function() {
                $('#gradeform')[0].reset();
                $('#edit_id').val('');
                $('#gradeFormWrapper').removeClass('d-none');
                $('#newColForm').removeClass('d-none');
                $('#newColBtnDiv').addClass('d-none');
                $('html, body').animate({
                    scrollTop: $('#gradeFormWrapper').offset().top - 100
                }, 400);
            });

            // function for  get gardens data and set it into datatable
            function loaddata() {
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
                        url: "{{ route('grade.index') }}",
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
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'id'
                        },
                        {
                            data: 'grade',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            name: 'grade'
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = '';
                                @if (session('user_permissions.teamodule.grade.view') == '1')
                                    actionBtns += `   
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                            <button type="button"  data-view = '${data}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0 ">
                                                <i class="ri-indent-decrease"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif
                                @if (session('user_permissions.teamodule.grade.edit') == '1')
                                    actionBtns += `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit grade">
                                        <button type="button"
                                            class="btn btn-success btn-rounded btn-sm my-0 edit-btn"
                                            data-id="${data}">
                                            <i class="ri-edit-fill"></i>
                                        </button>
                                    </span>
                                `;
                                @endif


                                @if (session('user_permissions.teamodule.grade.delete') == '1')
                                    actionBtns += `
                                         <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete grade Details">
                                                <button type="button" data-id= '${data}' class="del-btn btn btn-danger btn-rounded btn-sm my-0">
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
                        $('[data-toggle="tooltip"]').tooltip({
                            boundary: 'window',
                            offset: '0, 10' // Push tooltip slightly away from the button
                        });

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
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, grade) {
                    if (grade.id == data) {

                        $('#details').append(`
                        <tr>
                            <th>Id</th>
                            <td>${grade.id || '-'}</td>
                        </tr>
                        <tr>
                            <th>Grade</th>
                            <td>${grade.grade|| '-'}</td>
                        </tr>
                    `);
                    }
                });
            });
            //call data function for load garden data
            loaddata();
            $('#cancelbtn').click(function() {
                $('#newColForm').addClass('d-none');
                $('#gradeFormWrapper').addClass('d-none');

                $('#newColBtnDiv').removeClass('d-none');
                $('#gradeform')[0].reset();

            });
            loaderhide();
            $(document).on('click', '.edit-btn', function() {
                let editId = $(this).data('id');
                loadershow();
                $('#edit_id').val(editId);
                $('#gradeFormWrapper').removeClass('d-none');
                $('#newColForm').removeClass('d-none');
                $('#newColBtnDiv').addClass('d-none');
                let url = "{{ route('grade.edit', '__id__') }}".replace('__id__', editId);

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        token: "{{ session('api_token') }}",
                        company_id: "{{ session('company_id') }}",
                        user_id: "{{ session('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status === 200) {
                            $('#error-grade').text('');

                            $('#grade').val(response.garde.grade);
                            $('html, body').animate({
                                scrollTop: $('#gradeform').offset().top - 100
                            }, 500);
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function() {
                        loaderhide();
                        Toast.fire({
                            icon: "error",
                            title: "Failed to load grade data"
                        });
                    }
                });
            });

            // submit garden form 
            $('#gradeform').on('submit', function(e) {
                e.preventDefault();
                loadershow();

                let edit_id = $('#edit_id').val();
                let url = edit_id ?
                    "{{ route('grade.update', '__id__') }}".replace('__id__', edit_id) :
                    "{{ route('grade.store') }}";

                let method = edit_id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#newColForm').addClass('d-none');
                            $('#gradeFormWrapper').addClass('d-none');

                            $('#newColBtnDiv').removeClass('d-none');
                            $('#gradeform')[0].reset();
                            table.draw();
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr) {
                        loaderhide();
                        handleAjaxError(xhr);
                    }
                });
            });
            // record delete 
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
                        let gardenDeleteUrl = "{{ route('grade.delete', '__deleteId__') }}"
                            .replace(
                                '__deleteId__', deleteid);
                        $.ajax({
                            type: 'PUT',
                            url: gardenDeleteUrl,
                            data: {
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}",
                            },
                            success: function(response) {
                                loaderhide();
                                if (response.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: response.message
                                    });
                                    loaddata();
                                    table.drow();
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



        });
    </script>
@endpush
