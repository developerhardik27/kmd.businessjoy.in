@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Cron Jobs
@endsection
@section('table_title')
    Cron Jobs
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
    <table id="data" class="table  table-bordered display w-100 table-striped">
        <thead>
            <tr>
                <th>Id</th>
                <th>Task Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Last Run Time</th>
                <th>Next Run Time</th>
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>
    <div id="cron_overdue" style="display: none;" role="alert">
    </div>
@endsection


@push('ajax')
    <script>
        function showWarning(message) {
            // Check if already exists
            if ($('#cronWarning').length === 0) {
                const warningHtml = `
                    <div id="cronWarning" class="alert alert-warning mt-3">
                        ${message}
                    </div>
                `;
                $('#cron_overdue').show();
                $('#cron_overdue').append(warningHtml);
            } else {
                $('#cron_overdue').show();
                $('#cronWarning').html(message);
            }
        }

        function removeWarning() {
            $('#cron_overdue').hide();
            $('#cronWarning').remove();
        }


        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            var global_response = '';
            let table = '';

            // load slow page records data in table 
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
                        url: "{{ route('getcronjobs') }}",
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

                            // Check if sync:scheduled-tasks cron is behind
                            // Check if sync:scheduled-tasks is overdue
                            const now = new Date();
                            let cronIsOverdue = false;

                            if (Array.isArray(json.data)) {
                                for (const item of json.data) {
                                    if (item.name === 'sync:scheduled-tasks' && item.next_run) {
                                        // Normalize next_run format:
                                        const trimmed = item.next_run.trim();
                                        const normalized = trimmed.length === 16 ? trimmed + ':00' :
                                            trimmed;

                                        // Convert "YYYY-MM-DD HH:mm:ss" to ISO "YYYY-MM-DDTHH:mm:ss"
                                        const nextRun = new Date(normalized.replace(' ', 'T'));

                                        if (!isNaN(nextRun.getTime()) && nextRun < now) {
                                            cronIsOverdue = true;
                                            showWarning(
                                                `⚠️ The 'sync:scheduled-tasks' cron job is overdue.`
                                            );
                                            break; // no need to check further once found overdue
                                        }
                                    }
                                }
                            }

                            if (!cronIsOverdue) {
                                removeWarning();
                            }


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
                            data: 'name',
                            name: 'name',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                return data == 1 ? `<span class="text-success">Active</span>` :
                                    `<span class="text-danger">InActive</span>`;
                            }
                        },
                        {
                            data: 'last_run',
                            name: 'last_run',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-'
                        },
                        {
                            data: 'schedule_formatted',
                            name: 'schedule_formatted',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                if (row.is_active == 1) {
                                    return data;
                                } else {
                                    return '-';
                                }
                            }
                        }
                    ],

                    pagingType: "full_numbers",
                    drawCallback: function(settings) {
                        $('[data-toggle="tooltip"]').tooltip({
                            boundary: 'window',
                            offset: '0, 10' // Push tooltip slightly away from the button
                        });

                        // Jump to Page input injection
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
        });
    </script>
@endpush
