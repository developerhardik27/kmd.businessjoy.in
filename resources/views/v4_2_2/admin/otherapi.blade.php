@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Api Server Key
@endsection
@section('table_title')
    Api Server Key
@endsection

@section('style')
    <style>
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


@if (session('user_permissions.leadmodule.leadapi.add') == '1')
    @section('addnew')
        #
    @endsection
    @section('addnewbutton')
        <button class="btn btn-sm btn-primary" id="createNewServerKey">
            <span data-toggle="tooltip" data-placement="bottom" data-original-title="Create New Server Key">
                + Create New
            </span>
        </button>
    @endsection
@endif


@section('table-content')
    <div class="card mt-4" style="display: none" id="newServerKeyFormDiv">
        <div class="card-header">
            <h5 class="mb-0">🔐 Generate Server Key</h5>
        </div>
        <div class="card-body">
            <form id="newServerKeyForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ session('user_id') }}" placeholder="user_id" required />
                <input type="hidden" name="token" value="{{ session('api_token') }}" placeholder="token" required />
                <input type="hidden" name="company_id" value="{{ session('company_id') }}" placeholder="company_id"
                    required />
                <input type="hidden" name="module" value="{{ $module }}" />
                <input type="hidden" name="edit_id" id="edit_id" />
                <div class="col-md-12 mb-2">
                    <label for="title">Title</label><span style="color: red">*</span>
                    <input type="text" id="title" name="title" class="form-control"
                        placeholder="Enter server key title" required>
                    <span class="error-msg" id="error-text" style="color: red"></span>
                </div>

                <div class="col-md-12 mb-2">
                    <label for="title">Remarks</label>
                    <textarea id="remarks" name="remarks" class="form-control" rows="3"
                        placeholder="Any additional notes (optional)"></textarea>
                    <span class="error-msg" id="error-remarks" style="color: red"></span>
                </div>

                <div class="col-12 text-right">
                    <button id="submitBtn" type="submit" data-toggle="tooltip" data-placement="bottom"
                        data-original-title="Submit" class="btn btn-primary">
                        <i class="ri-key-line"></i> Generate
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancelBtn" data-toggle="tooltip"
                        data-placement="bottom" data-original-title="Cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Server Key</th>
                <th>Title</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
    <div class="card mt-3">
    <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center">
        <div>
            <h5 class="mb-2 mb-md-0">
                Company ID: <span id="companyuuid"></span>
            </h5>
        </div>
        <button id="copyButton" class="btn btn-sm btn-outline-primary d-none ml-2">Copy</button>
    </div>
