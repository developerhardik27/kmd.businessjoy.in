@php
    $folder = session('folder_name');
@endphp

@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Create Letter
@endsection

@section('table_title')
    Create Letter
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/theme.css">
    <style>
        .letter-preview {
            width: 100%;
            border: 1px solid #000;
            padding: 20px;
            margin-top: 20px;
            background: #fff;
            box-sizing: border-box;
        }

        .preview-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .preview-section img {
            max-height: 80px;
            object-fit: contain;
        }

        .body-content {
            margin-bottom: 15px;
        }

        .flex-left {
            flex-direction: row;
        }

        .flex-right {
            flex-direction: row-reverse;
        }

        .flex-center {
            justify-content: center;
            text-align: center;
            flex-wrap: wrap;
        }

        .flex-center img,
        .flex-center .text {
            margin: 0 auto;
        }

        .text {
            max-width: 70%;
        }
    </style>
@endsection

@section('table-content')
    @if (session('user_permissions.hrmodule.letters.add') == '1')
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#letterModal">
            Create Letter
        </button>
        <div class="modal fade" id="letterModal" tabindex="-1" role="dialog" aria-labelledby="letterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document"> <!-- modal-xl for large modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="letterModalLabel">Create Letter</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="letterForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="token" value="{{ session('api_token') }}" />
                            <input type="hidden" name="user_id" value="{{ session('user_id') }}" />
                            <input type="hidden" name="company_id" value="{{ session('company_id') }}" />

                            <div class="form-group">
                                <label>Letter Name <span class="text-danger">*</span></label>
                                <input type="text" name="letter_name" class="form-control"
                                    placeholder="Enter letter Name">
                                <span class="error-msg" id="error-letter_name" style="color:red"></span>
                            </div>


                            <div class="card mb-3">
                                <div class="card-header">Header</div>
                                <div class="card-body">
                                    <div class="form-row mb-2">
                                        <div class="col-md-4">
                                            <label>Header Image</label>
                                            <input type="file" name="header_image" class="form-control">
                                            <span class="error-msg" style="color:red"></span>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Alignment</label>
                                            <select name="header_align" class="form-control">
                                                <option value="left">Left</option>
                                                <option value="center">Center</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Width (%)</label>
                                            <input type="number" name="header_width" class="form-control" value="30">
                                        </div>
                                    </div>
                                    <label>Header Content</label>
                                    <textarea name="header_content" placeholder="Enter header content" class="form-control"></textarea>
                                </div>
                            </div>


                            <div class="card mb-3">
                                <div class="card-header">Body</div>
                                <div class="card-body">
                                    <textarea name="body_content" placeholder="Enter body content" class="form-control"></textarea>
                                </div>
                            </div>


                            <div class="card mb-3">
                                <div class="card-header">Footer</div>
                                <div class="card-body">
                                    <div class="form-row mb-2">
                                        <div class="col-md-4">
                                            <label>Footer Image</label>
                                            <input type="file" name="footer_image" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Alignment</label>
                                            <select name="footer_align" class="form-control">
                                                <option value="left">Left</option>
                                                <option value="center">Center</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Width (%)</label>
                                            <input type="number" name="footer_width" class="form-control" value="30">
                                        </div>
                                    </div>
                                    <label>Footer Content</label>
                                    <textarea name="footer_content" placeholder="Enter Footer content" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Letter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Letter Name</th>
                <th>Header Align</th>
                <th>Header Width (%)</th>
                <th>Footer Align</th>
                <th>Footer Width (%)</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

@push('ajax')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        loaderhide();
        const params = new URLSearchParams({
            user_id: "{{ session()->get('user_id') }}",
            company_id: "{{ session()->get('company_id') }}",
            token: "{{ session()->get('api_token') }}"
        });

        function letterload() {
            table = $('#lettersTable').DataTable({
                language: {
                    lengthMenu: '_MENU_ &nbsp;Entries per page'
                },
                destroy: true, // allows re-initialization
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('letter.index') }}",
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
                        data: 'letter_name',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'letter_name'
                    },
                    {
                        data: 'header_align',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'header_align'
                    },
                    {
                        data: 'header_width',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'header_width'
                    },
                    {
                        data: 'footer_align',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'footer_align'
                    },
                    {
                        data: 'footer_width',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'lastname'
                    },
                    {
                        data: 'created_by',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'created_by'
                    },
                    {
                        data: 'created_at',
                        orderable: true,
                        searchable: true,
                        defaultContent: '-',
                        name: 'created_at'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let actionBtns = '';
                            @if (session('user_permissions.hrmodule.letters.edit') == '1')
                                let editlettersUrl =
                                    `{{ route('admin.editletter', '__id__') }}`.replace(
                                        '__id__', data);
                                actionBtns += `
                                        <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit letter">
                                            <a href=${editlettersUrl}>
                                                <button type="button" class="btn btn-success btn-rounded btn-sm my-0">
                                                    <i class="ri-edit-fill"></i>
                                                </button>
                                            </a>
                                        </span>
                                    `;
                            @endif

                            @if (session('user_permissions.hrmodule.letters.delete') == '1')
                                actionBtns += `
                                         <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete letter Details">
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
        let headerEditor, bodyEditor, footerEditor;
        const editorConfig = {
            toolbar: [
                'bold', 'italic', 'underline',
                '|', 'fontSize', 'fontColor',
                '|', 'bulletedList', 'numberedList',
                '|', 'insertTable',
                '|', 'undo', 'redo'
            ]
        };
        $('#letterModal').on('shown.bs.modal', function() {

            ClassicEditor
                .create(document.querySelector('#header_content'))
                .then(editor => {
                    headerEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });

            ClassicEditor
                .create(document.querySelector('#body_content'))
                .then(editor => {
                    bodyEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });

            ClassicEditor
                .create(document.querySelector('#footer_content'))
                .then(editor => {
                    footerEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        });

        function toBase64(file) {
            return new Promise((resolve, reject) => {
                if (!file) return resolve('');
                let reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        }
        $('#letterForm').on('submit', async function(e) {
            e.preventDefault();
            loadershow()
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('letter.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    loaderhide();
                    if (response.status == "success") {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        $("#letterForm")[0].reset();
                        window.location = "{{ route('admin.letter') }}";
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                    }

                },
                error: function(xhr) {
                    loaderhide()
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#error-' + key.replace(/\./g, '_')).html(value[
                                0]);

                        });

                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "An error occurred. Please try again."
                        });

                    }
                }
            });
        });
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
                    let consignorDeleteUrl = "{{ route('letter.delete', '__deleteId__') }}"
                        .replace(
                            '__deleteId__', deleteid);
                    $.ajax({
                        type: 'PUT',
                        url: consignorDeleteUrl,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            loaderhide();
                            if (response.status == 'success') {
                                letterload();
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
                            handleAjaxError(xhr);
                        }
                    });
                }
            );
        });


        $(document).ready(function() {
            letterload();
        });
    </script>
@endpush
