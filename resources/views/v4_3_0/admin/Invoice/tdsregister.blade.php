@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - TDS Register
@endsection
@section('table_title')
    TDS Register
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
    <table id="data" class="table display table-bordered w-100 table-striped">
        <thead>
            <tr>
                <th>TDS Date</th>
                <th>Invoice No</th>
                <th>Customer/Company</th>
                <th>Challan No</th>
                <th>TDS Amount</th>
                <th>Entered From</th>
                <th>Status</th>
                <th>Credited</th> 
            </tr>
        </thead>
        <tbody id="tabledata">

        </tbody>
    </table>  
@endsection

@push('ajax')
    <script>
        let isEventBound = false;
        $('document').ready(function() {
            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data
 
            let table = '';

            var global_response = '';


            // function for  get invoice data and set it table
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
                        url: "{{ route('tdsregister.list') }}",
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
                            data: 'tds_date_formatted',
                            name: 'tds_date_formatted',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'inv_no',
                            name: 'inv_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                        },
                        {
                            data: 'customer',
                            name: 'customer',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'challan_no',
                            name: 'challan_no',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-'
                        },
                        {
                            data: 'tds_amount',
                            name: 'tds_amount',
                            orderable: false,
                            searchable: true,
                            defaultContent: '-',
                        }, 
                        {
                            data: 'tds_amount',
                            name: 'tds_amount',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render : function (date, type, row){
                                let label = 'TDS-Only Entry';
                                if(row.tds_amount > 0 && row.paid_amount > 0){
                                    label = 'Payment + TDS';
                                }
                                return label;
                            }
                        },
                        {
                            data: 'tds_status',
                            name: 'tds_status',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                actions = row.tds_status;
                                @if (session('user_permissions.invoicemodule.tdsregister.edit') == '1')
                                    actions = `  
                                        <select data-status='${row.id}' data-original-value="${row.tds_status}" class="status form-control" id="status_${row.id}" name="" required >
                                            <option disabled="">Select Status</option>
                                            <option value="Recorded" ${row.tds_status == "Recorded" ? 'selected' : ''}>Recorded</option>
                                            <option value="Mapped to Challan" ${row.tds_status == "Mapped to Challan" ? 'selected' : ''}>Mapped to Challan</option>
                                            <option value="Filed in Return" ${row.tds_status == "Filed in Return" ? 'selected' : ''}>Filed in Return</option>
                                            <option value="Reconciled (matches 26AS)" ${row.tds_status == "Reconciled (matches 26AS)" ? 'selected' : ''}>Reconciled (matches 26AS)</option>
                                        </select>
                                    `;
                                @endif

                                return actions;

                            }
                        }, 
                        {
                            data: 'tds_credited',
                            name: 'tds_credited',
                            orderable: true,
                            searchable: true,
                            defaultContent: '-',
                            render: function(data, type, row) {
                                actions = row.tds_credited;
                                @if (session('user_permissions.invoicemodule.tdsregister.edit') == '1')
                                    actions = `  
                                        <select data-status='${row.id}' data-original-value="${row.tds_credited}" class="tdscredited form-control" id="credited_${row.id}" name="" required >
                                            <option disabled="">Select Status</option>
                                            <option value="1" ${row.tds_credited == "1" ? 'selected' : ''}>Yes</option>
                                            <option value="0" ${row.tds_credited == "0" ? 'selected' : ''}>No</option>
                                        </select>
                                    `;
                                @endif

                                return actions;

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
            //call data function for load customer data
            loaddata();

         
            //status change function
            function statuschange(id, value, statustype = 'status') {
                loadershow();
                let tdsStatusUrl = "{{ route('tdsregister.updatestatus', '__id__') }}".replace('__id__', id);
                if (statustype != 'status'){
                    tdsStatusUrl = "{{ route('tdsregister.updatecreditedstatus', '__id__') }}".replace('__id__', id);
                }
                $.ajax({
                    type: 'PUT',
                    url: tdsStatusUrl,
                    data: {
                        status: value,
                        token: "{{ session()->get('api_token') }}",
                        company_id: " {{ session()->get('company_id') }}",
                        user_id: " {{ session()->get('user_id') }}"
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                            loaddata();
                        } else if (response.status == 500) {
                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "Status not updated."
                            });
                        }
                        loaderhide();
                    },
                    error: function(xhr, status, error) { // if calling api request error 
                        loaderhide();
                        console.log(xhr
                            .responseText); // Log the full error response for debugging
                        handleAjaxError(xhr);
                    }
                });
            }

            //call status change function
            $(document).on("change", ".status", function() {
                var element = $(this);
                var oldstatus = element.data('original-value'); //get original invoice status value
                var statusid = element.data('status'); // get invoice id
                var status = element.val(); //get current value
                showConfirmationDialog(
                    'Are you sure?',
                    'to change this record status ?',
                    'Yes, change it',
                    'No, cancel',
                    'question',
                    () => {
                        loadershow();
                        element.data('original-value', status); // set current value to original value
                        statuschange(statusid, status);
                        loaderhide(); // Success callback
                    },
                    () => {
                        $('#status_' + statusid).val(oldstatus);
                    }
                );
            });


            //call status change function on update credited status
            $(document).on("change", ".tdscredited", function() {
                var element = $(this);
                var oldstatus = element.data('original-value'); //get original invoice status value
                var statusid = element.data('status'); // get invoice id
                var status = element.val(); //get current value
                showConfirmationDialog(
                    'Are you sure?',
                    'to change this record status ?',
                    'Yes, change it',
                    'No, cancel',
                    'question',
                    () => {
                        loadershow();
                        element.data('original-value', status); // set current value to original value
                        statuschange(statusid, status, 'credited');
                        loaderhide(); // Success callback
                    },
                    () => {
                        $('#credited_' + statusid).val(oldstatus);
                    }
                );
            });

        });
    </script>
@endpush