</div>
    <div class="card mt-4">
        @if ($module == 'lead')
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📤 API Documentation: Create a New Lead</h5>
            </div>
            <div class="card-body">
                <h6>🔗 <strong>Endpoint</strong></h6>
                <pre><code>POST {{ route('otherapi.addnewlead') }}</code></pre>

                <h6>🛡 <strong>Required Headers</strong></h6>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Header</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>X-Server-Key</code></td>
                            <td>string</td>
                            <td>API authentication key (generated via app)</td>
                        </tr>
                    </tbody>
                </table>

                <h6>📥 <strong>Request Body Parameters (JSON)</strong></h6>
                <p>Required fields are marked with <strong class="text-danger">*</strong></p>

                <pre><code>{
        "company_id": fa-dfsajkaeio.....,     // Provided by us when you generate server key first time
        "first_name": "John",                 // <span class="text-danger">*</span>
        "last_name": "Doe",                   // <span class="text-danger">*</span>
        "email": "john@example.com",
        "contact_no": "+15551234567",
        "lead_title": "Interested in Product X",
        "title": "Marketing Manager",
        "budget": 10000,
        "company": "Acme Corp",
        "audience_type": "cool",
        "customer_type": "new", 
        "last_follow_up": "2025-06-10",
        "next_follow_up": "2025-06-20",
        "number_of_follow_up": 2,
        "web_url": "https://acme.com", 
        "notes": "Contacted via email",  
        "source": "LinkedIn",
        "ip": "192.168.1.1",
        "number_of_attempt": 1
    }</code></pre>

                <h6>✅ <strong>Success Response</strong></h6>
                <pre><code>{
        "status": 200,
        "message": "lead successfully created"
    }</code></pre>

                <h6>❌ <strong>Validation Error Response Example</strong></h6>
                <pre><code>{
        "status": 422, 
        "errors": {
            "first_name": ["The first name field is required."],
            "last_name": ["The last name field is required."]
        }
    }</code></pre>

                <h6>❌ <strong>Submittion Error Response Example</strong></h6>
                <pre><code>{
        "status": 500,
        "message": "Lead Not Succesfully Created" 
    }</code></pre>
            </div>
        @elseif($module == 'blog')
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📤 API: Get Blog </h5>
            </div>
            <div class="card-body">
                <h6>🔗 <strong>Endpoint</strong></h6>
                <pre><code>GET {{ route('otherapi.blog') }}</code></pre>

                <h6>🛡 <strong>Required Headers</strong></h6>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Header</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>X-Server-Key</code></td>
                            <td>string</td>
                            <td>API authentication key (generated via app)</td>
                        </tr>
                    </tbody>
                </table>

                <h6>📥 <strong>Request Body Parameters (JSON)</strong></h6>
                <p>Required fields are marked with <strong class="text-danger">*</strong></p>

                <pre><code>{
    "company_id": "fa-dfsajkaeio.....",    // <span class="text-danger">*</span>  Provided by us when you generate server key first time  
    "category" : "test",
    "tag" : "test",
    'recent_post' : 3, // pass limit  
}</code></pre>

                <h6>✅ <strong>Success Response</strong></h6>
                <pre><code>{
    "status": 200,
    "blog": [
        {
            "firstname": "John",
            "lastname": "Doe",
            "id": 1,
            "title": "test",
            "img": "1748238906.png", // use path // {{ config('app.url') }}/blog/thumbnail/1748238906.png
            "slug": "test",
            "short_desc": "test",
            "categories": "test 2",
            "tags": "test",
            "created_at_formatted": "31-12-2024"
        }
    ]
}</code></pre>

                <h6>❌ <strong>No Blog Exists</strong></h6>
                <pre><code>{
    "status": 404,
    "blog": "No Records Found"
}</code></pre>

                <h6>❌ <strong>X-Server-Key/company_id Not Match</strong></h6>
                <pre><code>{
    "error": "Unauthorized"
}</code></pre>
            </div>
            <div class="card-header bg-info text-white mt-2">
                <h5 class="mb-0">📤 API: Get Blog Details</h5>
            </div>
            <div class="card-body">
                <h6>🔗 <strong>Endpoint</strong></h6>
                <pre><code>GET {{ route('otherapi.blogdetails', 'slug') }} -> slug = blogslug</code></pre>

                <h6>🛡 <strong>Required Headers</strong></h6>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Header</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>X-Server-Key</code></td>
                            <td>string</td>
                            <td>API authentication key (generated via app)</td>
                        </tr>
                    </tbody>
                </table>

                <h6>📥 <strong>Request Body Parameters (JSON)</strong></h6>
                <p>Required fields are marked with <strong class="text-danger">*</strong></p>

                <pre><code>{
    "company_id": "fa-dfsajkaeio.....",    // <span class="text-danger">*</span>  Provided by us when you generate server key first time 
}</code></pre>

                <h6>✅ <strong>Success Response</strong></h6>
                <pre><code>{
    "status": 200,
    "blog": [
        {
            "firstname": "parth",
            "lastname": "Goswami",
            "id": 1,
            "title": "test",
            "img": "1748238906.png",
            "short_desc": "test",
            "meta_dsc": "test",
            "meta_keywords": "test, test test",
            "content": "<p>test</p>",
            "cat_ids": "2",
            "categories": "test 2",
            "tags": "test",
            "created_at_formatted": "31-December-2024"
        }
    ]
}</code></pre>

                <h6>❌ <strong>No Blog Exists</strong></h6>
                <pre><code>{
    "status": 404,
    "blog": "No Records Found"
}</code></pre>

                <h6>❌ <strong>X-Server-Key/company_id Not Match</strong></h6>
                <pre><code>{
    "error": "Unauthorized"
}</code></pre>
            </div>
        @endif
        <h6>🗝 <strong>How to Get a Server Key</strong></h6>
        <p>
            Log in To {{ config('app.name') }} → Go to <strong>{{ ucfirst($module) }} &gt; API</strong> → Click
            <strong>Create Server
                Key</strong>.
            <br>Use that key in the <code>X-Server-Key</code> header.
        </p>
    </div>
@endsection


