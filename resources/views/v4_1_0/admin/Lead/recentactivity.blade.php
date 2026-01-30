@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Lead Recent Activity
@endsection
@section('table_title')
    Lead Recent Activity
@endsection

@section('style')
    {{-- lead style --}}
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

        .select2 {
            min-width: 100% !important;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection 

@section('table-content')
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Lead Name</th> 
                <th>Lead Title</th>
                <th>Activity Type</th>
                <th>Activity Description</th>
                <th>Perform By</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table> 
    <div class="col-12 p-0 text-primary">
        <p>*Displaying record from the past 90 days.</p>
    </div>
@endsection


@push('ajax')
    <script> 
        $(document).ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

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
                        url: "{{ route('lead.recentactivityindex') }}",
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
                            data: 'lead_name',
                            name: 'lead_name',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'lead_title',
                            name: 'lead_title',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'created_by_name',
                            name: 'created_by_name',
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
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let actionBtns = ``; 

                                @if (session('user_permissions.leadmodule.recentactivity.delete') == '1')
                                    actionBtns += `
                                        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Delete Activity">
                                            <button type="button" data-uid= '${row.id}' class="del-btn btn btn-danger btn-rounded btn-sm my-1">
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

           
            // lead delete
            $(document).on("click", ".del-btn", function() {
                var id = $(this).data('uid');
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
                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('lead.recentactivitydelete') }}",
                            data: {
                                id: id,
                                token: "{{ session()->get('api_token') }}",
                                company_id: "{{ session()->get('company_id') }}",
                                user_id: "{{ session()->get('user_id') }}"
                            },
                            success: function(data) {
                                if (data.status == false) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });
                                } else if (data.status == 500) {
                                    Toast.fire({
                                        icon: "error",
                                        title: data.message
                                    });

                                } else {
                                    Toast.fire({
                                        icon: "success",
                                        title: data.message
                                    });
                                    table.draw();
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


        });
    </script>
@endpush
