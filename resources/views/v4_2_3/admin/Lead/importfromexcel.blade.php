@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.masterlayout')

@section('page_title')
    {{ config('app.name') }} - Import New Lead
@endsection
@section('title')
    Import New Lead
@endsection

@section('style')
    <style>
        .multiselect {
            border: 0.5px solid #00000073;
        }
    </style>
@endsection

@section('form-content')
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <strong><i class="fa fa-info-circle"></i> How to Use the Lead Import Template</strong>
        </div>
        <div class="card-body">
            <ul class="mb-3">
                <li>Click the button below to <strong>download the example Excel file</strong>.</li>
                <li>The file contains two sheets:
                    <ul>
                        <li><strong>Leads:</strong> This sheet includes an example record. <span class="text-danger">Please
                                delete or replace it</span> with your own lead data.</li>
                        <li><strong>Reference:</strong> This sheet provides valid options for fields such as <em>Job
                                Title</em>, <em>Budget</em>, <em>Status</em>, <em>Lead Stage</em>, and <em>Customer
                                Type</em>.</li>
                    </ul>
                </li>
                <li>Ensure your values match the reference sheet exactly to avoid import errors.</li>
                <li>After updating the file, use the form below to upload your lead data.</li>
                <li><strong>Note:</strong> The <code>first_name</code> and <code>last_name</code> fields are required for
                    each lead.</li>
                <li><strong>Important:</strong> The sheet containing lead data must be named <code>Leads</code>.</li>
            </ul>

            <a href="{{ route('lead.importtemplatedownload') }}" class="text-white btn btn-info btn-sm">
                <i class="fa fa-download"></i> Download Example Excel File
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">
            <strong><i class="fa fa-upload"></i> Upload Lead File</strong>
        </div>
        <div class="card-body">
            <div id="import-message"></div>
            <form id="leadimportform" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ session('user_id') }}">
                <input type="hidden" name="company_id" value="{{ session('company_id') }}">
                <input type="hidden" name="token" value="{{ session('api_token') }}">
                <div class="form-group">
                    <label for="lead_file">Choose Excel File (.xlsx)</label>
                    <input type="file" class="form-control-file" id="lead_file" name="lead_file"
                        accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                </div>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-check"></i> Import Leads
                </button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <strong><i class="ri-file-upload-line"></i> Import History</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="data" class="table display table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Import Date</th>
                            <th>Total Rows</th>
                            <th>Success</th>
                            <th>Failed</th>
                            <th>Imported By</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody id="tabledata">
                        <!-- DataTables will populate rows here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle"><span id="viewmodaltitle"><b>Details</b></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="details" width='100%' class="table table-bordered table-responsive-md table-striped">
                    </table>
                </div>
                <div class="modal-footer">
                    <span id="addfooterbutton"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {
            let table = '';
            let global_response;

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
                        url: "{{ route('lead.importhistory') }}",
                        data: function(d) {
                            d.user_id = "{{ session()->get('user_id') }}";
                            d.company_id = "{{ session()->get('company_id') }}";
                            d.token = "{{ session()->get('api_token') }}";
                        },
                        dataSrc: function(json) {
                            if (json.status && json.status !== 200) {
                                Toast.fire({
                                    icon: 'error',
                                    title: json.message || 'Something went wrong!'
                                });
                                return []; // Return empty array so DataTables shows 'No data available' message
                            }

                            if (!json.data || json.data.length === 0) {
                                // Optionally, show some toast or leave DataTables default "No data available"
                                // Toast.fire({ icon: 'info', title: 'No data found' });
                                return [];
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
                        [1, 'desc']
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id',
                            orderable: true,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'created_at_formatted',
                            name: 'created_at_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'total_count',
                            name: 'total_count',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'imported_count',
                            name: 'imported_count',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'fail_count',
                            name: 'fail_count',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'created_by_name',
                            name: 'created_by_name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                let actionBtns = `
                                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="View Details">
                                        <button type="button"  data-view = '${row.id}' data-toggle="modal" data-target="#exampleModalScrollable" class="view-btn btn btn-info btn-rounded btn-sm my-0">
                                            <i class="ri-indent-decrease"></i>
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

            // view import history in pop-up
            $(document).on("click", ".view-btn", function() {
                $('#details').html('');
                var data = $(this).data('view');
                $.each(global_response.data, function(key, importhistorydetails) {
                    if (importhistorydetails.id == data) {
                        $('#details').append(`
                                <tr>
                                    <th>Import Date</th>
                                    <td>${importhistorydetails.created_at_formatted || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Total Rows</th>
                                    <td>${importhistorydetails.total_count || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Success</th>
                                    <td>${importhistorydetails.imported_count || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Failed</th>
                                    <td>${importhistorydetails.fail_count || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Success Rows</th>
                                    <td style="word-break: break-word; white-space: normal;">${importhistorydetails.success_rows || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Failed Rows</th>
                                    <td style="word-break: break-word; white-space: normal;">${importhistorydetails.error_rows || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Imported By</th>
                                    <td>${importhistorydetails.created_by_name || '-'}</td>
                                </tr>
                        `);
                    }
                });
            });

            $('#leadimportform').submit(function(e) {
                e.preventDefault();
                $('#import-message').html('');
                loadershow(); // your custom loader function

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('lead.importfromexcel') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        loaderhide();

                        if (response.status === 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            $('#leadimportform')[0].reset();
                            table.draw();
                        } else if (response.status === 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "warning",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        loaderhide();
                        $('#leadimportform')[0].reset();
                        table.draw();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let output =
                                `<div class="alert alert-danger flex-column">
                                    <strong>Validation failed for the following rows:</strong><br/>
                                    <ul>`;
                            $.each(errors, function(row, fields) {
                                output += `<li><strong>${row}</strong><ul>`;
                                $.each(fields, function(field, message) {
                                    output += `<li>${field}: ${message}</li>`;
                                });
                                output += '</ul></li>';
                            });
                            output += '</ul></div>';
                            $('#import-message').html(output);
                        } else {
                            let message = "An error occurred. Please try again.";
                            try {
                                let json = JSON.parse(xhr.responseText);
                                message = json.message || message;
                            } catch (e) {}
                            Toast.fire({
                                icon: "error",
                                title: message
                            });
                        }
                    }
                });
            });

        });
    </script>
@endpush