@push('ajax')
    <script>
        $(document).ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            // Copy button handler
            $('#copyButton').on('click', function () {
                const uuid = $('#companyuuid').text().trim();
                if (!uuid) return;

                const tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(uuid).select();
                document.execCommand('copy');
                tempInput.remove();

                // Feedback
                const btn = $(this);
                const originalText = btn.text();
                btn.text('Copied!').prop('disabled', true);

                Toast.fire({
                    icon: "success",
                    title: 'Successfully copied!'
                });

                setTimeout(() => {
                    btn.text(originalText).prop('disabled', false);
                }, 1500);
            });

            // Event delegation for dynamically generated "Copy" buttons
            $('#data').on('click', '.copy-server-key', function () {
                const serverKey = $(this).siblings('.server-key').text().trim();

                const tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(serverKey).select();
                document.execCommand('copy');
                tempInput.remove();

                // Optional: feedback
                const btn = $(this);
                const originalText = btn.text();
                btn.text('Copied!').prop('disabled', true);

                Toast.fire({
                    icon: "success",
                    title: 'Successfully copied!'
                });

                setTimeout(() => {
                    btn.text(originalText).prop('disabled', false);
                }, 1500);
            });

            // show generate new serverkey form
            $('#createNewServerKey').on('click', function() {
                $('#createNewServerKey').hide();
                $('#newServerKeyFormDiv').show();
            });

            // hide and reset generate new serverkey form
            $('#cancelBtn').on('click', function() {
                $('#edit_id').val('');
                $('#submitBtn').html('<i class="ri-key-line"></i> Generate');
                $('#newServerKeyForm')[0].reset();
                $('#newServerKeyFormDiv').hide();
                $('#createNewServerKey').show();
            });

            var global_response = '';

            let table = '';

            // get lead data and set in the table
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
                        url: "{{ route('other.getapiserverkey') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                            d.module = "{{ $module }}";
                        },
                        dataSrc: function(json) {
                            if (json.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: json.message || 'Something went wrong!'
                                })
                            }
                            
                            $('#companyuuid').text(json.companyuuid || 'Not Generated Yet!');

                            // Show or hide the Copy button based on whether a valid ID exists
                            if (json.companyuuid && json.companyuuid.trim() !== '') {
                                $('#copyButton').removeClass('d-none');
                            } else {
                                $('#copyButton').addClass('d-none');
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
                            data: 'server_key',
                            name: 'server_key',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row){
                                return `
                                    <span class="server-key">${data}</span>
                                    <button class="btn btn-sm btn-outline-primary copy-server-key">Copy</button>
                                `;
                            }
                        },
                        {
                            data: 'title',
                            name: 'title',
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
                                    <span class="" data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                        <button type="button" data-view = '${row.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                            <i class="ri-indent-decrease"></i>
                                        </button>
                                    </span>
                                `;

                                @if (session('user_permissions.leadmodule.leadapi.edit') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Edit">
                                            <button type="button" data-id= '${row.id}' class="edit-btn btn btn-success btn-rounded btn-sm my-1">
                                                <i class="ri-edit-fill"></i>
                                            </button>
                                        </span>
                                    `;
                                @endif

                                @if (session('user_permissions.leadmodule.leadapi.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete">
                                            <button type="button" data-id= '${row.id}' class="del-btn btn btn-danger btn-rounded btn-sm my-1">
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

            loaddata();

            // view individual lead data
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, serverkey) {
                    if (serverkey.id == data) {
                        $('#details').append(` 
                        <tr> 
                            <th>Server Key</th>
                            <td>${serverkey.server_key || '-'}</td>
                        </tr>  
                        <tr> 
                            <th>Title</th>
                            <td>${serverkey.title || '-'}</td>
                        </tr>  
                        <tr> 
                            <th>Created On</th>
                            <td>${serverkey.created_at_formatted || '-'}</td>
                        </tr>  
                        <tr> 
                            <th>Remarks</th>
                            <td><div style="white-space: pre-line;">${serverkey.remarks || '-'}</div></td>
                        </tr>  
                     `);
                    }
                });
            });

            // edit record   
            $(document).on("click", ".edit-btn", function() {
                var id = $(this).data('id');
                $('#edit_id').val(id);
                $('#submitBtn').html('<i class="ri-key-line"></i> Update Details');
                $('#createNewServerKey').hide();
                $('#newServerKeyFormDiv').show();
                $.each(global_response.data, function(key, serverkey) {
                    if (serverkey.id == id) {
                        $('#title').val(serverkey.title);
                        $('#remarks').val(serverkey.remarks);
                    }

                });
            });

            // delete record
            $(document).on("click", ".del-btn", function() {
                var id = $(this).data('id');
                showConfirmationDialog(
                    'Are you sure?', // Title
                    'to delete this record?', // Text
                    'Yes, delete', // Confirm button text
                    'No, cancel', // Cancel button text
                    'question', // Icon type (question icon)
                    () => {
                        // Success callback
                        loadershow();
                        serverKeyDeleteUrl = "{{ route('other.deleteserverkey', '__id__') }}".replace(
                            '__id__', id);
                        $.ajax({
                            type: 'PUT',
                            url: serverKeyDeleteUrl,
                            data: {
                                id: id,
                                module: "{{ $module }}",
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    table.draw();
                                } else {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
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
                                reject(errorMessage);
                            }
                        });
                    }
                );
            })

            // submit server key form
            $('#newServerKeyForm').submit(function(event) {
                event.preventDefault();
                $('.error-msg').text('');
                loadershow();
                let edit_id = $('#edit_id').val();
                if (edit_id) {
                    url = "{{ route('other.updateserverkey', '__id__') }}".replace('__id__', edit_id);
                } else {
                    url = "{{ route('other.generateserverkey') }}";
                }
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            table.draw();
                            $('#newServerKeyForm')[0].reset();
                            $('#newServerKeyFormDiv').hide();
                            $('#createNewServerKey').show();
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: response.message || 'Something went wrong!'
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
                })
            });
        });
    </script>
@endpush
